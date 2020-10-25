<?php declare(strict_types = 1);
namespace msqphp\base\ini;

use msqphp\core\traits;

final class Ini
{
    use traits\CallStatic;

    // 扔出异常
    private static function exception(string $message) : void
    {
        throw new IniException($message);
    }

    /**
     * 转为ini编码
     * @param   array   $data  数据
     *
     * @return  string
     */
    public static function encode(array $data) : string
    {
        $string = '';
        foreach ($data as $key => $value) {
            is_array($value) || static::exception('数据值不能为数组,无法转化为ini格式');
            $string .= '[' . $key . ']' . PHP_EOL;
            foreach ($value as $k => $v) {
                (is_array($v) || is_object($v)) && static::exception('数组维数过多,无法转化为ini格式');
                $string .= $k . '=' . $v . PHP_EOL;
            }
        }
        return trim($string, PHP_EOL);
    }

    /**
     * ini解码
     * @param   string  $ini  ini内容
     *
     * @return  miexd
     */
    public static function decode(string $ini)
    {
        return parse_ini_string($ini);
    }
}