<?php
namespace wstmart\mobile\controller;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 关闭提示处理控制器
 */
use think\Controller;
class Switchs extends Controller{
	public function __construct(){
		parent::__construct();
		WSTConf('CONF',WSTConfig());
		$style = WSTConf('CONF.wstmobileStyle')?WSTConf('CONF.wstmobileStyle'):'default';
		$this->view->filter(function($content){
            $style = WSTConf('CONF.wstmobileStyle')?WSTConf('CONF.wstmobileStyle'):'default';
            $content = str_replace("__RESOURCE_PATH__",WSTConf('CONF.resourcePath'),$content);
            $content = str_replace("__MOBILE__",str_replace('/index.php','',$this->request->root()).'/wstmart/mobile/view/'.$style,$content);
            return $content;
        });
		$this->assign("v",WSTConf('CONF.wstVersion')."_".WSTConf('CONF.wstPcStyleId'));
	}
	protected function fetch($template = '', $vars = [], $replace = [], $config = []){
		$style = WSTConf('CONF.wstmobileStyle')?WSTConf('CONF.wstmobileStyle'):'default';
		return $this->view->fetch($style."/".$template, $vars, $replace, $config);
	}
    public function index(){
        return $this->fetch('error_switch');
    }
}
