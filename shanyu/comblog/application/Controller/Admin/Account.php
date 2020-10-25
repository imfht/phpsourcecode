<?php
namespace App\Controller\Admin;

use App\Controller\Controller;
use App\Model\Admin as AccountModel;
use App\Model\AdminLoginLog as LoginLogModel;
use App\Service\Account as AccountService;

class Account extends Controller
{
    public function index()
    {
        return $this->fetch('account_login');
    }
    public function login()
    {
        //非法请求
        if($_SERVER['REQUEST_METHOD'] != 'POST') show_404();

        //参数获取
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        //参数判断
        if(!$username || !$password) return $this->error('账号和密码都不能为空');

        //参数过滤
        $username = htmlspecialchars($username);
        $password = htmlspecialchars($password);

        // 验证账号
        if($username != getenv('ADMIN_USER') || $password != getenv('ADMIN_PASS')){
            return $this->error('账号或密码错误');
        }
        $account_model = new AccountModel();
        $account = $account_model->findOrCreate($username,$password);

        //添加登录标记,添加持久登录
        $account_event = new AccountService();
        $account_event->removeLogin($account['session_id'],$account['update_time']);
        $account_event->setLoginId($account['id']);
        $account_event->setCookieToken();

        //修改登录信息
        $login_data = $account_event->getLoginData();
        $login_status = $account_model->loginHandle($account['id'],$login_data);

        //添加登录记录
        $log_model = new LoginLogModel();
        $log_status = $log_model->insert($account['id']);

        if($login_status && $log_status){
            return $this->success('成功登录','/admin?c=Home&a=index');
        }

        return $this->success('成功失败');
    }

    protected function getDbAccount($username,$password)
    {
        //判断账号存在
        $account_model = new AccountModel();
        $account = $account_model->getDataByUsername($username);
        if(!$account) return false;

        //判断密码
        $password_encode = $account_model->encryptPassword($password);
        if($password_encode != $account['password']) return false;

        return $account;
    }


}