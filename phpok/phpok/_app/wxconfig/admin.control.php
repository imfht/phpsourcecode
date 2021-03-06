<?php
/**
 * 后台管理_用于配置微信各种参数信息
 * @作者 phpok.com <admin@phpok.com>
 * @版权 深圳市锟铻科技有限公司
 * @主页 http://www.phpok.com
 * @版本 5.x
 * @许可 http://www.phpok.com/lgpl.html PHPOK开源授权协议：GNU Lesser General Public License
 * @时间 2020年03月24日 20时22分
**/
namespace phpok\app\control\wxconfig;
/**
 * 安全限制，防止直接访问
**/
if(!defined("PHPOK_SET")){
	exit("<h1>Access Denied</h1>");
}
class admin_control extends \phpok_control
{
	private $popedom;
	public function __construct()
	{
		parent::control();
		$this->popedom = appfile_popedom('wxconfig');
		$this->assign("popedom",$this->popedom);
	}

	public function index_f()
	{
		if(!$this->popedom['list']){
			$this->error(P_Lang('您没有查看权限'));
		}
		$config = $this->model('wxconfig')->get_all();
		if($config){
			$this->assign('rs',$config);
		}
		$iplist = $this->model('wxconfig')->iplist();
		$this->assign('iplist',$iplist);
		$this->display('admin_index');
	}

	public function save_f()
	{
		if(!$this->popedom['setting']){
			$this->error(P_Lang('您没有配置权限'));
		}
		$config = array();
		$config['mp'] = $this->get('mp');
		$config['op'] = $this->get('op');
		$config['ap'] = $this->get('ap');
		$config['ip'] = $this->get('ip');
		$this->model('wxconfig')->save($config);
		$this->success();
	}
}
