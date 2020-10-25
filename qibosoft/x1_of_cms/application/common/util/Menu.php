<?php

namespace app\common\util;
use app\admin\model\AdminMenu;

class Menu{
    //protected static $instance;
    protected static $type = 'admin';
    protected static $groupid = null;
    protected static $sysmenu = [] ;
    protected static $tag = ''; //指定显示哪个模块的菜单,也即频道专属菜单
    
    protected function __construct($type)
    {
        self::$type = $type;
    }
    
    /**
     * 获取会员中心或后台菜单
     * @param unknown $type member admin
     * @return string|array
     */
    public static function make($type,$tag=''){
        //if (is_null(self::$instance)) {
         //   self::$instance = new static($type);
       // }
        if ($type=='member') {
            self::$sysmenu = Menu::member_sys_cache();
        }
        self::$type = $type;
        self::$tag = $tag;
        return self::get_menu();
    }    
    
    /**
     * 打上标志是哪个模块的系统，方便处理URL指向
     * @param unknown $ar 菜单数组
     * @param unknown $model 哪个模块
     * @param string $isplugin 判断是不是插件
     */
    public static function menu_make_url(&$ar,$model,$isplugin=false)
    {
        foreach($ar['sons'] AS $key1=>$v){
            foreach($v['sons'] AS $key2=>$v2){
                $ar['sons'][$key1]['sons'][$key2]['model'] = $model;
                if (is_array($v2['link'])) {
                    $v2['param'] = $v2['link'][1];
                    $v2['link'] = $v2['link'][0];                    
                }
                if (preg_match("/^[\w]+/", $v2['link'])) {
                    $url = $isplugin ? purl($model.'/'.$v2['link'],$v2['param']) : url($model.'/'.$v2['link'],$v2['param']);
                }else{
                    $url = $v2['link']; //支持站外URL
                }
                
                if (self::$sysmenu[$url]) { //会员中心系统默认菜单被定义过
                    $array = self::$sysmenu[$url];
                    if ($array['ifshow']==0 || ($array['allowgroup']&&!in_array(login_user('groupid'), explode(',', $array['allowgroup'])))) {
                        unset($ar['sons'][$key1]['sons'][$key2]);   //后台设置了权限或不显示
                    }else{
                        if ($array['name']!='') {
                            $array['title'] = $array['name'];   //名称被定义过  
                        }else{
                            unset($array['title']);  
                        }
                        $_a = $ar['sons'][$key1]['sons'][$key2];
                        $ar['sons'][$key1]['sons'][$key2] = array_merge($_a,$array);
                    }                    
                }else{
                    $ar['sons'][$key1]['sons'][$key2]['url'] = $url;
                }
            }
        }
    }
    
    /**
     * 系统菜单, 不是模型与插件的菜单 
     * @return unknown
     */
    public static function get_sys_menu(){
        if (self::$type=='admin') {
            $base_menu = include(APP_PATH.'admin/admin_menu.php');
            self::get_ext_menu($base_menu,APP_PATH.'admin/admin_menu.php');
        }else{
            $base_menu = include(APP_PATH.'member/member_menu.php');
            self::get_ext_menu($base_menu,APP_PATH.'member/member_menu.php');
        }
        return $base_menu;
    }
    
    /**
     * 二开的扩展菜单
     * @param array $array
     * @param string $file
     */
    protected static function get_ext_menu(&$array=[],$file=''){
        $path = dirname($file);
        $dir = opendir($path);
        $name = substr(basename($file),0,-4);
        while($f=readdir($dir)){
            if(preg_match("/^".$name."([\w\.-]+)\.php$/", $f)){
                $ar = include($path.'/'.$f);
                foreach($ar AS $key1=>$value1){
                    foreach($array[$key1]['sons'] AS $k1=>$v1){
                        if($v1['title']==$value1['sons'][0]['title']){
                            $array[$key1]['sons'][$k1]['sons'] = array_merge($v1['sons'],$value1['sons'][0]['sons']) ;
                            unset($value1['sons'][0]);
                        }
                    }
                    if ($value1['sons'][0]) {
                        $array[$key1]['sons'][] = $value1['sons'][0];
                    }
                }
            }
        }
    }
    
    /**
     * 取得前台或后台的菜单文件
     * @return string
     */
    public static function get_menu_file(){
        return self::$type=='admin' ? 'admin_menu.php' : 'member_menu.php' ;
    }
    
