<?php
namespace Ysf;
/**
 * template class 
 */
class View
{
	public static $engine = 'smarty';
	
	public static function display( $template='', $data=[] )
	{
		switch (self::$engine) {
			case 'smarty':
			default:
				return self::smarty_display($template,$data);
				break;
		}
	}
	
	public static function smarty_display($template,$data)
	{
		$smarty = new \Smarty;
		$smarty_config = config('smarty');
		foreach ($smarty_config as $k => $v) {
			$smarty->$k = $v;	
		}
		$smarty->assign($data);
		return $smarty->fetch($template);
	}
}