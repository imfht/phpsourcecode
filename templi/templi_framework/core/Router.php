<?php
/**
 * 路由类
 *
 * php 模板引擎
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date 2013-3-19
 */
namespace framework\core;
use Templi;


/**
 * Class Router
 * @package framework\core
 */
class Router extends Object
{

    use Singleton;

    /** @var string 当前模块 */
    public $controller = '';

    /** @var string 当前操作 */
    public $action = '';

    private $_queryParams = [];

    protected $urlProtocol ='auto';
    /**
     * 初始化应用
     */
    public function init()
    {
        $queryString = trim(Templi::getApp()->request->queryString, '/');
        if(($pos = strpos($queryString, '?'))!==false){
            $queryString = substr($queryString, 0, $pos);
        }
        $this->_queryParams = explode('-', $queryString);

        $this->controller = $this->_getCurrentController();
        $this->action     = $this->_getCurrentAction();
    }


    /**
     * 获取当前 控制器
     *
     * @return string
     */
    private function _getCurrentController() {
        $controller = '';
        if($this->urlProtocol='auto'){
            $controller = trim(Templi::getApp()->request->get('c'), '/');
            if(empty($controller) && !empty($this->_queryParams)){
                $controller = array_shift($this->_queryParams);
            }else{
                $this->_queryParams = [];
            }
        }

        $controller = str_replace('/', '\\', $controller);
        if (empty($controller)) {
            $controller =  Templi::getApp()->getConfig('default_controller');
        }else {
            $controller = trim(strval($controller));
        }
        $appName = Templi::getApp()->appName;

        return '\\'.$appName .'\\controllers\\'.$controller;
    }

    /**
     * 获取当前 操作
     *
     * @return string
     */
    private function _getCurrentAction() {
        $action = '';
        if($this->urlProtocol='auto'){
            $action = Templi::getApp()->request->get('a');
            if(empty($action) && !empty($this->_queryParams)){
                $action = array_shift($this->_queryParams);
            }
        }

        if (empty($action)) {
            $action =  Templi::getApp()->getConfig('default_action');
        } else {
            $action = trim(strval($action));
        }
        return 'action'.ucfirst($action);
    }

    /**
     * 获取绑定参数
     * @return array
     */
    public function getQueryParams()
    {
        return $this->_queryParams;
    }
}