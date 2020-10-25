<?php
namespace app\admin\controller;

use think\Controller;
use think\facade\Lang;
use app\common\model\AuthRule;
use expand\Auth;
use app\common\model\Config;
use app\admin\controller\Login;
use app\common\model\TokenUser;
use app\common\model\User;


class Common extends Controller {

    public function initialize() {
        $this->restLogin();
        $userId = session('userId');
        define('UID', $userId);   //设置登陆用户ID常量
		define('H_NAME', request()->domain());	//获取当前域名,包含"http://"
		define('M_NAME', request()->module());	//当前模块名称
        define('C_NAME', request()->controller());	//当前控制器名称
        define('A_NAME', request()->action());	//当前操作名称
		if( !empty($userId) ){
			$user = User::get( ['id' => $userId] );
			$user->userInfo;
			$this->assign('user',$user);
		}
        $box_is_pjax = $this->request->isPjax();
        $this->assign('box_is_pjax', $box_is_pjax);
        $treeMenu = $this->treeMenu();
        $this->assign('treeMenu', $treeMenu);
        //跳过权限
        $jump_auth = [
            'Index/icons',
            'Index/forms',
            'Index/box',
            'Index/tab',
            'Index/tables',
            'Index/question',
        ];
        if (in_array(C_NAME.'/'.A_NAME, $jump_auth)){
            return true;
        }
        $isbrowse = confv('isbrowse','system');
		$user_admin_arr = config('user_admin');
		if( !in_array(UID,$user_admin_arr) ){
			if ( $isbrowse == 1){   //是否开启浏览模式
	            if (input('post.')){
	                return ajaxReturn('当前处于浏览模式，不允许修改任何数据');
	            }
	        }
		}
        $auth = new Auth();
        if (!$auth->check(C_NAME.'/'.A_NAME, UID)){
            return ajaxReturn('没有权限');
        }
    }

    public function treeMenu() {
        $treeMenu = cache('DB_TREE_MENU_'.UID);
        if(!$treeMenu){
            $where = [
                'ismenu' => 1,
                'module' => 'admin',
            ];
            if (UID != '-1'){
                $where['status'] = 1;
            }
            $arModel = new AuthRule();
            $lists =  $arModel->where($where)->order('sorts ASC,id ASC')->select();
            $treeClass = new \expand\Tree();
            $treeMenu = $treeClass->create($lists);
            //判断导航tree用户使用权限
            foreach($treeMenu as $k=>$val){
                if( authcheck($val['name'], UID) == 'noauth' ){
                    unset($treeMenu[$k]);
                }
            }
            cache('DB_TREE_MENU_'.UID, $treeMenu);
        }
        return $treeMenu;
    }

    private function restLogin() {
        $login = new Login();
        $userId = session('userId');
        if (empty($userId)){   //未登录
            $login->loginOut();
        }
        $config = new Config();
        $login_time = $config->where(['type'=>'system', 'k'=>'login_time'])->value('v');
        $now_token = session('user_token');   //当前token
        $tkModel = new TokenUser();
        $db_token = $tkModel->where(['uid'=>$userId, 'type'=>'1'])->find();   //数据库token
        if ($db_token['token'] != $now_token){   //其他地方登录
            $this->loginBox('账户已在其他地方登陆，请重新登录');
        }else{
            if ($db_token['token_time'] < time()){   //登录超时
                $this->loginBox('登陆超时');
            }else{
            	$this->assign('rest_login', $rest_login = 2 );
                $token_time = time() + $login_time;
                $data = ['token_time' => $token_time];
                $tkModel->where(['uid'=>$userId, 'type'=>'1'])->update($data);
            }
        }
        return;
    }

    private function loginBox($info='')
    {
        if (request()->isGet()){
            $rest_login = 1;
            $this->assign('rest_login_info', $info);
            $this->assign('rest_login', $rest_login);
        }else{
            ajaxReturn($info, '', 2);
        }
    }
}