<?php

// +----------------------------------------------------------------------
// | HopePHP
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.wispx.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: WispX <i@wispx.cn>
// +----------------------------------------------------------------------

// [ 控制器基类 ]

namespace hope;

use think\Template;
use traits\Jump;

class Controller
{
    use Jump;

    /**
     * 视图类实例
     * @var null
     */
    public $view = null;

    /**
     * 构造函数
     * Controller constructor.
     */
    public function __construct()
    {
        if(is_null($this->view)) {
            $this->view = new Template(Config::get('template'));
        }
        return $this->view;
    }
}