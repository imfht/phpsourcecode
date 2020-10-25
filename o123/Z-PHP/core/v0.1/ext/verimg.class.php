<?php
namespace ext;

class verimg
{
    private $width; //验证码图片的宽度
    private $height; //验证码图片的高度
    private $codeNum; //验证码字符的个数
    private $disturbColorNum; //干扰元素数量
    private $vercode; //验证码字符
    private $image; //验证码资源
    private $fontSize; //字符尺寸
    private $mime; //资源类型
    private $act;
    private $image_data;
    private $ttf;

    /**
     * 构造方法
     * @param    int    $width        设置验证码图片的宽度
     * @param    int    $height        设置验证码图片的高度
     * @param    int    $codeNum    设置验证码中字符的个数
     * @param    int    $fontSize    设置验证码中字符的尺寸
     */
    public function __construct($width = 100, $height = 38, $codeNum = 4, $fontSize = 14)
    {
        $this->width = $width;
        $this->height = $height;
        $this->codeNum = $codeNum;
        $this->fontSize = $fontSize;
        $this->ttf = P_CORE . 'ext/ttfs/1.ttf';
    }
    public function Set($key, $value)
    {
        $this->$key = $value;
    }
    public function Create()
    {
        $tp = imagetypes();
        if ($tp & IMG_GIF) {
            ($this->act = 'imagegif') && $this->mime = 'image/gif';
        } elseif ($tp & IMG_JPG) {
            ($this->act = 'imagejpeg') && $this->mime = 'image/jpeg';
        } elseif ($tp & IMG_PNG) {
            ($this->act = 'imagepng') && $this->mime = 'image/png';
        } elseif ($tp & IMG_WBMP) {
            ($this->act = 'imagewbmp') && $this->mime = 'image/vnd.wap.wbmp';
        } else {
            return false;
        }

        $number = floor($this->height * $this->width / 15);
        if ($number > 240 - $this->codeNum) {
            $this->disturbColorNum = 240 - $this->codeNum;
        } else {
            $this->disturbColorNum = $number;
        }

        $this->vercode = $this->createVercode();
        $this->getCreateImage();
        $this->setDisturbColor();
        $this->outputText();
        return $this->vercode;
    }
    public function Out()
    {
        ob_clean();
        header("Content-type: {$this->mime}");
        $act = $this->act;
        $act($this->image);
    }
    public function GetCode()
    {
        return $this->vercode;
    }
    public function Base64()
    {
        ob_start();
        if (!$create = $this->Create()) {
            die('不支持创建图像资源');
        }

        $act = $this->act;
        $act($this->image);
        $this->image_data = ob_get_contents();
        ob_end_clean();
        $base64_image = "data:{$this->mime};base64," . chunk_split(base64_encode($this->image_data));
        return $base64_image;
    }

    /**
     * 输出图像并把验证码保存到SESSION
     * @param  string $name [SESSION中验证码的键名]
     * @return [type]       [description]
     */
    public function Img($name = '_verimgcode')
    {
        if (!$create = $this->Create()) {
            die('不支持创建图像资源');
        }

        $_SESSION[$name] = md5(strtolower($this->vercode));
        ob_clean();
        header("Content-type: {$this->mime}");
        $act = $this->act;
        $act($this->image);
    }

    /**
     * 检查验证码是否正确
     * @param  string $code [用户输入的验证码（不区分大小写）]
     * @param  string $name [SESSION中保存验证码的键名]
     * @return boolean      [description]
     */
    public static function Check($code, $name = '_verimgcode')
    {
        if (md5(strtolower($code)) == $_SESSION[$name]) {
            unset($_SESSION[$name]);
            return true;
        } else {
            return false;
        }

    }

    private function getCreateImage()
    {
        $this->image = imagecreatetruecolor($this->width, $this->height);
        $backColor = imagecolorallocate($this->image, mt_rand(150, 255), mt_rand(150, 255), mt_rand(150, 255)); //背景色（随机）
        @imagefill($this->image, 0, 0, $backColor);
    }

    /**
     * 随机生成指定个数的字符,去掉容易混淆的字符oOLlz和数字012
     * @return [string] [description]
     */
    private function createVercode()
    {
        $str = '';
        $code = "3456789abcdefghijkmnpqrstuvwxyABCDEFGHIJKMNPQRSTUVWXY";
        for ($i = 0; $i < $this->codeNum; ++$i) {
            $char = $code[mt_rand(0, strlen($code) - 1)];
            $str .= $char;
        }
        return $str;
    }

    /**
     * 添加干扰
     */
    private function setDisturbColor()
    {
        imagesetthickness($this->image, mt_rand(3, 6));
        for ($i = 0; $i < 3; $i++) {
            $color = imagecolorallocate($this->image, mt_rand(100, 200), mt_rand(100, 200), mt_rand(100, 200));
            imagearc($this->image, mt_rand(-10, $this->width - 10), mt_rand(-10, $this->height - 10), mt_rand(30, 2 * $this->width - 4), mt_rand(20, 2 * $this->height), 50, 20, $color);
        }
        for ($i = 0; $i < 5; $i++) {
            $char = mt_rand(0, 9);
            $color = imagecolorallocate($this->image, mt_rand(150, 255), mt_rand(150, 255), mt_rand(150, 255));
            imagechar($this->image, 5, mt_rand(1, $this->width - 2), mt_rand(1, $this->height - 2), $char, $color);
        }
    }

    /**
     * 随机颜色、随机摆放、
     * @return [type] [description]
     */
    private function outputText()
    {
        for ($i = 0; $i != $this->codeNum; ++$i) {
            $fontcolor = imagecolorallocate($this->image, mt_rand(0, 128), mt_rand(0, 128), mt_rand(0, 128));
            $ii = mt_rand(-30, 30);
            $x = $i ? floor($this->width / $this->codeNum) * $i + mt_rand(-3, 5) : 5;
            $y = mt_rand(($this->fontSize + 5), ($this->height - 5));
            imagettftext($this->image, $this->fontSize, $ii, $x, $y, $fontcolor, $this->ttf, $this->vercode[$i]);
        }
    }

    /**
     * 销毁图像资源释放内存
     */
    public function __destruct()
    {
        imagedestroy($this->image);
    }
}
