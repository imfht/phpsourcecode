<?php

declare(strict_types=1);

namespace TencentAI\Kernel;

/**
 * @method static \TencentAI\Audio                     audio()
 * @method static \TencentAI\Face                      face()
 * @method static \TencentAI\Image                     image()
 * @method static \TencentAI\NaturalLanguageProcessing nlp()
 * @method static \TencentAI\OCR                       ocr()
 * @method static \TencentAI\Photo                     photo()
 * @method static \TencentAI\Translate                 translate()
 */
class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'tencent-ai';
    }
}
