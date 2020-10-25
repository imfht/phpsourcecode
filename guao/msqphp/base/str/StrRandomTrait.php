<?php declare(strict_types = 1);
namespace msqphp\base\str;

trait StrRandomTrait
{
    /**
     * @param  int $length 字符长度
     * @param  int $type   字符类型
     */

    /**
     * 得到指定类型随机字符
     *       // type1:0-9
     *       // type2:0-9a-z
     *       // type3:0-9a-zA-Z
     *       // type4:0-9a-zA-Z~!@#$%^&*()_+`-=[]{};'"\|:<>?, ./
     */
    public static function randomString(int $length = 4, int $type = 3) : string
    {
        if ($length < 1) {

            throw new StrException($length.'必须大于0');
        }

        $random = '';
        switch ($type) {
            case 4:
                $random .= '~!@#$%^&*()_+`-=[]{};\'"\\|:<>?, ./';
            case 3:
                $random .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            case 2:
                $random .= 'abcdefghijklmnopqrstuvwxyz';
            case 1:
            default:
                $random .= '0123456789';
                break;
        }

        // 打乱字符串后截取指定个长度
        return substr(str_shuffle(str_repeat($random, $length)), 0, $length);
    }
    // 得到随机的加密字符
    public static  function randomBytes(int $length = 16) : string
    {
        if ($length < 1) {
            throw new StrException($length.'必须大于0');
        }
        return str_shuffle(random_bytes($length));
    }
    // 得到随机字符(高安全)
    public static  function random(int $length = 16) : string
    {
        if ($length < 1) {
            throw new StrException($length.'必须大于0');
        }
        $string = '';

        while ($length > 0) {
            $size = rand(1, $length);
            $string .= substr(str_shuffle(bin2hex(random_bytes($size))) , 0, $size);
            $length -= $size;
        }

        return $string;
    }
    // 快速得到一个字符串
    public static  function quickRandom($length = 16) : string
    {
        if ($length <= 0) {
            throw new StrException($length.'必须大于0');
        }
        return substr(str_shuffle((str_repeat('7O56JpRkTjPvKNn1S849zuXIYgCFaZ3GrmeUds02yWqcwAhQHfLMDiVlboxtEB', $length))), 0, $length);
    }
}