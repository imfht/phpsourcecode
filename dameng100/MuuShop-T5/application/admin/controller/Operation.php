<?php
namespace app\admin\controller;

use app\admin\controller\Admin;
use app\admin\builder\AdminConfigBuilder;

/**
 * Class OperationController  运维控制器
 * @package Admin\Controller
 */
class OperationController extends AdminController
{

    public function index()
    {
        $this->redirect('Message/userList');
    }
}
