<?php

/**
 * 原本是以静态方法的形式放到C类中的，但为了让用户最小化修改，暂时放在此处
 * 必须在Discuz_Application->init()后调用
 * 目的：代码分工，并尽可能将代码按模块进行划分，防止一个文件夹几十上百个文件的情况
 */
function runMvc(){
    define('UX_MODULES_DIR', 'modules');
    define('UX_MODEL_DIR', 'model');
    define('UX_VIEW_DIR', 'view');
    
    $router_conf_file = DISCUZ_ROOT . './config/router.conf.php';
    $router_conf['rules'] = array();
    if(is_file($router_conf_file))
	$router_conf = include_once $router_conf_file;
    Mvc_Router::setRouteRules($router_conf['rules']);
    Mvc_Router::beginUrl();
    
    $mo = Mvc_Router::getModule();//module
    $ct_id = Mvc_Router::getContrller();
    $ac = Mvc_Router::getAction();

    $ct = ucfirst($ct_id) . 'Controller';
    C::import(strtolower($ct), UX_MODULES_DIR."/$mo");
    
    $instance = new $ct($mo, $ct_id, $ac);
    $instance->run();
}

/**
 * 创建url
 * 
 * 这里有个特殊的地方，需要在多入口环境如portal.php页面中使用该函数
 * 导致Router类中只能构造固定的script名如index.php
 * 
 * @global type $_G
 * @param type $url
 * @param string $scriptDir
 * @return type
 */
function createurl($url = '', $scriptDir = '/') {
    if (!$scriptDir)
        $scriptDir = '/';

    $url = Mvc_Router::creatUrl($url, $scriptDir);
    
    return $url;
}

/**
 * Discuz在初始化时，已经设置了input，且将get post都合并到get中，同时又引入到_G['gp_xxxx']中
 * 在初始化路由时，需要调用此方法
 * 
 * @global array $_G
 * @param string $k
 * @param mixed $v
 */
function setgpc($k, $v = ''){
    global $_G;
    $_G['gp_'.$k] = $v;
    $_GET[$k] = $v;
}

/**
 * XML编码
 * @param mixed $data 数据
 * @param string $root 根节点名
 * @param string $item 数字索引的子节点名
 * @param string $attr 根节点属性
 * @param string $id   数字索引子节点key转换的属性名
 * @param string $encoding 数据编码
 * @return string
 */
function xml_encode($data, $root='root', $item='item', $attr='', $id='id', $encoding='utf-8') {
    if(is_array($attr)){
        $_attr = array();
        foreach ($attr as $key => $value) {
            $_attr[] = "{$key}=\"{$value}\"";
        }
        $attr = implode(' ', $_attr);
    }
    $attr   = trim($attr);
    $attr   = empty($attr) ? '' : " {$attr}";
    $xml    = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
    $xml   .= "<{$root}{$attr}>";
    $xml   .= data_to_xml($data, $item, $id);
    $xml   .= "</{$root}>";
    return $xml;
}

/**
 * 数据XML编码
 * @param mixed  $data 数据
 * @param string $item 数字索引时的节点名称
 * @param string $id   数字索引key转换为的属性名
 * @return string
 */
function data_to_xml($data, $item='item', $id='id') {
    $xml = $attr = '';
    foreach ($data as $key => $val) {
        if(is_numeric($key)){
            $id && $attr = " {$id}=\"{$key}\"";
            $key  = $item;
        }
        $xml    .=  "<{$key}{$attr}>";
        $xml    .=  (is_array($val) || is_object($val)) ? data_to_xml($val, $item, $id) : $val;
        $xml    .=  "</{$key}>";
    }
    return $xml;
}