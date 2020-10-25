<?php
/**
 * 图像操作类库 thinkphp
 * @author    liu21st <liu21st@gmail.com>
 */
namespace framework\libraries;

class Image
{

    /**
     * 取得图像信息
     * @static
     * @access public
     * @param $img
     * @internal param string $image 图像文件名
     * @return mixed
     */

    public static function getImageInfo($img) {
        $imageInfo = getimagesize($img);
        if ($imageInfo !== false) {
            //$imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
            $imageType = strtolower(image_type_to_extension($imageInfo[2],false));
            $imageSize = filesize($img);
            $info = array(
                "width" => $imageInfo[0],
                "height" => $imageInfo[1],
                "type" => $imageType,
                "size" => $imageSize,
                "mime" => $imageInfo['mime']
            );
            return $info;
        } else {
            return false;
        }
    }

    /**
     * 为图片添加水印
     * @static public
     * @param string $source 原文件名
     * @param string $water 水印图片
     * @param null $savename
     * @param int|string $alpha 水印的透明度
     * @internal param $string $$savename  添加水印后的图片名
     * @return void
     */
    static public function water($source, $water, $savename=null, $alpha=80) {
        //检查文件是否存在
        if (!file_exists($source) || !file_exists($water))
            return false;

        //图片信息
        $sInfo = self::getImageInfo($source);
        $wInfo = self::getImageInfo($water);

        //如果图片小于水印图片，不生成图片
        if ($sInfo["width"] < $wInfo["width"] || $sInfo['height'] < $wInfo['height'])
            return false;

        //建立图像
        $sCreateFun = "imagecreatefrom" . $sInfo['type'];
        $sImage = $sCreateFun($source);
        $wCreateFun = "imagecreatefrom" . $wInfo['type'];
        $wImage = $wCreateFun($water);

        //设定图像的混色模式
        imagealphablending($wImage, true);

        //图像位置,默认为右下角右对齐
        $posY = $sInfo["height"] - $wInfo["height"];
        $posX = $sInfo["width"] - $wInfo["width"];

        //生成混合图像
        imagecopymerge($sImage, $wImage, $posX, $posY, 0, 0, $wInfo['width'], $wInfo['height'], $alpha);

        //输出图像
        $ImageFun = 'Image' . $sInfo['type'];
        //如果没有给出保存文件名，默认为原图像名
        if (!$savename) {
            $savename = $source;
            @unlink($source);
        }
        //保存图像
        $ImageFun($sImage, $savename);
        imagedestroy($sImage);
    }

    public static function showImg($imgFile, $text='', $x='10', $y='10', $alpha='50') {
        //获取图像文件信息
        //2007/6/26 增加图片水印输出，$text为图片的完整路径即可
        $info = self::getImageInfo($imgFile);
        if ($info !== false) {
            $createFun = str_replace('/', 'createfrom', $info['mime']);
            $ImageFun = str_replace('/', '', $info['mime']);
            if (is_callable($createFun)) {
                //水印开始
                $im = call_user_func_array($createFun, $imgFile);
                if (!empty($text)) {
                    $tc = imagecolorallocate($im, 0, 0, 0);
                    if (is_file($text) && file_exists($text)) {//判断$text是否是图片路径
                        // 取得水印信息
                        $textInfo = self::getImageInfo($text);
                        $createFun2 = str_replace('/', 'createfrom', $textInfo['mime']);
                        $waterMark = call_user_func_array($createFun2, $text);
                        //$waterMark=imagecolorallocatealpha($text,255,255,0,50);
                        $imgW = $info["width"];
                        $imgH = $info["width"] * $textInfo["height"] / $textInfo["width"];
                        //$y	=	($info["height"]-$textInfo["height"])/2;
                        //设置水印的显示位置和透明度支持各种图片格式
                        imagecopymerge($im, $waterMark, $x, $y, 0, 0, $textInfo['width'], $textInfo['height'], $alpha);
                    } else {
                        imagestring($im, 80, $x, $y, $text, $tc);
                    }
                    //ImageDestroy($tc);
                }
                //水印结束
                if ($info['type'] == 'png' || $info['type'] == 'gif') {
                    imagealphablending($im, FALSE); //取消默认的混色模式
                    imagesavealpha($im, TRUE); //设定保存完整的 alpha 通道信息
                }
                Header("Content-type: " . $info['mime']);
                call_user_func_array($ImageFun, $im);
                @ImageDestroy($im);
                return;
            }

            //保存图像
            call_user_func_array($ImageFun, array($sImage, $savename));
            imagedestroy($sImage);
            //获取或者创建图像文件失败则生成空白PNG图片
            $im = imagecreatetruecolor(80, 30);
            $bgc = imagecolorallocate($im, 255, 255, 255);
            $tc = imagecolorallocate($im, 0, 0, 0);
            imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
            imagestring($im, 4, 5, 5, "no pic", $tc);
            self::output($im);
            return;
        }
    }

