<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 个人收益记录
 */
namespace app\fastshop\controller\api\v3;
use app\fastshop\controller\api\Base;
use app\fastshop\model\BankInfo;
use app\fastshop\model\BankLogs;
use app\fastshop\model\BankCash;
use app\fastshop\model\BankRecharge;
use app\fastshop\model\Vip;
use app\fastshop\model\Bank as BankModel;
use app\fastshop\model\Config;
use app\common\facade\WechatPay;
use app\common\model\SystemMemberPayment;
use app\common\model\SystemMemberBank;
use think\facade\Request;

class Bank extends Base{

    public function initialize() {
        parent::initialize();
        $this->isUserAuth();
    }
 
     /**
     * 个人钱包
     * @param string $no
     * @return void
     */
    public function index(){
        $info = BankModel::where(['user_id' => $this->user->id])->find();
        if(empty($info)){
            return json(['code'=>204,'msg'=>'empty']);
        }else{
            $info['money']         = money($info['money']/100);
            $info['due_money']     = money($info['due_money']/100);
            $info['lack_money']    = money($info['lack_money']/100);
            $info['income_money'] = money($info['income_money']/100);
            $info['shop_money']    = money($info['shop_money']/100);
            return json(['code'=>200,'msg'=>'success','data' => $info]);
        }
    }

