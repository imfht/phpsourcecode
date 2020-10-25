<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\util;

use tfc\ap\ErrorException;

/**
 * Image class file
 * 图片处理类，包含生成验证码、缩略图、图片水印、文字水印
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Image.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.util
 * @since 1.0
 */
class Image
{
    /**
     * 生成验证码
     * @param string $fontFile
     * @param array $chars
     * @param integer $width 画布宽
     * @param integer $height 画布高
     * @return void
     * @throws ErrorException 如果字体文件不存在，抛出异常
     * @throws ErrorException 如果参数验证码不是字符数组，抛出异常
     */
    public static function verify($fontFile, $chars, $width = 140, $height = 40)
    {
        if (!is_file($fontFile)) {
            throw new ErrorException(sprintf(
                'Image verify failed, font file "%s" is not a valid directory.', $fontFile
            ));
        }

        if (!is_array($chars)) {
            throw new ErrorException(sprintf(
                'Image verify failed, char "%s" must be a array.', $chars
            ));
        }

        @header('Content-Type: image/png');
        $image = imagecreatetruecolor($width, $height);

        $bgColor = imagecolorallocate($image, mt_rand(0, 155), mt_rand(0, 155), mt_rand(0, 155));
        imagefill($image, 0, 0, $bgColor);

        $fbColor = imagecolorallocate($image, mt_rand(156, 255), mt_rand(156, 255), mt_rand(156, 255));
        $midHeight = $height / 2;
        foreach ($chars as $key => $value) {
            $X = $key * 21 + 20;
            $Y = ($key % 2 === 0) ? 4 : 15;
            $angle = (mt_rand(0, 1) === 1) ? 15 : -15;
            imagettftext($image, 18, $angle, $X, $midHeight + $Y, $fbColor, $fontFile, $value);
        }

        imagerectangle($image, 0, 0, $width - 1, $height - 1, $fbColor);
        // imagerectangle($image, 1, 1, $width - 2, $height - 2, $fbColor);

        $lines = array();
        $thick = 1;
        // 粗横线
        $lines[] = array(
            mt_rand($thick, $width / 2),
            $height / 2,
            mt_rand($width / 2, $width - $thick),
            $height / 2
        );
        // 上斜线
        $lines[] = array(
            mt_rand($thick, $width / 2),
            mt_rand($height / 2, $height - $thick),
            mt_rand($width / 2, $width - $thick),
            mt_rand($thick, $height / 2)
        );
        // 下斜线
        $lines[] = array(
            mt_rand($thick, $width / 2),
            mt_rand($thick, $height / 2),
            mt_rand($width / 2, $width - $thick),
            mt_rand($height / 2, $height - $thick)
        );

        foreach ($lines as $line) {
            imagesetthickness($image, $thick);
            $lineColor = imagecolorallocate($image, mt_rand(150, 255), mt_rand(150, 255), mt_rand(150, 255));
            imageline($image, $line[0], $line[1], $line[2], $line[3], $lineColor);
        }

        for ($i = 0; $i < 60; $i++) {
            $pixColor = imagecolorallocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            imagesetpixel($image, mt_rand(3, $width - 3), mt_rand(3, $height - 3), $pixColor);
        }

        imagepng($image);
        imagedestroy($image);
    }

    /**
     * 生成缩略图
     * @param string $source
     * @param integer $thumbWidth
     * @param integer $thumbHeight
     * @param string $toPath
     * @param boolean $scale 是否按比例缩小
     * @param boolean $inflate 如果原始图片比缩略图小，是否放大它们以填充缩略图
     * @return boolean
     * @throws ErrorException 如果原始图片地址错误，抛出异常
     */
    public static function thumbnail($source, $thumbWidth, $thumbHeight, $toPath = null, $scale = true, $inflate = true)
    {
        if (!($size = @getimagesize($source))) {
            throw new ErrorException(sprintf(
                'Image thumbnail failed, source path "%s" is not a valid file.', $source
            ));
        }

        $source = self::create($source, $size['mime']);
        if (($size[0] <= $thumbWidth) && ($size[1] <= $thumbHeight) && !$inflate) {
            $thumb = $source;
        }
        else {
            if ($scale) {
                if ($size[0] > $size[1]) {
                    $thumbHeight = floor($size[1] * ($thumbWidth / $size[0]));
                }
                elseif ($size[0] < $size[1]) {
                    $thumbWidth = floor($size[0] * ($thumbHeight / $size[1]));
                }
            }

            $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
            imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $size[0], $size[1]);
        }

