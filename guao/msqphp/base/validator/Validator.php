<?php declare(strict_types = 1);
namespace msqphp\base\validator;

use msqphp\core\traits;

final class Validator
{
    use traits\CallStatic;

    // 扔出异常
    private static function exception(string $message) : void
    {
        throw new ValidatorException($message);
    }


    // Ip验证, 要求为合法的IPv4/v6 IP
    public static function ip(string $ip) : bool
    {
        return false !== filter_var($ip, FILTER_VALIDATE_IP);
    }


    // 手机号码验证
    public static function mobile($phone) : bool
    {
        return 0 !== preg_match('/^13[0-9]{1}[0-9]{8}$|15[012356789]{1}[0-9]{8}$|18[012356789]{1}[0-9]{8}$|14[57]{1}[0-9]{8}$/', $phone);
    }

    // 邮箱验证
    public static function emaile(string $email) : bool
    {
        return false !== filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    // qq号验证
    public static function qq(string $qq) : bool {
        return 0 !== preg_match('/^[1-9]\d{4, 12}$/', trim($qq));
    }

    // 邮政编码验证
    public static function zip(string $zip) : bool
    {
        return 0 !== preg_match('/^[1-9]\d{5}$/', trim($zip));
    }

    // url验证
    public static function url(string $url) : bool
    {
        return false !== filter_var($url, FILTER_VALIDATE_URL);
    }

    // 身份证验证
    public static function idCard(string $idCard) : bool
    {
        $len = strlen($idCard);
        // if ($len === 15) {
        //     $patten = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";
        //     if (false === preg_match($patten, $idCard, $matches)) {
        //         return false;
        //     }
        //     $dtm_birth = '19'.$matches[2] . '/' . $matches[3]. '/' .$matches[4];
        //     return strtotime($dtm_birth) ? true : false;
        // }
        if ($len === 18) {
            // 最后一位大写
            $idCard[-1] === 'x' && $idCard[-1] = 'X';

            //检查18位
            $patten = '/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/';
            if (false === preg_match($patten, $idCard, $matches)) {
                return false;
            }
            $dtm_birth = $matches[2] . '/' . $matches[3]. '/' .$matches[4];
             //检查生日日期是否正确
            if (!strtotime($dtm_birth)) {
              return FALSE;
            }

            //检验18位身份证的校验码是否正确。
            //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
            $arr_int = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
            $sign = 0;
            for ($i = 0; $i < 17; $i++ ) {
                $sign += (int) $idCard[$i] * $arr_int[$i];
            }
            $arr_ch = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
            $val_num = $arr_ch[$sign % 11];

            return $val_num === $idCard[-1];
        }
        return false;
    }

    // 颜色验证
    public static function color(string $color) : bool
    {
        static $patterns = array(
            // #000000->#FFFFFF
            '/^\\#([0-9a-fA-F]{6})$/',
            // #000->#FFF
            '/^\\#([0-9a-fA-F]{3})$/',
            // rgb(0,0,0)->RGB(255,255,255)
            '/^[rR][gG][bB]\\(((2[0-4][0-9]|25[0-5]|[01]?[0-9][0-9]?),){2}(2[0-4][0-9
            // rgb(0%,0%,0%)->RGB(100%,100%,100%)]|25[0-5]|[01]?[0-9][0-9]?)\\)$/',
            '/^[rR][gG][bB]\\(((100%|[0-9][0-9]?%|0),){2}(100%|[0-9][0-9]?%|0)\\)$/',
            // rgba(0%,0%,0%,0%)->RGBA(100%,100%,100%,100%)
            '/^[rR][gG][bB][aA]\\(((100%|[0-9][0-9]?%|0),){3}(100%|[0-9][0-9]?%|0)\\)$/',
            // rgba(0%,0%,0%,0.00)->RGBA(100%,100%,100%,1)
            '/^[rR][gG][bB][aA]\\(((100%|[0-9][0-9]?%|0),){3}([01]\\.?[0-9]?[0-9]?)\\)$/',
            // rgb(0,0,0,0)->RGB(255,255,255,100%)
            '/^[rR][gG][bB][aA]\\(((2[0-4][0-9]|25[0-5]|[01]?[0-9][0-9]?),){3}(100%|[0-9][0-9]?%|0)\\)$/',
            // rgb(0%,0%,0%,0.00)->RGB(100%,100%,100%,1)
            '/^[rR][gG][bB][aA]\\(((2[0-4][0-9]|25[0-5]|[01]?[0-9][0-9]?),){3}([01]\\.?[0-9]?[0-9]?)\\)$/',
        );
        foreach ($patterns as $pattern) {
            if (0 !== preg_match($pattern, $color)) {
                return true;
            }
        }
        return false;
    }

    // 检查是否是一个合法json
    public static function json(string $json) : bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROE_NONE;
    }

    // 是否是一个xml文本
    public static function xml(string $xml) : bool
    {
        define('LIBXML_VERSION') || static::exception('libxml is required', 500);
        $internal_errors = libxml_use_internal_errors();
        libxml_use_internal_errors(true);
        $result = simplexml_load_string($xml) !== false;
        libxml_use_internal_errors($internal_errors);

        return $result;
    }

    // 是否是一个html文本
    public static function html(string $html) : bool
    {
        return strlen(strip_tags($html)) < strlen($html);
    }

    // 是否全部为字母和(或)数字字符。
    public static function alnum(string $alnum) : bool
    {
        return ctype_alnum($alnum);
    }

    // 纯字母检测,字母仅仅是指 [A-Za-z]
    public static function alpha(string $alpha) : bool
    {
        return ctype_alpha($alpha);
    }

    // 查提供的 string 里面的字符是不是都是控制字符。 控制字符就是例如：换行、缩进、空格
    public static function cntrl(string $cntrl) : bool
    {
        return ctype_cntrl($cntrl);
    }

    // 是不是都是数字。 (允许小数)
    public static function digit(string $digit) : bool
    {
        return ctype_digit($digit);
    }

    // 没有空白
    public static function graph(string $graph) : bool
    {
        return ctype_graph($graph);
    }

    // 检查提供的 string 和 text 里面的字符是不是都是可以打印出来。
    public static function print(string $print) : bool
    {
        return ctype_print($print);
    }

    // 小写字母
    public static function lower(string $lower) : bool
    {
        return ctype_lower($lower);
    }

    // 大写字母
    public static function upper(string $upper) : bool
    {
        return ctype_upper($upper);
    }

    // 标点符号
    public static function punct(string $punct) : bool
    {
        return ctype_punct($punct);
    }

    // 空白字符
    public static function space(string $space) : bool
    {
        return ctype_space($space);
    }

    // 十六进制检测
    public static function xdigit(string $xdigit) : bool
    {
        return ctype_xdigit($xdigit);
    }

}