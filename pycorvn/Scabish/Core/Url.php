<?php
namespace Scabish\Core;

use SCS;

/**
 * Scabish\Core\Url
 * URL管理类
 * 
 * @author keluo <keluo@focrs.com>
 * @copyright 2016 Focrs, Co.,Ltd
 * @package Scabish
 * @since 2015-01-24
 */
class Url {
    
    // 项目asset目录
    public $asset;
    
    // 项目基本路径
    public $base;
    
    public function __construct() {
        $this->asset = SCS::Request()->base.'/asset';
        $this->base = SCS::Request()->baseUri;
    }
    
    /**
     * 根据路由生成完整URL
     * @param string $route
     * @param mixed $params url中请求参数
     * @param boolean $absolute 是否生成绝对路径
     * @return string
     * @example
     * <pre>
     * $url = SCS::Url()->Create('control/action', ['a' => '1', 'b' => '你好']);
     * $url = SCS::Url()->Create('control/action', 'a/1/b/你好'); // control/action/a/1/b/你好
     * $url = SCS::Url()->Create('control', 'a/1/b/你好'); // control/index/a/1/b/你好
     * $url = SCS::Url()->Create(null, 'a/1/b/你好'); // index/index/a/1/b/你好
     */
    public function Create($route = '', $params = null, $absolute = false, $space = '-') {
        $baseUri = ($absolute ? 'http://'.$_SERVER['HTTP_HOST'] : '').$this->base;
        if(!$route) return $baseUri.'/';
        $route = explode('/', trim($route));
        $control = (isset($route[0]) && strlen($route[0])) ? $route[0] : ($params ? 'index' : '');
        $action = (isset($route[1]) && strlen($route[1])) ? $route[1] : ($params ? 'index' : '');
        $route = $control.'/'.$action;
        if(is_array($params)) {
            $queryString = '';
            foreach($params as $key=>$value) {
                $queryString .= $key.'/'.urlencode($value).'/';
            }    
        } else {
            $queryString = $params;
        }
        if($space) {
            $queryString = preg_replace('/\s+/', $space, $queryString);
        }
        return rtrim($baseUri.(SCS::Instance()->rewrite ? '/' : '/?').$route.'/'.$queryString, '/');
    }
    
    /**
     * 重新构建url
     * @param array $extends 附加参数
     * @return string
     */
    public function Rebuild($extends = []) {
        if($extends && is_array($extends)) {
            $params = array_merge(SCS::Request()->Param(), $extends);
        } else {
            $params = SCS::Request()->Param();
        }
        $url = $this->Create(SCS::Request()->control.'/'.SCS::Request()->action);
        foreach($params as $param=>$value) {
            if(0 === strcasecmp('page', $param)) continue;
            $url .= '/'.$param.'/'.urlencode($value);
        }
        return $url;
    }
    
    /**
     * 基本路径上翻路径
     * @param number $level
     * @param boolean $absolute 是否使用绝对路径
     * @return string
     */
    public function Upper($level = 1, $absolute = true) {
        $parent = ($absolute ? 'http://'.$_SERVER['HTTP_HOST'] : '').$this->base;
        for($i = 0; $i < $level; $i++) {
            $parent = rtrim(dirname($parent), '/');
        }
        return $parent;
    }
}