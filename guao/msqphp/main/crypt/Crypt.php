<?php declare (strict_types = 1);
namespace msqphp\main\crypt;

use msqphp\core\config\Config;
use msqphp\core\traits;

final class Crypt
{
    use traits\CallStatic;

    private static $salt = '';
    private static $type = '';

    private static function getCryptSalt(): string
    {
        return static::$salt = static::$salt ?? Config::get('framework.salt');
    }
    private static function getCryptType(): string
    {
        return static::$type = static::$type ?? Config::get('framework.crypt_type');
    }
    public static function encode(string $data): string
    {
        $data = openssl_encrypt($data, static::getCryptType(), static::getCryptSalt(), OPENSSL_RAW_DATA);
        return base64_encode($data);
    }
    public static function decode(string $data): string
    {
        $data = base64_decode($data);
        return openssl_decrypt($data, static::getCryptType(), static::getCryptSalt(), OPENSSL_RAW_DATA);
    }
    public static function hash(string $data, string $salt): string
    {
        return password_hash($data, PASSWORD_BCRYPT, ['salt' => $salt, 'cost' => 10]);
    }
    public static function vertify(string $data, string $hash): bool
    {
        return password_verify($data, $hash);
    }
}
