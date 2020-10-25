<?php

/*
 * 图片上传功能模块
 */

use PHPImageWorkshop\ImageWorkshop;

class Image extends Eloquent {

    /**
     * 图片上传功能
     * @param array $file图像文件
     * @param string $dirPath保存地址
     * @param string $file_name图像名字
     * @param int $width图像宽带
     * @param int $height图像高度
     * @return boolean|string
     */
    public static function upload($file, $dirPath, $file_name = null, $width = null, $height = null) {
        //1、验证图片格式
        if ((($file["type"] == "image/gif") || ($file["type"] == "image/jpeg") || ($file["type"] == "image/pjpeg") || ($file["type"] == "image/png"))) {
            if ($file["error"] > 0) {
                //文件上传错误
                return false;
            }
        } else {
            return false;
        }


        $layer = ImageWorkshop::initFromPath($file['tmp_name']);
        //2、处理图片
        if ($width && $height) {
            $layer->resizeInPixel($width, $height, true);
        } elseif ($width) {
            $layer->resizeInPixel($width, null, true);
        } else {
            $layer->resizeInPixel(null, $height, true);
        }

        //3、保存图片
        //设置上传路径，若为空，则为默认
        if (!$dirPath) {
            $dirPath = 'public/upload/other/';
        }
        //重新命名--年月日时分秒+8位流水号
        $lastdot = strrpos($file['name'], "."); //找到区分文件名与扩展名的标记符“.”最后出现的位置
        $extended = substr($file['name'], $lastdot + 1); //取出扩展名
        if ($file_name) {
            $file_name = $file_name . '.' . $extended;
        } else {
            $file_name = date('YmdHi', time()) . rand(11111111, 99999999) . '.' . $extended;
        }
        $createFolders = true;
        $backgroundColor = null; // transparent, only for PNG (otherwise it will be white if set null)
        $imageQuality = 70; // useless for GIF, usefull for PNG and JPEG (0 to 100%)

        $layer->save($dirPath, $file_name, $createFolders, $backgroundColor, $imageQuality);

        return $dirPath . $file_name;
    }

    /**
     * 图片删除功能
     * 删除以数组形式的图片
     */
    public static function delete_array($file_array) {
        foreach ($file_array as $row) {
            if (file_exists($row)) {
                unlink($row);
            }
        }
    }

}
