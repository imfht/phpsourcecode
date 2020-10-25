<?php
/**
 * @package        OpenCart
 * @author        Daniel Kerr
 * @copyright    Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license        https://opensource.org/licenses/GPL-3.0
 * @link        https://www.opencart.com
 */

/**
 * Registry class
 */
final class Registry
{
    private $data = array();

    private static $registry;

    public static function getSingleton()
    {
        if (self::$registry instanceof Registry) {
            return self::$registry;
        }
        return self::$registry = new Registry();
    }

    /**
     *
     *
     * @param    string $key
     * @param    null $default
     * @return    mixed
     */
    public function get($key, $default = null)
    {
        return (isset($this->data[$key]) ? $this->data[$key] : $default);
    }

    /**
     *
     *
     * @param    string $key
     * @param    string $value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     *
     *
     * @param    string $key
     *
     * @return    bool
     */
    public function has($key)
    {
        return isset($this->data[$key]);
    }
}