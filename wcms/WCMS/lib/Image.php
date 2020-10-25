<?php
/**
 * 原地址http://hi.baidu.com/yutiedun/item/46d9d44dc9c620e31e19bc81
 * @author wolf
 *
 */
class Image
{
    //类开始
    public $originimage = ""; //源图片文件地址
    public $imageext = ""; //源图片格式
    public $thumbimage = ""; //缩略图文件存放地址
    public $thumb_maxwidth = 2000; //缩略图最大宽度
    public $thumb_maxheight = 2000; //缩略图最大高度
    public $watermark_text = "dszpvip.com"; //水印文字内容
    public $watermark_minwidth = 300; //源图片最小宽度：大于此值时加水印
    public $watermark_minheight = 200; //源图片最小高度：大于此值时加水印
    public $watermark_fontfile = "config/captcha.ttf"; //字体文件
    public $watermark_fontsize = 16; //字体大小
    public $watermark_logo = "config/water.png"; //水印LOGO地址
    public $watermark_transparent = 100; //水印LOGO不透明度
    private $origin_width = 0; //源图片宽度
    private $origin_height = 0; //源图片高度
    private $imageQuilty = 100; //图片质量
    private $tmp_originimage = ""; //临时图片(源图片)
    private $tmp_thumbimage = ""; //临时图片(缩略图)
    private $tmp_waterimage = ""; //临时图片(水印LOGO)
    public $_waterPosition = 2; //1正中间 2右下角 3为左上角
    public $water_type = 1; //1为文字 2为水印
    //生成缩略图
    public function gen_thumbimage ()
    {
        if ($this->originimage == "" || $this->thumbimage == "") {
            return 0;
        }
        $this->get_oriwidthheight();
        if ($this->origin_width < $this->thumb_maxwidth &&
         $this->origin_height < $this->thumb_maxheight) {
            $this->thumb_maxwidth = $this->origin_width;
            $this->thumb_maxheight = $this->origin_height;
        } else {
            if ($this->origin_width < $this->origin_height) {
                $this->thumb_maxwidth = ($this->thumb_maxheight /
                 $this->origin_height) * $this->origin_width;
            } else {
                $this->thumb_maxheight = ($this->thumb_maxwidth /
                 $this->origin_width) * $this->origin_height;
            }
        }
        $this->get_imagetype();
        $this->gen_tmpimage_origin();
        $this->gen_tmpimage_thumb();
        if ($this->tmp_originimage == "" || $this->tmp_thumbimage == "") {
            return - 1;
        }
        imagecopyresampled($this->tmp_thumbimage, $this->tmp_originimage, 0, 0, 
        0, 0, $this->thumb_maxwidth, $this->thumb_maxheight, $this->origin_width, 
        $this->origin_height);
        switch ($this->imageext) {
            case "gif":
                imagegif($this->tmp_thumbimage, $this->thumbimage);
                return 1;
                break;
            case "jpg":
                imagejpeg($this->tmp_thumbimage, $this->thumbimage, 
                $this->imageQuilty);
                return 2;
                break;
            case "png":
                imagepng($this->tmp_thumbimage, $this->thumbimage);
                return 3;
                break;
            default:
                return - 2;
                break;
        }
    }
    //添加文字水印
    public function add_watermark1 ()
    {
        if ($this->originimage == "" || $this->watermark_text == "" ||
         $this->watermark_fontfile == "") {
            return 0;
        }
        $this->get_oriwidthheight();
        if ($this->origin_width < $this->watermark_minwidth ||
         $this->origin_height < $this->watermark_minheight) {
            return 0;
        }
        $this->get_imagetype();
        $this->gen_tmpimage_origin();
        if ($this->tmp_originimage == "") {
            return - 1;
        }
        $textcolor = imagecolorallocate($this->tmp_originimage, 255, 255, 255);
        $angle = 0;
        //正中间
        $waterZb = $this->waterPosition(
        strlen($this->watermark_text) * 15, $this->watermark_fontsize * 2);
        $px = $waterZb['x'];
        $py = $waterZb['y'];
        imagettftext($this->tmp_originimage, $this->watermark_fontsize, $angle, 
        $px, $py, $textcolor, $this->watermark_fontfile, $this->watermark_text);
        switch ($this->imageext) {
            case "gif":
                imagegif($this->tmp_originimage, $this->originimage);
                return 1;
                break;
            case "jpg":
                imagejpeg($this->tmp_originimage, $this->originimage, 
                $this->imageQuilty);
                return 2;
                break;
            case "png":
                imagepng($this->tmp_originimage, $this->originimage);
                return 3;
                break;
            default:
                return - 2;
                break;
        }
    }
    //添加LOGO水印
    public function add_watermark2 ()
    {
        if ($this->originimage == "" || $this->watermark_logo == "") {
            return 0;
        }
        $this->get_oriwidthheight();
        if ($this->origin_width < $this->watermark_minwidth ||
         $this->origin_height < $this->watermark_minheight) {
            return 0;
        }
        $this->get_imagetype();
        $this->gen_tmpimage_origin();
        $this->gen_tmpimage_waterlogo();
        if ($this->tmp_originimage == "" || $this->tmp_waterimage == "") {
            return - 1;
        }
        list ($logo_width, $logo_height) = getimagesize($this->watermark_logo);
        //正中间
        $waterZb = $this->waterPosition($logo_width, $logo_height);
        $px = $waterZb['x'];
        $py = $waterZb['y'];
        $this->tmp_originimage = $this->logo($px, $py, $logo_width, 
        $logo_height);
        //imagecopymerge ( $this->tmp_originimage, $this->tmp_waterimage, $px, $py, 0, 0, $logo_width, $logo_height, $this->watermark_transparent );
        switch ($this->imageext) {
            case "gif":
                imagegif($this->tmp_originimage, $this->originimage);
                return 1;
                break;
            case "jpg":
                imagejpeg($this->tmp_originimage, $this->originimage, 
                $this->imageQuilty);
                return 2;
                break;
            case "png":
                imagepng($this->tmp_originimage, $this->originimage);
                return 3;
                break;
            default:
                return - 2;
                break;
        }
    }
    /**
     * 
     * @param Source $path_1  原图
     * @param Source $path_2  logo
     */
    protected function logo ($px, $py, $logo_width, $logo_height)
    {
        //将人物和装备图片分别取到两个画布中
        $image_1 = $this->tmp_originimage;
        $image_2 = $this->tmp_waterimage;
        //创建一个和人物图片一样大小的真彩色画布（ps：只有这样才能保证后面copy装备图片的时候不会失真）
        $image_3 = imageCreatetruecolor(imagesx($image_1), 
        imagesy($image_1));
        //为真彩色画布创建白色背景，再设置为透明
        $color = imagecolorallocate($image_3, 255, 255, 255);
        imagefill($image_3, 0, 0, $color);
        //  imageColorTransparent($image_3, $color);
        //首先将人物画布采样copy到真彩色画布中，不会失真
        imagecopyresampled($image_3, $image_1, 0, 0, 0, 0, 
        imagesx($image_1), imagesy($image_1), imagesx($image_1), 
        imagesy($image_1));
        //再将装备图片copy到已经具有人物图像的真彩色画布中，同样也不会失真
        imagecopymerge($image_3, $image_2, $px, $py, 0, 0, $logo_width, 
        $logo_height, $this->watermark_transparent);
        return $image_3;
    }
    /**
     * 上传缩略图
     * 注意上传文件大小限制
     *@param String $files $_FILES['upload'] 类型
     *@param String  $path  存储的目录 默认在/static/attached/
     *@param boolean $isWater 是否添加水印
     * @return string $filePath 网页url图片路径
     */
    public function upload ($files, $path, $isWater)
    {
        if (is_uploaded_file($files['tmp_name'])) {
            $upfile = $files;
            $name = $upfile[name];
            $type = $upfile[type];
            $size = $upfile[size];
            $tmp_name = $upfile[tmp_name];
            $error = $upfile[error];
            if ($size > 1048576) {
                return array('status' => false, 'message' => $name."图片太大超过1MB");
            }
            $rs = $this->getImageSize($tmp_name);
            if (! $rs['status']) {
                $rs['message'] = $name . $rs['message'];
                return $rs;
            }
            // 创建文件夹
            $savePath = getcwd() . "/static/attached/" . $path .
             DIRECTORY_SEPARATOR;
            $saveUrl = "./static/attached/" . $path . "/";
            $ym = date("Ym");
            $savePath = $savePath . $ym . DIRECTORY_SEPARATOR;
            $saveUrl = $saveUrl . $ym . "/";
            if (! is_dir($savePath)) {
                mkdir($savePath, 0777, true) or die("无法创建文件夹,权限不够");
            }
            if ($error == '0') {
                $fileType = substr($name, strpos($name, ".") + 1);
                $prefix = $this->getRandPrefix();
                $newName = date("YmdHi") . $prefix . "." . $fileType;
                $filepath = $savePath . $newName;
                move_uploaded_file($tmp_name, $filepath);
            }
            if ($isWater) {
                $this->water($filepath);
            }
            return array('status' => true, 'message' => $saveUrl . $newName);
        }
    }

