<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/**
 * @deprecated 1.5.0
 */
class OrderDiscountCore extends OrderCartRule
{
	public function __get($key)
	{
		Tools::displayAsDeprecated();
		if ($key == 'id_order_discount')
			return $this->id_order_cart_rule;
		if ($key == 'id_discount')
			return $this->id_cart_rule;
		return $this->{$key};
	}
	
	public function __set($key, $value)
	{
		Tools::displayAsDeprecated();
		if ($key == 'id_order_discount')
			$this->id_order_cart_rule = $value;
		if ($key == 'id_discount')
			$this->id_cart_rule = $value;
		$this->{$key} = $value;
	}
	
	public function __call($method, $args)
	{
		Tools::displayAsDeprecated();
		return call_user_func_array(array($this->parent, $method), $args);
	}	
}