    /**
     * 文本菜单处理
     * @param array $base_menu
     * @return array|\app\common\util\unknown
     */
    protected static function build_sys_menu(){
        $base_menu=[];
        $base_menu = empty($base_menu) ? self::get_sys_menu() : array_merge($base_menu,self::get_sys_menu());
        foreach($base_menu AS $key=>$ar){
            foreach($ar['sons'] AS $i=>$v){
                $ar['sons'][$i] = self::get_member_set($v);
            }
            //打上标志是哪个模块的系统，方便处理URL指向
            self::menu_make_url( $ar , self::$type );
            $base_menu[$key]=$ar;
        }
        return $base_menu;
    }
    
    /**
     * 系统默认所有会员的菜单
     * @return mixed|\think\cache\Driver|boolean
     */
    public static function member_sys_cache($make=false){
        $listdb = cache('member_sys_menu');
        if (empty($listdb)||$make) {
            $map = ['type'=>1,'groupid'=>0];
            if (is_file(APP_PATH.'common/upgrade/96.sql')) {    //避免升级不成功导致数据库报错进不了后台
                $map['is_use'] = 1;
            }
            $data = AdminMenu::where($map)->column(true);
            $listdb = [];
            foreach($data AS $rs){
                if ($rs['pid']) {
                    $listdb[$rs['url']] = $rs;
                }else{
                    $listdb[$rs['title']] = $rs;
                }
            }
            cache('member_sys_menu',$listdb);
        }
        return $listdb;
    }
    
    /**
     * 指定用户组的后台或会员中心个性菜单处理
     * @param array $base_menu
     * @param number $type 0的话就是后台菜单,1是会员中心菜单,2的话是WAP会员中心菜单,
     * @return unknown|array|string[]|NULL[]
     */
    protected static function build_often_menu($base_menu=[],$type=0){
        $map = [
                'type'=>$type,
                'ifshow'=>1,
                'groupid'=>self::$groupid?:login_user('groupid'),
        ];
        $_array = [];
        $listdb = self::$tag ? [] : get_sons( AdminMenu::getTreeList($map) ); //频道专属菜单模式下就不显示会员个性菜单
        foreach ($listdb AS  $key=>$rs) {
            $_array[$key]['title'] = $rs['name'];
            $_array[$key]['icon'] = $rs['icon'];
            foreach ($rs['sons'] AS $k=>$vs){
                $vs['title'] = $vs['name'];
                $_array[$key]['sons'][]=$vs;/*[
                        'title'=>$vs['name'],
                        'url'=>$vs['url'],
                        'icon'=>$vs['icon'],
                        'target'=>$vs['target'],
                ];*/
            }             
        }
        if ($type!=0&&empty($_array)) {     //会员中心
            return $base_menu;
        }
        if(is_array($base_menu['often']['sons'])){
            $base_menu['often']['sons'] = array_merge($base_menu['often']['sons'],$_array);
        }else{
            $base_menu['often'] = [
                    'title'=>'常用菜单',
                    'sons'=>array_values($_array),
            ];
        }

        return $base_menu;
    }
    
    /**
     * 获取默认菜单一级分类的自定义属性
     * @param array $array
     * @return array|unknown
     */
    protected static function get_member_set($array=[]){
        if (self::$sysmenu[$array['title']]) {
            $ar = self::$sysmenu[$array['title']];
            if($ar['name']!=''){
                $ar['title'] = $ar['name'];
            }else{
                unset($ar['title']);
            }            
            return array_merge($array,$ar);
        }else{
            return $array;
        }
    }
    
