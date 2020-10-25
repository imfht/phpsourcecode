<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;

class Base extends controller{	
	/**
	 * 模块 
	 */
	public $module;
	
	/**
	 * 域名
	 */
	public $domain;
	
	/**
	 * 登录后记录的用户id
	 */
	public $uid = '';
	
	/**
	 * 用户名
	 */
	public $username = '';
	
	/**
	 * 用户昵称
	 */
	public $nickname = '';
	
	/**
	 * 获取控制器名称
	 */
	public $controller = '';
	
	/**
	 * 获取动作名称
	 */
	public $action = '';
	
	/**
	 * 授权后的菜单
	 */
	public $menu = [];
	
	/**
	 * 全局操作初始化方法
	 */
    protected function _initialize(){
		//检测当前是否登录
		if(!is_login()){
			$this->redirect(url('login/index'));
		}
		
		//获取session信息
		$user_auth = session('user_auth');
		
		//获取当前模块名称
		$this->module = request()->module();
		
		//赋值控制器名称
		$this->controller = request()->controller();
		
		//赋值动作名称
		$this->action = request()->action();
		
		//获取当前域名
		$this->domain = config('url_domain');
		
		//session 登录的管理员uid
		$this->uid = $user_auth['uid'];
		
		//管理员用户名
		$this->username = $user_auth['username'];
		
		//管理员用户昵称
		$this->nickname = $user_auth['nickname'];
		
		//实例授权auth模块
		$auth = new Auth();
		
		//获取访问的url地址
		$menu_path = $this->module. '/' . $this->controller . '/' . $this->action;
		
		//id为1的用户是超级管理员，不做权限验证
		if(intval(is_administrator()) != intval($this->uid)){
			if(!$auth->check($menu_path, $this->uid,['in','1,2'])){
				$this->error('没有授权访问');
			}
		}
		
		//对模版输出全局静态文件根路径
		$this->assign('static',$this->domain.'/static/'.$this->module);
		
		//控制器获取菜单方法
		$this->menu = model('Menu')->getMenus();
		
		//获取全局授权后的菜单
		$this->assign('menu', $this->menu);
		
	}
	
	/**
	 * 公共index
	 */
	public function index(){
		$model = $this->controller;
		$list = model($model)->paginate(15);
		$this->assign('list',$list);
		return $this->fetch();
	}
	
	public function lists($model = '', $map = [], $order = '', $field = true, $page = 15){
		$options = [];
		if(empty($model)){
			$model = $this->controller;
		}
		if(is_string($model)){
            $model = model($model);
        }
		return $model->where($map)->field($field)->order($order)->paginate($page);
	}
	
	/**
	 * 默认操作方法，在子类中可以被重写
	 */
	public function add(){
		return $this->fetch();
	}
	
	/**
	 * 状态启用和禁用
	 */
	public function state(){
		$model = $this->controller;
		$m = model($model);
		$pk = $m->getPK();
		$id = input($pk);
		$status = input('status');
		if($id && $status){
			$map = [$pk => ['in',$id]];
			if($m->where($map)->update(['status'=>$status])){
				$this->success('设置成功');
			}else{
				$this->error('设置失败');
			}
		}else{
			$this->error('参数有误');
		}
	}
	
	/**
	 * 物理删除
	 */
	public function remove(){
		$model = $this->controller;
		$m = model($model);
		$pk = $m->getPK();
		$data = input($pk);
		if($data){
			if($m->where([$pk => ['in',$data]])->delete()){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}
		}else{
			$this->error('提交的数据有误');
		}
		
	}
}
