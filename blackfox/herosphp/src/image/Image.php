<?php
/**
 * 图片处理类
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2017-05-29 v2.0.0
 */
namespace herosphp\image;
use herosphp\files\FileUtils;
use herosphp\string\StringUtils;

class Image {

    /**
     * @var resource 原图图片资源
     */
    protected $imgSrc = null;

    /**
     * @var Image resource 目标图片资源
     */
    private $imgDst;

    /**
     * @var string 源图片路径
     */
    private $fileSrc = '';

    const POS_LEFT_TOP = 1; //水印位置：左上
    const POS_LEFT_BOTTOM = 2; //水印位置：左下
    const POS_CENTER = 3; //水印位置：中部居中
    const POS_RIGHR_TOP = 4; //水印位置：右上
    const POS_RIGHR_BOTTOM = 5; //水印位置：右下
    const POS_RANDOM = 6; //水印位置：随机
    private $position = self::POS_RIGHR_BOTTOM;  //水印位置(置默认在右下角)

    private $extension = ''; //图片后缀

    const THUMB_DIRECT_SCALE = 1; //缩放模式：直接缩放到指定的大小
    const THUMB_UNIFORM_SCALE = 2; //缩放模式：等比缩放，固定一维的尺寸，另一维等比缩放
    const THUMB_CROP_SCALE_TOP = 3; //缩放模式：等比缩放，固定尺寸较小的一维，另一维缩放后从图像顶部裁剪一段出来
    const THUMB_CROP_SCALE_MIDDLE = 4; //缩放模式：等比缩放，固定尺寸较小的一维，另一维缩放后从图像中间裁剪一段出来

    /**
     * 私有化构造方法
     */
    private function __construct() {}

    /**
     * 获取实例
     * @return Image
     */
    public static function getInstance() {
        return new self();
    }

    /**
     * 打开图片
     * @param $sourceImage
     * @return $this
     */
    public function open($sourceImage) {
        $this->imgSrc = &$this->getImageSource($sourceImage);
        $this->imgDst = $this->imgSrc;
        $this->fileSrc = $sourceImage;
        $this->extension = self::getFileExt($this->fileSrc);
        return $this;
    }

    /**
     * 给图片图片水印
     * @param tring $waterImage 水印图
     * @param int $position 水印位置
     * @param int $alpha 水印透明度
     * @return $this
     */
    public function addImageWater($waterImage, $position, $alpha=80) {

        $sizeDst = $this->getImageSize($this->imgSrc);
        if ( !$position ) {
            $this->position = self::POS_RANDOM;
        } else {
            $this->position = $position;
        }

        //创建目标画布
        $this->imgDst = $this->creatImageTrueColor($sizeDst[0], $sizeDst[1], $this->extension);

        //将图像载入画布
        imagecopyresampled($this->imgDst, $this->imgSrc, 0, 0, 0, 0, $sizeDst[0], $sizeDst[1], $sizeDst[0], $sizeDst[1]);

        $imageWater = &$this->getImageSource($waterImage);
        $waterSize = $this->getImageSize($imageWater);	//获取水印大小
        $waterPosition = $this->getWaterPos($sizeDst, $waterSize);	//获取水印位置
        if ( $this->imgDst && $imageWater ) {
            imagecopymerge(
                $this->imgDst,
                $imageWater,
                $waterPosition[0],
                $waterPosition[1],
                0, 0,
                $waterSize[0],
                $waterSize[1], $alpha);
        }
        return $this;

    }

    /**
     * 给图片添加文字水印
     * @param Text $text 水印文字
     * @param int $position 水印位置
     * @return $this
     */
    public function addStringWater(Text $text, $position) {

        if ( !$this->imgDst ) return;

        $this->position = $position;

        if ( !$position ) {
            $this->position = self::POS_RANDOM;
        } else {
            $this->position = $position;
        }
        $fontColor = StringUtils::hex2rgb($text->getColor());
        $color = imagecolorallocatealpha($this->imgDst,
            $fontColor['r'],
            $fontColor['g'],
            $fontColor['b'],
            $text->getAlpha());

        $sizeSrc = $this->getImageSize($this->imgDst);
        if ( !file_exists($text->getFont()) ) {
            $text->setFont(dirname(__FILE__).$text->getFont());
        }

        $ttfBox = imagettfbbox($text->getFontsize(), $text->getAngle(), $text->getFont(), $text->getContent());
        $textWidth = $ttfBox[2] - $ttfBox[0];
        $textHeight = abs($ttfBox[7]);
        $waterPosition = $this->getWaterPos($sizeSrc, array($textWidth, $textHeight));
        if ( $this->imgDst ) {
            imagettftext(
                $this->imgDst,
                $text->getFontsize(),
                $this->angle,
                $waterPosition[0],
                $waterPosition[1]+$textHeight,
                $color,
                $text->getFont(),
                $this->getEncodedText($text->getContent()));
        }

        return $this;

    }

