<?php
/**
 * helper.php
 * In twig: {{ helper.model('catalog/product').getProduct(28).name }}
 * Others:  model('catalog/product')->getProduct(28)
 *
 * @copyright  2017 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     Edward Yang <yangjin@opencart.cn>
 * @created    2017-11-29 10:52
 * @modified   2017-11-29 10:52
 */

namespace Utils;

class Helper
{
    private static $helper;

    public static function getSingleton()
    {
        if (self::$helper instanceOf Helper) {
            return self::$helper;
        }
        return self::$helper = new Helper;
    }

    public function __call($name, $arguments)
    {
        $name = snake_case($name);
        if (!function_exists($name)) {
            throw new \Exception("The function {$name} is not exist!");
        }
        return call_user_func_array($name, $arguments);
    }
}
