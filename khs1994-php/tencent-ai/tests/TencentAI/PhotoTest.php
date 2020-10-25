<?php

declare(strict_types=1);

namespace TencentAI\Tests;

use TencentAI\Exception\TencentAIException;

class PhotoTest extends TencentAITestCase
{
    const IMAGE = __DIR__.'/../resource/face/wxc.jpg';

    const OUTPUT = __DIR__.'/../output/photo/';

    private $name;

    private $array;

    private function photo()
    {
        return $this->ai()->photo();
    }

    /**
     * 人脸美妆.
     *
     * @throws TencentAIException
     */
    public function testCosmetic(): void
    {
        $this->name = __FUNCTION__;
        $this->array = $this->photo()->cosmetic(self::IMAGE, 11);
    }

    /**
     * 人脸变妆.
     *
     * @throws TencentAIException
     */
    public function testDecoration(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->photo()->decoration(self::IMAGE, 22);
    }

    /**
     * 滤镜 天天 P 图.
     *
     * @throws TencentAIException
     */
    public function testFilter(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->photo()->filter(self::IMAGE, 32);
    }

    /**
     * 滤镜 AILAB.
     *
     * @throws TencentAIException
     */
    public function testAiLabFilter(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->photo()->aiLabFilter(self::IMAGE, '65', 1);
    }

    /**
     * 人脸融合.
     *
     * @group dont-test
     *
     * @deprecated Not Available at 2018-11-30
     *
     * @throws TencentAIException
     */
    public function testMerge(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->photo()->merge(self::IMAGE, 8);
    }

    /**
     * 大头贴.
     *
     * @throws TencentAIException
     */
    public function testSticker(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->photo()->sticker(self::IMAGE, 21);
    }

    /**
     * 颜龄检测.
     *
     * @throws TencentAIException
     */
    public function testAge(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->photo()->age(self::IMAGE);
    }

    public function tearDown(): void
    {
        $this->assertEquals(0, $this->array['ret']);

        file_put_contents(self::OUTPUT.$this->name.'.json', json_encode($this->array, JSON_UNESCAPED_UNICODE));

        file_put_contents(self::OUTPUT.$this->name.'.jpg', base64_decode($this->array['data']['image'], true));
    }
}
