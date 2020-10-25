<?php
/*  用法:
    $s = new Image_process( $item );
    $s->watermarkImage($logo);  //生成水印
    $s->scaleImage(0.8);
    $s->fixSizeImage(200,false);//生成缩略图
 */
class Image_process{
    public $source;//原图
    public $source_width;//宽
    public $source_height;//高
    public $source_type_id;
    public $orign_name;
    public $orign_dirname;
    //传入图片路径
    public function __construct($source){
        $this->typeList      = array(1=>'gif',2=>'jpg',3=>'png');
        $ginfo               = getimagesize($source);
        $this->source_width  = $ginfo[0];
        $this->source_height = $ginfo[1];
        $this->source_type_id= $ginfo[2];
        $this->orign_url     = $source;
        $this->orign_name    = basename($source);
        $this->orign_dirname = dirname($source);
    }

    //判断并处理,返回PHP可识别编码
    public function judgeType($type,$source){
        if($type==1){
            return ImageCreateFromGIF($source);//gif
        }else if($type==2){
            return ImageCreateFromJPEG($source);//jpg
        }else if($type==3){
            return ImageCreateFromPNG($source);//png
        }else{
            return false;
        }
    }

    //生成水印图
    public function watermarkImage($logo){
        $linfo        = getimagesize($logo);
        $logo_width   = $linfo[0];
        $logo_height  = $linfo[1];
        $logo_type_id = $linfo[2];
        $sourceHandle = $this->judgeType($this->source_type_id,$this->orign_url);
        $logoHandle   = $this->judgeType($logo_type_id,$logo);

        if( !$sourceHandle || ! $logoHandle ){
            return false;
        }
        $x = $this->source_width - $logo_width;
        $y = $this->source_height- $logo_height;

        ImageCopy($sourceHandle,$logoHandle,$x,$y,0,0,$logo_width,$logo_width) or die("fail to combine");
      //  $newPic = $this->orign_dirname .'\water_'. time().'.'. $this->typeList[$this->source_type_id];
        $newPic = $this->orign_url;
        if( $this->saveImage($sourceHandle,$newPic)){
            imagedestroy($sourceHandle);
            imagedestroy($logoHandle);
        }
    }

    // fix 宽度
    // height = true 固顶高度
    // width  = true 固顶宽度
    public function fixSizeImage($width,$height){
        if( $width > $this->source_width) $this->source_width;
        if( $height > $this->source_height ) $this->source_height;
        if( $width === false){
            $width = floor($this->source_width / ($this->source_height / $height));
        }
        if( $height === false){
            $height = floor($this->source_height / ($this->source_width / $width));
        }
        $this->tinyImage($width,$height);
    }

    //比例缩放
    // $scale 缩放比例
    public function scaleImage($scale){
        $width  = floor($this->source_width * $scale);
        $height = floor($this->source_height * $scale);
        $this->tinyImage($width,$height);
    }

    //创建略缩图
    private function tinyImage($width,$height){
        $tinyImage = imagecreatetruecolor($width, $height );
        $handle    = $this->judgeType($this->source_type_id,$this->orign_url);
        if(function_exists('imagecopyresampled')){
            imagecopyresampled($tinyImage,$handle,0,0,0,0,$width,$height,$this->source_width,$this->source_height);
        }else{
            imagecopyresized($tinyImage,$handle,0,0,0,0,$width,$height,$this->source_width,$this->source_height);
        }

        // $newPic = time().'_'.$width.'_'.$height.'.'. $this->typeList[$this->source_type_id];
        // $newPic = $this->orign_dirname .'\thumb_'. $newPic;
        $newPic = $this->orign_url;
        if( $this->saveImage($tinyImage,$newPic)){
            imagedestroy($tinyImage);
            imagedestroy($handle);
        }
    }

    //保存图片
    private function saveImage($image,$url){
        if(ImageJpeg($image,$url)){
            return true;
        }
    }
}
