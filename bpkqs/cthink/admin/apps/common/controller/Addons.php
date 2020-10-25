<?php
namespace app\common\controller;

/**
 * 插件父类，插件需要继承该类
 * @author zhanghd <zhanghd1987@foxmail.com>
 */
abstract class Addons extends \think\Controller{
	/**
     * 视图实例对象
     * @var view
     * @access protected
     */
    protected $view = null;
	
	/**
	 * 定义模版资源文件路径
	 */
	protected $static = '';
	
	/**
	 * 初始化魔法方法
	 */
	public function __construct(){
		$this->view = new \think\View(['view_path' =>EXTEND_PATH.'addons/']);
		$this->static = config('url_domain');
		$this->view->assign('static',$this->static.'/static/addons/');
	}
	
	/**
	 * 必须包含run方法,而且包含两个参数
	 * @param array|string $data 传递的参数值
	 * @param string $ainame 插件的名称
	 */
	abstract public function run($data,$aoname);
}
