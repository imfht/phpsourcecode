<?php

declare(strict_types=1);

namespace TencentAI;

use TencentAI\Exception\TencentAIException;
use TencentAI\Kernel\Request;

/**
 * Tencent AI OCR 能力.
 */
class OCR
{
    use Module\Image;
    use Module\OCR;

    const  BASE_URL = 'ocr/';

    const ID_CARD = self::BASE_URL.'ocr_idcardocr';

    const BUSINESS_CARD = self::BASE_URL.'ocr_bcocr';

    const DRIVE = self::BASE_URL.'ocr_driverlicenseocr';

    const BIZ = self::BASE_URL.'ocr_bizlicenseocr';

    const CREDIT_CARD = self::BASE_URL.'ocr_creditcardocr';

    const GENERAL = self::BASE_URL.'ocr_generalocr';

    const PLATE = self::BASE_URL.'ocr_plateocr';

    const HAND_WRITING = self::BASE_URL.'ocr_handwritingocr';

    /**
     * 身份证识别.
     *
     * @param string|\SplFileInfo $image 支持 JPG、PNG、BMP 格式
     * @param bool                $front 正面为 true
     *
     * @throws TencentAIException
     *
     * @return array
     *
     * @see   https://ai.qq.com/doc/ocridcardocr.shtml
     */
    public function idCard($image, bool $front = true)
    {
        $image = self::encode($image);

        $card_type = (int) !$front;

        return Request::exec(self::ID_CARD, compact('image', 'card_type'));
    }

    /**
     * 名片识别.
     *
     * @param mixed $image 支持 JPG、PNG、BMP 格式
     *
     * @throws TencentAIException
     *
     * @return mixed
     *
     * @see   https://ai.qq.com/doc/ocrbcocr.shtml
     */
    public function businessCard($image)
    {
        return $this->image(self::BUSINESS_CARD, $image);
    }

    /**
     * 行驶证驾驶证识别.
     *
     * @param mixed $image 支持 JPG PNG BMP 格式
     * @param int   $type  识别类型，0-行驶证识别，1-驾驶证识别
     *
     * @throws TencentAIException
     *
     * @return array
     *
     * @see   https://ai.qq.com/doc/ocrdriverlicenseocr.shtml
     */
    private function driver($image, int $type = 0)
    {
        $image = self::encode($image);

        return Request::exec(self::DRIVE, compact('type', 'image'));
    }

    /**
     * 驾驶证识别.
     *
     * @param mixed $image 支持 JPG PNG BMP 格式
     *
     * @throws TencentAIException
     *
     * @return array
     */
    public function driverLicense($image)
    {
        return $this->driver($image, 1);
    }

    /**
     * 行驶证识别.
     *
     * @param mixed $image 支持 JPG PNG BMP 格式
     *
     * @throws TencentAIException
     *
     * @return array
     */
    public function drivingLicense($image)
    {
        return $this->driver($image);
    }

    /**
     * 营业执照识别.
     *
     * @param mixed $image 支持 JPG PNG BMP 格式
     *
     * @throws TencentAIException
     *
     * @return mixed
     *
     * @see   https://ai.qq.com/doc/ocrbizlicenseocr.shtml
     */
    public function bizLicense($image)
    {
        return $this->image(self::BIZ, $image);
    }

    /**
     * 银行卡识别.
     *
     * @param mixed $image 支持 JPG PNG BMP 格式
     *
     * @throws TencentAIException
     *
     * @return mixed
     *
     * @see   https://ai.qq.com/doc/ocrcreditcardocr.shtml
     */
    public function creditCard($image)
    {
        return $this->image(self::CREDIT_CARD, $image);
    }

    /**
     * 通用识别.
     *
     * @param mixed $image 支持 JPG PNG BMP 格式
     *
     * @throws TencentAIException
     *
     * @return mixed
     *
     * @see   https://ai.qq.com/doc/ocrgeneralocr.shtml
     */
    public function general($image)
    {
        return $this->image(self::GENERAL, $image);
    }

    /**
     * 车牌 OCR.
     *
     * @param      $image
     * @param bool $isUrl 图片是否为网络地址 url
     *
     * @return mixed
     *
     * @throws TencentAIException
     *
     * @see https://ai.qq.com/doc/plateocr.shtml
     */
    public function plate($image, bool $isUrl = false)
    {
        return $this->image(self::PLATE, $image, $isUrl);
    }

    /**
     * 手写体 OCR.
     *
     * @param      $image
     * @param bool $isUrl 图片是否为网络地址 url
     *
     * @return mixed
     *
     * @throws TencentAIException
     *
     * @see https://ai.qq.com/doc/handwritingocr.shtml
     */
    public function handwriting($image, bool $isUrl = false)
    {
        return $this->image(self::HAND_WRITING, $image, $isUrl);
    }
}
