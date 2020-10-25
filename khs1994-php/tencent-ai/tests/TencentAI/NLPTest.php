<?php

declare(strict_types=1);

namespace TencentAI\Tests;

use TencentAI\Exception\TencentAIException;

class NLPTest extends TencentAITestCase
{
    const OUTPUT = __DIR__.'/../output/nlp/';

    private $name;

    private $array;

    private function nlp()
    {
        return $this->ai()->nlp();
    }

    /**
     * 分词.
     *
     * @throws TencentAIException
     */
    public function testWordseg(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->nlp()->wordseg('腾讯人工智能');
    }

    /**
     * 词性标注.
     *
     * @throws TencentAIException
     */
    public function testWordpos(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->nlp()->wordpos('腾讯人工智能');
    }

    /**
     * 专有名词识别.
     *
     * @throws TencentAIException
     */
    public function testWordner(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->nlp()->wordner('最近张学友在深圳开了一场演唱会');
    }

    /**
     * 同义词识别.
     *
     * @throws TencentAIException
     */
    public function testWordsyn(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->nlp()->wordsyn('今天的天气怎么样');
    }

    /**
     * 语义解析 => 意图成分识别.
     *
     * @throws TencentAIException
     */
    public function testWordcom(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->nlp()->wordcom('今天深圳的天气怎么样？明天呢');
    }

    /**
     * 情感分析.
     *
     * @throws TencentAIException
     */
    public function testTextPolar(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->nlp()->textPolar('今天的天气不错呀');
    }

    /**
     * 智能闲聊.
     *
     * @throws TencentAIException
     */
    public function testChat(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->nlp()->chat('中国女演员王晓晨', '1');
    }

    public function tearDown(): void
    {
        $this->assertEquals(0, $this->array['ret']);

        file_put_contents(self::OUTPUT.$this->name.'.json', json_encode($this->array, JSON_UNESCAPED_UNICODE));
    }
}
