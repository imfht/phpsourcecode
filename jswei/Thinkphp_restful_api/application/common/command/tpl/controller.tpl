<?php
namespace app\{$namespace}\controller;

/**
 * Class {$_controller}
 * @title {$_name}
 * @url /{$api_version}/{$controller}
 * @desc  {$_name}
 * @version {$version}
 * @package app\{$namespace}\{$controller}
 * @route('{$api_version}/{$controller}')
 */
class {$_controller} extends Base{
    //是否开启授权认证,默认开启
    public $apiAuth = true;
    protected $model = null;
    protected $validate = null;

    /*
    * 需要附加的其他方法,比如:topList,newList
    * protected $extraActionList = ['topList','newList',...];
    * 已经对核心的类进行了重写,扩展的新方法会自动添加到认证中,无需再手动书写
    */
    protected $extraActionList = [];

    public function __construct(){
        parent::__construct();
        $this->model = new \app\{$namespace}\model\{$_controller};
        $this->validate = new \app\{$namespace}\validate\{$_controller};
    }

    /**
     * @title 获取资源
     * @method get 方法
     * @param int  $id 资源id
     */
    public function index($id = 0){
        //@todo your get code here
    }

    /**
     * @title 保存
     * @method save 方法
     * @return  返回结果
     */
    public function save(){
        //@todo your create code here
    }

     /**
     * @title 更新
     * @method update 方法
     * @param  int $id 资源id
     * @return  返回结果
     */
    public function update(){
       //@todo your update code here
    }

    /**
     * @title 删除
     * @method delete 方法
     * @param int $id  资源id
     * @return  返回结果
     */
    public function delete($id = 0){
        //@todo your delete code here
    }
}