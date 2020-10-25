<?php

declare(strict_types=1);

namespace TencentAI;

use TencentAI\Exception\TencentAIException;
use TencentAI\Kernel\Request;
use TencentAI\Module\NLP;

/**
 * Tencent AI 自然语言相关能力.
 */
class NaturalLanguageProcessing
{
    const BASE_URL = 'nlp/';

    const SEG = self::BASE_URL.'nlp_wordseg';

    const POS = self::BASE_URL.'nlp_wordpos';

    const NER = self::BASE_URL.'nlp_wordner';

    const SYN = self::BASE_URL.'nlp_wordsyn';

    const COM = self::BASE_URL.'nlp_wordcom';

    const POLAR = self::BASE_URL.'nlp_textpolar';

    const CHAT = self::BASE_URL.'nlp_textchat';

    use NLP;

    /**
     * 分词 GBK.
     *
     * 对文本进行智能分词识别，支持基础词与混排词粒度
     *
     * @param string $text
     *
     * @throws TencentAIException
     *
     * @return array
     *
     * @see   https://ai.qq.com/doc/nlpbase.shtml
     */
    public function wordseg(string $text)
    {
        return $this->nlp(self::SEG, $text);
    }

    /**
     * 词性标注 GBK.
     *
     * 在分词接口的基础上，增加词性标注能力，将分词结果中的每个分词赋予一个正确的词性，例如形容词、动名词或者名词等等
     *
     * @param string $text
     *
     * @throws TencentAIException
     *
     * @return array
     */
    public function wordpos(string $text)
    {
        return $this->nlp(self::POS, $text);
    }

    /**
     * 专有名词识别 GBK.
     *
     * 对文本进行专有名词的分词识别，找出文本中的专有名词
     *
     * @param string $text
     *
     * @throws TencentAIException
     *
     * @return array
     */
    public function wordner(string $text)
    {
        return $this->nlp(self::NER, $text);
    }

    /**
     * 同义词识别 GBK.
     *
     * 识别文本中存在同义词的分词，并返回相应的同义词
     *
     * @param string $text
     *
     * @throws TencentAIException
     *
     * @return array
     */
    public function wordsyn(string $text)
    {
        return $this->nlp(self::SYN, $text);
    }

    /**
     * 语义解析 => 意图成分识别.
     *
     * 对文本进行意图识别，快速找出意图及上下文成分.
     *
     * @param string $text
     *
     * @throws TencentAIException
     *
     * @return array
     *
     * @see   https://ai.qq.com/doc/nlpsem.shtml
     */
    public function wordcom(string $text)
    {
        return $this->nlp(self::COM, $text, false);
    }

    /**
     * 情感分析.
     *
     * 对文本进行情感分析，快速判断情感倾向（正面 1 或负面 -1 中性 0）
     *
     * @param string $text
     *
     * @throws TencentAIException
     *
     * @return array
     *
     * @see   https://ai.qq.com/doc/nlpemo.shtml
     */
    public function textPolar(string $text)
    {
        return $this->nlp(self::POLAR, $text, false);
    }

    /**
     * 智能闲聊.
     *
     * 基于文本的基础聊天能力，可以让您的应用快速拥有具备上下文语义理解的机器聊天功能.
     *
     * @param string $question 上限300字节
     * @param string $session  上限32字节
     *
     * @throws TencentAIException
     *
     * @return array
     *
     * @see   https://ai.qq.com/doc/nlpchat.shtml
     */
    public function chat(string $question, string $session)
    {
        return Request::exec(self::CHAT, compact('question', 'session'));
    }
}
