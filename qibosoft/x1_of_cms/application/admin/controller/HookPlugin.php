<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\traits\AddEditList;
use app\common\model\Hook as HookModel;
use app\common\model\Hook_plugin as Hook_pluginModel;
use app\common\model\Plugin;
use app\common\traits\Market;
use think\Db;

class HookPlugin extends AdminBase
{
    use AddEditList,Market;	
    protected $validate = '';
    protected $model;
    protected $form_items;
    protected $list_items;
    protected $tab_ext;
    protected static $type = 'hook';
    
    public function index($hook_key='') {
        $map = [];
        if ($hook_key) {
            $map = ['hook_key'=>$hook_key];
        }
        $listdb = $this->getListData($map, $order = []);
        return $this -> getAdminTable($listdb);
    }
    
    public function delete($ids = null) {
        $ids = is_array($ids)?$ids:[$ids];
        foreach($ids AS $id){
            $info = $this->model->get($id);
            if(preg_match('/^app\\\common\\\hook/', $info['hook_class'])){
                if( method_exists($info['hook_class'],'uninstall') ){
                    $class = new $info['hook_class'];
                    $class->uninstall($id);
                }
                unlink(APP_PATH.'common/hook/'.substr(strrchr($info['hook_class'],'\\'),1).'.php');
            }
        }
        cache('hook_plugins', NULL);
        if ($this -> deleteContent($ids)) {
            $this -> success('删除成功');
        } else {
            $this -> error('删除失败');
        }
    } 
    
    protected function get_app_hook($id=0,$type='hook'){
        $keywords = input('keywords');
        $appkey= input('appkey');
        $domain= input('domain');
        
        $basepath = APP_PATH.'common/hook/';
        
        if(!is_writable($basepath) && !mkdir($basepath,0777,true)){
            return $this->err_js($basepath.'目录不可写,请先修改目录属性可写,如果此目录不存在,就手工创建');
        }elseif ( is_file($basepath.ucfirst($keywords).'.php' ) ){
            return $this->err_js($basepath.$keywords.'文件已经存在了,无法安装此钩子,请先卸载再安装');
        }
        $url = "https://x1.php168.com/appstore/getapp/down.html?id=$id&domain=$domain&appkey=$appkey";
        $result = $this->downModel($url,$keywords,$type);
        if($result!==true){
            return $this->err_js($result);
        }
        
        $result = $this->install($keywords,$id);
        if($result!==true){
            unlink($basepath.ucfirst($keywords).'.php');
            return $this->err_js($result);
        }
        if(self::$type == 'packet'){
            return $this->ok_js(['url'=>url('market')],'增强包安装成功');
        }elseif(self::$type == 'task'){
            \app\admin\controller\Timedtask::make_cfg();
            return $this->ok_js(['url'=>url('timedtask/index')],'定时任务安装成功,请重新配置参数');
        }
        cache('hook_plugins', NULL);
        return $this->ok_js(['url'=>url('hook_plugin/index')],'钩子安装成功,请在钩子设置那里选择启用');
        
    }
    
    /**
     * 安装增强包,不一定是钩子
     * @param unknown $keywords
     * @param unknown $id
     */
    protected function add_packet($keywords,$id){
        $appkey= input('appkey');
        $domain= input('domain');
        $url = "https://x1.php168.com/appstore/getapp/info.html?id=$id&domain=$domain&appkey=$appkey";
        if(($str=file_get_contents($url))==false){
            $str = http_curl($url);
        }
        $info = json_decode($str,true);

        $data = [
                'type'=>$info['type']?:'packet',
                'keywords'=>$keywords,
                'version_id'=>$id,
                'name'=>$info['title']?:'',
                'author'=>$info['author']?:'',
                'author_url'=>$info['author_url']?:'',
        ];
        \app\common\model\Market::create($data);
    }
    
    /**
     * 定时任务入库
     * @param unknown $keywords
     * @param number $id
     * @return string|boolean
     */
    protected function into_task($keywords,$id=0){
        $classname = "app\\common\\task\\".ucfirst($keywords);
        if(!class_exists($classname)){
            return '定时任务程序代码不符合规则!'.$classname;
        }
        $class = new $classname;
        $info = $class->info;
        if(empty($info)){
            return '定时任务程序代码不完整,缺少配置参数!';
        }
        $info['version_id'] = $id;
        $info['class_file'] = $classname;
        $info['create_time'] = time();
        $id = Db::name('timed_task')->insertGetId($info);
        
        if( method_exists($classname,'install') ){
            $class->install($id);
        }
        if ($id) {
            return true;
        }else{
            return '定时任务入库失败!';
        }
    }

