<?php

namespace Addons\Ucuser\Controller;
use Home\Controller\AddonsController;
use Common\Model\UcuserModel;
use Ucuser\Model\UcuserScoreModel;
use Com\TPWechat;
use Com\JsSdkPay;
use Com\ErrCode;

class UcuserController extends AddonsController{

              public function index(){
                $params['mp_id'] = $map['mp_id'] = get_mpid();
                $this->assign ( 'mp_id', $params['mp_id'] );

		      $mid = get_ucuser_mid();   //获取粉丝用户mid，一个神奇的函数，没初始化过就初始化一个粉丝
		      if($mid === false){
                  $this->error('只可在微信中访问');
              }
              $user = get_mid_ucuser($mid);                    //获取本地存储公众号粉丝用户信息

              $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
              $surl = get_shareurl();
              if(!empty($surl)){
                  $this->assign ( 'share_url', $surl );
              }

              $appinfo = get_mpid_appinfo ( $params ['mp_id'] );   //获取公众号信息
              $this->assign ( 'appinfo', $appinfo );

              $options['appid'] = $appinfo['appid'];    //初始化options信息
              $options['appsecret'] = $appinfo['secret'];
              $options['encodingaeskey'] = $appinfo['encodingaeskey'];
              $weObj = new TPWechat($options);

        $auth = $weObj->checkAuth();
        $js_ticket = $weObj->getJsTicket();
        if (!$js_ticket) {
            $this->error('获取js_ticket失败！错误码：'.$weObj->errCode.' 错误原因：'.ErrCode::getErrText($weObj->errCode));
        }
        $js_sign = $weObj->getJsSign($url);

        $this->assign ( 'js_sign', $js_sign );

        $fans = $weObj->getUserInfo($user['openid']);
        if($user['status'] != 2 && !empty($fans['openid'])){      //没有同步过用户资料，同步到本地数据
            $user = array_merge($user ,$fans);
            $user['status'] = 2;
            $model = D('Ucuser');
            $model->save($user);
        }

        if($user['login'] == 1){              //登录状态就显示微信的用户资料，未登录状态显示本地存储的用户资料
            if(!empty($fans['openid'])){
                $user = array_merge($user ,$fans);
            }
          }
        $this->assign ( 'user', $user );

        $member = get_member_by_openid($user["openid"]);          //获取会员信息
        $score = D('Ucenter/Score')->getUserScore($member['id'],1);//查积分
        $this->assign ( 'member', $member );
        $this->assign ( 'score', $score );
		//$templateFile = $this->model ['template_list'] ? $this->model ['template_list'] : '';
		$this->display ( );
	}
}