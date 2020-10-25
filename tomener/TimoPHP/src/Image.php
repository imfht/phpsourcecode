<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo;


use Timo\Image\Gif;

/**
 * 图片处理类
 *
 * Class Image
 * @package Timo
 */
class Image
{
    private $img;

    /**
     * @var Gif
     */
    private $gif;

    private $info;

    private $water;

    private $water_info;

    /* 缩略图相关常量定义 */
    const IMAGE_THUMB_SCALE = 1; //常量，标识缩略图等比例缩放类型
    const IMAGE_THUMB_FILLED = 2; //常量，标识缩略图缩放后填充类型
    const IMAGE_THUMB_CENTER = 3; //常量，标识缩略图居中裁剪类型
    const IMAGE_THUMB_NORTHWEST = 4; //常量，标识缩略图左上角裁剪类型
    const IMAGE_THUMB_SOUTHEAST = 5; //常量，标识缩略图右下角裁剪类型
    const IMAGE_THUMB_FIXED = 6; //常量，标识缩略图固定尺寸缩放类型

    /* 水印相关常量定义 */
    const IMAGE_WATER_NORTHWEST = 1; //常量，标识左上角水印
    const IMAGE_WATER_NORTH = 2; //常量，标识上居中水印
    const IMAGE_WATER_NORTHEAST = 3; //常量，标识右上角水印
    const IMAGE_WATER_WEST = 4; //常量，标识左居中水印
    const IMAGE_WATER_CENTER = 5; //常量，标识居中水印
    const IMAGE_WATER_EAST = 6; //常量，标识右居中水印
    const IMAGE_WATER_SOUTHWEST = 7; //常量，标识左下角水印
    const IMAGE_WATER_SOUTH = 8; //常量，标识下居中水印
    const IMAGE_WATER_SOUTHEAST = 9; //常量，标识右下角水印

    public function __construct($image = null)
    {
        $image && $this->open($image);
    }

    public function setSrc($image)
    {
        return $this->open($image);
    }

    /**
     * 打开一张图像
     *
     * @param  string $image 图像路径
     * @return bool
     * @throws \Exception
     */
    public function open($image)
    {
        //检测图像文件
        if (!is_file($image)) {
            return false;
        }

        //获取图像信息
        $info = @getimagesize($image);

        if (!$info) {
            return false;
        }

        //检测图像合法性
        if (false === $info || (IMAGETYPE_GIF === $info[2] && empty($info['bits']))) {
            return false;
        }

        //设置图像信息
        $this->info = [
            'width' => $info[0],
            'height' => $info[1],
            'type' => image_type_to_extension($info[2], false),
            'mime' => $info['mime'],
        ];

        //销毁已存在的图像
        empty($this->img) || imagedestroy($this->img);

        //打开图像
        if ('gif' == $this->info['type']) {
            $this->gif = new Gif($image);
            $this->img = imagecreatefromstring($this->gif->image());
        } else {
            $fun = "imagecreatefrom{$this->info['type']}";
            $this->img = @$fun($image);
        }
        if (!$this->img) {
            return false;
        }
        return true;
    }

    /**
     * 保存图像
     *
     * @param string $image 图像保存名称
     * @param string $type 图像类型
     * @param int $quality 图像质量
     * @param bool $interlace 是否对JPEG类型图像设置隔行扫描
     * @throws \Exception
     */
    public function save($image, $type = null, $quality = 80, $interlace = true)
    {
        if (empty($this->img)) {
            $this->setError('没有可以被保存的图像资源');
        }

        //自动获取图像类型
        if (is_null($type)) {
            $type = $this->info['type'];
        } else {
            $type = strtolower($type);
        }
        //保存图像
        if ('jpeg' == $type || 'jpg' == $type) {
            //JPEG图像设置隔行扫描
            imageinterlace($this->img, $interlace);
            imagejpeg($this->img, $image, $quality);
        } elseif ('gif' == $type && !empty($this->gif)) {
            $this->gif->save($image);
        } else {
            $fun = 'image' . $type;
            $fun($this->img, $image);
        }
    }