    /**
     * 生成缩略图
     * @static
     * @access public
     * @param string $image 原图
     * @param string $thumbname 缩略图文件名
     * @param string $type 图像格式
     * @param int|string $maxWidth 宽度
     * @param int|string $maxHeight 高度
     * @param boolean $interlace 启用隔行扫描
     * @internal param string $position 缩略图保存目录
     * @return void
     */
    static function thumb($image, $thumbname, $type='', $maxWidth=200, $maxHeight=200, $interlace=true) {
        // 获取原图信息
        $info = self::getImageInfo($image);
        if ($info !== false) {
            $srcWidth = $info['width'];
            $srcHeight = $info['height'];
            $type = empty($type) ? $info['type'] : $type;
            $type = strtolower($type);
            $interlace = $interlace ? 1 : 0;
            unset($info);
            // 计算缩放比例            
            if($maxWidth && $maxHeight){
                $scale = min($maxWidth / $srcWidth, $maxHeight / $srcHeight);         
            }else{
                $scale = max($maxWidth / $srcWidth, $maxHeight / $srcHeight);
            }                                     
            if ($scale >= 1 || $scale==0) {
                // 超过原图大小不再缩略
                $width = $srcWidth;
                $height = $srcHeight;
            } else {
                // 缩略图尺寸
                $width = (int) ($srcWidth * $scale);
                $height = (int) ($srcHeight * $scale);
            }

            // 载入原图
            $createFun = 'ImageCreateFrom' . ($type == 'jpg' ? 'jpeg' : $type);
            if(!function_exists($createFun)) {
                return false;
            }
            $srcImg = $createFun($image);

            //创建缩略图
            if ($type != 'gif' && function_exists('imagecreatetruecolor'))
                $thumbImg = imagecreatetruecolor($width, $height);
            else
                $thumbImg = imagecreate($width, $height);
              //png和gif的透明处理 by luofei614
            if('png'==$type){
                imagealphablending($thumbImg, false);//取消默认的混色模式（为解决阴影为绿色的问题）
                imagesavealpha($thumbImg,true);//设定保存完整的 alpha 通道信息（为解决阴影为绿色的问题）    
            }elseif('gif'==$type){
                $trnprt_indx = imagecolortransparent($srcImg);
                 if ($trnprt_indx >= 0) {
                        //its transparent
                       $trnprt_color = imagecolorsforindex($srcImg , $trnprt_indx);
                       $trnprt_indx = imagecolorallocate($thumbImg, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
                       imagefill($thumbImg, 0, 0, $trnprt_indx);
                       imagecolortransparent($thumbImg, $trnprt_indx);
              }
            }
            // 复制图片
            if (function_exists("ImageCopyResampled"))
                imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
            else
                imagecopyresized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);

            // 对jpeg图形设置隔行扫描
            if ('jpg' == $type || 'jpeg' == $type)
                imageinterlace($thumbImg, $interlace);

            // 生成图片
            $imageFun = 'image' . ($type == 'jpg' ? 'jpeg' : $type);
            $imageFun($thumbImg, $thumbname);
            imagedestroy($thumbImg);
            imagedestroy($srcImg);
            return $thumbname;
        }
        return false;
    }

