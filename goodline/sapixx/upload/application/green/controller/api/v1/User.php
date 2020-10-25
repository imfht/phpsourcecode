<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户中心
 */
namespace app\green\controller\api\v1;
use app\green\controller\api\Base;
use app\green\model\GreenBankCash;
use app\green\model\GreenConfig;
use app\green\model\GreenUser;
use app\common\facade\Inform;
use app\common\model\SystemMemberSms;

class User extends Base{
    /**
     * 提现申请
     **/
    public function cash(){
        if (request()->isPost()) {
            $data = [
                'money'        => $this->request->param('money/f', ''),
                'safepassword' => $this->request->param('safepassword/s', ''),
                'sign'         => $this->request->param('sign/s', ''),
            ];
            $rel = $this->apiSign($data);
            if($rel['code'] != 200){
                return enjson(500,'签名验证失败');
            }
            $data['user_id']           = $this->user->id;
            $data['member_miniapp_id'] = $this->miniapp_id;
            $validate = $this->validate($data, 'Bank.getcash');
            if (true !== $validate) {
                return enjson(403,$validate);
            }
            if($data['money'] < 0){
                return enjson(403,'申请不能小于 0 元');
            }
            //判断安全密码是否正确
            if(!password_verify(md5($data['safepassword']),$this->user->safe_password)) {
                return enjson(403,'安全密码不正确');
            }
            //判断是否关联的公众号账户和输入提现信息
            $setting = GreenConfig::where(['member_miniapp_id' => $this->miniapp_id])->find();
            if($setting->is_wechat_touser == 0){
                $bankInfo = model('SystemUserBank')->where(['user_id' => $this->user->id])->find();
                if(empty($bankInfo)){
                    return enjson(302,'请先完善您的个人信息',['url'=>'/pages/helper/bindbank']);
                }       
            }
            //判断提现周期
            $bankCash = GreenBankCash::where(['user_id' => $this->user->id])->field('state,update_time')->order('id desc')->find();
            if(!empty($bankCash)){
                if($bankCash->state == 0){
                    return enjson(403,'上次申请还未到账,禁止连续提现');
                }
            }
            //减少
            $info = GreenUser::cash($this->miniapp_id,$this->user->id,$data['money']);
            if(!$info){
                return enjson(403,"金额不足");
            }
            //增加申请记录
            $cash['member_miniapp_id'] = $data['member_miniapp_id'];
            $cash['user_id']           = $data['user_id'];
            $cash['money']             = $data['money'];
            $cash['realmoney']         = 0;
            $cash['state']             = 0;
            $cash['update_time']       = time();
            $rel = GreenBankCash::insert($cash);
            if($rel){
                //后台通知
                SystemMemberSms::sms($this->miniapp_id,'您有一条提现待审核',url('green/bank/cash'));
                //申请者微信通知
                Inform::sms($this->user->id,$this->miniapp_id,['title' =>'业务进展通知','type' => '提现申请','content' =>'您的提现申请正在审核中']);
                //后台管理员微信通知
                Inform::sms(\app\common\event\User::isFounder($this->miniapp_id)->user_id,$this->miniapp_id,['title' =>'业务进展通知', 'type' => '提现申请', 'content' =>'您有一条新的提现申请待审核']);
                return enjson(200,"申请已提交,请等待审核.");
            }
            return enjson(403,"金额不足");
        }
    }


    /**
     * 提现记录
     */
    public function log(){
        $param['date'] = $this->request->param('date/s', '');
        $param['page'] = $this->request->param('page/d', 1);
        $param['sign'] = $this->request->param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(500,'签名验证失败');
        }
        $condition[] = ['user_id','=',$this->user->id];
        if($param['date']){
            list($start, $end) = self::month( $param['date']);
            $condition[] = ['update_time','>=',$start];
            $condition[] = ['update_time','<=',$end];
        }

        $info   = GreenBankCash::where(['member_miniapp_id' => $this->miniapp_id])->where($condition)->order('id desc')->paginate(10)->toArray();
        if(empty($info['data'])){
            return enjson(204,'empty');
        }else{
            return enjson(200,'success', $info);
        }
    }

    protected static function month($str){
        $day   = explode("-",$str);
        $begin = mktime(0, 0, 0, $day[1], 1, $day[0]);
        $end   = mktime(23, 59, 59, $day[1], date('t', $begin), $day[0]);
        return [$begin, $end];
    }
}