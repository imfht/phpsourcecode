<?php

declare(strict_types=1);

namespace TencentAI\Module;

use TencentAI\Exception\TencentAIException;

trait Translate
{
    private $text_array = [
        'en' => [
            'zh', 'fr', 'es', 'it', 'de', 'tr', 'ru', 'pt', 'vi', 'id', 'ms', 'th',
        ],
        'zh' => [
            'en', 'fr', 'es', 'it', 'de', 'tr', 'ru', 'pt', 'vi', 'id', 'ms', 'th', 'jp', 'kr',
        ],
        'fr' => ['en', 'zh', 'es', 'it', 'de', 'tr', 'ru', 'pt'],
        'es' => ['en', 'zh', 'fr', 'it', 'de', 'tr', 'ru', 'pt'],
        'it' => ['en', 'zh', 'fr', 'es', 'de', 'tr', 'ru', 'pt'],
        'de' => ['en', 'zh', 'fr', 'es', 'it', 'tr', 'ru', 'pt'],
        'tr' => ['en', 'zh', 'fr', 'es', 'it', 'de', 'ru', 'pt'],
        'ru' => ['en', 'zh', 'fr', 'es', 'it', 'de', 'tr', 'pt'],
        'pt' => ['en', 'zh', 'fr', 'es', 'it', 'de', 'tr', 'ru'],
        'vi' => ['en', 'zh'],
        'id' => ['en', 'zh'],
        'ms' => ['en', 'zh'],
        'th' => ['en', 'zh'],
        'jp' => ['zh'],
        'kr' => ['zh'],
    ];

    private $image_array = [
        'en' => ['zh'],
        'zh' => ['en', 'jp', 'kr'],
        'jp' => ['zh'],
        'kr' => ['zh'],
    ];

    private $detect_array = [
        'zh',
        'en',
        'jp',
        'kr',
    ];

    /**
     * @param int $type
     *
     * @throws TencentAIException
     */
    private function checkAILabTextType(int $type): void
    {
        if ($type > 16 or $type < 0) {
            throw new TencentAIException(90700);
        }
    }

    /**
     * 检查文本翻译（翻译君）源语言与目标语言
     *
     * @param $source
     * @param $target
     *
     * @throws TencentAIException
     */
    private function checkTextTarget($source, $target): void
    {
        $text_array = $this->text_array;
        if (!(\array_key_exists($source, $text_array))) {
            throw new TencentAIException(90701);
        }
        if (!(\in_array($target, $text_array[$source], true))) {
            throw new TencentAIException(90702);
        }
    }

    /**
     * 检查图片翻译源语言与目标语言
     *
     * @param $source
     * @param $target
     *
     * @throws TencentAIException
     */
    private function checkImageTarget($source, $target): void
    {
        $image_array = $this->image_array;
        if (!(\array_key_exists($source, $image_array))) {
            throw new TencentAIException(90703);
        }
        if (!(\in_array($target, $image_array[$source], true))) {
            throw new TencentAIException(90704);
        }
    }

    /**
     * 语种识别待选类型检查.
     *
     * @param $language
     *
     * @throws TencentAIException
     */
    private function checkDetectType($language): void
    {
        if (!(\in_array($language, $this->detect_array, true))) {
            throw new TencentAIException(90705);
        }
    }
}
