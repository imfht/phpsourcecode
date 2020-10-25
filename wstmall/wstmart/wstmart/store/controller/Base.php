<?php
namespace wstmart\store\controller;
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
 * 基础控制器
 */
use think\Controller;
use think\Db;
class Base extends Controller {
	public function __construct(){
		parent::__construct();
		$this->assign("v",WSTConf('CONF.wstVersion'));
		$this->view->filter(function($content){
			$content = str_replace("__STORE__",str_replace('/index.php','',$this->request->root()).'/wstmart/store/view/default',$content);
            $content = str_replace("__RESOURCE_PATH__",WSTConf('CONF.resourcePath'),$content);
            return $content;
        });
	}
    protected function fetch($template = '', $vars = [], $config = []){
        return $this->view->fetch("default/".$template, $vars, $config);
    }

	public function getVerify(){
		WSTVerify();
	}
	/**
     * 上传图
     */
	public function uploadPic(){
        $this->checkAuth();
		return WSTUploadPic(0);
	}
    /**
     * 上传视频
     */
    public function uploadVideo(){
        $this->checkAuth();
        return WSTUploadVideo();
    }

	/**
    * 编辑器上传文件
    */
    public function editorUpload(){
        $this->checkAuth();
        return WSTEditUpload(0);
    }

    //登录验证方法--商家
    protected function checkAuth(){
       	$USER = session('WST_STORE');
        if(!empty($USER) && $USER['userType']==2){
            //如果是店主就跳转，不是店主的话，就判断请求是否有权限。
            if(!$USER['STORE_MASTER']){
                $request = request();
                $visit = strtolower($request->module()."/".$request->controller()."/".$request->action());
                if(!in_array($visit,$USER['visitPrivilegeUrls'])){
                    if(request()->isAjax()){
                        die('{"status":-998,"msg":"您没有操作权限"}');
                    }else{
                        die('您没有操作权限');
                    }
                }
            }
        }else{
            if(request()->isAjax()){
                die('{"status":-999,"msg":"您还未登录"}');
            }else{
                $this->redirect('store/index/login');
                exit;
            }
        }
    }
}