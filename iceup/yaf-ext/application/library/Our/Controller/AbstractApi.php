<?php

namespace Our;

/**
 * api模块控制器抽象类
 *
 * @package Our
 * @author iceup <sjlinyu@qq.com>
 */
abstract class Controller_AbstractApi extends \Our\Controller_Abstract {

    /**
     * api控制器直接输出json格式数据，不需要渲染视图
     */
    public function init() {
        \Yaf\Dispatcher::getInstance()->disableView();
    }

}