    /**
     * +----------------------------------------------------------
     * 生成特定尺寸缩略图 解决原版缩略图不能满足特定尺寸的问题 PS：会裁掉图片不符合缩略图比例的部分
     * +----------------------------------------------------------
     * @static
     * @access public
     * +----------------------------------------------------------
     * @param string $image 原图
     * @param string $thumbname 缩略图文件名
     * @param string $type 图像格式
     * @param int|string $maxWidth 宽度
     * @param int|string $maxHeight 高度
     * @param boolean $interlace 启用隔行扫描
     * +----------------------------------------------------------
     * @return void
    +----------------------------------------------------------
     */
    static function thumb2($image, $thumbname, $type='', $maxWidth=200, $maxHeight=50, $interlace=true) {
        // 获取原图信息
        $info = self::getImageInfo($image);
        if ($info !== false) {
            $srcWidth = $info['width'];
            $srcHeight = $info['height'];
            $type = empty($type) ? $info['type'] : $type;
            $type = strtolower($type);
            $interlace = $interlace ? 1 : 0;
            unset($info);
            $scale = max($maxWidth / $srcWidth, $maxHeight / $srcHeight); // 计算缩放比例
            //判断原图和缩略图比例 如原图宽于缩略图则裁掉两边 反之..
            if($maxWidth / $srcWidth > $maxHeight / $srcHeight){
                //高于
                $srcX = 0;
                $srcY = ($srcHeight - $maxHeight / $scale) / 2 ;
                $cutWidth = $srcWidth;
                $cutHeight = $maxHeight / $scale;
            }else{
                //宽于
                $srcX = ($srcWidth - $maxWidth / $scale) / 2;
                $srcY = 0;
                $cutWidth = $maxWidth / $scale;
                $cutHeight = $srcHeight;
            }

            // 载入原图
            $createFun = 'ImageCreateFrom' . ($type == 'jpg' ? 'jpeg' : $type);
            $srcImg = $createFun($image);

            //创建缩略图
            if ($type != 'gif' && function_exists('imagecreatetruecolor'))
                $thumbImg = imagecreatetruecolor($maxWidth, $maxHeight);
            else
                $thumbImg = imagecreate($maxWidth, $maxHeight);

            // 复制图片
            if (function_exists("ImageCopyResampled"))
                imagecopyresampled($thumbImg, $srcImg, 0, 0, $srcX, $srcY, $maxWidth, $maxHeight, $cutWidth, $cutHeight);
            else
                imagecopyresized($thumbImg, $srcImg, 0, 0, $srcX, $srcY, $maxWidth, $maxHeight, $cutWidth, $cutHeight);
            if ('gif' == $type || 'png' == $type) {
                //imagealphablending($thumbImg, false);//取消默认的混色模式
                //imagesavealpha($thumbImg,true);//设定保存完整的 alpha 通道信息
                $background_color = imagecolorallocate($thumbImg, 0, 255, 0);  //  指派一个绿色
                imagecolortransparent($thumbImg, $background_color);  //  设置为透明色，若注释掉该行则输出绿色的图
            }

            // 对jpeg图形设置隔行扫描
            if ('jpg' == $type || 'jpeg' == $type)
                imageinterlace($thumbImg, $interlace);

            // 生成图片
            $imageFun = 'image' . ($type == 'jpg' ? 'jpeg' : $type);
            $imageFun($thumbImg, $thumbname);
            imagedestroy($thumbImg);
            imagedestroy($srcImg);
            return $thumbname;
        }
        return false;
    }

