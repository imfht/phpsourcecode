<?php
// +----------------------------------------------------------------------
// | Loadphp Framework designed by www.loadphp.com
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.loadphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 亓斌 <qibin0506@gmail.com>
// +----------------------------------------------------------------------

/**
 +------------------------------------------------------------------------------
 * URL路由处理类
 +------------------------------------------------------------------------------
 */
class UrlRouter {
    static function parseUrl() {
       if(isset($_SERVER['PATH_INFO'])) {
            //如果开启伪静态 
            if(IS_STATIC) {
                $_SERVER['PATH_INFO'] = preg_replace("/\.".STATIC_FOLLOWING."$/i",'',$_SERVER['PATH_INFO']);  //去除伪静态扩展名
                $_SERVER['PATH_INFO'] = str_replace(STATIC_SEPARATOR,'/',$_SERVER['PATH_INFO']);
            }    
            
            $url = explode('/',trim($_SERVER['PATH_INFO'],'/'));
           
            $_GET['c'] = !empty($url[0]) ? ucfirst($url[0]) : "Index";
            array_shift($url);
            
            $_GET['a'] = !empty($url[0]) ? $url[0] : "action";
            array_shift($url);
            
            for($i=0;$i<count($url);$i+=2) {
                $_GET[$url[$i]] = $url[$i+1];
            } 
       }else {
           $_GET['c'] = "Index";
           $_GET['a'] = "action";
       }
    }
}
?>