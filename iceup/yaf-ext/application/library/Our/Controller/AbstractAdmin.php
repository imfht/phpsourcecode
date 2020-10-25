<?php

namespace Our;

/**
 * admin模块控制器抽象类
 * 
 * @package Our
 * @author iceup <sjlinyu@qq.com>
 */
abstract class Controller_AbstractAdmin extends \Our\Controller_Abstract {

    /**
     * admin控制器初始化方法，通常可以在这里校验是否登录或者授权
     */
    public function init() {
        
    }

}
