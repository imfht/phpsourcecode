<?php
namespace addons\shuiguo\stores\controller;

use think\addons\Controller;

class Index extends Controller
{
	public function _initialize(){
        $this->view->engine->layout(false);
        $this->view->replace([
                '__CSS__'       =>  '/addons/shuiguo/stores/view/public/css',
                '__IMG__'       =>  '/addons/shuiguo/stores/view/public/image',
            ]);
	}

    public function index()
    {
        // $user_id = action('api/BaseController/get_user_id',[]);
        // $user_id = (new \app\api\controller\basecontroller)->get_user_id();

        // $user = model('app\common\model\User')->with('contact,avater')->find($user_id)->toArray();
        // $this->assign("user", $user);
        // $config = model('addons\card\model\AddonCardConfig')->find()->toArray();
        // $this->assign("config", $config);

        return '1111';
    }





}
