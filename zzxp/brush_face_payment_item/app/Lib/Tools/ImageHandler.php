<?php
namespace Tools;

/*
|------------------
| 图片处理类
|------------------
*/
class ImageHandler
{
    // 全局变量参数
    public  $dst_img,       // 目标文件
            $h_src,         // 图片资源句柄
            $h_dst,         // 目标新图句柄
            $src_w = 0,     // 原图宽度
            $src_h = 0,     // 原图高度
            $dst_w = 0,     // 新图总宽度
            $dst_h = 0,     // 新图总高度
            $mask_word,     // 水印文字
            $mask_font_color = "#FFFFFF",   // 水印文字颜色
            $mask_font_alpha = 0,   // 水印字透明度
            $font,              // 水印字体
            $font_size,         // 尺寸
            $mask_pos_x = 0,    // 水印横坐标
            $mask_pos_y = 0,    // 水印纵坐标
            $mask_offset_x = 5, // 水印横向偏移
            $mask_offset_y = 5, // 水印纵向偏移
            $mask_w,            // 水印宽
            $mask_h;            // 水印高

    // 文件类型定义,并指出了输出图片的函数
    protected $all_type = ["jpg" => ["output" => "imagejpeg"],
                 "gif" => ["output" => "imagegif"],
                 "png" => ["output" => "imagepng"],
                 "wbmp" => ["output" => "image2wbmp"],
                 "jpeg" => ["output" => "imagejpeg"]];

    // 构造函数
    public function ImageHandler() {
        $this->mask_font_color = "#000000";
        $this->font = 2;
        $this->font_size = 12;
    }
    
    /**
     * 取得图片的宽
     */
    public function getImgWidth($src) {
        return imagesx($src);
    }
    
    /**
     * 取得图片的高
     */
    public function getImgHeight($src) {
        return imagesy($src);
    }
    
    /**
     * 设置文字水印
     *
     * @param string $word 水印文字
     */
    public function setMaskWord($word) {
        $this->mask_word .= $word;
    }
    
    /**
     * 设置字体颜色
     *
     * @param string $color 字体颜色
     */
    public function setMaskFontColor($color = "#ffffff") {
        $this->mask_font_color = $color;
    }
    
    /**
     * 设置水印字体
     *
     * @param string|integer $font 字体
     */
    public function setMaskFont($font = 2) {
        if (!is_numeric($font) && !file_exists($font)) {
            die("字体文件不存在");
        }

        $this->font = $font;
    }

    /**
     * 设置字体信息
     */
    public function _setFontInfo() {
        if (is_numeric( $this->font)) {
            $this->font_w = imagefontwidth($this->font);
            $this->font_h = imagefontheight($this->font);

            // 计算水印字体所占宽高
            $word_length = strlen($this->mask_word);
            $this->mask_w = $this->font_w * $word_length;
            $this->mask_h = $this->font_h;
        } else {
            $arr = imagettfbbox($this->font_size, 0, $this->font, $this->mask_word);
            $this->mask_w = abs($arr[0] - $arr[2]);
            $this->mask_h = abs($arr[7] - $arr[1]);
        }
    }
    
    /**
     * 设置文字字体大小,仅对truetype字体有效
     */
    public function setMaskFontSize($size = "12") {
        $this->font_size = $size;
    }

    /**
     * 设置水印横向偏移
     *
     * @param integer $x 横向偏移量
     * @param integer $y 纵向偏移量
     */
    public function setMaskOffset($x, $y) {
        $this->mask_pos_x = (int) $x;
        $this->mask_pos_y = (int) $y;
    }

    /**
     * 设置来源图片
     *
     * @param string $src_img 图片生成路径
     */
    public function setSrcImg($src_img, $img_type = null) {
        if (!file_exists($src_img)) {
            die ("图片不存在");
        }
        
        if (!empty($img_type)) {
            $this->img_type = $img_type;
        }
        else {
            $this->img_type = $this->_getImgType($src_img);
        }
        
        $this->_checkValid($this->img_type);
        
        // file_get_contents函数要求php版本>4.3.0
        $src = '';
        if (function_exists("file_get_contents")) {
            $src = file_get_contents($src_img);
        }
        else {
            $handle = fopen($src_img, "r");
            while(!feof($handle)) {
                $src .= fgets($handle, 4096);
            }
            fclose($handle);
        }

        if (empty($src)) {
            die ( "图片源为空" );
        }

        $this->h_src = @ImageCreateFromString($src);
        $this->src_w = $this->getImgWidth($this->h_src);
        $this->src_h = $this->getImgHeight ($this->h_src);
    }
    
