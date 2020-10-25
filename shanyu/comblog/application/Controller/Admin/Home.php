<?php
namespace App\Controller\Admin;

use Kernel\Loader;

use App\Controller\Controller;
use App\Service\Account as AccountService;
use App\Model\Admin as AccountModel;

class Home extends Controller
{
    public function index()
    {
        return $this->fetch('home_index');
    }

    public function logout()
    {   
        //删除登录标识
        $account_event = new AccountService();
        $account_event->delLoginId();
        $account_event->delCookieToken();

        //更新登录信息
        $account_model = new AccountModel();
        $account_model->logoutHandle(UID);

        return $this->success('退出成功','admin?c=Account&a=index');
    }


}