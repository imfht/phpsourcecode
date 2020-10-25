<?php

declare(strict_types=1);

namespace TencentAI\Tests;

use TencentAI\Exception\TencentAIException;

class TranslateTest extends TencentAITestCase
{
    const IMAGE = __DIR__.'/../resource/translate/english.jpg';

    const OUTPUT = __DIR__.'/../output/translate/';

    private $name;

    private $array;

    private function translate()
    {
        return $this->ai()->translate();
    }

    /**
     * 文本翻译 AILAB.
     *
     * @throws TencentAIException
     */
    public function testAILabText(): void
    {
        $this->expectException(\TencentAI\Exception\TencentAIException::class);

        $this->name = __FUNCTION__;

        $this->array = $this->translate()->aILabText('中华人民共和国', 0);

        $this->array = $this->translate()->aILabText('中华人民共和国', 17);
    }

    /**
     * 文本翻译 翻译君.
     *
     * @throws TencentAIException
     */
    public function testText(): void
    {
        $this->expectException(\TencentAI\Exception\TencentAIException::class);

        $this->name = __FUNCTION__;

        $this->array = $this->translate()->text('中华人民共和国', 'zh', 'en');

        $this->array = $this->translate()->text('中华人民共和国', 'mock', 'mock');
    }

    /**
     * 图片翻译.
     *
     * @throws TencentAIException
     */
    public function testImage(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->translate()->image(self::IMAGE, '1', 'word', 'en', 'zh'
        );
    }

    /**
     * 图片翻译异常抛出.
     */
    public function testImageThrowException(): void
    {
        $this->expectException(\TencentAI\Exception\TencentAIException::class);

        $this->name = __FUNCTION__;

        $this->array = $this->translate()->image(
            self::IMAGE, '1', 'mock', 'en', 'zh'
        );
    }

    /**
     * 图片翻译.
     */
    public function testImageThrowExceptionFunction(): void
    {
        $this->name = __FUNCTION__;

        try {
            $this->array = $this->translate()->image(
                self::IMAGE, '1', 'word', 'mock', 'zh'
            );
        } catch (TencentAIException $e) {
            $e->__toString();
            $this->assertEquals(['ret' => $e->getCode(), 'msg' => $e->getMessage()], $e->getExceptionAsArray());
            $this->assertEquals('{"ret":90703,"msg":"图片翻译源语言错误"}', $e->getExceptionAsJson());
        }
    }

    /**
     * 语音翻译.
     *
     * @throws TencentAIException
     */
    public function testAudio(): void
    {
        $this->name = __FUNCTION__;

        $voice = __DIR__.'/../resource/translate/t.pcm';
        $this->array = $this->translate()->audio($voice, '1', 6);
    }

    /**
     * 语种识别.
     *
     * @throws TencentAIException
     */
    public function testDetect(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->translate()->detect('中国');
    }

    /**
     * 语种识别.
     *
     * @throws TencentAIException
     */
    public function testDetectWithArray(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->translate()->detect('chinese', ['en', 'zh']);
    }

    /**
     * 语种识别抛出异常.
     */
    public function testDetectWithArrayThrowException(): void
    {
        $this->expectException(\TencentAI\Exception\TencentAIException::class);

        $this->name = __FUNCTION__;

        $this->array = $this->translate()->detect('chinese', ['en', 'mock']);
    }

    public function tearDown(): void
    {
        $this->assertEquals(0, $this->array['ret']);

        file_put_contents(self::OUTPUT.$this->name.'.json', json_encode($this->array, JSON_UNESCAPED_UNICODE));
    }
}
