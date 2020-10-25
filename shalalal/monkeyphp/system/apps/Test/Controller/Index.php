<?php
namespace Test\Controller;

use Monkey\Controller;

/**
 * 控制器示例 Index
 */
class Index extends Controller {

    /**
     * index action示例，方面名前面必须加“action_”前缀，以标明这是浏览器路由访问的方法
     */
    public function action_index() {
        //演示使用响应对象向浏览器发送内容
        $this->writeLine('测试 response::writeLine');
        $this->writeLine('');
        $param = $this->getRouteParameter();
        if (empty($param)) {
            $this->writeLine('--你好hello!--');
        }
        if ($param['language'] == 'zh') {
            $this->writeLine('--你好!--');
        }
        if ($param['language'] == 'en') {
            $this->writeLine('--hello!--');
        }
        $this->writeLine(date('Y-m-d H:i:s'));
    }

    /**
     * hello测试
     * 方面名前面必须加“action_”前缀，以标明这是浏览器路由访问的方法
     */
    public function action_hello() {
        $this->writeLine('测试hello!');
    }

    public function writeLine($string) {
        $this->response->addBody($string . '<br/>');
    }

}