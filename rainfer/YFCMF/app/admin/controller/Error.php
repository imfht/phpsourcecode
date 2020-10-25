<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Controller;

/**
 * 空控制器
 * @author rainfer <rainfer520@qq.com>
 */
class Error extends Controller
{
    /**
     * 转后台首页
     * @throws
     */
    public function index()
    {
        $this->redirect('admin/Index/index');
    }
}
