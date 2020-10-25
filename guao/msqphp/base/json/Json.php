<?php declare(strict_types = 1);
namespace msqphp\base\json;

use msqphp\core\traits;

final class Json
{
    use traits\CallStatic;

    // 扔出异常
    private static function exception(string $message) : void
    {
        throw new JsonException($message);
    }

    public static function decode(string $json)
    {
        return json_decode($json, true);
    }
    public static function encode($data) : string
    {
        return json_encode($data);
    }
}