    public function uploadBinaryFile($binary,$path){


        $fileType=$this->get_file_type_binary_data($binary);
        // 创建文件夹
        $savePath = getcwd() . "/static/attached/" . $path .
            DIRECTORY_SEPARATOR;
        $saveUrl = "./static/attached/" . $path . "/";
        $ym = date("Ym");
        $savePath = $savePath . $ym . DIRECTORY_SEPARATOR;
        $saveUrl = $saveUrl . $ym . "/";
        if (! is_dir($savePath)) {
            mkdir($savePath, 0777, true) or die("无法创建文件夹,权限不够");
        }

        $prefix = $this->getRandPrefix();
        $newName = date("YmdHi") . $prefix . "." . $fileType;
        $filepath = $savePath . $newName;
        $handle=fopen($filepath,"w");
        fwrite($handle,$binary);
        fclose($handle);

        return array('status' => true, 'message' => $saveUrl . $newName);
    }

    /**
     * 通过二进制流获取文件类型
     * @param string $binary_data
     * @return string
     */
    private function get_file_type_binary_data($binary_data){
        $str_info   = @unpack("c2chars", substr($binary_data, 0, 2));
        $type_code  = $str_info['chars1'].$str_info['chars2'];
        switch ($type_code) {
            case '7790':
                $file_type = 'exe';
                break;
            case '7784':
                $file_type = 'midi';
                break;
            case '8075':
                $file_type = 'zip';
                break;
            case '8297':
                $file_type = 'rar';
                break;
            case '255216':
            case '-1-40':
            case '10097':
                $file_type = 'jpg';
                break;
            case '7173':
                $file_type = 'gif';
                break;
            case '6677':
                $file_type = 'bmp';
                break;
            case '13780':
            case '-11980':
                $file_type = 'png';
                break;
            default:
                $file_type = 'unknown';
                break;
        }
        return $file_type;
    }

