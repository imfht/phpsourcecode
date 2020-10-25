<?php 
/**
 * 控制器分配类
 * 
 * php 模板引擎
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date 2013-3-19
 */
namespace framework\core;


class Dispatcher extends Object
{
    /** @var object 控制器实例 */
    public $controller = null;

    public $beforeMethod = '';
    public $afterMethod ='';
    /** @var  Router */
    private $router;
    /**
     * 初始化应用
     * @param Router $router
     */
    function __construct(Router $router)
    {
        $this->router = $router;
        $action = substr($router->action, 6);
        $this->beforeMethod = 'before'.$action;
        $this->afterMethod = 'after'.$action;
    }

    /**
     * 执行
     */
    public function execute()
    {
        $this->controller = new $this->router->controller;
        $this->_checkAction($this->router->action);
        // 关闭APP_DUBUG时 对页面压缩
        if(APP_DEBUG || !ob_start('ob_gzhandler')) {
            ob_start();
        };
        if ($this->hasMethod($this->beforeMethod) && is_callable([$this->controller, $this->beforeMethod])){
            call_user_func([$this->controller, $this->beforeMethod]);
        }

        call_user_func_array([$this->controller, $this->router->action], $this->router->getQueryParams());

        if($this->hasMethod($this->afterMethod) && is_callable([$this->controller, $this->afterMethod])){
            call_user_func([$this->controller, $this->afterMethod]);
        }
        ob_end_flush();
    }

    /**
     * 检查action是否有效
     * @param string $action
     * @throws Abnormal
     */
    private function _checkAction($action)
    {
        if(substr($action, 0, 6) == 'before' || substr($action, 0, 5) == 'after'){
            if(APP_DEBUG){
                throw new Abnormal($action.'不可访问', 500);
            } else {
                Common::show404();
            }
        }
    }
}