    /**
     * 入库处理
     * @param unknown $keywords
     * @param unknown $id 服务端对应的ID
     * @return string|boolean
     */
    protected function install($keywords,$id=0){
        $classname = "app\\common\hook\\".ucfirst($keywords);
        
        if(!class_exists($classname)){
            //return '钩子程序代码不符合规则!'.$classname;
            $classname = "app\\common\\task\\".ucfirst($keywords);
            if(class_exists($classname)){
                self::$type = 'task';
                return $this->into_task($keywords,$id); //定时任务
            }else{
                $this->add_packet($keywords,$id);   //不是钩子,仅仅是增强包
                self::$type = 'packet';
                return true;
            }            
        }
        $class = new $classname;
        $info = $class->info;
        if(empty($info)){
            return '钩子程序代码不完整,缺少配置参数!';
        }
        if ($info['plugin_key']) {
            $detail = explode(',',$info['plugin_key']);
            foreach($detail AS $value){
                if ($value && empty(plugins_config($value))) {
                    return '请先安装插件:'.$value.'!';
                }
            }
        }
        if ($info['module_key']) {
            $detail = explode(',',$info['module_key']);
            foreach($detail AS $value){
                if ($value && empty(modules_config($value))) {
                    return '请先安装频道:'.$value.'!';
                }
            }
        }
        
        $string = http_curl("https://x1.php168.com/appstore/upgrade/get_version.html?id=".$id);
        if ($string!='') {
            $detail = json_decode($string,true);
            if ($detail['md5']) {
                $info['version'] = $detail['time']."\t".$detail['md5'];
            }
        }
        
        $detail = explode(',',$info['hook_key']);
        foreach($detail AS $value){
            $info['hook_key'] = $value;
            $info['hook_class'] = $classname;
            $info['version_id'] = $id;
            $result = $this->model->create($info);
        }
        
        if( method_exists($classname,'install') ){
            $class->install($result->id);
        }
        if ($result) {
            return true;
        }else{
            return '钩子入库失败!';
        }
    }
    
    /**
     * 应用市场
     */
    public function market($id=0,$page=0){
        //执行安装云端模块
        if($id){
            return $this->get_app_hook($id,'hook');
        }
        $this->assign('fid',3);
        return $this->fetch();
    }
    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new Hook_pluginModel();
        $this->form_items = [
                ['text', 'hook_class', '钩子类名'],
                ['select', 'hook_key', '归属接口','',HookModel::getTitleListByKey()],
                ['select', 'plugin_key', '归属插件','',Plugin::getTitleListByKey()],
                ['text', 'about', '功能描述'],                
                ['radio', 'ifopen', '是否启用', '', ['禁用','启用'], 1],
        ];
        $this->tab_ext = [
                'page_title'=>'钩子管理(实现接口功能)',
                'top_button'=>[
                        [
                                'title'=>'手工添加钩子',
                                'url'=>url('add'),
                                'icon'  => 'fa fa-plus-circle',
                                'class' => '',
                        ],
                        [
                                'title'=>'钩子云市场',
                                'url'=>url('market'),
                                'icon'  => 'fa fa-cloud-download',
                                'class' => '',
                        ],
                        [
                                'title'=>'返回接口列表',
                                'url'=>url('hook/index'),
                                'icon'  => 'fa fa-microchip',
                                'class' => '',
                        ],
                ],
        ];
        $this->list_items = [
                ['hook_class', '钩子类名', 'text'],
                ['about', '钩子功能描述', 'text'],          
                ['hook_key', '归属接口', 'callback',function($key,$rs){
                    return $key.' ('. HookModel::where('name',$rs['hook_key'])->value('about') .')';
                },'__data__'],
                ['plugin_key', '归属插件', 'callback',function($key,$rs){
                    return $key?plugins_config($key)['name']:'';
                },'__data__'],
                ['author', '开发者', 'link','__author_url__','_blank'],
                ['ifopen', '是否启用', 'switch'],
        ];
    }
}