    /**
     * 根据给定的字符串生成图像
     * @static
     * @access public
     * @param string $string 字符串
     * @param array|string $size 图像大小 width,height 或者 array(width,height)
     * @param array|string $font 字体信息 fontface,fontsize 或者 array(fontface,fontsize)
     * @param string $filename
     * @param array $rgb
     * @param string $type 图像格式 默认PNG
     * @param integer $disturb 是否干扰 1 点干扰 2 线干扰 3 复合干扰 0 无干扰 4 间断直线
     * @param bool $border 是否加边框 array(color)
     * @throws Abnormal
     * @return string
     */
    static function buildString($string, $size=array(), $font = array(), $filename='', $rgb=array(), $type='png', $disturb =3, $border = true) {
        if (is_string($size))
            $size = explode(',', $size);
        $width = $size[0];
        $height = $size[1]?$size[1]:22;
        if (is_string($font))
            $font = explode(',', $font);
        $fontface = isset($font[0]) ? $font[0] : 5;
        $fontsize = isset($font[1]) ? $font[1] : NULL;
        $fontfile = isset($font[2]) ? $font[2] : NULL;
        $length = strlen($string);
        $width = ($length * 9 + 10) > $width ? $length * 9 + 10 : $width;

        if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
            $im = @imagecreatetruecolor($width, $height);
        } else {
            if(!function_exists('imagecreate')){
                throw new Abnormal('请检查是否安装了gd库', 500);
            }
            $im = @imagecreate($width, $height);
        }

