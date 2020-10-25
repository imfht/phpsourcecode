<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 通用模板消息
 */
namespace app\common\widget;
use app\common\facade\WechatProgram;
use app\common\model\MemberWechatTpl;
use app\common\model\MemberMiniapp;
use app\common\model\MemberSubscribeQueue;
use app\common\model\User;
use Exception;

class Subscribe{

    /**
     * 订阅通知
     * @param int $uid  用户ID
     * @param int $miniapp_id  小程序ID
     * @param array $param
     * $param['title']   = 标题;
     * $param['content'] = 内容;
     * $param['state']   = 状态;
     * @return void
     */
    public function sms(int $uid,int $miniapp_id,array $param){
        if(empty($uid) || empty($param['content'])){
            return;
        }
        return MemberSubscribeQueue::create(['uid' => $uid,'member_miniapp_id' => $miniapp_id,'param' => json_encode($param),'is_send' => 0,'create_time' => time()]);
    }

    /**
     * 群发队列服务
     */
    public function subscribeQueue(){
        $info = MemberSubscribeQueue::where(['is_send' => 0])->find();
        if(empty($info)){
            return;
        }
        $miniapp_id = $info->member_miniapp_id;
        $uid        = $info->uid;
        $param      = json_decode($info->param,true);
        $content    = $param['content'];  //内容
        $state      = empty($param['state']) ? '待审' : $param['state'];  //状态
        $url        = empty($param['url']) ? 'pages/index/index' : $param['url'];  //访问地址
        $miniapp    = MemberMiniapp::where(['id' => $miniapp_id])->field('id,mp_appid,miniapp_appid')->find();
        if(empty($miniapp)){
            return;
        }
        $user = User::where(['id' => $uid])->field('miniapp_uid')->find();
        if (empty($user->miniapp_uid)) {
            MemberSubscribeQueue::where(['id' => $info->id])->update(['is_send' => 1]);
            return;
        }
        //用户类别
        $wechat = WechatProgram::isTypes($miniapp->id);
        if (empty($wechat)) {
            return;
        }
        $setting = MemberWechatTpl::getConfig($miniapp->id);
        if (empty($setting)) {
            return;
        }
        //订阅消息
        if(!empty($miniapp->mp_appid) || !empty($setting->tplmsg_common_wechat)){
            try {
                $rel = $wechat->subscribe_message->send([
                    'touser' => $user->miniapp_uid,
                    'template_id' => $setting->tplmsg_common_app,
                    'page' => $url,
                    'data' => [
                            'thing01'   => [
                                'value' => $content
                            ],
                            'phrase01'  => [
                                'value' => $state
                            ],
                            'time01'    => [
                                'value' => date('Y-m-d H:i')
                            ],
                    ],
                ]);
                if($rel['errcode'] == 0){
                    MemberSubscribeQueue::where(['id' => $info->id])->update(['is_send' => 1]);
                    return true;
                }
            }catch (Exception $e) {
                return;
            }
        }
    }
}