    /**
     * 设置图片生成路径
     *
     * @param string $dst_img 图片生成路径
     */
    public function setDstImg($dst_img) {
        $arr = explode('/', $dst_img);
        $last = array_pop($arr);
        $path = implode('/', $arr);
        $this->_mkdirs($path);
        $this->dst_img = $dst_img;
    }
    
    /**
     * 生成水印文字
     */
    public function _createMaskWord($src_img, $dst_img) {
        // 获取来源文件信息
        $this->setSrcImg($src_img);

        // 设置目标图片
        $this->setDstImg($dst_img);
        // 创建目标文件
        $this->dst_w = $this->src_w;
        $this->dst_h = $this->src_h;

        $this->h_dst = imagecreatetruecolor($this->dst_w, $this->dst_h);
        $white = ImageColorAllocate($this->h_dst, 255, 255, 255);
        imagefilledrectangle($this->h_dst, 0, 0, $this->dst_w, $this->dst_h, $white);   // 填充背景色
        //$this->_drawBorder();
        imagecopyresampled($this->h_dst, $this->h_src, 0, 0, 0, 0, $this->dst_w, $this->dst_h, $this->src_w, $this->src_h);

        if (!empty($this->mask_word)) {
            // 获取字体信息
            $this->_setFontInfo();
        }

        $c = $this->_parseColor($this->mask_font_color);
        $color = imagecolorallocatealpha($this->h_dst, $c[0], $c[1], $c[2], $this->mask_font_alpha);

        if (is_numeric($this->font)) {
            imagestring($this->h_dst, $this->font, $this->mask_pos_x, $this->mask_pos_y, $this->mask_word, $color);
        }
        else {
            imagettftext($this->h_dst, $this->font_size, 0, $this->mask_pos_x, $this->mask_pos_y, $color, $this->font, $this->mask_word);
        }

        $this->_output();
        imagedestroy($this->h_src) && imagedestroy($this->h_dst);
    }
    
    /**
     * 取得图片类型
     *
     * @param string $file_path 文件路径
     */
    public function _getImgType($file_path) {
        $type_list = array ("1" => "gif", "2" => "jpg", "3" => "png", "4" => "swf", "5" => "psd", "6" => "bmp", "15" => "wbmp" );
        if (!file_exists($file_path)) {
            die ("文件不存在,不能取得文件类型!");
        }

        $img_info = @getimagesize($file_path);
        if (isset($type_list[$img_info[2]])) {
            return $type_list [$img_info[2]];
        }
    }

    /**
     * 检查图片类型是否合法
     *
     * @param string $img_type 文件类型
     */
    public function _checkValid($img_type) {
        if (!isset($this->all_type[$img_type])) {
            return false;
        }
    }

    /**
     * 分析颜色
     *
     * @param string $color 十六进制颜色
     */
    public function _parseColor($color) {
        $arr = array ();
        for ($ii = 1; $ii < strlen($color); $ii++) {
            $arr[] = hexdec(substr($color, $ii, 2));
            $ii++;
        }

        return $arr;
    }
    
    /**
     * 按指定路径生成目录
     *
     * @param string $path 路径
     */
    public function _mkdirs($path) {
        $adir = explode('/', $path);
        $dirlist = '';
        $rootdir = array_shift($adir);
        if (($rootdir != '.' || $rootdir != '..') && ! file_exists($rootdir)) {
            @mkdir($rootdir);
        }

        foreach ($adir as $key => $val) {
            if ($val == '.' || $val == '..') {
                continue;
            }

            $dirlist .= "/" . $val;
            $dirpath = $rootdir . $dirlist;
            if (!file_exists($dirpath)) {
                @mkdir($dirpath);
                @chmod($dirpath, 0777);
            }
        }
    }
    
    /**
     * 图片输出
     */
    public function _output() {
        $img_type = $this->img_type;
        $func_name = $this->all_type[$img_type]['output'];

        if (function_exists($func_name)) {
            // 判断浏览器,若是IE就不发送头
            if (isset($_SERVER ['HTTP_USER_AGENT'])) {
                $ua = strtoupper($_SERVER['HTTP_USER_AGENT']);
                if (!preg_match('/^.*MSIE.*\)$/i', $ua)) {
                    //header("Content-type:$img_type");
                }
            }

            $func_name($this->h_dst, $this->dst_img);
        }
        else {
            return false;
        }
    }
}