    /**
     * 图片增加水印处理
     * @param unknown_type $image
     */
    public function water ($image)
    {
        $this->watermark_logo = ROOT . $this->watermark_logo;
        $this->originimage = $image;
        //LOGO水印
        if ($this->water_type == 1) {
            $this->add_watermark1();
        } else {
            $this->add_watermark2();
        }
    }
    /**
     * 
     * 获取随机前缀
     */
    private function getRandPrefix ()
    {
        $string = "abcdefghijklmnopqrstuvwxyz0123456789";
        $prefix = '';
        for ($i = 0; $i < 4; $i ++) {
            $rand = rand(0, 33);
            $prefix .= $string{$rand};
        }
        return $prefix;
    }
    //检测图片大小
    private function getImageSize ($image)
    {
        list ($width, $height, $type, $attr) = getimagesize($image);
        //检测图片大小
        if ($width > $this->thumb_maxwidth) {
            return array('status' => false, 
            'message' => "图片宽度请小于" . $this->thumb_maxwidth . ",当前为" . $width .
             "px");
        }
        if ($height > $this->thumb_maxheight) {
            return array('status' => false, 
            'message' => "图片高度请小于" . $this->thumb_maxheight . ",当前为" . $height .
             "px");
        }
        return array('status' => true);
    }
    /**
     * 生成缩略图
     * 
     * @param String $imagefile 原始文件
     * @param String $thumbWidth  缩略图宽度
     * @param String $thumbHeight 缩略图高度
     * @return String 缩略图url      	
     */
    public function reduceImage ($imagefile, $thumbWidth, $thumbHeight, 
    $path = "thumb")
    {
        // 生成缩略图
        $dir = date("Ym", time());
        $imagefile = ROOT . $imagefile;
        $imagefile_s = ROOT . "static/attached/" . $path . "/" . $dir . "/s_" .
         basename($imagefile);
        $imagetrans = new Image();
        $imagetrans->originimage = $imagefile;
        $imagetrans->thumbimage = $imagefile_s;
        $imagetrans->thumb_maxwidth = $thumbWidth;
        $imagetrans->thumb_maxheight = $thumbHeight;
        $isokid = $imagetrans->gen_thumbimage();
        return "./static/attached/" . $path . "/" . $dir . "/s_" .
         basename($imagefile);
    }
    /**
     * 水印位置
     * @param int $logo_width
     * @param int $logo_height
     * @return 水印坐标
     */
    private function waterPosition ($logo_width, $logo_height)
    {
        switch ($this->_waterPosition) {
            case 1:
                $px = $this->origin_width / 2 - $logo_width / 2;
                $py = $this->origin_height / 2 - $logo_height / 2;
                break;
            case 2:
                $px = $this->origin_width - $logo_width;
                $py = $this->origin_height - $logo_height;
                break;
            case 3:
                $px = 50;
                $py = 20;
                break;
            default:
                $px = $this->origin_width / 2 - $logo_width / 2;
                $py = $this->origin_height / 2 - $logo_height / 2;
                break;
        }
        return array('x' => $px, 'y' => $py);
    }
    //获取图片尺寸
    private function get_oriwidthheight ()
    {
        list ($this->origin_width, $this->origin_height) = getimagesize(
        $this->originimage);
        return 1;
    }
    /*
     * 检测图片格式
     * 原方法需要开启exif 扩展
     */
    private function get_imagetype ()
    {
        $ext = $this->getImgext($this->originimage);
        switch ($ext) {
            case 1:
                $this->imageext = "gif";
                break;
            case 2:
                $this->imageext = "jpg";
                break;
            case 3:
                $this->imageext = "png";
                break;
            default:
                $this->imageext = "unknown";
                break;
        }
    }
    //创建临时图片(源图片)
    private function gen_tmpimage_origin ()
    {
        $ext = $this->getImgext($this->originimage);
        switch ($ext) {
            case 1:
                $this->tmp_originimage = imagecreatefromgif($this->originimage);
                $bgcolor = imagecolorallocate($this->tmp_originimage, 0, 0, 0);
                $bgcolortrans = imagecolortransparent($this->tmp_originimage, 
                $bgcolor);
                break;
            case 2:
                $this->tmp_originimage = imagecreatefromjpeg($this->originimage);
                break;
            case 3:
                $this->tmp_originimage = imagecreatefrompng($this->originimage);
                imagesavealpha($this->tmp_originimage, true);
                break;
            default:
                $this->tmp_originimage = "";
                break;
        }
    }
    //创建临时图片(缩略图)
    private function gen_tmpimage_thumb ()
    {
        $ext = $this->getImgext($this->originimage);
        switch ($ext) {
            case 1:
                $this->tmp_thumbimage = imagecreatetruecolor(
                $this->thumb_maxwidth, $this->thumb_maxheight);
                $bgcolor = imagecolorallocate($this->tmp_thumbimage, 255, 255, 
                255);
                imagefill($this->tmp_thumbimage, 0, 0, $bgcolor);
                break;
            case 2:
                $this->tmp_thumbimage = imagecreatetruecolor(
                $this->thumb_maxwidth, $this->thumb_maxheight);
                break;
            case 3:
                $this->tmp_thumbimage = imagecreatetruecolor(
                $this->thumb_maxwidth, $this->thumb_maxheight);
                $bgcolor = imagecolorallocate($this->tmp_thumbimage, 255, 255, 
                255);
                imagefill($this->tmp_thumbimage, 0, 0, $bgcolor);
                imagealphablending($this->tmp_thumbimage, false);
                imagesavealpha($this->tmp_thumbimage, true);
                break;
            default:
                $this->tmp_thumbimage = "";
                break;
        }
    }
    //创建临时图片(LOGO水印)
    private function gen_tmpimage_waterlogo ()
    {
        $ext = $this->getImgext($this->watermark_logo);
        switch ($ext) {
            case 1:
                $this->tmp_waterimage = imagecreatefromgif(
                $this->watermark_logo);
                $bgcolor = imagecolorallocate($this->tmp_waterimage, 0, 0, 0);
                $bgcolortrans = imagecolortransparent($this->tmp_waterimage, 
                $bgcolor);
                break;
            case 2:
                $this->tmp_waterimage = imagecreatefromjpeg(
                $this->watermark_logo);
                break;
            case 3:
                $this->tmp_waterimage = imagecreatefrompng(
                $this->watermark_logo);
                imagesavealpha($this->tmp_waterimage, true);
                break;
            default:
                $this->tmp_waterimage = "";
                break;
        }
    }
    /*
     * 获取后缀名
     */
    public function getImgext ($filename)
    {
        return exif_imagetype($filename);
    }
    //释放资源
    public function __destruct ()
    {
        if (is_object($this->tmp_originimage) == true) {
            imagedestroy($this->tmp_originimage);
        }
        if (is_object($this->tmp_thumbimage) == true) {
            imagedestroy($this->tmp_thumbimage);
        }
        if (is_object($this->tmp_waterimage) == true) {
            imagedestroy($this->tmp_waterimage);
        }
    }
}
