<?php
namespace app\ebcms\controller;
use think\Controller;
class Api extends Controller
{

    public function _initialize()
    {

        \think\Session::boot();
        \think\Session::prefix(\think\Config::get('session.prefix').'admin');

        if (!\think\Session::get('manager_id')) {
            $this->error('请登录');
        }
    }

    public function index()
    {
        if(request()->isPost()) {
            if ($api = input('api')) {
                return $this->$api();
            }
        }
    }

    private function suggest_keywords()
    {
        return \ebcms\Server::api('keywords_suggest', ['k' => input('k')]);
    }

    private function check_upgrade(){
        $myapps = \think\Db::name('app') -> column('version','app_id');
        return \ebcms\Server::store('checkUpgrade', ['apps'=>json_encode($myapps)]);
    }

}