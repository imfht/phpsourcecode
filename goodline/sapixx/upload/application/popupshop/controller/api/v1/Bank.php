<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 个人收益记录
 */
namespace app\popupshop\controller\api\v1;
use app\popupshop\controller\api\Base;
use app\popupshop\model\Bank as BankModel;
use app\popupshop\model\BankBill;
use app\popupshop\model\BankRecharge;
use app\popupshop\model\BankCash;
use app\popupshop\model\BankInfo;
use app\common\model\SystemUser;
use app\popupshop\model\Config;
use app\common\facade\WechatPay;
use think\facade\Request;

class Bank extends Base
{

    public function initialize() {
        parent::initialize();
        if(!$this->user){
            exit(json_encode(['code'=>401,'msg'=>'用户认证失败']));
        }
    }

     /**
     * 个人钱包
     * @param string $no
     * @return void
     */
    public function index(){
        //查询提现金额
        $info = BankModel::where(['user_id' => $this->user->id])->find();
        if(empty($info)){
            return enjson(204,'未开通帐号');
        }else{
            $data['due_money']  = $info->due_money;
            $data['lack_money'] = $info->lack_money;
            $data['shop_money'] = $info->shop_money;
            return enjson(200,'成功',$data);
        }
    }

    /**
     * 个人收益记录
     * @param string $today  0是今天 1是历史
     * @return void
     */
    public function bill(int $today = 0){
        $list = [];
        $times = strtotime(date('Y-m-d 00:00:00'));
        $condition[] = ['user_id','=',$this->user->id];
        if($today){
            $condition[] = ['update_time','<=',$times];
        }else{  
            $condition[] = ['update_time','>=',$times];
        }
        $info = BankBill::with(['formuser'=> function($query) {
            $query->field('id,face,nickname');
        }])->where($condition)->order('id desc')->paginate(10)->toArray();
        if(!empty($info['data'])){
            foreach ($info['data'] as $key => $value) {
               $list[$key] = $value;
               $list[$key]['update_time'] = date('Y-m-d H:i:s',$value['update_time']);
               $list[$key]['money']       = '￥'.$value['money'];
               $list[$key]['formuser']    = empty($value['formuser']) ? [] : $value['formuser'];
            }
        }
        return enjson(200,'成功',$list);
    }  

    /**
     * 提现申请
     **/ 
    public function cash(){
        if (request()->isPost()) {
            $data = [
                'money'             => money(Request::param('money/d',0)),
                'safepassword'      => Request::param('safepassword/s'),
                'user_id'           => $this->user->id,
                'member_miniapp_id' => $this->miniapp_id,
            ];
            $validate = $this->validate($data, 'Bank.getcash');
            if (true !== $validate) {
                return enjson(403,$validate);
            }
            //判断安全密码是否正确
            if(!password_verify(md5($data['safepassword']),$this->user->safe_password)) {
                return enjson(403,'安全密码不正确');
            }
            $config = Config::where(['member_miniapp_id' => $this->miniapp_id])->field('cycle,lack_cash')->find();
            if($data['money'] < $config->lack_cash){
                return enjson(403,'转出最少 '.money($config->lack_cash).' 元');
            }
            //判断提现周期
            $bankCash = BankCash::where(['user_id' => $this->user->id])->field('state,update_time')->order('id desc')->find();
            if(!empty($bankCash)){
                if($bankCash->state == 0){
                    return json(['code'=>403,'msg'=>'上次申请还未到账,禁止连续提现']);
                }else{
                    $cycle = intval($config->cycle); //计算天数
                    $day   = intval((time()-$bankCash->update_time)/(86400));
                    if($day < $cycle){
                        return json(['code'=>403,'msg'=>'距离上次申请必须间隔'.$cycle.'天']);
                    }
                }
            }
            //积分减少
            $rel = BankModel::cash($this->miniapp_id,$this->user->id,$data['money']);
            if(!$rel){
                return json(['code'=>403,'msg'=>"剩余积分不够"]);
            }
            //增加申请记录
            BankCash::insert(['user_id'=> $data['user_id'],'money' => $data['money'],'member_miniapp_id' => $data['member_miniapp_id'],'update_time' => time(),'state' => 0,'realmoney' => 0]);
            return json(['code'=>200,'message'=>"申请已提交,请等待审核."]);
        }
    }

