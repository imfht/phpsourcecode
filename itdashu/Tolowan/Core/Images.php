<?php

namespace Core;

use Phalcon\Image\Adapter\Gd as ImagePs;

class Images
{

    /*
     * 根据指定的尺寸缩放图片，不关心原图比例
     */
    public static function thumbnail($path, $width, $height, $dstImgPath)
    {
        if (ImagePs::check()) {
            $image = new ImagePs($path);
            $image->resize($width, $height);
            if ($image->save()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    /*
     * 自动根据指定的尺寸缩放图片,会根据原图长宽最小尺寸缩放，较长的一方会被裁剪
     */
    public static function autoThumbnail($path, $width, $height, $dstImgPath,$offsetX = null,$offsetY = null)
    {
        if (ImagePs::check()) {
            $image = new ImagePs($path);
            $imgWidth = $image->getWidth();
            $imgHeight = $image->getHeight();
            // 计算拉伸尺寸
            $widthRatio = $width / $imgWidth;
            $heightRatio = $height / $imgHeight;
            if ($widthRatio > $heightRatio) {
                $newWidth = $width;
                $newHeight = $imgHeight * $widthRatio;
            } else {
                $newHeight = $height;
                $newWidth = $imgWidth * $heightRatio;
            }
            if(is_null($offsetX)){
                $offsetX = ($newWidth - $width) / 2;
            }
            if(is_null($offsetY)){
                $offsetY = ($newHeight - $height) / 2;
            }
            // 最终裁剪
            $image->resize($newWidth, $newHeight);
            $image->crop($width, $height, $offsetX, $offsetY);
            if ($image->save($dstImgPath)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public static function editorImage($file, $oldFile)
    {
        $imageConfig = Config::get('config', 'images');
        $baseConfig = Config::get('config');
        $fullFileName = rtrim($baseConfig['dir']['pubDir'], '/') . $oldFile;

        if (in_array($file->getType(), $config['imageType'])) {
            if (file_exists($fullFileName)) {
                $upInfo['size'] = $file->getSize();
                $upInfo['state'] = 'fileExists';
                if ($file->moveTo($fullFileName)) {
                    if (ImagePs::check()) {
                        $image = new ImagePs($fileName);
                        $upInfo['width'] = $image->getWidth();
                        $upInfo['height'] = $image->getHeight();
                        $upInfo['type'] = $image->getType();
                        $upInfo['url'] = $url;
                        $upInfo['state'] = 'success';
                    } else {
                        $upInfo['width'] = '';
                        $upInfo['height'] = '';
                        $upInfo['type'] = '';
                        $upInfo['url'] = $url;
                        $upInfo['state'] = '上传成功，但是您没有开启ＰＨＰ的ＧＤ扩展，图片无法编辑和获取基本信息！';
                    }
                } else {
                    $upInfo['state'] = 'moveTmp';
                }
            } else {
                $upInfo['state'] = 'fileNoExists';
            }
        } else {
            $upInfo['state'] = 'typeCheck';
            $upInfo['width'] = '';
            $upInfo['height'] = '';
            $upInfo['type'] = '';
            $upInfo['url'] = '';
            $upInfo['state'] = '';
        }
        return $upInfo;
    }
    public static function addImage($file)
    {
        $imageConfig = Config::get('config', 'images');
        $baseConfig = Config::get('config');
        $upInfo = array();
        if (true) {
            $upInfo['size'] = $file->getSize();

            $folder = $baseConfig["dir"]['imageBaseDir'];
            $dir = date('Y/m/d');
            if (!file_exists($folder . $dir)) {
                if (!mkdir($folder . $dir, 0777, true)) {
                    $upInfo['state'] = 'mkDir';
                }
            }

            $fileName = time() . rand(1, 10000) . strtolower(strrchr($file->getName(), '.'));
            $url = '/images/' . $dir . '/' . $fileName;
            $fileOldName = $fileName;
            $fileName = $folder . $dir . '/' . $fileName;

            if ($file->moveTo($fileName)) {
                if (ImagePs::check()) {
                    $image = new ImagePs($fileName);
                    $upInfo['originalName'] = $fileOldName;
                    $upInfo['name'] = $fileOldName;
                    $upInfo['width'] = $image->getWidth();
                    $upInfo['height'] = $image->getHeight();
                    $upInfo['type'] = $image->getType();
                    $upInfo['url'] = $url;
                    $upInfo['state'] = '上传成功';
                } else {
                    $upInfo['originalName'] = $fileOldName;
                    $upInfo['name'] = $fileOldName;
                    $upInfo['width'] = '';
                    $upInfo['height'] = '';
                    $upInfo['type'] = '';
                    $upInfo['url'] = $url;
                    $upInfo['state'] = '上传成功，但是您没有开启ＰＨＰ的ＧＤ扩展，图片无法编辑和获取基本信息！';
                }
            } else {
                $upInfo['state'] = 'moveTmp';
            }
        } else {
            $upInfo['state'] = 'typeCheck';
            $upInfo['width'] = '';
            $upInfo['height'] = '';
            $upInfo['type'] = '';
            $upInfo['url'] = '';
            $upInfo['state'] = '';
        }
        return $upInfo;
    }
}
