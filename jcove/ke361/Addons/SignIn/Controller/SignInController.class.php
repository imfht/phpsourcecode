<?php
namespace Addons\SignIn\Controller;
use Home\Controller\AddonsController;
use Common;


class SignInController extends AddonsController{

    public function render($data)
    {
    }
    public function sign_in()
    {

        $result = array(
            'errno'     => 0,
            'message'   => '',
            'content'   => '',
            'score'     => 0    
        );
        $uid =is_login();
        $config = Common\Controller\Addon::getConfig('SignIn');
        $score=$config['points'] ;
      
        $map['create_time'] = array('gt', strtotime(date('Ymd')));
        $map['uid'] = $uid;
        $isSign = D('SignIn')->where($map)->find();
        if (!$isSign) {
            $map['create_time'] = array('lt', strtotime(date('Ymd')));
            $last = D('SignIn')->where($map)->order('create_time desc')->find();
            $data['uid'] = $uid;
            $data['create_time'] = $_SERVER['REQUEST_TIME'];
           //是否有签到记录
            if ($last) {
                //是否是连续签到
                if ($last['create_time'] > (strtotime(date('Ymd')) -86400)) {
                    $data['con_num'] = $last['con_num'] + 1;
                } else {
                    $data['con_num'] = 1;
                }
                $data['total_num'] = $last['total_num'] + 1;
                D('SignIn')->where(array('uid'=>$uid))->save($data);

            } else {
                $data['con_num'] = 1;
                $data['total_num'] = 1;
                D('SignIn')->add($data);
            }
            $result['content'] =  $data['con_num'];
            $userInfo = D('Member')->info($uid);
            $userScore = $userInfo['score']+$score;
            if(D('Member')->setScore($uid,$userScore)){
                $result['score'] = $score;
            }
           
        }else{
            $result['errno'] = 1;
            $result['message'] = '您已签到';
        }
        
        $this->ajaxReturn($result);

    }
    

 }