    /**
     * 在图片上绘制文字
     * @param Text $text 要绘制的文字
     * @return $this
     */
    public function drawText(Text $text) {

        if ( !$this->imgDst ) return;

        $fontColor = StringUtils::hex2rgb($text->getColor());
        $color = imagecolorallocatealpha($this->imgDst,
            $fontColor['r'],
            $fontColor['g'],
            $fontColor['b'],
            $text->getAlpha());
        if ( !file_exists($text->getFont()) ) {
            $text->setFont(dirname(__FILE__).$text->getFont());
        }
        $content = $this->distribute($this->imgDst, $text);

        $ttfBox = imagettfbbox($text->getFontsize(), $text->getAngle(), $text->getFont(), "我");
        $wordHeight = abs($ttfBox[7]); //文字高度

        foreach ($content as $num => $line) {
            if ( $text->isVertical() ) {    //绘制垂直文本
                foreach ($line as $key => $word) {
                    imagettftext(
                        $this->imgDst,
                        $text->getFontsize(),
                        $text->getAngle(),
                        $text->getStartX() + ($wordHeight+$text->getLineHeight())*$num ,
                        $text->getStartY() + $wordHeight*($key+0.5),
                        $color,
                        $text->getFont(),
                        $this->getEncodedText($word));
                }
            } else {
                $__text = implode('',$line);
                imagettftext(
                    $this->imgDst,
                    $text->getFontsize(),
                    $text->getAngle(),
                    $text->getStartX() ,
                    $text->getStartY() + $wordHeight*($num+1) + $text->getLineHeight()*$num,
                    $color,
                    $text->getFont(),
                    $this->getEncodedText($__text));
            }
        }
        return $this;
    }

    /**
     * 生成缩略图
     * @param array $size
     * @param int $flag
     * @return $this
     */
    public function thumb($width, $height, $flag=self::THUMB_DIRECT_SCALE)
    {
        $size = array($width, $height);
        $rectangle = $this->getThumbImageRectagle($this->imgSrc, $size, $flag);
        $sizeSrc = $this->getImageSize($this->imgSrc);
        //创建目标图像资源
        $this->imgDst = $this->creatImageTrueColor($rectangle['w'], $rectangle['h'], $this->extension);

        //目标图片的拷贝
        if ( $this->imgSrc && $this->imgDst ) {
            imagecopyresampled(
                $this->imgDst,
                $this->imgSrc,
                0, 0,
                $rectangle['x'],
                $rectangle['y'],
                $rectangle['w'],
                $rectangle['h'],
                $sizeSrc[0]-$rectangle['x']*2,
                $sizeSrc[1]-$rectangle['c_h']);
        }

        return $this;
    }

    /**
     * 裁剪图片,如果不传入$startX, $startY, 则默认从图片正中间开始裁剪
     * @param int $width 裁剪高度
     * @param int $height 裁剪宽度
     * @param int $startX 裁剪起始位置横坐标
     * @param int $startY 裁剪起始位置纵坐标
     * @return $this
     */
    public function crop($width, $height, $startX=null, $startY=null)
    {
        $sizeSrc = $this->getImageSize($this->imgSrc);
        if ( $startX === null ) {
            $startX = ($sizeSrc[0] - $width)/2;
        }
        if ( $startY === null ) {
            $startY = ($sizeSrc[1] - $height)/2;
        }
        $this->imgDst = imagecrop($this->imgDst,
            ['x' => $startX, 'y' => $startY, 'width' => $width, 'height' => $height]);
        return $this;
    }

