<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 通用模板消息
 */
namespace app\common\facade\library;
use app\common\facade\WechatProgram;
use app\common\model\SystemMemberWechatTpl;
use app\common\model\SystemMemberMiniapp;
use app\common\model\SystemMemberSmsQueue;
use app\common\model\SystemUser;
use Exception;

class Inform{

    /**
     * 通用通知
     * @param int $uid  用户ID
     * @param int $miniapp_id  小程序ID
     * @param array $param
     * $param['title']   = 标题;
     * $param['content'] = 内容;
     * $param['type']    = 类型;
     * $param['state']   = 状态;
     * $param['remark']  = 备注;
     * @return void
     */
    public function sms(int $uid,int $miniapp_id,array $param){
        if(empty($uid) || empty($param['title']) ||empty($param['content'])){
            return;
        }
        return SystemMemberSmsQueue::create(['uid' => $uid,'member_miniapp_id' => $miniapp_id,'param' => json_encode($param),'is_send' => 0,'create_time' => time()]);
    }

    /**
     * 群发队列服务
     */
    public function smsQueue(){
        $info = SystemMemberSmsQueue::where(['is_send' => 0])->find();
        if(empty($info)){
            return;
        }
        $miniapp_id = $info->member_miniapp_id;
        $uid        = $info->uid;
        $param      = json_decode($info->param,true);
        $title      = $param['title'];   //通知标题
        $content    = $param['content'];  //业务内容
        $type       = empty($param['type']) ? '申请' : $param['type']; //业务类型
        $state      = empty($param['state']) ? '待审' : $param['state'];  //状态
        $remark     = empty($param['remark']) ? '如要疑问请咨询官方客服' : $param['remark'];  //备注
        $url        = empty($param['url']) ? 'pages/index/index' : $param['url'];  //访问地址
        $miniapp    = SystemMemberMiniapp::where(['id' => $miniapp_id])->field('id,mp_appid,miniapp_appid')->find();
        if(empty($miniapp)){
            return;
        }
        $user = SystemUser::where(['id' => $uid])->field('official_uid')->find();
        if (empty($user->official_uid)) {
            SystemMemberSmsQueue::where(['id' => $info->id])->update(['is_send' => 1]);
            return;
        }
        //用户类别
        $wechat = WechatProgram::isTypes($miniapp->id);
        if (empty($wechat)) {
            return;
        }
        $setting = SystemMemberWechatTpl::getConfig($miniapp->id);
        if (empty($setting)) {
            return;
        }
        //小程序模板消息
        $weapp_template_msg = [];
        //公众号消息
        if(!empty($miniapp->mp_appid) || !empty($setting->tplmsg_common_wechat)){
            try {
                $rel = $wechat->uniform_message->send([
                    'touser' => $user->official_uid,
                    'weapp_template_msg' => $weapp_template_msg,
                    'mp_template_msg' => [
                        'appid'       => $miniapp->mp_appid,
                        'template_id' => $setting->tplmsg_common_wechat,
                        'url'         => $url,
                        'miniprogram' => [
                            'pagepath' => $url,
                            'appid' => $miniapp->miniapp_appid
                        ],
                        'data' => [
                            'first'    => $title,
                            'keyword1' => $type,    //业务类型
                            'keyword2' => $content, //业务内容
                            'keyword3' => $state,   //处理结果
                            'keyword4' => date('Y-m-d H:i:s'),  //时间
                            'keyword5' => $remark
                        ],
                    ],
                ]);
                if($rel['errcode'] == 0){
                    SystemMemberSmsQueue::where(['id' => $info->id])->update(['is_send' => 1]);
                    return true;
                }
            }catch (Exception $e) {
                return;
            }
        }
    }
}