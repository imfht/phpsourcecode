<?php
namespace addons\shuiguo\delivery\controller;

use think\addons\Controller;

class Index extends Controller
{
	public function _initialize(){
        $this->view->engine->layout(false);
        $this->view->replace([
                // '__PUBLIC__'       =>  request()->root(true).'/tpl/addons/common/wheel',
            ]);
	}

    public function index()
    {
        // $user_id = action('Api/BaseController/get_user_id',[]);
        // $user_id = (new \app\api\controller\basecontroller)->get_user_id();
        $user_id = 1;
        $user = model('app\common\model\User')->with('contact,avater')->find($user_id);
        $this->assign("user", $user);


        $config = model('addons\shuiguo\delivery\model\AddonDeliveryConfig')->find();
        $this->assign("config", $config);

        return $this->fetch();
    }





}