    /**
     * 获取缩略图矩形框
     * @param resoure $image 原图片资源
     * @param array $size 原始图的尺寸
     * @param int $flag 缩放方式
     * @return array
     */
    private function getThumbImageRectagle($image, $size, $flag)
    {
        $rectangle = array(
            'x' => 0,
            'y' => 0,
            'w' => 0,
            'h' => 0,
            'c_h' => 0, //被裁剪的高度
        );
        $sizeSrc = $this->getImageSize($image);
        switch ( $flag ) {
            //直接缩放
            case self::THUMB_DIRECT_SCALE:
                $rectangle['w'] = $size[0];
                $rectangle['h'] = $size[1];
                break;
            //等比缩放到指定size
            case self::THUMB_UNIFORM_SCALE:
                //获取缩放比例
                $ratio = max($size[0] / $sizeSrc[0], $size[1] / $sizeSrc[1]);
                if ($ratio > 1) {
                    $rectangle['w'] = $size[0];
                    $rectangle['h'] = $size[1];
                    break;
                }
                $rectangle['w'] = floor($sizeSrc[0] * $ratio);
                $rectangle['h'] = floor($sizeSrc[1] * $ratio);
                break;
            //规定高|宽, 等比缩放,长的那一方从中间裁剪掉
            case self::THUMB_CROP_SCALE_MIDDLE:
                $ratio = max($size[0] / $sizeSrc[0], $size[1] / $sizeSrc[1]);
                $rectangle['w'] = $size[0];
                $rectangle['h'] = $size[1];
                if ($ratio > 1) break;

                $w = $sizeSrc[0] * $ratio;
                $h = $sizeSrc[1] * $ratio;
                $rectangle['x'] = (($w - $size[0])/$ratio)/2;
                $rectangle['c_h'] = ($h - $size[1])/$ratio;
                $rectangle['y'] = $rectangle['c_h']/2;
                break;

            //规定高|宽, 等比缩放,长的那一方从顶部裁剪掉
            case self::THUMB_CROP_SCALE_TOP:
                $ratio = max($size[0] / $sizeSrc[0], $size[1] / $sizeSrc[1]);
                $rectangle['w'] = $size[0];
                $rectangle['h'] = $size[1];
                if ($ratio > 1) break;

                $w = $sizeSrc[0] * $ratio;
                $h = $sizeSrc[1] * $ratio;
                $rectangle['x'] = (($w - $size[0])/$ratio)/2;
                $rectangle['c_h'] = ($h - $size[1])/$ratio;
                $rectangle['y'] = 0;
                break;

        }
        return $rectangle;
    }

    /**
     * 获取编码后的文本,解决通过表单接收过来的文本乱码的问题
     * @param $text
     * @return mixed|string
     */
    private function getEncodedText($text) {
        return mb_convert_encoding($text, "html-entities", "utf-8");
    }

    /**
     * 获取离散文本
     * @param $str
     * @return array
     */
    private function getDiscreteText($str) {
        $arr = array();
        for ( $i = 0; $i < strlen($str); $i++ ) {
            if ( $str[$i] == "\n" ) continue;
            if ( ord($str[$i]) < 127 ) {
                array_push($arr, $str[$i]);
                continue;
            }
            if ( ord($str[$i]) > 127 &&
                ord($str[$i+1]) > 127 &&
                ord($str[$i+2]) > 127 ) {
                array_push($arr, substr($str, $i, 3));
                $i += 2;
            }
        }
        return $arr;
    }

    /**
     * 根据文字坐标,字体大小，图片尺寸，获取文字分布，实现自动文本换行
     * @param $image
     * @param $text
     * @return array
     */
    private function distribute($image, Text $text) {

        if ( is_array($text->getContent()) ) {
            $textArray = array();
            foreach ($text->getContent() as $value) {
                $textArray[] = $this->getDiscreteText($value);
            }
            return $textArray;
        }

        $ttfBox = imagettfbbox($text->getFontsize(), $text->getAngle(), $text->getFont(), "我");
        $wordWidth = $ttfBox[2] - $ttfBox[0];  //文字宽度
        $wordHeight = abs($ttfBox[7]); //文字高度
        $imageSize = $this->getImageSize($image); //获取图片尺寸
        $textPool = $this->getDiscreteText($text->getContent()); //获取离散的文本
        $textWidth = $text->getMaxLineWidth(); //每行文本最大宽度
        if ( $textWidth == 0 ) {
            if ( $text->isVertical() ) {
                $textWidth = $imageSize[1]-$text->getStartY();
            } else {
                $textWidth = $imageSize[0]-$text->getStartX();
            }
        }
        if ( $text->isVertical() ) {
            $wordNumPerLine = floor($textWidth/$wordHeight); //每行字符数，垂直文本
        } else {
            $wordNumPerLine = floor($textWidth/$wordWidth); //每行字符数,水平文本
        }
        $textArray = array_chunk($textPool,$wordNumPerLine,false);

        return $textArray;
    }