    /**
     * 裁剪图像
     *
     * @param  int $w 裁剪区域宽度
     * @param  int $h 裁剪区域高度
     * @param  int $x 裁剪区域x坐标
     * @param  int $y 裁剪区域y坐标
     * @param  int $width 图像保存宽度
     * @param  int $height 图像保存高度
     */
    public function crop($w, $h, $x = 0, $y = 0, $width = null, $height = null)
    {
        if (empty($this->img)) {
            $this->setError('没有可以被裁剪的图像资源');
        }

        //设置保存尺寸
        empty($width) && $width = $w;
        empty($height) && $height = $h;

        do {
            //创建新图像
            $img = imagecreatetruecolor($width, $height);
            // 调整默认颜色
            $color = imagecolorallocate($img, 255, 255, 255);
            imagefill($img, 0, 0, $color);

            //裁剪
            imagecopyresampled($img, $this->img, 0, 0, $x, $y, $width, $height, $w, $h);
            imagedestroy($this->img); //销毁原图

            //设置新图像
            $this->img = $img;
        } while (!empty($this->gif) && $this->gifNext());

        $this->info['width'] = $width;
        $this->info['height'] = $height;
    }

    /**
     * 生成缩略图
     * @param  integer $width 缩略图最大宽度
     * @param  integer $height 缩略图最大高度
     * @param  integer $type 缩略图裁剪类型
     */
    public function thumb($width, $height, $type = Image::IMAGE_THUMB_SCALE)
    {
        if (empty($this->img)) {
            $this->setError('没有可以被缩略的图像资源');
        }

        //原图宽度和高度
        $w = $this->info['width'];
        $h = $this->info['height'];
        $x = $y = 0;

        /* 计算缩略图生成的必要参数 */
        switch ($type) {
            /* 等比例缩放 */
            case Image::IMAGE_THUMB_SCALE:
                //原图尺寸小于缩略图尺寸则不进行缩略
                if ($w < $width && $h < $height) return;

                //计算缩放比例
                $scale = min($width / $w, $height / $h);

                //设置缩略图的坐标及宽度和高度
                $x = $y = 0;
                $width = $w * $scale;
                $height = $h * $scale;
                break;

            /* 居中裁剪 */
            case Image::IMAGE_THUMB_CENTER:
                //计算缩放比例
                $scale = max($width / $w, $height / $h);

                //设置缩略图的坐标及宽度和高度
                $w = $width / $scale;
                $h = $height / $scale;
                $x = ($this->info['width'] - $w) / 2;
                $y = ($this->info['height'] - $h) / 2;
                break;

            /* 左上角裁剪 */
            case Image::IMAGE_THUMB_NORTHWEST:
                //计算缩放比例
                $scale = max($width / $w, $height / $h);

                //设置缩略图的坐标及宽度和高度
                $x = $y = 0;
                $w = $width / $scale;
                $h = $height / $scale;
                break;

            /* 右下角裁剪 */
            case Image::IMAGE_THUMB_SOUTHEAST:
                //计算缩放比例
                $scale = max($width / $w, $height / $h);

                //设置缩略图的坐标及宽度和高度
                $w = $width / $scale;
                $h = $height / $scale;
                $x = $this->info['width'] - $w;
                $y = $this->info['height'] - $h;
                break;

            /* 填充 */
            case Image::IMAGE_THUMB_FILLED:
                //计算缩放比例
                if ($w < $width && $h < $height) {
                    $scale = 1;
                } else {
                    $scale = min($width / $w, $height / $h);
                }

                //设置缩略图的坐标及宽度和高度
                $neww = $w * $scale;
                $newh = $h * $scale;
                $posx = ($width - $w * $scale) / 2;
                $posy = ($height - $h * $scale) / 2;

                do {
                    //创建新图像
                    $img = imagecreatetruecolor($width, $height);
                    // 调整默认颜色
                    $color = imagecolorallocate($img, 255, 255, 255);
                    imagefill($img, 0, 0, $color);

                    //裁剪
                    imagecopyresampled($img, $this->img, $posx, $posy, $x, $y, $neww, $newh, $w, $h);
                    imagedestroy($this->img); //销毁原图
                    $this->img = $img;
                } while (!empty($this->gif) && $this->gifNext());

                $this->info['width'] = $width;
                $this->info['height'] = $height;
                return;

            /* 固定 */
            case Image::IMAGE_THUMB_FIXED:
                $x = $y = 0;
                break;

            default:
                $this->setError('不支持的缩略图裁剪类型');
        }

        /* 裁剪图像 */
        $this->crop($w, $h, $x, $y, $width, $height);
    }

