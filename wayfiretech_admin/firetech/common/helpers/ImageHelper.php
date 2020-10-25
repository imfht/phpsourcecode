<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-01 15:32:39
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-24 20:55:23
 */

namespace common\helpers;

use common\models\UploadFile;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * Class ImageHelper.
 *
 * @author chunchun <2192138785@qq.com>
 */
class ImageHelper
{
    /**
     * 默认图片.
     *
     * @param $imgSrc
     * @param string $defaultImgSre
     *
     * @return string
     */
    public static function default($imgSrc, $defaultImgSre = '/resources/img/error.png')
    {
        return !empty($imgSrc) ? $imgSrc : Yii::getAlias('@web').$defaultImgSre;
    }

    /**
     * 默认头像.
     *
     * @param $imgSrc
     */
    public static function defaultHeaderPortrait($imgSrc, $defaultImgSre = '/resources/img/profile_small.jpg')
    {
        return !empty($imgSrc) ? $imgSrc : Yii::getAlias('@web').$defaultImgSre;
    }

    /**
     * 点击大图.
     *
     * @param string $imgSrc
     * @param int    $width  宽度 默认45px
     * @param int    $height 高度 默认45px
     */
    public static function fancyBox($imgSrc, $width = 45, $height = 45)
    {
        $image = Html::img($imgSrc, [
            'width' => $width,
            'height' => $height,
        ]);

        return Html::a($image, $imgSrc, [
            'data-fancybox' => 'gallery',
        ]);
    }

    /**
     * 显示图片列表.
     *
     * @param $covers
     *
     * @return string
     */
    public static function fancyBoxs($covers, $width = 45, $height = 45)
    {
        $image = '';
        if (empty($covers)) {
            return $image;
        }

        !is_array($covers) && $covers = Json::decode($covers);

        foreach ($covers as $cover) {
            $image .= Html::tag('span', self::fancyBox($cover, $width, $height), [
                'style' => 'padding-right:5px;padding-bottom:5px',
            ]);
        }

        return $image;
    }

    /**
     * 判断是否图片地址
     *
     * @param string $imgSrc
     *
     * @return bool
     */
    public static function isImg($imgSrc)
    {
        $extend = StringHelper::clipping($imgSrc, '.', 1);

        $imgExtends = [
            'bmp',
            'jpg',
            'gif',
            'jpeg',
            'jpe',
            'jpg',
            'png',
            'jif',
            'dib',
            'rle',
            'emf',
            'pcx',
            'dcx',
            'pic',
            'tga',
            'tif',
            'tiffxif',
            'wmf',
            'jfif',
        ];
        if (in_array($extend, $imgExtends) || strpos($imgSrc, 'http://wx.qlogo.cn') !== false) {
            return true;
        }

        return false;
    }

    public static function tomedia($image, $type = 'default.jpg')
    {
        $hostUrl = Yii::$app->request->hostInfo;
        $default = '/resource/images/public/'.$type;

        if (is_array($image)) {
            foreach ($image as $key => &$value) {
                if ('//' == substr($value, 0, 2)) {
                    $value = 'http:' . $value;
                }elseif(('http://' == substr($value, 0, 7)) || ('https://' == substr($value, 0, 8))) {
                    $value = $value;
                }else{
                   $value = $value ? $hostUrl.'/attachment/'.$value : $hostUrl.$default;
                }
            }
        } else {
            if ('//' == substr($image, 0, 2)) {
                return 'http:' . $image;
            }
            if (('http://' == substr($image, 0, 7)) || ('https://' == substr($image, 0, 8))) {
                return $image;
            }
            
            $image = $image ? $hostUrl.'/attachment/'.$image : $hostUrl.$default;
        }

        return $image;
    }

    /**
     * 写入文件上传记录.
     *
     * @param int|null post
     *
     * @return string
     *
     * @throws NotFoundHttpException
     */
    public static function uploadDb($file_name, $file_size, $file_type, $extension, $file_url = '', $group_id = 0, $storage = 'local')
    {
        $datas = [
            'storage' => $storage,
            'group_id' => $group_id,
            'file_url' => $file_url,
            'file_name' => $file_name,
            'file_size' => $file_size,
            'file_type' => $file_type,
            'extension' => $extension,
            'is_delete' => 0,
        ];

        $UploadFile = new UploadFile();
        if ($UploadFile->load($datas, '') && $UploadFile->save()) {
            return $UploadFile;
        } else {
            return  ErrorsHelper::getModelError($UploadFile);
        }
    }
}
