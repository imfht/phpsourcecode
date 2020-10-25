<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-04-28
 * Time: 17:03
 */

namespace inhere\gearman\traits;

/**
 * Trait EventTrait
 * @package inhere\gearman\traits
 */
trait EventTrait
{
    /**
     * @var array
     */
    private $_events = [];

//////////////////////////////////////////////////////////////////////
/// events method
//////////////////////////////////////////////////////////////////////

    /**
     * register a event callback
     * @param string $name event name
     * @param callable $cb event callback
     * @param bool $replace replace exists's event cb
     * @return $this
     */
    public function on($name, callable $cb, $replace = false)
    {
        if ($replace || !isset($this->_events[$name])) {
            $this->_events[$name] = $cb;
        }

        return $this;
    }

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     */
    protected function trigger($name, array $args = [])
    {
        if (!isset($this->_events[$name]) || !($cb = $this->_events[$name])) {
            return null;
        }

        return call_user_func_array($cb, $args);
    }

    /**
     * @param $name
     * @return null
     */
    public function off($name)
    {
        $cb = null;

        if (isset($this->_events[$name])) {
            $cb = $this->_events[$name];
            unset($this->_events[$name]);
        }

        return $cb;
    }
}
