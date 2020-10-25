<?php

namespace Addons\Third;
use Common\Controller\Addon;

/**
 * 第三方拓展插件
 * @author 肥仔聪要淡定
 */

    class ThirdAddon extends Addon{

        public $info = array(
            'name'=>'Third',
            'title'=>'第三方拓展',
            'description'=>'第三方应用拓展',
            'status'=>1,
            'author'=>'肥仔聪要淡定',
            'version'=>'0.1',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/Third/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Third/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }