<?php
namespace Wpf\Common\Controllers;

class CommonController extends \Phalcon\Mvc\Controller{
    
    public $headercss;
    public $headercss__;
    public $headercssurl;
    public $headerjs;
    public $headerjsurl;
    
    public $footercss;
    public $footercssurl;
    public $footerjs;
    public $footerjsurl;
    
    public function initialize(){

    }
    
    public function onConstruct(){
        
        $this->setDbConfigData();
        
        define(MODULE_NAME,$this->dispatcher->getModuleName());
        define(CONTROLLER_NAME,$this->dispatcher->getControllerName());
        define(ACTION_NAME,$this->dispatcher->getActionName());
        
        define(NAMESPACE_NAME,$this->dispatcher->getNamespaceName());
        define(CONTROLLER_CLASS,$this->dispatcher->getControllerClass());
        
        $this->headercss =$this->createCollection('headercss');
        $this->headercss__ = $this->createCollection('headercss__');
        $this->headercssurl = $this->createCollection('headercssurl');
        $this->headerjs =$this->createCollection('headerjs');
        $this->headerjsurl = $this->createCollection('headerjsurl');
        
        $this->footercss =$this->createCollection('footercss');
        $this->footercssurl =$this->createCollection('footercssurl');
        $this->footerjs =$this->createCollection('footerjs');
        $this->footerjsurl =$this->createCollection('footerjsurl');
        
    }
    
    public function setDbConfigData($del = false){
        
        if($del){
            $this->cache->delete("DB_CONFIG_DATA");
        }
        
        if($this->cache->exists('DB_CONFIG_DATA')){
            $config = $this->cache->get('DB_CONFIG_DATA');
        }
		if(!$config){
            $ConfigModel = new \Wpf\Common\Models\Config();
            $config = $ConfigModel->lists();
            $this->cache->save('DB_CONFIG_DATA',$config,0);
		}
        
        $this->config->merge(new \Phalcon\Config($config));
    }
    
    public function afterCssJsToMin(){
        $this->setjoinmin($this->headercss,"css");
        $this->setjoinmin($this->headerjs,"js");
        $this->setjoinmin($this->footercss,"css");
        $this->setjoinmin($this->footerjs,"js");
    }
    
    public function createCollection($collectionname = ""){
        if(! $collectionname){
            return false;
        }
        
        return $this->assets->collection($collectionname)->setLocal(false);
    }
    
    public function setjoinmin($collection,$type = "css"){
        if(! is_object($collection)){
            return false;
        }
        
        if(! $this->getMinCssJsfilename($collection,$type,PUBLIC_PATH."/")){
            return false;
        }
        
        $collection
            ->setTargetPath($this->getMinCssJsfilename($collection,$type,PUBLIC_PATH."/"))
            ->setTargetUri($this->getMinCssJsfilename($collection,$type))
            ->setTargetLocal(false)
            ->join(true);
        if($type == "css"){
            $collection->addFilter(new \Phalcon\Assets\Filters\Cssmin());
        }else{
            $collection->addFilter(new \Phalcon\Assets\Filters\Jsmin());
        }
        return $collection;
        
    }
    
    
    public function getMinCssJsfilename($collection,$type="css",$out=""){
        if(! is_object($collection)){
            return false;
        }
        
        $filename = "";
        
        if($out && stripos($out,"/") === false){
            $out .= "/";
        }
        
        if($collection->getResources()){
            foreach($collection->getResources() as $value){
                $filename .= $value->getPath();
            }
            $filename = $out."temp/min/".$type."/".md5($filename).".".$type;
            return $filename;
        }else{
            return false;
        }
        
        
        
    }
    
    
    private function createCssJsTempDIR(){
        $dir = $this->dispatcher->getModuleName().$this->dispatcher->getControllerName().$this->dispatcher->getActionName();
    }
    
    
    public function beforeExecuteRoute($dispatcher){
        
        $class = $dispatcher->getActiveController();
        
        $beforeaction = "before_".$dispatcher->getActionName();
        
        if(method_exists($class,$beforeaction)){
            $class->$beforeaction();
        }
        
    }
    
