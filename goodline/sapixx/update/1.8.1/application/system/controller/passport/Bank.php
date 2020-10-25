<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 我的余额
 */
namespace app\system\controller\passport;
use app\common\model\SystemApis;
use app\common\model\SystemMemberMiniapp;
use app\common\model\SystemMemberBank;
use app\common\model\SystemMemberBankBill;
use app\common\model\SystemMemberBankRecharge;
use app\common\facade\WechatPay;


class Bank extends Common{

    public function initialize() {
        parent::initialize();
        if($this->user->parent_id){
            $this->error('无权限访问,只有创始人身份才允许使用。');
        }
    }

    /**
     * 我的应用
     * @access public
     */
    public function index(){
        $view['list'] = SystemMemberMiniapp::where(['member_id' => $this->user->id])->order('create_time desc')->paginate(10,false);
        $view['pathMaps'] = [['name'=>'财务管理','url'=>'javascript:;'],['name'=>'我的应用','url'=>'javascript:;']];   
        return view()->assign($view);
    }

    /**
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 我的帐单
     */
    public function bill(){
        $view['bank']     = SystemMemberBank::where(['member_id' => $this->user->id])->find();
        $view['consume']  = SystemMemberBankBill::where(['member_id' => $this->user->id,'state' => 1])->sum('money');
        $view['list']     = SystemMemberBankBill::where(['member_id' => $this->user->id])->order('update_time desc')->paginate(20,false);
        $view['pathMaps'] = [['name'=>'财务管理','url'=>'javascript:;'],['name'=>'我的帐单','url'=>'javascript:;']];
        return view()->assign($view);
    }

    /**
     * 账户充值
     * @return \think\response\View
     */
    public function pay(){
        if(request()->isAjax()) {
            $param = [
                'payType'   => $this->request->param('payType/d',0),
                'money'     => $this->request->param('money/d',10),
                'order_sn'  => order_no(),
            ];
            $validate = $this->validate($param,'MemberBank.recharge');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $rel = SystemMemberBankRecharge::order(['money' => $param['money'],'member_id' => $this->user->id,'order_sn'  => $param['order_sn']]);
            if ($rel) {
                switch ($param['payType']) {
                    case 1:
                        # 支付宝
                        break;
                    default:
                        $config = SystemApis::Config('wepay');
                        $order = [
                            'trade_type'   => 'NATIVE',
                            'body'         => '充值',
                            'out_trade_no' => $param['order_sn'],
                            'total_fee'    => $param['money'] * 100,//分
                            'mch_id'       => $config['mch_id'],
                            'appid'        => $config['app_id'],
                            'notify_url'   => api(1,'system/notify/index'),
                        ];
                        $result = WechatPay::doPay()->order->unify($order);
                        if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
                            return enjson(200,'操作成功',$result);
                        }
                        return enjson(0,$result['return_msg']);
                        break;
                }
            }
            return enjson(0);
        }else{
            $view['pathMaps'] = [['name'=>'财务管理','url'=>'javascript:;'],['name'=>'账户充值','url'=>'javascript:;']];   
            return view()->assign($view);
        }
    }
}