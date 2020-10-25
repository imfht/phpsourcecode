<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/12/8
 * Time: 13:19
 */

namespace naples\lib;

/** 图像处理库
 * editor love_fc
 */
class Image
{
    // 当前图片
    private $img;
    private $status;
    private $ext;
    // 图像types 对应表
    private $types = array(1 => 'gif', 2 => 'jpg', 3 => 'png', 6 => 'bmp');
    // 设置
    private $_config = array('do_gif' => 1);


    /**
     * 设置图片来源
     * @param $img string path
     * @param $status int 状态
     */
    public function __construct($img, $status = 0)
    {
        $this->img    = $img;
        $this->status = $status;
        $this->ext=\Yuri2::getExtension($img);
    }

    /**
     * 返回图片信息数组
     * @return  array
     */
    public function getImageInfo($img='')
    {
        if ($img==''){$img=$this->img;}
        $info = @getimagesize($img);
        if (isset($this->types[$info[2]])) {
            $info['ext'] = $info['type'] = $this->types[$info[2]];
        } else {
            $info['ext'] = $info['type'] = 'jpg';
        }
        $info['type'] == 'jpg' && $info['type'] = 'jpeg';
        $info['size'] = @filesize($img);
        return $info;
    }

    /**
     * thumb(新图地址, 宽, 高, 裁剪, 允许放大, 清淅度)
     * @param $filename string
     * @param $new_w int
     * @param $new_h int
     * @param $cut int
     * @param $big int
     * @param $pct int
     * @return array 图片信息
     */
    public function thumb($filename, $new_w = 160, $new_h = 120, $cut = 0, $big = 0, $pct = 100)
    {
        if ($this->status == 1) {
            if (file_exists($filename))
                return true;
        }
        // 获取原图信息
        $info = $this->getImageInfo($this->img);
        if (!empty($info[0])) {
            $old_w = $info[0];
            $old_h = $info[1];
            $type  = $info['type'];
            $ext   = $info['ext'];
            unset($info);
            $result['type']   = $type;
            $result['width']  = $old_w;
            $result['height'] = $old_h;
            $just_copy        = false;
            // 是否处理GIF
            if ($ext == 'gif' && !$this->_config['do_gif']) {
                $just_copy = true;
            }
            // 如果原图比缩略图小 并且不允许放大
            if ($old_w < $new_h && $old_h < $new_w && !$big) {
                $just_copy = true;
            }
            if ($just_copy) {
                // 检查目录
                if (!is_dir(dirname($filename))) {
                    self::makeDir(dirname($filename));
                }
                @copy($this->img, $filename);
                return $result;
            }

            // 裁剪图片
            if ($cut == 0) { // 等比列
                $scale   = min($new_w / $old_w, $new_h / $old_h); // 计算缩放比例
                $width   = (int) ($old_w * $scale); // 缩略图尺寸
                $height  = (int) ($old_h * $scale);
                $start_w = $start_h = 0;
                $end_w   = $old_w;
                $end_h   = $old_h;
            } elseif ($cut == 1) { // center center 裁剪
                $scale1 = round($new_w / $new_h, 2);
                $scale2 = round($old_w / $old_h, 2);
                if ($scale1 > $scale2) {
                    $end_h   = round($old_w / $scale1, 2);
                    $start_h = ($old_h - $end_h) / 2;
                    $start_w = 0;
                    $end_w   = $old_w;
                } else {
                    $end_w   = round($old_h * $scale1, 2);
                    $start_w = ($old_w - $end_w) / 2;
                    $start_h = 0;
                    $end_h   = $old_h;
                }
                $width  = $new_w;
                $height = $new_h;
            } elseif ($cut == 2) { // left top 裁剪
                $scale1 = round($new_w / $new_h, 2);
                $scale2 = round($old_w / $old_h, 2);
                if ($scale1 > $scale2) {
                    $end_h = round($old_w / $scale1, 2);
                    $end_w = $old_w;
                } else {
                    $end_w = round($old_h * $scale1, 2);
                    $end_h = $old_h;
                }
                $start_w = 0;
                $start_h = 0;
                $width   = $new_w;
                $height  = $new_h;
            }
            // 载入原图
            $createFun = 'ImageCreateFrom' . $type;
            $oldimg    = $createFun($this->img);
            // 创建缩略图
            if ($type !== 'gif' && function_exists('imagecreatetruecolor')) {
                $newimg = @imagecreatetruecolor($width, $height);
            } else {
                $newimg = @imagecreate($width, $height);
            }
            // 复制图片
            if (function_exists("ImageCopyResampled")) {
                @ImageCopyResampled($newimg, $oldimg, 0, 0, $start_w, $start_h, $width, $height, $end_w, $end_h);
            } else {
                @ImageCopyResized($newimg, $oldimg, 0, 0, $start_w, $start_h, $width, $height, $end_w, $end_h);
            }
            // 检查目录
            if (!is_dir(dirname($filename))) {
                self::makeDir(dirname($filename));
            }

            // 对jpeg图形设置隔行扫描
            $type == 'jpeg' && imageinterlace($newimg, 1);
            // 生成图片
            $imageFun = 'image' . $type;
            if ($type == 'jpeg') {
                $did = @$imageFun($newimg, $filename, $pct);
            } else {
                $did = @$imageFun($newimg, $filename);
            }
            if (!$did)
                return false;
            imagedestroy($newimg);
            imagedestroy($oldimg);
            $result['width']  = $width;
            $result['height'] = $height;
            return $result;
        }
        return false;
    }

