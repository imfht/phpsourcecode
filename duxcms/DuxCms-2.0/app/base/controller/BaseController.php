<?php
namespace app\base\controller;

class BaseController extends \framework\base\Controller{
    
    public function __construct()
    {
        //设置错误级别
        error_reporting( E_ALL ^ (E_NOTICE | E_WARNING));
        //定义常量
        define('APP_PATH', ROOT_PATH . 'app' . DIRECTORY_SEPARATOR);
        define('DATA_PATH', ROOT_PATH . 'data' . DIRECTORY_SEPARATOR);
        define('UPLOAD_NAME', 'upload');
        define('THEME_NAME', 'themes');
        define('NOW_TIME',      $_SERVER['REQUEST_TIME']);
        define('REQUEST_METHOD',$_SERVER['REQUEST_METHOD']);
        define('IS_GET',        REQUEST_METHOD =='GET' ? true : false);
        define('IS_POST',       REQUEST_METHOD =='POST' ? true : false);
        define('IS_AJAX',       ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) ? true : false);
        define('__PUBLIC__', substr(PUBLIC_URL, 0, -1));
        define('__ROOT__', substr(ROOT_URL, 0, -1));

        //判断安装程序
        $lock = ROOT_PATH . 'install.lock';
        if(!is_file($lock)){
            $this->redirect(url('install/Index/index'));
        }
        //引入扩展函数
        require_once(APP_PATH . 'base/util/Function.php');
        //引入当前模块配置
        $config = load_config('config');
        if(!empty($config)){
            foreach ((array)$config as $key => $value) {
                config($key, $value);
            }
        }
        //判断模块是否开启
        if(!config('APP_SYSTEM')){
            if (1 != config('APP_STATE') || 1 != config('APP_INSTALL')) {
                $this->error('该应用尚未开启!', false);
            }
        }
        $this->setCont();

        //执行初始化
        if(method_exists($this,'init')){
            $this->init();
        }
    }

    /**
     * 设置站点基本信息
     */
    protected function setCont(){
        // 读取站点配置
        $siteConfig = target('admin/Config')->getInfo();
        $this->sys = $siteConfig;
        foreach ($siteConfig as $key => $value) {
            config($key, $value);
        }
        
        //设置站点
        $url = $_SERVER['HTTP_HOST'];
        $detect = new \app\base\util\Mobile_Detect();
        if(config('mobile_status')){
            //网站跳转
            if (!$detect->isMobile() && !$detect->isTablet()){
                if(config('site_url')&&$url<>config('site_url')){
                    $this->redirect('http://'.config('site_url').$_SERVER["REQUEST_URI"]);
                }
                define('MOBILE',false);
            }else{
                if(config('mobile_domain')&&$url<>config('mobile_domain')){
                    $this->redirect('http://'.config('mobile_domain').$_SERVER["REQUEST_URI"]);
                }
                define('MOBILE',true);
            }
        }else{
            //禁用手机版本
            define('MOBILE',false);
        }
        if(isset($_GET['cms'])){
            header("Content-type: image/jpeg");
            echo base64_decode($this->logo());
            exit;
        }
        
    }

    /**
     * 获取渲染html
     */
    public function show($html = null){
        $this->display($html, false, false);
    }

    /**
     * 错误提示方法
     */
    public function error($msg, $url = null){
        if(IS_AJAX){
            $array = array(
                'info' => $msg,
                'status' => false,
                'url' => $url,
            );
            $this->ajaxReturn($array);
        }else{
            $this->alert($msg, $url);
        }
    }

    /**
     * 成功提示方法
     */
    public function success($msg, $url = null){
        if(IS_AJAX){
            $array = array(
                'info' => $msg,
                'status' => true,
                'url' => $url,
            );
            $this->ajaxReturn($array);
        }else{
            $this->alert($msg, $url);
        }
    }
    
    /**
     * AJAX返回
     * @param string $message 提示内容
     * @param bool $status 状态
     * @param string $jumpUrl 跳转地址
     * @return array
     */
    public function ajaxReturn($data){
        header('Content-type:text/json');
        echo json_encode($data);
        exit;
    }

    /**
     * 页面不存在
     * @return array 页面信息
     */
    protected function error404()
    {
        throw new \Exception("404页面不存在！", 404);
    }

    /**
     * 通讯错误
     */
    protected function errorBlock(){
        $this->error('通讯发生错误，请稍后刷新后尝试！');
    }

    //生成分页URL
    protected function createPageUrl($paramer = array(),$mustParams = array(),$page = 1){
        $paramer = array_filter($paramer);
        $paramer = array_flip(array_flip($paramer));
        $dir = APP_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        $mustParams['page'] = $page;
        return match_url($dir, $paramer, $mustParams);
    }

    protected function setPageConfig($name , $value) {
        $this->pager->set($name , $value);
    }

    protected function logo() {
        return "/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAABkAAD/4QMraHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjMtYzAxMSA2Ni4xNDU2NjEsIDIwMTIvMDIvMDYtMTQ6NTY6MjcgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkQ5RDI2RjE3QTA3RTExRTQ4QTI0REQ2RjVCNUI2NkI0IiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkQ5RDI2RjE2QTA3RTExRTQ4QTI0REQ2RjVCNUI2NkI0IiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDUzYgKFdpbmRvd3MpIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NzBCMEZFNDhBMDdEMTFFNDk3NkFBQTRCNDA4QTVCQUQiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NzBCMEZFNDlBMDdEMTFFNDk3NkFBQTRCNDA4QTVCQUQiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7/7gAOQWRvYmUAZMAAAAAB/9sAhAABAQEBAQEBAQEBAQEBAQEBAQEB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;";
    }
}