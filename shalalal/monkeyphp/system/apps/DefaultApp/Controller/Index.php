<?php
namespace DefaultApp\Controller;

use Monkey\Controller;

/**
 * 控制器示例 Index
 */
class Index extends Controller {

    /**
     * index action示例，方面名前面必须加“action_”前缀，以标明这是浏览器路由访问的方法
     */
    public function action_index() {
        $param = $this->getRouteParameter('language', ''); //演示从路由中获取参数
        if (empty($param)) {
            echo '--你好hello!--<br/>';
        }
        if ($param == 'zh') {
            echo '--你好!--<br/>';
        }
        if ($param == 'en') {
            echo '--hello!--<br/>';
        }
        echo date('Y-m-d H:i:s');

    }

    /**
     * hello测试
     * 方面名前面必须加“action_”前缀，以标明这是浏览器路由访问的方法
     */
    public function action_hello() {
        echo '测试hello!<br/>';
    }

}