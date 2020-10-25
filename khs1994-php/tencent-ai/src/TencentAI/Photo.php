<?php

declare(strict_types=1);

namespace TencentAI;

use TencentAI\Exception\TencentAIException;
use TencentAI\Kernel\Request;

/**
 * Tencent AI 照片相关能力.
 */
class Photo
{
    const BASE_URL = 'ptu/';

    const COSMETIC = self::BASE_URL.'ptu_facecosmetic';

    const DECORATION = self::BASE_URL.'ptu_facedecoration';

    const FILTER = self::BASE_URL.'ptu_imgfilter';

    const AILAB_FILTER = 'vision/vision_imgfilter';

    const MERGE = self::BASE_URL.'ptu_facemerge';

    const STICKER = self::BASE_URL.'ptu_facesticker';

    const AGE = self::BASE_URL.'ptu_faceage';

    use Module\Image;

    /**
     * 人脸美妆 jpg png.
     *
     * 提供人脸美妆特效功能，可以帮您快速实现原始图片的人脸美妆特效处理
     *
     * @param mixed $image    仅支持 JPG、PNG 类型图片，尺寸长宽不超过 1080，返回格式 JPG
     * @param int   $cosmetic 美妆编码 1-23
     *
     * @throws TencentAIException
     *
     * @return array
     *
     * @see   https://ai.qq.com/doc/facecosmetic.shtml
     */
    public function cosmetic($image, int $cosmetic = 23)
    {
        $image = self::encode($image);

        return Request::exec(self::COSMETIC, compact('cosmetic', 'image'));
    }

    /**
     * 人脸变妆.
     *
     * 提供人脸变妆特效功能，可以帮您快速实现原始图片的人脸变妆特效处理.
     *
     * @param mixed $image      仅支持 JPG、PNG 类型图片，尺寸长宽不超过 1080，返回格式 JPG
     * @param int   $decoration 变妆编码 1-22
     *
     * @throws TencentAIException
     *
     * @return array
     *
     * @see   https://ai.qq.com/doc/facedecoration.shtml
     */
    public function decoration($image, int $decoration = 22)
    {
        $image = self::encode($image);

        return Request::exec(self::DECORATION, compact('decoration', 'image'));
    }

    /**
     * 图片滤镜（天天P图）.
     *
     * @param mixed $image  仅支持 JPG、PNG 类型图片，尺寸长宽不超过 1080，返回格式 JPG
     * @param int   $filter 滤镜效果编码 1-32
     *
     * @throws TencentAIException
     *
     * @return array
     *
     * @see   https://ai.qq.com/doc/ptuimgfilter.shtml
     */
    public function filter($image, int $filter = 32)
    {
        $image = self::encode($image);

        return Request::exec(self::FILTER, compact('filter', 'image'));
    }

    /**
     * 图片滤镜（AI Lab）.
     *
     * @param mixed  $image      仅支持 JPG、PNG 类型图片，尺寸长宽不超过 1080，返回格式 JPG
     * @param string $session_id
     * @param int    $filter     滤镜效果编码 1-65
     *
     * @throws TencentAIException
     *
     * @return array
     */
    public function aiLabFilter($image, string $session_id, int $filter)
    {
        $image = self::encode($image);

        return Request::exec(self::AILAB_FILTER, compact('filter', 'image', 'session_id'));
    }

    /**
     * 人脸融合.
     *
     * @param mixed $image 仅支持 JPG、PNG 类型图片，尺寸长宽不超过 1080，返回格式 JPG
     * @param int   $model 内置素材模板编码 1-10，自定义素材除外
     *
     * @throws TencentAIException
     *
     * @return array
     *
     * @deprecated Not Available at 2018-11-30
     * @see https://ai.qq.com/doc/facemerge.shtml
     */
    public function merge($image, int $model = 10)
    {
        $image = self::encode($image);

        return Request::exec(self::MERGE, compact('model', 'image'));
    }

    /**
     * 大头贴.
     *
     * @param mixed $image   仅支持 JPG、PNG 类型图片，尺寸长宽不超过 1080，返回格式 JPG
     * @param int   $sticker 大头贴编码 1-30
     *
     * @throws TencentAIException
     *
     * @return array
     *
     * @see   https://ai.qq.com/doc/facesticker.shtml
     */
    public function sticker($image, int $sticker = 30)
    {
        $image = self::encode($image);

        return Request::exec(self::STICKER, compact('sticker', 'image'));
    }

    /**
     * 颜龄检测.
     *
     * @param mixed $image 仅支持 JPG、PNG 类型图片，尺寸长宽不超过 1080，返回格式 JPG
     *
     * @throws TencentAIException
     *
     * @return mixed
     *
     * @see https://ai.qq.com/doc/faceage.shtml
     */
    public function age($image)
    {
        return self::image(self::AGE, $image);
    }
}
