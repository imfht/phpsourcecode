<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Addons\Kefu;
use Common\Controller\Addon;

/**
 * 附件插件
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class KefuAddon extends Addon{

	public $info = array(
		'name'        => 'Kefu',
		'title'       => '客服插件',
		'description' => '用于前台客服显示',
		'status'      => 1,
		'author'      => 'tensent',
		'version'     => '0.1'
	);

	public function install(){
		return true;
	}

	public function uninstall(){
		return true;
	}

    //实现的pageFooter钩子方法
    public function pageFooter($param){
    	$config = $this->getConfig();
    	if ($config['is_open'] === '1') {
    		if ($config['qq']) {
				$qq = explode(',',$config['qq']);
				foreach($qq as $val){
					$list[] = explode('|',$val);
				}
				$config['qq'] = $list;
    		}
			
			$this->assign('kefu', $config);
			$this->display('kefu');
    	}
    }
}