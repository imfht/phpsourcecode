<?php 
namespace Common\Controller;
use Think\Controller;

class BaseController extends Controller
{
	/**
	 * 从快速缓存或配置表中加载系统配置信息
	 */
	public function loadSettings()
	{
		$settings = F('settings') ? F('settings') : M('Settings')->getField('name,value');
		C('settings', $settings);
	}

}
?>