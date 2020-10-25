<?php declare (strict_types = 1);
namespace msqphp\base\dom;

use msqphp\core\traits;

final class Dom
{
    use traits\CallStatic;

    // 扔出异常
    private static function exception(string $message): void
    {
        throw new DomException($message);
    }
}