    /**
     * 帐号充值
     * @access public
     */
    public function recharge(){
        if(request()->isPost()){
            $data = [
                'money'             => Request::param('money/d',0),
                'safepassword'      => Request::param('safepassword/s'),
                'user_id'           => $this->user->id,
                'member_miniapp_id' => $this->miniapp_id,
            ];
            if($data['money'] < 100){
                return enjson(403,'充值金额最小100元');
            }
            $validate = $this->validate($data, 'Bank.getcash');
            if (true !== $validate) {
                return enjson(403,$validate);
            }
            //判断安全密码是否正确
            if(!password_verify(md5($data['safepassword']),$this->user->safe_password)) {
                return enjson(403,'安全密码不正确');
            }
            $order_no = 'RE'.order_no();
            $rel = BankRecharge::insert(['state'=>0,'money' => $data['money'],'order_no' => $order_no,'update_time'=>time(),'user_id' => $this->user->id,'member_miniapp_id' => $this->miniapp_id]);
            if (empty($rel)) {
                return enjson(403,'创建充值信息失败');
            }
            $payparm = [
                'openid'     => $this->user->miniapp_uid,
                'miniapp_id' => $this->miniapp_id,
                'name'       => '帐号充值',
                'order_no'   => $order_no,
                'total_fee'  => $data['money']*100,
                'notify_url' => api(1,'popupshop/notify/recharge',$this->miniapp_id)
            ];
            $ispay = WechatPay::orderPay($payparm);;
            if($ispay['code'] == 0){
                return enjson(403,$ispay['msg']);
            }
            return enjson(200,'成功',$ispay['data']); 
        }
    }   
    
    /**
     * 把购物金转账给朋友
     * @return void
     */
    public function transfer(){
        if (request()->isPost()) {
            $data = [
                'money'        => Request::param('money/s',0),
                'sms_code'     => Request::param('code/s'),
                'phone_id'     => Request::param('phone/s'),
                'safepassword' => Request::param('safepassword/s'),
            ];
            $validate = $this->validate($data, 'Bank.transfer');
            if (true !== $validate) {
                return enjson(403,$validate);
            }  
            //判断安全密码是否正确
            if(!password_verify(md5($data['safepassword']),$this->user->safe_password)) {
                return enjson(403,'安全密码不正确');
            }
            //查找接收方用户存在不存在
            $info = SystemUser::where(['member_miniapp_id' => $this->miniapp_id,'phone_uid'=>$data['phone_id']])->field('id')->find();
            if($info->isEmpty()){
                return enjson(403,'未找到好友账户');
            }
            //积分减少
            $reldown = BankModel::transfer($this->miniapp_id,$this->user->id,$data['money'],false);
            if(!$reldown){
                return enjson(403,'剩余积分不够');
            }
            BankBill::add($this->miniapp_id,$this->user->id,-($data['money']*100),'转账给好友扣除积分'.money(-$data['money']));
            //积分增加
            $relup = BankModel::transfer($this->miniapp_id,$info->id,$data['money'],true);
            if(!$relup){
                return enjson(403,'未入账成功,请联系客服');
            }
            BankBill::add($this->miniapp_id,$info->id,$data['money']*100,'来自好友积分'.money($data['money']));
            return enjson(200,'好友转账成功');
        }
    }

    /**
     * 提交个人提现信息
     **/ 
    public function bindBankInfo(){
        if (request()->isPost()) {
            $data = [
                'name'              => Request::param('name/s'),
                'bankname'          => Request::param('bankname/s'),
                'idcard'            => Request::param('idcard/s'),
                'bankid'            => Request::param('bankid/s'),
                'bankid_confirm'    => Request::param('bankid_confirm/s'),
                'safepassword'      => Request::param('safepassword/s'),
            ];
            $validate = $this->validate($data, 'Bank.bankInfo');
            if (true !== $validate) {
                return enjson(403,$validate);
            }
            //判断安全密码是否正确
            if(!password_verify(md5($data['safepassword']),$this->user->safe_password)) {
                return enjson(403,'安全密码不正确');
            }
            //更新银行信息
            $rel = BankInfo::editer($this->miniapp_id,$this->user->id,$data);
            if($rel){
                return enjson(403,'银行信息绑定成功');
            }else{
                return enjson(403,'银行信息绑定失败');
            }
        }
    }
    
    /**
     * 获取银行信息
     */
    public function getBankInfo(){
        if(!$this->user->safe_password){
            return json(['code'=>302,'msg'=>'请先设置您的安全密码','url'=>'/pages/helper/safepasspord']);
        }
        //更新银行信息
        $rel = BankInfo::where(['member_miniapp_id'=>$this->miniapp_id,'user_id' => $this->user->id])->field('name,idcard,bankname,bankid')->find();
        if($rel){
            $data['name']     = $rel['name'];
            $data['idcard']   = $rel['idcard'];
            $data['bankname'] = $rel['bankname'];
            $data['bankid']   = $rel['bankid'];
            return enjson(200,'成功',$data);
        }else{
            return enjson(204,'失败');
        }
    }
}