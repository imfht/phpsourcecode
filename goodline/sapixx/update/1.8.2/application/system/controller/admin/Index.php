<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 后台首页
 */
namespace app\system\controller\admin;
use filter\Filter;
use app\common\model\SystemMiniapp;
use app\common\model\SystemAdmin;
use app\system\event\AppConfig;
use app\common\event\Admin;

class Index extends Common{

    protected $login;

    public function initialize() {
        parent::initialize();
        $this->login = Admin::getLoginSession();
    }

    /**
     * 后台主框架
     */
    public function index(){
        $miniapp = Admin::getMiniapp();
        $ary = [];
        if($miniapp ){
            $menu = SystemMiniapp::where(['is_manage' => 1,'id' => $miniapp['miniapp_id']])->find();
            if(!empty($menu)){
                $ary['name']   = $menu['title'];
                $ary['wechat'] = $menu['miniapp_dir'];
            }
        }
        $view['miniapp'] = $ary;
        return view('admin/index/index')->assign($view);
    }

    /**
     * 管理菜单
     * @return json
     */
    public function appmenu($app = 'system'){
        $app  = Filter::filter_escape($app);
        $menu = null;
        if($app == 'systemcms'){
            $app  = 'system';
            $menu = 'cms';
        }
        return json(AppConfig::menu($app,false,$menu));
    }
    /**
     * 后台登录
     */
    public function login(){
        if(request()->isPost()){
            $data = [
                '__token__'      => $this->request->param('__token__/s'),
                'captcha'        => $this->request->param('captcha/s'),
                'login_id'       => $this->request->param('login_id/s'),
                'login_password' => $this->request->param('login_password/s')
            ];
            $validate = $this->validate($data,'Admin.login');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $result  = SystemAdmin::login($data);
            if($result){
                Admin::setLoginSession($result);
                return enjson(200,'登录成功',['url' => url('system/admin.index/index')]);
            }else{
                return enjson(0,'管理帐号登录失败');
            }
        }else{
            return view('admin/index/login'); 
        }
    }

    /**
     * 退出
     */
    public function logout(){
        Admin::setlogoutSession(); 
        Admin::clearMiniapp();
        return redirect(url('system/admin.index/login'));
    } 
}