    /**
     * 设置水印图片
     *
     * @param $source
     * @throws \Exception
     */
    function setWaterImage($source)
    {
        if (!is_file($source)) {
            $this->setError('水印图像不存在');
        }

        //获取水印图像信息
        $info = getimagesize($source);
        if (false === $info || (IMAGETYPE_GIF === $info[2] && empty($info['bits']))) {
            $this->setError('非法水印文件');
        }

        //创建水印图像资源
        $fun = 'imagecreatefrom' . image_type_to_extension($info[2], false);
        $water = $fun($source);

        //设定水印图像的混色模式
        imagealphablending($water, true);

        $this->water_info = $info;
        $this->water = $water;
    }

    /**
     * 添加水印
     *
     * @param int $locate 水印位置
     * @param int $alpha 水印图片透明度
     * @param bool $des_water 打了之后是否销毁水印图片资源
     * @return bool
     */
    public function water($locate = Image::IMAGE_WATER_SOUTHEAST, $alpha = 80, $des_water = true)
    {
        //资源检测
        if (empty($this->img)) return false;

        $x = $y = 0;
        /* 设定水印位置 */
        switch ($locate) {
            /* 右下角水印 */
            case Image::IMAGE_WATER_SOUTHEAST:
                $x = $this->info['width'] - $this->water_info[0] - 10;
                $y = $this->info['height'] - $this->water_info[1] - 10;
                break;

            /* 左下角水印 */
            case Image::IMAGE_WATER_SOUTHWEST:
                $x = 10;
                $y = $this->info['height'] - $this->water_info[1] - 10;
                break;

            /* 左上角水印 */
            case Image::IMAGE_WATER_NORTHWEST:
                $x = $y = 0;
                break;

            /* 右上角水印 */
            case Image::IMAGE_WATER_NORTHEAST:
                $x = $this->info['width'] - $this->water_info[0];
                $y = 0;
                break;

            /* 居中水印 */
            case Image::IMAGE_WATER_CENTER:
                $x = ($this->info['width'] - $this->water_info[0]) / 2;
                $y = ($this->info['height'] - $this->water_info[1]) / 2;
                break;

            /* 下居中水印 */
            case Image::IMAGE_WATER_SOUTH:
                $x = ($this->info['width'] - $this->water_info[0]) / 2;
                $y = $this->info['height'] - $this->water_info[1];
                break;

            /* 右居中水印 */
            case Image::IMAGE_WATER_EAST:
                $x = $this->info['width'] - $this->water_info[0];
                $y = ($this->info['height'] - $this->water_info[1]) / 2;
                break;

            /* 上居中水印 */
            case Image::IMAGE_WATER_NORTH:
                $x = ($this->info['width'] - $this->water_info[0]) / 2;
                $y = 0;
                break;

            /* 左居中水印 */
            case Image::IMAGE_WATER_WEST:
                $x = 0;
                $y = ($this->info['height'] - $this->water_info[1]) / 2;
                break;

            default:
                /* 自定义水印坐标 */
                if (is_array($locate)) {
                    list($x, $y) = $locate;
                } else {
                    $this->setError('不支持的水印位置类型');
                }
        }

        //添加水印
        $src = imagecreatetruecolor($this->water_info[0], $this->water_info[1]);

        // 调整默认颜色
        $color = imagecolorallocate($src, 255, 255, 255);
        imagefill($src, 0, 0, $color);

        imagecopy($src, $this->img, 0, 0, $x, $y, $this->water_info[0], $this->water_info[1]);
        imagecopy($src, $this->water, 0, 0, 0, 0, $this->water_info[0], $this->water_info[1]);
        imagecopymerge($this->img, $src, $x, $y, 0, 0, $this->water_info[0], $this->water_info[1], $alpha);

        //销毁零时图片资源
        imagedestroy($src);

        //销毁水印资源
        $des_water && imagedestroy($this->water);
        return true;
    }

    /* 切换到GIF的下一帧并保存当前帧，内部使用 */
    private function gifNext()
    {
        ob_start();
        ob_implicit_flush(0);
        imagegif($this->img);
        $img = ob_get_clean();

        $this->gif->image($img);
        $next = $this->gif->nextImage();

        if ($next) {
            $this->img = imagecreatefromstring($next);
            return $next;
        } else {
            $this->img = imagecreatefromstring($this->gif->image());
            return false;
        }
    }

    /**
     * 抛出错误
     *
     * @param string $msg
     * @param int $code
     * @throws \Exception
     */
    private function setError($msg, $code = 0)
    {
        throw new \Exception($msg, $code);
    }

    /**
     * 析构方法，用于销毁图像资源
     */
    public function __destruct()
    {
        empty($this->img) || imagedestroy($this->img);
    }
}
