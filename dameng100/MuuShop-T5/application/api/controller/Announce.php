<?php
namespace app\api\controller;

use think\Controller;
use think\Db;

class Announce extends Controller{

    /**
     * 设置公告已确认收到
     * @return bool
     */
    public function setArrive()
    {
        $aAnnounceId=input('post.announce_id',0,'intval');
        if(!$aAnnounceId){
            return false;
        }
        $map['uid']=is_login();
        $map['announce_id']=$aAnnounceId;
        $announceArriveModel=model('common/AnnounceArrive');
        if(!$announceArriveModel->getDataByMap($map)){
            $data=$map;
            $data['create_time']=time();
            $announceArriveModel->addData($data);
        }
        return true;
    }

    /**
     * 发布公告后，给所有用户发送公告消息
     * @return bool
     */
    public function sendAnnounceMessage()
    {
        $aToken = input('get.token','','text');
        $aTime = input('get.time',0,'intval');

        if($aTime + 30  < time()){
            exit('Error');
        }
        if($aToken != md5($aTime.config('database.auth_key'))){
            exit('Error');
        }
        ignore_user_abort(true); //即使Client断开(如关掉浏览器)，PHP脚本也可以继续执行.
        set_time_limit(0); // 执行时间为无限制，php默认的执行时间是30秒，通过set_time_limit(0)可以让程序无限制的执行下去

        $aId=input('get.announce_id',0,'intval');

        $announce=model('Announce')->getData($aId);
        if($announce){
            
            $uids=Db::name('Member')->where(['status'=>1])->field('uid')->select();
            $uids=array_column($uids,'uid');

            $content=array(
                'keyword1'=>$announce['content'],
                'keyword2'=>$announce['create_time'],
            );
            model('Message')->sendALotOfMessageWithoutCheckSelf($uids,$announce['title'],$content,$announce['link'],null,-1,'Common_announce','Common_announce');
        }
        return true;
    }
} 