    public function afterExecuteRoute($dispatcher)
    {
        if(! APP_DEBUG){
            $this->afterCssJsToMin();
        }
        
        
        $class = $dispatcher->getActiveController();
        
        $afteraction = "after_".$dispatcher->getActionName();
        
        if(method_exists($class,$afteraction)){
            $class->$afteraction();
        }
        
        if($this->request->isAjax()){
            $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_NO_RENDER);
        }
        
    }
    
    
    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param string $message 错误信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    public function error($message='',$jumpUrl='',$ajax=false) {
        $this->dispatchJump($message,0,$jumpUrl,$ajax);
    }

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    public function success($message='',$jumpUrl='',$ajax=false) {
        $this->dispatchJump($message,1,$jumpUrl,$ajax);
    }
    
    /**
     * 默认跳转操作 支持错误导向和正确跳转
     * 调用模板显示 默认为public目录下面的success页面
     * 提示页面为可配置 支持模板标签
     * @param string $message 提示信息
     * @param Boolean $status 状态
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @access private
     * @return void
     */
    private function dispatchJump($message,$status=1,$jumpUrl='',$ajax=false) {
        if(true === $ajax || $this->request->isAjax()) {// AJAX提交
            $data           =   is_array($ajax)?$ajax:array();
            $data['info']   =   $message;
            $data['status'] =   $status;
            $data['url']    =   $jumpUrl;
            $this->ajaxReturn($data);
        }
        if(is_int($ajax)) $this->view->setVar('waitSecond',$ajax);
        if(!empty($jumpUrl)) $this->view->setVar('jumpUrl',$jumpUrl);
        
        // 提示标题
        $this->view->setVar('msgTitle',$status? '操作成功！' : '操作失败！');

        
        
        //如果设置了关闭窗口，则提示完毕后自动关闭窗口
        if($this->view->getVar('closeWin'))    $this->view->setVar('jumpUrl','javascript:window.close();');
        
        $this->view->setVar('status',$status);   // 状态
        //保证输出不受静态缓存影响
        //C('HTML_CACHE_ON',false);
        if($status) { //发送成功信息
            $this->view->setVar('message',$message);// 提示信息
            // 成功操作后默认停留1秒
            if(!($this->view->getVar('waitSecond')))    $this->view->setVar('waitSecond','1');
            // 默认操作成功自动返回操作前页面
            if(!($this->view->getVar("jumpUrl"))) $this->view->setVar("jumpUrl",$_SERVER["HTTP_REFERER"]);
            
        }else{
            $this->view->setVar('error',$message);// 提示信息
            //发生错误时候默认停留3秒
            if(!($this->view->getVar('waitSecond')))    $this->view->setVar('waitSecond','3');
            // 默认发生错误的话自动返回上页
            if(!($this->view->getVar("jumpUrl"))) $this->view->setVar('jumpUrl',"javascript:history.back(-1);");
            
        }
        
        $this->dispatchJumpToTmpl();
        
    }
    
    /**
     * 成功和错误提示的模板输出，如果分模块处理的话，请复写此方法,或者复写dispatchJump方法
     * commonController::dispatchJumpToTmpl()
     * 
     * @return void
     */
    protected function dispatchJumpToTmpl($viewsdir = "",$controllerName="",$actionName = "dispatchjump"){
        if(! $viewsdir){
            $viewsdir = COMMON_PATH."/Views/";
        }
        if(! $actionName){
            $actionName = "dispatchjump";
        }
        $this->view->setViewsDir($viewsdir);        
        $this->view->start();
        $this->view->render($controllerName,$actionName);
        $this->view->finish();
        echo $this->view->getContent();
        exit;
    }
    
    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @param int $json_option 传递给json_encode的option参数
     * @return void
     */
    protected function ajaxReturn($data,$type='JSON',$json_option=0) {
        
        switch (strtoupper($type)){
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data,$json_option));
            case 'XML'  :
                // 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data));
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                $handler  =   'jsonpReturn';
                exit($handler.'('.json_encode($data,$json_option).');');  
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($data);            
            default     :
                // 用于扩展其他返回格式数据
                //Hook::listen('ajax_return',$data);
                exit;
        }
    }

    
}