    /**
     * 个人收益记录
     * @param string $today  0是今天 1是历史
     * @return void
     */
    public function bill(){
        $param = [
            'today' => $this->request->param('today/d'),
            'page'  => $this->request->param('page/s'),
            'sign'  => $this->request->param('sign/s')
        ];
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $list = [];
        $times = strtotime(date('Y-m-d 00:00:00'));
        $condition[] = ['user_id','=',$this->user->id];
        if($param['today']){
            $condition[] = ['update_time','<=',$times];
        }else{  
            $condition[] = ['update_time','>=',$times];
        }
        $info = BankLogs::with(['formuser'=> function($query) {
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
            $param = [
                'money'             => $this->request->param('money/d'),
                'safepassword'      => $this->request->param('safepassword/s'),
                'sign'              => $this->request->param('sign/s')
            ];
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(204,'签名失败');
            }
            $param['user_id']           = $this->user->id;
            $param['member_miniapp_id'] = $this->miniapp_id;
            if($param['money'] < 100){
                return json(['code'=>403,'msg'=>'充值金额最小100元']);
            }
            $validate = $this->validate($param, 'Bank.getcash');
            if (true !== $validate) {
                return json(['code'=>403,'msg'=>$validate]);
            }
            //判断是否完善了信息
            $bankInfo = BankInfo::where(['user_id' => $this->user->id])->find();
            if(empty($bankInfo)){
                return json(['code'=>302,'msg'=>'请先完善您的个人信息','url'=>'/pages/user/info']);
            }
            //判断安全密码是否正确
            if(!password_verify(md5($param['safepassword']),$this->user->safe_password)) {
                return json(['code'=>403,'msg'=>'安全密码不正确']);
            }
            $config  = Config::field('cycle,lack_cash')->where(['member_miniapp_id' => $this->miniapp_id])->find();
            if($param['money'] < $config->lack_cash/100){
                return json(['code'=>403,'msg'=>'申请不能小于 '.money($config->lack_cash/100).' 元']);
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
            $rel = model('Bank')->cash($this->miniapp_id,$this->user->id,$param['money']);
            if(!$rel){
                return json(['code'=>403,'msg'=>"剩余积分不够"]);
            }
            model('BankLogs')->add($this->miniapp_id,$this->user->id,-($param['money']*100),'申请提取'.money(-$param['money']).' 已锁定');
            //增加申请记录
            $cash['user_id']           = $param['user_id']; 
            $cash['money']             = $param['money']*100;
            $cash['member_miniapp_id'] = $param['member_miniapp_id'];
            $cash['update_time']       = time();
            $cash['state']             = 0;
            $cash['realmoney']         = 0;
            model('BankCash')->insert($cash);
            return json(['code'=>200,'message'=>"申请已提交,请等待审核."]);
        }
    }

    /**
     * 帐号充值
     * @access public
     */
    public function recharge(){
        if(request()->isPost()){
            $param['money']        = Request::param('money');
            $param['safepassword'] = Request::param('safepassword');
            $param['sign']         = Request::param('sign');
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(204,'签名失败');
            }
            $param['user_id']           = $this->user->id;
            $param['member_miniapp_id'] = $this->miniapp_id;
            if($param['money'] < 100){
                return json(['code'=>403,'msg'=>'充值金额最小100元']);
            }
            $validate = $this->validate($param, 'Bank.getcash');
            if (true !== $validate) {
                return json(['code'=>403,'msg'=>$validate]);
            }
            //判断安全密码是否正确
            if(!password_verify(md5($param['safepassword']),$this->user->safe_password)) {
                return json(['code'=>403,'msg'=>'安全密码不正确']);
            }
            //支付方式
            $config = Config::where(['member_miniapp_id' => $this->miniapp_id])->find();
            //支付参数
            $order_no = 'RE'.order_no();
            if($config->is_pay_types == 1){
                //判断云收银台
                if($config->goodpay_tax > 0){
                    $goodpay_tax = $param['money']*$config->goodpay_tax/100;
                    $bank_rel = SystemMemberBank::moneyJudge($this->miniapp->member_id,$goodpay_tax);
                    if($bank_rel){
                        return ['code'=>0,'message'=>'官方帐号余额不足,请联系管理员'];
                    }
                }
                //支付接口
                $payment = SystemMemberPayment::where(['apiname'=>'wepay','member_miniapp_id'=>$this->miniapp_id])->find();
                if(empty($payment)){
                    return enjson(403,'未开通微信支付功能');
                }
                $pay_coinfig = json_decode($payment->config);
                //云收银台
                $ispay = [
                    'name'       => $this->miniapp->appname.'购买商品',
                    'mchid'      => json_decode($payment->config)->mch_id,
                    'total_fee'  => $param['money']*100,
                    'order_no'   => $order_no,
                    'note'       => $this->miniapp_id,
                    'notify_url' => api(3,'fastshop/goodpay/recharge',$this->miniapp_id),
                    'publickey'  => uuid(1)
                ];
                $paydata = $this->makeSign($ispay,$pay_coinfig->key);
            }else{
                //去请求微信支付接口
                $payparm = [
                    'openid'     => $this->user->miniapp_uid,
                    'miniapp_id' => $this->miniapp_id,
                    'name'       => '会员积分充值',
                    'order_no'   => $order_no,
                    'total_fee'  => $param['money']*100,
                    'notify_url' => api(3,'fastshop/goodpay/recharge',$this->miniapp_id),
                ];
                $ispay = WechatPay::orderPay($payparm);
                if($ispay['code'] == 0){
                    return enjson(403,$ispay['msg']);
                }
                $paydata = $ispay['data'];
            }
            BankRecharge::insert(['state'=>0,'money' => $param['money'],'order_no' => $order_no,'update_time'=>time(),'user_id' => $this->user->id,'member_miniapp_id' => $this->miniapp_id]);
            return enjson(200,'成功',['type' => $config->is_pay_types,'order' => $paydata]);
        }
    }   
    
    /**
     * 把购物金转账给朋友
     * @return void
     */
    public function transfer(){
        if (request()->isPost()) {
            $param = [
                'money'        => $this->request->param('money/s'),
                'sms_code'     => $this->request->param('code/s'),
                'phone_id'     => $this->request->param('phone/s'),
                'safepassword' => $this->request->param('safepassword/s'),
                'sign'         => $this->request->param('sign/s'),
            ];
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(204,'签名失败');
            }
            $validate = $this->validate($param, 'Bank.transfer');
            if (true !== $validate) {
                return json(['code'=>403,'msg'=>$validate]);
            }  
            //判断安全密码是否正确
            if(!password_verify(md5($param['safepassword']),$this->user->safe_password)) {
                return json(['code'=>403,'msg'=>'安全密码不正确']);
            }
            //查找接收方用户存在不存在
            $info = model('SystemUser')->where(['member_miniapp_id' => $this->miniapp_id,'phone_uid'=>$param['phone_id']])->field('id')->find();
            if($info->isEmpty()){
                return json(['code'=>403,'msg'=>"未找到好友账户"]);
            }
            //积分减少
            $reldown = model('Bank')->transfer($this->miniapp_id,$this->user->id,$param['money'],false);
            if(!$reldown){
                return json(['code'=>403,'msg'=>"剩余积分不够"]);
            }
            model('BankLogs')->add($this->miniapp_id,$this->user->id,-($param['money']*100),'转账给好友扣除积分'.money(-$param['money']));
            //积分增加
            $relup = model('Bank')->transfer($this->miniapp_id,$info->id,$param['money'],true);
            if(!$relup){
                return json(['code'=>403,'msg'=>"未入账成功,请联系客服"]);
            }
            model('BankLogs')->add($this->miniapp_id,$info->id,$param['money']*100,'来自好友积分'.money($param['money']));
            return json(['code'=>200,'msg'=>"好友转账成功"]);
        }
    }


