<?php

declare(strict_types=1);

namespace TencentAI\Tests;

use TencentAI\Exception\TencentAIException;

class OCRTest extends TencentAITestCase
{
    const IMAGE = __DIR__.'/../resource/ocr/';

    const OUTPUT = __DIR__.'/../output/ocr/';

    private $name;

    private $array;

    private function ocr()
    {
        return $this->ai()->ocr();
    }

    /**
     * 身份证识别.
     *
     * @throws TencentAIException
     * @throws \Exception
     */
    public function testIdCard(): void
    {
        $this->name = __FUNCTION__;

        // 本地文件

        $array = $this->ocr()->idCard(self::IMAGE.'idcardz.jpg');
        $this->assertEquals(0, $array['ret']);
        file_put_contents(self::OUTPUT.'testIdCardz.json', json_encode($array, JSON_UNESCAPED_UNICODE));

        // url

        $array = $this->ocr()->idCard('https://raw.githubusercontent.com/khs1994-php/resource/master/ocr/idcardz.jpg');
        $this->assertEquals(0, $array['ret']);
        file_put_contents(self::OUTPUT.'testIdCardzfromurl.json', json_encode($array, JSON_UNESCAPED_UNICODE));

        // 文件内容

        $array = $this->ocr()->idCard(file_get_contents(self::IMAGE.'idcardz.jpg'));
        $this->assertEquals(0, $array['ret']);
        file_put_contents(self::OUTPUT.'testIdCardzfromcontent.json', json_encode($array, JSON_UNESCAPED_UNICODE));

        // SplFileInfo

        $array = $this->ocr()->idCard(new \SplFileInfo(self::IMAGE.'idcardz.jpg'));
        $this->assertEquals(0, $array['ret']);
        file_put_contents(self::OUTPUT.'testIdCardzfromsplfileinfo.json', json_encode($array, JSON_UNESCAPED_UNICODE));

        // resource

        $array = $this->ocr()->idCard(fopen(self::IMAGE.'idcardz.jpg', 'r'));
        $this->assertEquals(0, $array['ret']);
        file_put_contents(self::OUTPUT.'testIdCardzfromresource.json', json_encode($array, JSON_UNESCAPED_UNICODE));

//        $image = self::IMAGE . 'idcardf.jpg';
//        $this->array = $this->ocr()->idCard($image, false);
    }

    /**
     * 名片识别.
     *
     * @throws TencentAIException
     */
    public function testBusinessCard(): void
    {
        $this->name = __FUNCTION__;

        $image = self::IMAGE.'businesscard.jpg';
        $this->array = $this->ocr()->businessCard($image);
    }

    /**
     * 驾驶证识别.
     *
     * @throws TencentAIException
     * @throws \Exception
     */
    public function testDriverLicense(): void
    {
        $this->name = __FUNCTION__;

        $image = self::IMAGE.'driver.jpg';
        $this->array = $this->ocr()->driverLicense($image);
    }

    /**
     * 行驶证识别.
     *
     * @throws TencentAIException
     * @throws \Exception
     */
    public function testDrivingLicense(): void
    {
        $this->name = __FUNCTION__;

        $image = self::IMAGE.'driving.jpg';
        $this->array = $this->ocr()->drivingLicense($image);
    }

    /**
     * 营业执照识别.
     *
     * @throws TencentAIException
     */
    public function testBizLicense(): void
    {
        $this->name = __FUNCTION__;

        $image = self::IMAGE.'biz.jpg';
        $this->array = $this->ocr()->bizLicense($image);
    }

    /**
     * 银行卡识别.
     *
     * @throws TencentAIException
     */
    public function testCreditCard(): void
    {
        $this->name = __FUNCTION__;

        $image = self::IMAGE.'creditcard.jpg';
        $this->array = $this->ocr()->creditCard($image);
    }

    /**
     * 通用识别.
     *
     * @throws TencentAIException
     */
    public function testGeneral(): void
    {
        $this->name = __FUNCTION__;

        $image = self::IMAGE.'general.jpg';
        $this->array = $this->ocr()->general($image);
    }

    /**
     * @throws TencentAIException
     */
    public function testPlateFromUrl(): void
    {
        $this->markTestSkipped();

        $this->name = __FUNCTION__;

        $this->array = $this->ocr()->plate('https://yyb.gtimg.com/ai/assets/ai-demo/large/plate-1-lg.jpg', true);
    }

    /**
     * @throws TencentAIException
     */
    public function testPlate(): void
    {
        $this->name = __FUNCTION__;

        $image = self::IMAGE.'plate.jpg';
        $this->array = $this->ocr()->plate($image);
    }

    /**
     * @throws TencentAIException
     */
    public function testHandWritingFromUrl(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->ocr()->handwriting('https://yyb.gtimg.com/ai/assets/ai-demo/large/hd-1-lg.jpg', true);
    }

    /**
     * @throws TencentAIException
     */
    public function testHandWriting(): void
    {
        $this->name = __FUNCTION__;

        $image = self::IMAGE.'hd.jpg';
        $this->array = $this->ocr()->handwriting($image);
    }

    public function tearDown(): void
    {
        $this->assertEquals(0, $this->array['ret']);

        file_put_contents(self::OUTPUT.$this->name.'.json', json_encode($this->array, JSON_UNESCAPED_UNICODE));
    }
}
