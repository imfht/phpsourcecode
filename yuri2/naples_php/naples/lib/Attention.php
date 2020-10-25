<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/12/1
 * Time: 10:11
 */

namespace naples\lib;


use naples\lib\base\Service;

/**
 * 用于显示页面大篇幅的提示文字 如错误和成功提示
 * 替换规则 [$n]->args[n-1] , [$n x]->x
 * 可以去配置文件attention.php添加替换模板
 */
class Attention extends Service
{

    public function __call($method, $args) {
        if ($method=='init'){return;}
        if ($this->config($method)){
            $content=$this->config($method);
            foreach ($args as $key=> $item){
                $content=preg_replace("/\\[\\$".($key+1)."(=.*?)?\\]/",$item,$content);
            }
            $content=preg_replace("/\\[\\$\\d=(.*?)\\]/",'$1',$content);
            return $content;
        }
        return "<br/>unknown method " . $method;
  }
}