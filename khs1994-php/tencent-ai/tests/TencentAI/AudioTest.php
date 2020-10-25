<?php

declare(strict_types=1);

namespace TencentAI\Tests;

use TencentAI\Exception\TencentAIException;

class AudioTest extends TencentAITestCase
{
    const AUDIO = __DIR__.'/../resource/audio/';

    const OUTPUT = __DIR__.'/../output/audio/';

    private $name;

    private $array;

    private function audio()
    {
        return $this->ai()->audio();
    }

    /**
     * 语音识别.
     *
     * @throws TencentAIException
     */
    public function testAsr(): void
    {
        $this->name = __FUNCTION__;

        $voice = self::AUDIO.'1.wav';
        $this->array = $this->audio()->asr($voice, 2, 16000);
    }

    /**
     * 语音识别 流式版 AILAB.
     *
     * @throws TencentAIException
     */
    public function testasrs(): void
    {
        $this->name = __FUNCTION__;

        $voice = self::AUDIO.'2.wav';
        $this->array = $this->audio()->asrs($voice, '1', 0, 2);
    }

    /**
     * 语音识别 流式版 微信
     *
     * @throws TencentAIException
     */
    public function testWxasrs(): void
    {
        $this->name = __FUNCTION__;

        $voice = self::AUDIO.'2.wav';
        $this->array = $this->audio()->wxasrs($voice, '1', 0, 2);
    }

    /**
     * 长语音识别.
     *
     * @throws TencentAIException
     */
    public function testWxAsrLong(): void
    {
        $this->name = __FUNCTION__;

        $speech = self::AUDIO.'15s.wav';

        $this->array = $this->audio()->wxasrlong($speech, 'http://www.baidu.com/callback.php', 2);
    }

    public function testDetectKeyword(): void
    {
        $this->name = __FUNCTION__;

        $speech = self::AUDIO.'15s.wav';

        $this->array = $this->audio()->detectKeyword($speech,
            'http://www.baidu.com/callback.php',
            ['竞争', '商业'], 2);
    }

    /**
     * 语音合成 AILAB.
     *
     * @throws TencentAIException
     */
    public function testTts(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $array = $this->audio()->tts('北京天气怎么样', 1, 2);
        $this->put(__FUNCTION__.'.wav', $array['data']['speech']);
    }

    /**
     * 语音合成 优图.
     *
     * @throws TencentAIException
     */
    public function testTta(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $array = $this->audio()->tta('北京天气怎么样');
        $this->put(__FUNCTION__.'.mp3', $array['data']['voice']);
    }

    /**
     * @throws TencentAIException
     */
    public function testAaievilaudio(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->audio()->aaievilaudio('test', 'https://gitee.com/khs1994-php/resource/raw/master/audio/1.wav');
    }

    public function put(string $name, string $content): void
    {
        file_put_contents(self::OUTPUT.$name, base64_decode($content, true));
    }

    public function tearDown(): void
    {
        $this->assertEquals(0, $this->array['ret']);

        if ($this->name) {
            file_put_contents(self::OUTPUT.$this->name.'.json', json_encode($this->array, JSON_UNESCAPED_UNICODE));
        }
    }
}
