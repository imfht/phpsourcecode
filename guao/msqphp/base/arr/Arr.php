<?php declare (strict_types = 1);
namespace msqphp\base\arr;

use msqphp\core\traits;

final class Arr
{
    // 万能静态call
    use traits\CallStatic;

    use ArrOperateTrait;

    // 扔出异常
    private static function exception(string $message): void
    {
        throw new ArrException($message);
    }
}

trait ArrOperateTrait
{
    /**
     * 操作数组值,
     *
     * @example
     *  $arr= [
     *      'liming' => [
     *          'username'=>'test',
     *          'password'=>'123456'
     *      ]
     *  ];
     *
     *  Arr::get($arr, 'liming.username')
     *  ---->'test'
     *  Arr::set($arr, 'liming.username', 'liming');
     *  ----> $arr ----> ['liming'=>['username'=>'liming','password'=>'123456']];
     *  Arr::get($arr, 'liming.username')
     *  ----> 'liming'
     *  Arr::isset($arr, 'liming.username')
     *  ---->true
     *  Arr::isset($arr, 'liming.sex')
     *  ---->false
     *
     * @param  array  $array     待设置数组
     * @param  string $arr_key   数组键,以点为分隔符,若键为null,则代表整个数组
     * @param  miexd  $arr_value 对应值
     *
     * @throws ArrException
     */

    public static function set(array &$array,  ? string $arr_key, $arr_value) : void
    {
        // 如果键为空
        if (null === $arr_key) {
            $array = $arr_value;
            return;
        }

        // 以点分割
        $key    = explode('.', $arr_key);
        $result = &$array;
        // 递归
        for ($i = 0, $l = count($key); $i < $l; ++$i) {
            $result = &$result[$key[$i]];
        }

        // 赋值
        $result = $arr_value;
    }

    public static function get(array $array,  ? string $arr_key)
    {
        // 键为空
        if (null === $arr_key) {
            return $array;
        }

        // 以点分割
        $key    = explode('.', $arr_key);
        $result = &$array;
        // 递归赋值
        for ($i = 0, $l = count($key); $i < $l; ++$i) {
            if (!isset($result[$key[$i]])) {
                return null;
            }
            $result = &$result[$key[$i]];
        }

        // 返回
        return $result;
    }

    public static function isset(array $array,  ? string $arr_key) : bool
    {
        // 键为空
        if (null === $arr_key) {
            return true;
        }

        // 以点分割
        $key    = explode('.', $arr_key);
        $result = &$array;
        // 递归赋值
        for ($i = 0, $l = count($key); $i < $l; ++$i) {
            if (!isset($result[$key[$i]])) {
                return false;
            }
            $result = &$result[$key[$i]];
        }

        // 返回
        return true;
    }
}