    /**
     * 提交个人提现信息
     **/ 
    public function bindBankInfo(){
        if (request()->isPost()) {
            $param = [
                'name'              => $this->request->param('name/s'),
                'bankname'          => $this->request->param('bankname/s'),
                'idcard'            => $this->request->param('idcard/s'),
                'bankid'            => $this->request->param('bankid/s'),
                'bankid_confirm'    => $this->request->param('bankid_confirm/s'),
                'safepassword'      => $this->request->param('safepassword/s'),
                'sign'              => $this->request->param('sign/s'),
                'formId'            => $this->request->param('formId/s'),
            ];
            $validate = $this->validate($param, 'Bank.bankInfo');
            if (true !== $validate) {
                return enjson(403,$validate);
            }
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(403,'签名失败');
            }
            //判断安全密码是否正确
            if(!password_verify(md5($param['safepassword']),$this->user->safe_password)) {
                return enjson(403,'安全密码不正确');
            }
            //更新银行信息
            $rel = BankInfo::editer($this->miniapp_id,$this->user->id,$param);
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

    /**
     * 判断是否VIP
     */
    public function isVip(){
        $rel = model('Vip')->where(['member_miniapp_id'=>$this->miniapp_id,'user_id' => $this->user->id,'state'=>1])->find();
        if($rel){
            return json(['code'=>200,'msg'=>"开通会员",'data' => $rel]);
        }else{
            $config = Config::where(['member_miniapp_id' => $this->miniapp_id])->find();
            return json(['code'=>204,'msg'=>"未开通会员",'data' => $config['regvip_price']]);
        }
        
    }

    /**
     * 开通会员
     */
    public function openVip(){
        $order_no = $this->user->invite_code.order_no();
        $rel = Vip::where(['member_miniapp_id'=>$this->miniapp_id,'user_id' => $this->user->id])->find();
        if($rel){
            if($rel['state'] == 1){
                return json(['code'=>403,'msg'=>"您已是专享特权会员了"]);
            }
            Vip::where(['id'=>$rel->id])->update(['state'=>0,'order_no' => $order_no,'update_time'=>time()]);
        }else{
            Vip::insert(['state'=>0,'order_no' => $order_no,'update_time'=>time(),'user_id' => $this->user->id,'member_miniapp_id' => $this->miniapp_id]);
        }
        //读取费用
        $config = Config::where(['member_miniapp_id' => $this->miniapp_id])->find();
        if($config['regvip_price'] <= 0){
            return json(['code'=>403,'msg'=>'暂时关闭开通会员功能']);
        }
        //支付参数
        if($config->is_pay_types == 1){
            //判断云收银台
            if($config->goodpay_tax > 0){
                $goodpay_tax = $config->regvip_price*$config->goodpay_tax/100;
                $bank_rel = SystemMemberBank::moneyJudge($this->miniapp->member_id,$goodpay_tax);
                if($bank_rel){
                    return ['code'=>0,'message'=>'官方帐号余额不足,请联系管理员'];
                }
            }
            $payment = SystemMemberPayment::where(['apiname'=>'wepay','member_miniapp_id'=>$this->miniapp_id])->find();
            if(empty($payment)){
                return enjson(403,'未开通微信支付功能');
            }
            $pay_coinfig = json_decode($payment->config);
            $ispay = [
                'name'       => $this->miniapp->appname.'开通专享会员',
                'mchid'      => $pay_coinfig->mch_id,
                'total_fee'  => $config->regvip_price*100,
                'order_no'   => $order_no,
                'note'       => $this->miniapp_id,
                'notify_url' => api(3,'fastshop/goodpay/openVip',$this->miniapp_id),
                'publickey'  => uuid(1)
            ];
            $paydata = $this->makeSign($ispay,$pay_coinfig->key);
        }else{
            $payparm = [
                'openid'     => $this->user->miniapp_uid,
                'miniapp_id' => $this->miniapp_id,
                'name'       => $this->miniapp->appname.'开通专享会员',
                'order_no'   => $order_no,
                'total_fee'  => $config->regvip_price*100,
                'notify_url' => api(3,'fastshop/goodpay/openVip',$this->miniapp_id),
            ];
            $ispay = WechatPay::orderPay($payparm);
            if($ispay['code'] == 0){
                return enjson(403,$ispay['msg']);
            }
            $paydata = $ispay['data'];
        }
        return enjson(200,'成功',['type' => $config->is_pay_types,'order' => $paydata]);
    }
}