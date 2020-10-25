<?php declare(strict_types = 1);
namespace msqphp\base\html;

use msqphp\core\traits;

final class Html
{
    use traits\CallStatic;

    // 扔出异常
    private static function exception(string $message) : void
    {
        throw new HtmlException($message);
    }
}