    /**
     * sava image
     * @param    string $filename 保存新的文件名称
     * @return   $this
     */
    public function save($filename=null) {

        if ( !$filename ) $filename = $this->fileSrc;

        switch ( $this->extension ) {

            case 'jpg':
            case 'jpeg':
                imagejpeg($this->imgDst, $filename, 90);
                break;

            case 'gif':
                imagegif($this->imgDst, $filename);
                break;

            case 'png':
                imagepng($this->imgDst, $filename);
                break;

        }

        return $this;

    }

    /**
     * show the image
     */
    public function show() {

        switch ( $this->extension ) {
            case 'jpg':
            case 'jpeg':
                header("Content-type: image/jpeg");
                imagejpeg($this->imgDst);
                break;

            case 'gif':
                header("Content-type: image/gif");
                imagegif($this->imgDst);
                break;

            case 'png':
                header("Content-type: image/png");
                imagepng($this->imgDst);
                break;

            default:
                die("No image support in this PHP server");

        }
        return $this;
    }

    /**
     * get water postion (获取图片水印的位置)
     * @param array $dstImageSize   	目标图片的大小
     * @param array $waterImageSize    水印图片的大小
     * @return array
     */
    private function getWaterPos($dstImageSize, $waterImageSize) {

        $position = array();

        switch ( $this->position ) {
            case self::POS_LEFT_TOP :	//左上角
                $position[0] = 10;
                $position[1] = 10;
                break;

            case self::POS_RIGHR_TOP :	//右上角
                $position[0] = ($dstImageSize[0] - $waterImageSize[0]) - 10;
                $position[1] = 10;
                break;

            case self::POS_RIGHR_BOTTOM :	//右下角
                $position[0] = ($dstImageSize[0] - $waterImageSize[0])-10;
                $position[1] = ($dstImageSize[1] - $waterImageSize[1])-10;
                break;

            case self::POS_LEFT_BOTTOM :	//左下角
                $position[0] = 10;
                $position[1] = ($dstImageSize[1] - $waterImageSize[1]) - 10;
                break;

            case self::POS_CENTER : 	//居中
                $position[0] = (int) ($dstImageSize[0] - $waterImageSize[0])/2;
                $position[1] = (int) ($dstImageSize[1] - $waterImageSize[1])/2;
                break;

            case self::POS_RANDOM :	//随机
                $position[0] = mt_rand(10, ($dstImageSize[0] - $waterImageSize[0]) - 10);
                $position[1] = mt_rand(10, ($dstImageSize[1] - $waterImageSize[1]) - 10);
                break;
        }
        return $position;
    }

    /**
     * 获取图片尺寸
     * @param $filename
     * @return array
     */
    private function getImageSize($image) {
        return array(imagesx($image), imagesy($image));
    }

    /**
     * 根据图片地址获取图片资源
     * @param string $filename 图片路径
     * @return null|resource
     */
    private function  &getImageSource($filename) {

        $image = NULL;
        $extesion = self::getFileExt($filename);
        switch ( $extesion ) {

            case 'gif':
                $image = imagecreatefromgif($filename);
                break;

            case 'png':
                $image = imagecreatefrompng($filename);
                break;

            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($filename);
                break;

        }
        return $image;
    }

    /**
     * 获取文件名后缀
     * @param $filename
     * @return string
     */
    private static function getFileExt( $filename ) {
        return FileUtils::getFileExt($filename);
    }

    /**
     * 创建真彩色画布
     * @param int $width 画布宽度
     * @param int $height 画布高度
     * @param string $flag 画布类型参数
     * @return image resource 返回图片资源
     */
    private function creatImageTrueColor( $width, $height, $flag ) {

        $image = imagecreatetruecolor($width, $height);
        $color = hexdec("#FFFFFF");
        switch ( $flag ) {
            case 'gif':
                imagecolortransparent($image, $color);
                break;

            default:
                imagecolortransparent($image, $color);
                imagealphablending($image, false);
                imagesavealpha($image, true);
                break;
        }
        return $image;
    }

    /**
     * 销毁图片资源，释放内存
     */
    public function __distruct() {

        if ( $this->imgSrc )
            imagedestroy($this->imgSrc);

        if ( $this->imgDst )
            imagedestroy($this->imgDst);

    }

}