<?php
namespace Core\Util;

class File {
    public static function tree($path) {
        $files = array();
        $ds = glob($path . '/*');
        if(is_array($ds)) {
            foreach($ds as $entry) {
                if(is_file($entry)) {
                    $files[] = $entry;
                }
                if(is_dir($entry)) {
                    $rs = self::tree($entry);
                    foreach($rs as $f) {
                        $files[] = $f;
                    }
                }
            }
        }
        return $files;
    }
    
    public static function move($src, $des) {
        self::mkdirs(dirname($des));
        if(is_uploaded_file($src)) {
            move_uploaded_file($src, $des);
        } else {
            rename($src, $des);
        }
        @chmod($des, 0644);
        return is_file($des);
    }
    
    public static function mkdirs($path) {
        if(!is_dir($path)) {
            self::mkdirs(dirname($path));
            mkdir($path);
        }
        return is_dir($path);
    }

    public static function rmdirs($path, $clean=false) {
        if(!is_dir($path)) {
            return false;
        }
        $files = glob($path . '/*');
        if($files) {
            foreach($files as $file) {
                is_dir($file) ? self::rmdirs($file) : @unlink($file);
            }
        }
        return $clean ? true : @rmdir($path);
    }

    /**
     * 图像缩略处理
     * 可处理图像类型jpg和png
     * 如果原图像宽度小于指定宽度, 直接复制到目标地址
     * 如果原图像宽度大于指定宽度, 按比例缩放至指定宽度后保存至目标地址
     *
     * @param string $srcfile   原图像地址
     * @param string $desfile   新图像地址
     * @param int|number $width 大于0
     * @return bool true|error
     */
    public static function imageThumb($srcfile, $desfile, $width = 600) {
        if(!file_exists($srcfile)) {
            return error('-1','原图像不存在');
        }
        if(intval($width) <= 0) {
            return error('-1','缩放宽度无效');
        }

        $des = dirname($desfile);
        //创建存放目录
        if(!file_exists($des)) {
            if(!mkdirs($des)) {
                return error('-1','创建目录失败');
            }
        } elseif(!is_writable($des)) {
            return error('-1','目录无法写入');
        }

        //原图像信息
        $org_info = @getimagesize($srcfile);
        if($width > $org_info[0]) {
            copy($srcfile, $desfile);
            return true;
        }
        if($org_info) {
            if($org_info[2] == 1) { //gif不处理
                if(function_exists("imagecreatefromgif")) {
                    $img_org = imagecreatefromgif($srcfile);
                }
            } elseif($org_info[2] == 2) {
                if(function_exists("imagecreatefromjpeg")) {
                    $img_org = imagecreatefromjpeg($srcfile);
                }
            } elseif($org_info[2] == 3) {
                if(function_exists("imagecreatefrompng")) {
                    $img_org = imagecreatefrompng($srcfile);
                }
            }
        } else {
            return error('-1','获取原始图像信息失败');
        }
        //源图像的宽高比
        $scale_org = $org_info[0] / $org_info[1];
        //缩放后的高
        $height = $width / $scale_org;
        if(function_exists("imagecreatetruecolor") && function_exists("imagecopyresampled") && @$img_dst = imagecreatetruecolor($width, $height)) {
            imagecopyresampled($img_dst, $img_org, 0, 0, 0, 0, $width, $height, $org_info[0], $org_info[1]);
        } else {
            return error('-1','PHP环境不支持图片处理');
        }
        if(function_exists('imagejpeg')) {
            imagejpeg($img_dst, $desfile);
        } elseif(function_exists('imagepng')) {
            imagepng($img_dst, $desfile);
        }
        imagedestroy($img_dst);
        imagedestroy($img_org);
        return true;
    }
}