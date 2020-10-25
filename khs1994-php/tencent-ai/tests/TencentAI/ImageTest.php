<?php

declare(strict_types=1);

namespace TencentAI\Tests;

use TencentAI\Exception\TencentAIException;

class ImageTest extends TencentAITestCase
{
    const IMAGE = __DIR__.'/../resource/vision/';

    const OUTPUT = __DIR__.'/../output/image/';

    const IMAGE_FACE = self::IMAGE.'../face/wxc.jpg';

    const TERRORISM = self::IMAGE.'terrorism.jpg';

    const SCENER = self::IMAGE.'scener.jpg';

    const DOG = self::IMAGE.'dog.jpg';

    const FLOWER = self::IMAGE.'flower.jpg';

    const VEHICLE = self::IMAGE.'vehicle.jpg';

    const FOOD = self::IMAGE.'food.jpg';

    private $name;

    private $array;

    private function image()
    {
        return $this->ai()->image();
    }

    /**
     * 智能鉴黄.
     *
     * @throws TencentAIException
     */
    public function testPorn(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->image()->porn(self::IMAGE_FACE);
    }

    /**
     * 智能鉴黄 图片 url.
     *
     * @throws TencentAIException
     */
    public function testPornWithUrl(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->image()->porn(null, 'https://yyb.gtimg.com/aiplat/static/ai-demo/large/y-3.jpg');
    }

    /**
     * 暴恐识别.
     *
     * @throws TencentAIException
     */
    public function testTerrorism(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->image()->terrorism(self::TERRORISM);
    }

    /**
     * 暴恐识别 图片 url.
     *
     * @throws TencentAIException
     */
    public function testTerrorismWithUrl(): void
    {
        $this->markTestSkipped();

        $this->name = __FUNCTION__;

        $this->array = $this->image()->terrorism(null, 'https://yyb.gtimg.com/ai/assets/ai-demo/large/terror-14-lg.jpg');
    }

    /**
     * 物体场景识别 => 场景识别.
     *
     * @throws TencentAIException
     */
    public function testScener(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->image()->scener(self::SCENER);
    }

    /**
     * 物体场景识别 => 物体识别.
     *
     * @throws TencentAIException
     */
    public function testObject(): void
    {
        $this->markTestSkipped();

        $this->name = __FUNCTION__;

        $this->array = $this->image()->object(self::DOG);
    }

    /**
     * 标签识别.
     *
     * @throws TencentAIException
     */
    public function testTag(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->image()->tag(self::IMAGE_FACE);
    }

    /**
     * 花草识别.
     *
     * @throws TencentAIException
     */
    public function testIdentifyFlower(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->image()->identifyFlower(self::FLOWER);
    }

    /**
     * 车辆识别.
     *
     * @throws TencentAIException
     */
    public function testIdentifyVehicle(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->image()->identifyVehicle(self::VEHICLE);
    }

    /**
     * 看图说话.
     *
     * @throws TencentAIException
     */
    public function testImageToText(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->image()->imageToText(self::IMAGE_FACE, '1');
    }

    /**
     * 模糊图片检测.
     *
     * @throws TencentAIException
     */
    public function testFuzzy(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->image()->fuzzy(self::IMAGE_FACE);
    }

    /**
     * 美食图片.
     *
     * @throws TencentAIException
     */
    public function testFood(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->image()->food(self::FOOD);
    }

    public function tearDown(): void
    {
        $this->assertEquals(0, $this->array['ret']);

        file_put_contents(self::OUTPUT.$this->name.'.json', json_encode($this->array, JSON_UNESCAPED_UNICODE));
    }
}
