<?php
namespace app\api\controller;

use app\common\model\User;
use app\common\model\TokenUser;
use app\common\model\UserInfo;
use expand\Str;
use expand\ApiReturn;

class Wxlogin extends Base {
    public function initialize(){
        parent::initialize();

    }

    public function index($hash) {
    	$apiInfo = cache('apiInfo_'.$hash);	//接口信息
		$data = cache('input_'.$hash);	//请求字段
		$header = request()->header();
		$appid = confv('xcx_appid','system');
		$secret = confv('xcx_secret','system');
		$login_time = confv('login_time','system');

		$url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $appid . "&secret=" . $secret ."&js_code=". $data['code'] ."&grant_type=authorization_code";
        $res = file_get_contents($url);
        $resArr = json_decode($res, true);
		$user = new User;
		$UserInfo = new UserInfo;
		$user_data = $user->get(['openId'=>$resArr['openid']]);
		if( !empty($user_data) ){
			$login = $user->login($user_data['username'],$user_data['password']);
			if($login){
				$user_data['focus_num'] = $user_data['focus_num'];
				$user_data['focusb_num'] = $user_data['focusb_num'];
				$user_arr = $user_data->visible($this->fname)->toArray();	// 根据返回字段来返回
				$user_arr['last_time'] = $user_data['last_time_turn'];
				$userinfodata = $UserInfo->get(['uid'=>$user_data['id']]);
		 		unset($userinfodata['id']);
		 		unset($userinfodata['create_time']);
				$userinfodata['avatar'] = $userinfodata['avatar_turn'];
				$userinfodata['wx_imgurl'] = $userinfodata['wx_imgurl_turn'];
				$userinfo_arr = $userinfodata->visible($this->fname)->toArray();	// 根据返回字段来返回
				$reutrn_data = array_merge($user_arr,$userinfo_arr);
				$reutrn_data['userToken'] = TokenUser::where(['uid'=>$user_data['id']])->value('token');
				$reutrn_data['expire'] = $login_time;
				return ApiReturn::r(1,$reutrn_data,'登录成功');
			}else{
				return ApiReturn::r(0,null,$user->error);
			}
		}else{
			$data['openId'] = $resArr['openid'];
			$data['zhuce_type'] = 'wxxcx';
			$data['username'] = ''.Str::randString(8,1);
			$data['password'] = Str::randString(6,1);
			$add = $user->allowField(true)->save($data);	//添加数据
			$uid = $user->id;
			if($add){
				$user->userInfo()->save($data,['uid'=>$uid]);	//添加关联数据
				$userinfo = $user->where(['id'=>$uid])->find();
				$login = $user->login($userinfo['username'],$userinfo['password']);
				if($login){
					$userinfo['focus_num'] = $userinfo['focus_num'];
					$userinfo['focusb_num'] = $userinfo['focusb_num'];
					$user_arr = $userinfo->visible($this->fname)->toArray();	// 根据返回字段来返回
					$user_arr['last_time'] = $userinfo['last_time_turn'];
					$userinfodata = $UserInfo->get(['uid'=>$uid]);
			 		unset($userinfodata['id']);
			 		unset($userinfodata['create_time']);
					$userinfodata['avatar'] = $userinfodata['avatar_turn'];
					$userinfodata['wx_imgurl'] = $userinfodata['wx_imgurl_turn'];
					$userinfo_arr = $userinfodata->visible($this->fname)->toArray();	// 根据返回字段来返回
					$return = array_merge($user_arr,$userinfo_arr);
					$return['userToken'] = TokenUser::where(['uid'=>$uid])->value('token');
					$return['expire'] = $login_time;
					return ApiReturn::r(1,$return,'登录成功');
				}else{
					return ApiReturn::r(0,null,$user->error);
				}
			}else{
				return ApiReturn::r(0,null,'注册失败');
			}
		}
    }

}
