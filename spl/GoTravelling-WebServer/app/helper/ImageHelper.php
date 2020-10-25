<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-4-11
 * Time: 下午9:34
 */

namespace Helper;

class ImageHelper
{
    /**
     * 将图片文件转换为dataURL.
     *
     * @param $imgSrc string 待转换图片文件的绝对路径
     * @return string 转换的结果
     */
    public static function imageToDataUrl($imgSrc)
    {
        $imageInfo = getimagesize($imgSrc);
        $base64Content = "data:{$imageInfo['mime']};base64,". chunk_split(base64_encode(
                file_get_contents($imgSrc)
            ));

        return $base64Content;
    }
}