    /**
     * 频道模块菜单处理
     * @param array $base_menu
     * @return string|array|unknown[]
     */
    protected static function build_module_menu($base_menu=[]){
        $module_array = modules_config();
        foreach ($module_array AS $model){
            if (self::$tag!='' && !in_array($model['keywords'], explode(',', self::$tag))) {
                continue ; //指定显示哪个模块的菜单
            }
            $file = APP_PATH.$model['keywords'].'/'.self::get_menu_file();
            if(is_file($file)){
                $array = include($file);
                self::get_ext_menu($array,$file);
                foreach($array AS $key=>$ar){
                    
                    //打上标志是哪个模块的系统，方便处理URL指向
                    self::menu_make_url($ar,$model['keywords']);
                    
                    if($model['ifsys']){    // 使用顶部菜单的频道
                        $ar['sons'][0]['title'] = $model['name'];
                        $ar['sons'][0]['icon'] = $model['icon'];
                         
                        $base_menu[$model['keywords']] = self::get_member_set( array(
                                'title'=>$model['name'],
                                'icon'=>$model['icon'],
                                'sons'=>$ar['sons']
                        ));
//                     }elseif($base_menu[$key]){  //不是系统模型的话，可以追加到其它顶部导航下面
//                         $base_menu[$key]['sons'] = array_merge($base_menu[$key]['sons'],$ar['sons']);
                    }else{  //不使用顶部菜单的频道，追加到通用频道下面
                        $ar['sons'][0]['title'] = $model['name'];
                        $ar['sons'][0]['icon'] = $model['icon'];
                        $ar['sons'][0]['dirname'] = $model['keywords'];
                        $ar['sons'][0] = self::get_member_set($ar['sons'][0]);
                        if( !empty($base_menu['module']['sons']) ){
                            $base_menu['module']['sons'] = array_merge($base_menu['module']['sons'],$ar['sons']);
                        }else{
                            $base_menu['module']['sons'] = $ar['sons'];
                            $base_menu['module']['title'] = '模块中心';
                        }
                        //$base_menu['module']['sons'] = array_merge($base_menu['module']['sons'],$ar['sons']);
                    }
                }
            }
        }
        return $base_menu;
    }
    
    /**
     * 插件菜单处理
     * @param array $base_menu
     * @return string|array
     */
    protected static function build_plugin_menu($base_menu=[]){
        $plugin_array = plugins_config();        
        foreach ($plugin_array AS $model){
            $file = ROOT_PATH. 'plugins/'.$model['keywords'].'/'.self::get_menu_file();
            if(is_file($file)){
                $array = include($file);
                foreach($array AS $key=>$ar){                    
                    //打上标志是哪个模块的系统，方便处理URL指向
                    self::menu_make_url($ar,$model['keywords'],true);
                    count($ar['sons'])==1 && $ar['sons'][0]['title'] = $model['name'];   //插件一般情况都只有一组菜单,就用后台定义的名称
                    $ar['sons'][0]['icon'] || $ar['sons'][0]['icon']= $model['icon']; //如果图标不存在,就补上
                    $ar['sons'][0]['dirname'] = $model['keywords'];
                    $ar['sons'][0] = self::get_member_set($ar['sons'][0]);
                    
                    //如果是复制的插件 key不是plugin的话,要先修改一下 admin_menu.php 里边的key,不然多个插件的key雷同,会导致菜单重叠的BUG
                    if($base_menu[$key]){  //根据不同的参数,可以追加到任何顶部导航下面,一般情况$base_menu[plugin] 是存在的,所以基本都是在这里执行
                        $base_menu[$key]['sons'] = array_merge($base_menu[$key]['sons'],$ar['sons']);
                    }else{  //没特别指定的话，追加到顶部插件菜单下面. 下面的执行情况比较少                        
                        if( !empty($base_menu['plugin']['sons']) ){
                            $base_menu['plugin']['sons'] = array_merge($base_menu['plugin']['sons'],$ar['sons']);
                        }else{
                            $base_menu['plugin']['sons'] = $ar['sons'];
                            $base_menu['plugin']['title'] = '插件中心';
                        }
                    }
                }
            }
        }
        return $base_menu;
    }
    
    /**
     * 供外部调用所有菜单
     * @param unknown $groupid 用户组的ID
     * @return string|array
     */
    public static function get_menu($groupid=NULL)
    {
        if ($groupid) {
            self::$groupid = $groupid;
        }
        //系统菜单
        $base_menu = self::build_sys_menu();
        
        //常用菜单
        if (self::$type=='admin') {
            $base_menu = self::build_often_menu($base_menu);    //后台
        }else{
            $base_menu = self::build_often_menu($base_menu,1);  //会员中心
        }

        //模块菜单处理
        $base_menu = self::build_module_menu($base_menu);
        
        //插件菜单处理
        $base_menu = self::build_plugin_menu($base_menu);
        
        foreach($base_menu AS $key1=>$menu1){
            foreach ($menu1['sons'] AS $key2=>$menu2){
                if(empty($menu2['sons'])){
                    //将那些没有子菜单的父菜单清除，不显示
                    unset($base_menu[$key1]['sons'][$key2]);
                }
            }
        }

        return $base_menu;
    }	
}