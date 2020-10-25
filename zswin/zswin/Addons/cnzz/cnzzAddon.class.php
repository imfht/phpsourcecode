<?php

namespace Addons\cnzz;
use Common\Controller\Addon;

/**
 * 站长统计插件
 * @author zswin
 */

    class cnzzAddon extends Addon{

        public $info = array(
            'name'=>'cnzz',
            'title'=>'站长统计',
            'description'=>'只是给用户提供填站长统计代码的地方',
            'status'=>1,
            'author'=>'zswin',
            'version'=>'0.1'
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

        public function pageFooter($param)
        {
        	$platform_options = $this->getConfig();
        	
        	echo  $platform_options['cnzz'];
        }

    }