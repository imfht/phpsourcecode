<?php

namespace app\home\controller;
use think\facade\Db;
use think\facade\Lang;
use GatewayClient\Gateway;
/**
 * ============================================================================
 * DSKMS多用户商城
 * ============================================================================
 * 版权所有 2014-2028 长沙德尚网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.csdeshang.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 用户消息控制器
 */
class MemberInstantMessage extends BaseMember {

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/'.config('lang.default_lang').'/live.lang.php');
    }


    public function add() {
        if(!config('ds_config.instant_message_register_url')){
            ds_json_encode(10001, lang('instant_message_register_url_empty'));
        }
        // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值(ip不能是0.0.0.0)
        try{
        Gateway::$registerAddress = config('ds_config.instant_message_register_url');
        $instant_message_model = model('instant_message');
        }catch(\Exception $e){
          ds_json_encode(10001, $e->getMessage());
        }
        $to_id = input('param.to_id');
        $to_type = input('param.to_type');
        $message = input('param.message');
        $message_type = input('param.message_type');
        switch ($to_type) {
            case 0:
                $member_model = model('member');
                $member = $member_model->getMemberInfo(array('member_id' => $to_id, 'member_state' => 1));
                if (!$member) {
                    ds_json_encode(10001, lang('user_not_exist'));
                }
                $to_name = $member['member_name'];
                break;
            case 1:
                $store_model = model('store');
                $store = $store_model->getStoreOnlineInfoByID($to_id);
                if (!$store) {
                    ds_json_encode(10001, lang('store_not_exist'));
                }
                $to_name = $store['store_name'];
                break;
            case 2:
                $live_apply_model = model('live_apply');
                $live_apply = $live_apply_model->getLiveApplyInfo(array(array('live_apply_id' ,'=', $to_id), array('live_apply_state' ,'=', 1), array('live_apply_end_time','>', TIMESTAMP)));
                if (!$live_apply) {
                    ds_json_encode(10001, lang('live_not_exit'));
                }
                $to_name = $live_apply['live_apply_id'] . lang('live_room');
                break;
            default:
                ds_json_encode(10001, lang('param_error'));
        }
        $instant_message_data = array(
            'instant_message_from_id' => $this->member_info['member_id'],
            'instant_message_from_type' => 0,
            'instant_message_from_name' => $this->member_info['member_name'],
            'instant_message_from_ip' => request()->ip(),
            'instant_message_to_id' => $to_id,
            'instant_message_to_type' => $to_type,
            'instant_message_to_name' => $to_name,
            'instant_message' => $message,
            'instant_message_type' => $message_type,
            'instant_message_verify' => 0,
            'instant_message_add_time' => TIMESTAMP,
        );

        $instant_message_validate = ds_validate('instant_message');
        if (!$instant_message_validate->scene('instant_message_save')->check($instant_message_data)) {
            ds_json_encode(10001, $instant_message_validate->getError());
        }
        Db::startTrans();
        try {
            $instant_message_id = $instant_message_model->addInstantMessage($instant_message_data);
            if (!$instant_message_id) {
                throw new \think\Exception(lang('ds_common_op_fail'), 10006);
            }
            $instant_message_data['instant_message_id'] = $instant_message_id;
            if (!config('ds_config.instant_message_verify')) {
                //立即发送
                $instant_message_data['instant_message_from_avatar']= get_member_avatar_for_id($instant_message_data['instant_message_from_id']);
                $res = $instant_message_model->sendInstantMessage($instant_message_data,true);
                if (!$res['code']) {
                    throw new \think\Exception($res['msg'], 10006);
                }
                $instant_message_data['instant_message_verify']=1;
                $instant_message_data['instant_message_verify_time']=TIMESTAMP;
            }
            Gateway::sendToUid('-1', json_encode(array(
                'online_item'=>$instant_message_data
            )));
        } catch (\Exception $e) {
            Db::rollback();
            ds_json_encode(10001, $e->getMessage());
        }
        Db::commit();
        ds_json_encode(10000, config('ds_config.instant_message_verify') ? lang('message_wait_varify') : lang('message_send_success'), array('instant_message_id' => $instant_message_id, 'instant_message_verify' => config('ds_config.instant_message_verify')));
    }

}

?>