        return self::build($thumb, $size['mime'], $toPath);
    }

    /**
     * 生成文字水印
     * @param string $source
     * @param string $text
     * @param string $fontFile
     * @param string $toPath
     * @param integer $position 水印在图片上的位置
     * 1: 顶端居左      2: 顶端居中      3: 顶端居右
     * 4: 中间居左      5: 中间居中      6: 中间居右
     * 7: 下端居左      8: 下端居中      9: 下端居右
     * @param integer $offset 水印图片离原始图片边框的距离
     * @return boolean
     * @throws ErrorException 如果原始图片地址错误，抛出异常
     */
    public static function textWater($source, $text, $fontFile, $toPath = null, $position = 9, $offset = 1)
    {
        if (!$sourceSize = @getimagesize($source)) {
            throw new ErrorException(sprintf(
                'Image text water failed, source path "%s" is not a valid file.', $source
            ));
        }

        $textSize = self::getTextSize($text, $fontFile);
        $source = self::create($source, $sourceSize['mime']);
        $positions = self::getPosition($sourceSize[0], $sourceSize[1], $textSize['width'], $textSize['height'], $position, $offset);
        $fontColor = imagecolorallocate($source, 255, 0, 0);
        imagettftext($source, 14, 0, $positions['x'], $positions['y'] + $textSize['height'], $fontColor, $fontFile, $text);
        return self::build($source, $sourceSize['mime'], $toPath);
    }

    /**
     * 获取文字的尺寸
     * @param string $text
     * @param string $fontFile
     * @return array
     */
    public static function getTextSize($text, $fontFile)
    {
        $size = array();
        $box = imagettfbbox(14, 0, $fontFile, $text);
        $minX = min($box[0], $box[2], $box[4], $box[6]);
        $maxX = max($box[0], $box[2], $box[4], $box[6]);
        $minY = min($box[1], $box[3], $box[5], $box[7]);
        $maxY = max($box[1], $box[3], $box[5], $box[7]);
        $size['width'] = $maxX - $minX;
        $size['height'] = $maxY - $minY;
        return $size;
    }

    /**
     * 生成图片水印
     * @param string $source
     * @param string $water
     * @param string $toPath
     * @param integer $position 水印在图片上的位置
     * 1: 顶端居左      2: 顶端居中      3: 顶端居右
     * 4: 中间居左      5: 中间居中      6: 中间居右
     * 7: 下端居左      8: 下端居中      9: 下端居右
     * @param integer $offset 水印图片离原始图片边框的距离
     * @param integer $pct 水印的透明度
     * @return boolean
     * @throws ErrorException 如果水印图片地址错误，抛出异常
     * @throws ErrorException 如果原始图片地址错误，抛出异常
     */
    public static function imageWater($source, $water, $toPath = null, $position = 9, $offset = 1, $pct = 100)
    {
        if (!$waterSize = @getimagesize($water)) {
            throw new ErrorException(sprintf(
                'Image image water failed, water path "%s" is not a valid file.', $source
            ));
        }

        if (!$sourceSize = @getimagesize($source)) {
            throw new ErrorException(sprintf(
                'Image image water failed, source path "%s" is not a valid file.', $source
            ));
        }

        $water = self::create($water, $waterSize['mime']);
        $source = self::create($source, $sourceSize['mime']);
        $positions = self::getPosition($sourceSize[0], $sourceSize[1], $waterSize[0], $waterSize[1], $position, $offset);
        imagecopymerge($source, $water, $positions['x'], $positions['y'], 0, 0, $waterSize[0], $waterSize[1], $pct);

        return self::build($source, $sourceSize['mime'], $toPath);
    }

    /**
     * 根据水印在图片上的位置，计算水印在图片上的具体坐标
     * @param integer $sourceWidth
     * @param integer $sourceHeight
     * @param integer $waterWidth
     * @param integer $waterHeight
     * @param integer $position 水印在图片上的位置
     * 1: 顶端居左      2: 顶端居中      3: 顶端居右
     * 4: 中间居左      5: 中间居中      6: 中间居右
     * 7: 下端居左      8: 下端居中      9: 下端居右
     * @param integer $offset 水印图片离原始图片边框的距离
     * @return array
     */
    public static function getPosition($sourceWidth, $sourceHeight, $waterWidth, $waterHeight, $position = 9, $offset = 1)
    {
        $positions = array();
        switch ($position) {
            case 1: //顶端居左
                $positions['x'] = $offset;
                $positions['y'] = $offset;
                break;
            case 2: //顶端居中
                $positions['x'] = ($sourceWidth - $waterWidth) / 2;
                $positions['y'] = $offset;
                break;
            case 3: //顶端居右
                $positions['x'] = $sourceWidth - $waterWidth - $offset;
                $positions['y'] = $offset;
                break;
            case 4: //中间居左
                $positions['x'] = $offset;
                $positions['y'] = ($sourceHeight - $waterHeight) / 2;
                break;
            case 5: //中间居中
                $positions['x'] = ($sourceWidth - $waterWidth) / 2;
                $positions['y'] = ($sourceHeight - $waterHeight) / 2;
                break;
            case 6: //中间居右
                $positions['x'] = $sourceWidth - $waterWidth - $offset;
                $positions['y'] = ($sourceHeight - $waterHeight) / 2;
                break;
            case 7: //下端居左
                $positions['x'] = $offset;
                $positions['y'] = $sourceHeight - $waterHeight - $offset;
                break;
            case 8: //下端居中
                $positions['x'] = ($sourceWidth - $waterWidth) / 2;
                $positions['y'] = $sourceHeight - $waterHeight - $offset;
                break;
            case 9: //下端居右
            default:
                $positions['x'] = $sourceWidth - $waterWidth - $offset;
                $positions['y'] = $sourceHeight - $waterHeight - $offset;
                break;
        }

        return $positions;
    }

    /**
     * 新建一个图像
     * @param string $source
     * @param string $mime 图片MIME信息
     * @return resource
     */
    public static function create($source, $mime)
    {
        $createFunc = self::getCreateFuncByMime($mime);
        $image = $createFunc($source);
        return $image;
    }

    /**
     * 将图像输出到浏览器或文件
     * @param string $source
     * @param string $mime 图片MIME信息
     * @param string $toPath
     * @return boolean
     */
    public static function build($source, $mime, $toPath = null)
    {
        $buildFunc = self::getBuildFuncByMime($mime);
        if ($toPath !== null) {
            return $buildFunc($source, $toPath);
        }
        else {
            @header('Content-Type: ' . $mime);
            return $buildFunc($source);
        }
    }

    /**
     * 通过图片MIME信息获取新建图片的函数名
     * @param string $mime
     * @return string
     * @throws ErrorException 如果图片MIME信息错误，抛出异常
     */
    public static function getCreateFuncByMime($mime)
    {
        static $createFuncs = array(
            'image/jpeg' => 'imagecreatefromjpeg',
            'image/png'  => 'imagecreatefrompng',
            'image/gif'  => 'imagecreatefromgif',
            'image/bmp'  => 'imagecreatefromwbmp'
        );

        if (!isset($createFuncs[$mime])) {
            throw new ErrorException(sprintf(
                'Image get create func by mime failed, mime "%s" not supported.', $mime
            ));
        }

        return $createFuncs[$mime];
    }

    /**
     * 通过图片MIME信息获取输出图片的函数名
     * @param string $mime
     * @return boolean
     * @throws ErrorException 如果图片MIME信息错误，抛出异常
     */
    public static function getBuildFuncByMime($mime)
    {
        static $buildFuncs = array(
            'image/jpeg' => 'imagejpeg',
            'image/png'  => 'imagepng',
            'image/gif'  => 'imagegif',
            'image/bmp'  => 'imagewbmp'
        );

        if (!isset($buildFuncs[$mime])) {
            throw new ErrorException(sprintf(
                'Image get build func by mime failed, mime "%s" not supported.', $mime
            ));
        }

        return $buildFuncs[$mime];
    }
}
