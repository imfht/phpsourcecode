<?php
namespace addons\common\coupon\controller;

use think\addons\Controller;

class Init extends Controller
{
	public function _initialize(){
		
	}
	//安装
    public function install()
    {
    	$install_sql = './addons/common/coupon/data/install.sql';
        if (file_exists($install_sql)) {
            execute_sql_file($install_sql);
        }
        //添加钩子
        $config = config('addons');
        $config['useCoupon'] = 'common\coupon\coupon';
        save_config(APP_PATH . '/extra/addons.php', $config);

    	file_put_contents('./addons/common/coupon/install.lock','');
    	$this->success("安装成功", cookie("prevUrl"));
    }
    //卸载
    public function uninstall()
    {
    	$uninstall_sql = './addons/common/coupon/data/uninstall.sql';
        if (file_exists($uninstall_sql)) {
            execute_sql_file($uninstall_sql);
        }

        //删除钩子
        $config = config('addons');
        unset($config['useCoupon']);
        save_config(APP_PATH . '/extra/addons.php', $config);

    	unlink('./addons/common/coupon/install.lock');
    	$this->success("卸载成功", cookie("prevUrl"));
    }
}
