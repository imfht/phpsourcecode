<?php
/**
 * Author: smile的微笑
 * E-mail: twelife@163.com
 * Date:   2017/11/9 13:39
 * Created by PhpStorm.
 */
namespace App\Lib\Tools;

class Image
{
    private $info;
    private $image;
    
    public function __construct($src)
    {
        $info = getimagesize($src);
        $this->info = array("width" => $info[0], "height" => $info[1], "type" => image_type_to_extension($info[2], false), "mime" => $info['mime']);
        $fun = "imagecreatefrom{$this->info['type']}";
        $this->image = $fun($src);
    }
    
    public function thumb($width, $height)
    {
        $imageThumb = imagecreatetruecolor($width, $height);
        imagecopyresampled($imageThumb, $this->image, 0, 0, 0, 0, $width, $height, $this->info["width"], $this->info["height"]);
        imagedestroy($this->image);
        $this->image = $imageThumb;
    }
    
    public function waterMarkText($text, $fontPath, $fontSize, $color, $x, $y, $angle)
    {
        $color = imagecolorallocatealpha($this->image, $color[0], $color[1], $color[2], $color[3]);
        imagettftext($this->image, $fontSize, $angle, $x, $y, $color, $fontPath, $text);
    }
    
    public function waterMarkImage($source, $x, $y, $alpha)
    {
        $markInfo = getimagesize($source); //3、获取水印图片类型
        $markType = image_type_to_extension($markInfo[2], false); //4、在内存创建图像
        $markCreateImageFunc = "imagecreatefrom{$markType}"; //5、把水印图片复制到内存中
        $water = $markCreateImageFunc($source); //特别处理，设置透明
        $color=imagecolorallocate($water,255,255,255);
        imagefill($water,0,0,$color);
        imagecolortransparent($water,$color); //6、合并图片
        imagecopymerge($this -> image, $water, $x, $y, 0, 0, $markInfo[0], $markInfo[1], $alpha);
    }
    
    public function show()
    {
        header("Content-type:" . $this->info['mime']);
        $outputfunc = "image{$this -> info['type']}";
        $outputfunc($this->image);
    }
    
    public function save($newname,$need_type = true)
    {
        if($need_type){
            $outputfunc = "image{$this -> info['type']}";
        }else{
            $outputfunc = "image";
        }
        $outputfunc($this->image, $newname . '.' . $this->info['type']);
    }
    
    public function __destruct()
    {
        imagedestroy($this->image);
    }
}