    /**
     * water(保存地址,水印图片,水印位置,透明度)
     * @param $filename string path
     * @param $water string path
     * @param $pos int
     * @param $pct int
     * @return string fileName
     */
    public function water($filename, $water, $pos = 0, $pct = 80)
    {
        // 加载水印图片
        $info = $this->getImageInfo($water);
        if (!empty($info[0])) {
            $water_w  = $info[0];
            $water_h  = $info[1];
            $type     = $info['type'];
            $fun      = 'imagecreatefrom' . $type;
            $waterimg = $fun($water);
        } else {
            return false;
        }
        // 加载背景图片
        $info = $this->getImageInfo($this->img);
        if (!empty($info[0])) {
            $old_w  = $info[0];
            $old_h  = $info[1];
            $type   = $info['type'];
            $ext    = $info['ext'];
            $fun    = 'imagecreatefrom' . $type;
            $oldimg = $fun($this->img);
        } else {
            return false;
        }
        // 是否处理GIF
        if ($ext == 'gif' && !$this->_config['do_gif']) {
            return false;
        }

        // 剪切水印
        $water_w > $old_w && $water_w = $old_w;
        $water_h > $old_h && $water_h = $old_h;

        // 水印位置
        switch ($pos) {
            case 0: //随机
                $posX = rand(0, ($old_w - $water_w));
                $posY = rand(0, ($old_h - $water_h));
                break;
            case 1: //1为顶端居左
                $posX = 0;
                $posY = 0;
                break;
            case 2: //2为顶端居中
                $posX = ($old_w - $water_w) / 2;
                $posY = 0;
                break;
            case 3: //3为顶端居右
                $posX = $old_w - $water_w;
                $posY = 0;
                break;
            case 4: //4为中部居左
                $posX = 0;
                $posY = ($old_h - $water_h) / 2;
                break;
            case 5: //5为中部居中
                $posX = ($old_w - $water_w) / 2;
                $posY = ($old_h - $water_h) / 2;
                break;
            case 6: //6为中部居右
                $posX = $old_w - $water_w;
                $posY = ($old_h - $water_h) / 2;
                break;
            case 7: //7为底端居左
                $posX = 0;
                $posY = $old_h - $water_h;
                break;
            case 8: //8为底端居中
                $posX = ($old_w - $water_w) / 2;
                $posY = $old_h - $water_h;
                break;
            case 9: //9为底端居右
                $posX = $old_w - $water_w;
                $posY = $old_h - $water_h;
                break;
            default: //随机
                $posX = rand(0, ($old_w - $water_w));
                $posY = rand(0, ($old_h - $water_h));
                break;
        }
        // 设定图像的混色模式
        imagealphablending($oldimg, true);
        // 添加水印
        imagecopymerge($oldimg, $waterimg, $posX, $posY, 0, 0, $water_w, $water_h, $pct);

        // 检查目录
        if (!is_dir(dirname($filename))) {
            self::makeDir(dirname($filename));
        }
        $fun = 'image' . $type;
        if ($type == 'jpeg') {
            $did = @$fun($oldimg, $filename, $pct);
        } else {
            $did = @$fun($oldimg, $filename);
        }
        !$did && die('保存失败!检查目录是否存在并且可写?');
        imagedestroy($oldimg);
        imagedestroy($waterimg);
        return $filename;
    }

    /**
     * 连续创建目录
     * @param $dir string 目标文件夹
     * @param $mode int 权限
     * @return bool
     */
    static function makeDir($dir, $mode = 0777)
    {
        $dir  = str_replace("\\", "/", $dir);
        $mdir = "";
        foreach (explode("/", $dir) as $val) {
            $mdir .= $val . "/";
            if ($val == ".." || $val == "." || trim($val) == "")
                continue;
            if (!is_dir($mdir)) {
                @mkdir($mdir);
                @chmod($mdir, $mode);
            }
        }
        if (is_dir($dir)){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * 显示这张图
     * @param $delAfterDisplay bool 是否阅后删除
     */
    function display($delAfterDisplay=false){
        header("contentm-type:image/$this->ext");
        $content=file_get_contents($this->img);
        echo $content;
        config('debug',false);
        config('trace',false);
        if ($delAfterDisplay){
            unlink($this->img);
        }
        exit();
    }


}