        $r = Array(225, 255, 255, 223, 238);
        $g = Array(225, 236, 237, 255, 228);
        $b = Array(225, 236, 166, 125, 128);
        $key = mt_rand(0, 4);
        $backColor = imagecolorallocate($im, $r[$key], $g[$key], $b[$key]);    //背景色（随机）
        $borderColor = imagecolorallocate($im, 100, 100, 100);                    //边框色
        $color = $rgb?imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]):NULL;
        @imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        @imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
        if($fontsize){
            if(!is_file($fontface))$fontface = 'font/elephant.ttf';
            if (is_null($color))
                $color = imagecolorallocate($im, mt_rand(0, 120), mt_rand(0, 120), mt_rand(0, 120));
            @imagettftext($im, $fontsize, mt_rand(-15,15) , 10+5, ($height+$fontsize)/2, $color , $fontface, $string);
        }else{
            if (is_null($color))
                $color = imagecolorallocate($im, mt_rand(0, 120), mt_rand(0, 120), mt_rand(0, 120));
            for($i=0; $i<$length; $i++){
                @imagestring($im, $fontface, $i*10+5, mt_rand(0,$height-15), $string{$i}, $color);
            }
        }
        // 添加干扰
        switch($disturb) {
            case 0:
                break;
            case 1:
                for ($i = 0; $i < 30; $i++) {
                    $pointColor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
                    imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $pointColor);
                }
                break;
            case 2:
                for ($i = 0; $i < 6; $i++) {
                    $pointColor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
                    imagearc($im, mt_rand(-10, $width), mt_rand(-10, $height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $pointColor);
                }
                break;
            case 3:
                for ($i = 0; $i < 15; $i++) {
                    $pointColor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
                    if($i%5==0){
                        imagearc($im, mt_rand(-10, $width), mt_rand(-10, $height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $pointColor);
                    }else{
                        imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $pointColor); 
                    }
                }
                break;
            case 4:
                for($i=0; $i<3; $i++){
                    $a = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
                    $b = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
                    $style = array($a, $a, $a, $a, $a, $b, $b, $b, $b, $b);
                    imagesetstyle($im, $style);
                    imageline($im,  mt_rand(3,$width), mt_rand(3,$height), mt_rand(3,$width), mt_rand(3,$height), IMG_COLOR_STYLED);
                }
                break;
        }
        self::output($im, $type, $filename);
    }

    /**
     * 把图像转换成字符显示
     * @static
     * @access public
     * @param string $image 要显示的图像
     * @param string $string
     * @param string $type 图像类型，默认自动获取
     * @return string
     */
    static function showASCIIImg($image, $string='', $type='') {
        $info = self::getImageInfo($image);
        if ($info !== false) {
            $type = empty($type) ? $info['type'] : $type;
            unset($info);
            // 载入原图
            $createFun = 'ImageCreateFrom' . ($type == 'jpg' ? 'jpeg' : $type);
            $im = $createFun($image);
            $dx = imagesx($im);
            $dy = imagesy($im);
            $i = 0;
            $out = '<span style="padding:0px;margin:0;line-height:100%;font-size:1px;">';
            set_time_limit(0);
            for ($y = 0; $y < $dy; $y++) {
                for ($x = 0; $x < $dx; $x++) {
                    $col = imagecolorat($im, $x, $y);
                    $rgb = imagecolorsforindex($im, $col);
                    $str = empty($string) ? '*' : $string[$i++];
                    $out .= sprintf('<span style="margin:0px;color:#%02x%02x%02x">' . $str . '</span>', $rgb['red'], $rgb['green'], $rgb['blue']);
                }
                $out .= "<br>\n";
            }
            $out .= '</span>';
            imagedestroy($im);
            return $out;
        }
        return false;
    }

    /**
     * 生成UPC-A条形码
     * @static
     * @param $code
     * @param string $type 图像格式
     * @param int|string $lw 单元宽度
     * @param int|string $hi 条码高度
     * @return string
     */
    static function UPCA($code, $type='png', $lw=2, $hi=100) {
        static $Lencode = array('0001101', '0011001', '0010011', '0111101', '0100011',
    '0110001', '0101111', '0111011', '0110111', '0001011');
        static $Rencode = array('1110010', '1100110', '1101100', '1000010', '1011100',
    '1001110', '1010000', '1000100', '1001000', '1110100');
        $ends = '101';
        $center = '01010';
        /* UPC-A Must be 11 digits, we compute the checksum. */
        if (strlen($code) != 11) {
            die("UPC-A Must be 11 digits.");
        }
        /* Compute the EAN-13 Checksum digit */
        $ncode = '0' . $code;
        $even = 0;
        $odd = 0;
        for ($x = 0; $x < 12; $x++) {
            if ($x % 2) {
                $odd += $ncode[$x];
            } else {
                $even += $ncode[$x];
            }
        }
        $code.= ( 10 - (($odd * 3 + $even) % 10)) % 10;
        /* Create the bar encoding using a binary string */
        $bars = $ends;
        $bars.=$Lencode[$code[0]];
        for ($x = 1; $x < 6; $x++) {
            $bars.=$Lencode[$code[$x]];
        }
        $bars.=$center;
        for ($x = 6; $x < 12; $x++) {
            $bars.=$Rencode[$code[$x]];
        }
        $bars.=$ends;
        /* Generate the Barcode Image */
        if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor($lw * 95 + 30, $hi + 30);
        } else {
            $im = imagecreate($lw * 95 + 30, $hi + 30);
        }
        $fg = ImageColorAllocate($im, 0, 0, 0);
        $bg = ImageColorAllocate($im, 255, 255, 255);
        ImageFilledRectangle($im, 0, 0, $lw * 95 + 30, $hi + 30, $bg);
        $shift = 10;
        for ($x = 0; $x < strlen($bars); $x++) {
            if (($x < 10) || ($x >= 45 && $x < 50) || ($x >= 85)) {
                $sh = 10;
            } else {
                $sh = 0;
            }
            if ($bars[$x] == '1') {
                $color = $fg;
            } else {
                $color = $bg;
            }
            ImageFilledRectangle($im, ($x * $lw) + 15, 5, ($x + 1) * $lw + 14, $hi + 5 + $sh, $color);
        }
        /* Add the Human Readable Label */
        ImageString($im, 4, 5, $hi - 5, $code[0], $fg);
        for ($x = 0; $x < 5; $x++) {
            ImageString($im, 5, $lw * (13 + $x * 6) + 15, $hi + 5, $code[$x + 1], $fg);
            ImageString($im, 5, $lw * (53 + $x * 6) + 15, $hi + 5, $code[$x + 6], $fg);
        }
        ImageString($im, 4, $lw * 95 + 17, $hi - 5, $code[11], $fg);
        /* Output the Header and Content. */
        self::output($im, $type);
    }

    static function output($im, $type='png', $filename='') {
        header("Content-type: image/" . $type);
        $ImageFun = 'image' . $type;
        if (empty($filename)) {
            $ImageFun($im);
        } else {
            $ImageFun($im, $filename);
        }
        imagedestroy($im);
    }

}
