<?php

defined('IN_CART') or die;

/**
 *
 * 图片操作类
 * 
 */
class Image
{

    /**
     *
     * 获取image信息
     *
     */
    static function getImageInfo($img)
    {

        $imageInfo = getimagesize($img);
        if ($imageInfo !== false) {

            $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
            $imageSize = filesize($img);
            $info = array(
                "width" => $imageInfo[0],
                "height" => $imageInfo[1],
                "type" => $imageType,
                "size" => $imageSize,
                "mime" => $imageInfo["mime"]
            );
            return $info;
        }
        return false;
    }

    /**
     *
     * 验证码
     *
     */
    static function ImageVerify()
    {
        //验证码保存到session中
        $verify = "";
        for ($i = 0; $i < 5; $i++) {
            $verify .= getRandString(1);
        }
        $verify = strtoupper($verify);
        $_SESSION["verify"] = $verify;
        header("Content-type: image/png");


        //验证码
        $char_width = imagefontwidth(5);
        $char_height = imagefontheight(5);

        $string_width = 15 * $char_width;
        $string_height = 3 * $char_height;

        $img_width = $string_width + 16;
        $img_height = $string_height + 8;
        $img = @imagecreatetruecolor($img_width, $img_height) or die("imagecreatetruecolor failed");

        // 颜色
        $background_color = imagecolorallocate($img, 238, 238, 238);
        $bg_text_color = imagecolorallocate($img, 191, 191, 191);

        //背景
        imagefill($img, 0, 0, $background_color);

        $bg_char_width = imagefontwidth(1);
        $bg_char_height = imagefontheight(1);
        for ($x = mt_rand(-2, 2); $x < $img_width; $x += $bg_char_width + 1) {
            for ($y = mt_rand(-2, 2); $y < $img_height; $y += $bg_char_height + 1) {
                imagestring($img, mt_rand(1, 5), $x, $y, getRandString(1), $bg_text_color);
            }
        }

        // 验证码
        $font = DATADIR . "/font/arial.ttf";
        $x = 10 + mt_rand(-2, 2);
        $y = 40 + mt_rand(-2, 2);
        for ($i = 0; $i < strlen($verify); $i++) {
            $text_color = imagecolorallocate($img, mt_rand(0, 125), mt_rand(0, 125), mt_rand(0, 125));
            imagettftext($img, mt_rand(30, 40), mt_rand(-30, 30), $x, $y, $text_color, $font, substr($verify, $i, 1));
            //imagestring($img, 5, $x,$y  + mt_rand(-2, 2), substr($verify, $i, 1), $text_color);
            $x += 3 * $char_width;
        }
        imagepng($img);
        imagedestroy($img);
    }

    /**
     *
     * 缩略图
     *
     */
    static function thumb($image, $thumbname, $maxwidth = 0, $maxheight = 0, $interlace = true)
    {
        $info = Image::getImageInfo($image);
        if ($info) {

            $srcwidth = $info["width"];
            $srcheight = $info["height"];

            $type = strtolower($info["type"]);
            if ($type == "jpg")
                $type = "jpeg";

            $interlace = $interlace ? 1 : 0;
            unset($info);

            if ($maxwidth && $maxheight) {
                $scale = min($maxwidth / $srcwidth, $maxheight / $srcheight);
            } else if ($maxwidth) {
                $scale = $maxwidth / $srcwidth;
            } else if ($maxheight) {
                $scale = $maxheight / $srcheight;
            }

            if ($scale >= 1) {
                $width = $srcwidth;
                $height = $srcheight;
            } else {
                $width = (int) ($srcwidth * $scale);
                $height = (int) ($srcheight * $scale);
            }

            $createFun = 'ImageCreateFrom' . $type;
            $srcImg = $createFun($image);

            if ($type != 'gif' && function_exists('imagecreatetruecolor')) {

                $thumbImg = imagecreatetruecolor($width, $height);
            } else {
                $thumbImg = imagecreate($width, $height);
            }

            if (function_exists('imagecopyresampled')) {
                imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcwidth, $srcheight);
            } else {
                imagecopyresized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcwidth, $srcheight);
            }

            if ('gif' == $type || 'png' == $type) {
                $background_color = imagecolorallocate($thumbImg, 0, 255, 0);  //  指派一个绿色
                imagecolortransparent($thumbImg, $background_color);  //  设置为透明色，若注释掉该行则输出绿色的图
            }

            if ('jpg' == $type || 'jpeg' == $type)
                imageinterlace($thumbImg, $interlace);

            $imageFun = 'image' . $type;
            $imageFun($thumbImg, $thumbname);
            imagedestroy($thumbImg);
            imagedestroy($srcImg);
            return $thumbname;
        }
        Return false;
    }

}
