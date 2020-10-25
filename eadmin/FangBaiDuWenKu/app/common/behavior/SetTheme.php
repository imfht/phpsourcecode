<?php
/*http://www.eadmin.top
QQ:49007623
*/
namespace app\common\behavior;

use think\Config;
use think\Request;
class SetTheme{
	public function run(&$params) {
		
		/*根据配置和来访设备类型自动切换为电脑主题或手机主题。 S*/
		$default_view_path = Config::get('template.view_path');//获取用户访问的模块所设置的模版目录
		if ($default_view_path != '') {
			$request = Request::instance();
			$theme_path = Config::get('theme.pc'); //设置主题默认路径为电脑端
			if ($request->isMobile()){
				$theme_path = Config::get('theme.mobile'); //设置主题默认路径为电脑端
			}
			$module_path = $request->module();//获取模块名称
			Config::set('template.view_path', $default_view_path . $theme_path . '/' );//根据模块名称和来访设备类型重置模版路径设置
		
		
		}
		
		/*根据配置和来访设备类型自动切换为电脑主题或手机主题。 E*/
	}
}