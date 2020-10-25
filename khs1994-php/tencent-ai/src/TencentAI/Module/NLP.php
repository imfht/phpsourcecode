<?php

declare(strict_types=1);

namespace TencentAI\Module;

use TencentAI\Exception\TencentAIException;
use TencentAI\Kernel\Request;

trait NLP
{
    /**
     * 自然语言处理公共方法.
     *
     * @param      $url
     * @param      $text
     * @param bool $charSetGBK
     *
     * @throws TencentAIException
     *
     * @return array
     */
    private function nlp($url, $text, bool $charSetGBK = true)
    {
        if ($charSetGBK) {
            $data = [
                'text' => mb_convert_encoding($text, 'gbk', 'utf8'),
            ];

            return Request::exec($url, $data, false);
        }

        $data = [
            'text' => $text,
        ];

        return Request::exec($url, $data);
    }
}
