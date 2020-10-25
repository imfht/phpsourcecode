<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 客户收益管理
 */
namespace app\popupshop\controller;
use app\common\controller\Manage;
use app\popupshop\model\BankBill;
use app\popupshop\model\Bank as AppBank;
use app\popupshop\model\BankCash;
use app\popupshop\model\Config;
use app\popupshop\model\BankInfo;
use app\popupshop\model\Order;
use app\popupshop\model\Sale;
use app\popupshop\model\SaleOrder;
use app\popupshop\model\SaleUser;
use app\popupshop\model\Fees;
use think\facade\Request;

class Bank extends Manage
{

    public function initialize()
    {
        parent::initialize();
        $this->assign('pathMaps', [['name'=>'客户收益','url'=>'javascript:;']]);
    }

    /**
     * 客户收益管理
     * @return void
     */
    public function index(int $types = 0){
        switch ($types) {
            case 1:
                $order = 'income_money desc';
                break;
            case 2:
                $order = 'due_money desc';
                break;  
            case 3:
                $order = 'lack_money desc';
                break;
            case 4:
                $order = 'shop_money desc';
                break;  
            default:
                $order = 'update_time desc';
                break;
        }
        $condition['member_miniapp_id'] = $this->member_miniapp_id;
        $view['list']                   = AppBank::where($condition)->order($order)->paginate(20);
        $view['income_money']          = AppBank::where($condition)->sum('income_money');
        $view['due_money']              = AppBank::where($condition)->sum('due_money');
        $view['shop_money']             = AppBank::where($condition)->sum('shop_money');
        $view['lack_money']             = AppBank::where($condition)->sum('lack_money');
        $view['types']                  = $types;
        return view()->assign($view);
    }

    /**
     * 客户收益记录
     * @return void
     */
    public function bill(int $input = 0){
        $starttime = Request::param('starttime',0);
        $endtime   = Request::param('endtime',0);
        $where     = [];
        $condition = [];
        $where[]     = ['member_miniapp_id', '=', $this->member_miniapp_id];
        $condition[] = ['member_miniapp_id', '=', $this->member_miniapp_id];
        if($input){
            $where[]     = ['user_id','=',$input];
            $condition[] = ['user_id','=',$input];
        }
        if(!empty($starttime) && !empty($endtime)){
            if($starttime > $endtime){
                $this->error('开始日期不能大于结束日期');
            }
            $where[] = ['update_time','>=',strtotime($starttime)];
            $where[] = ['update_time','<=',strtotime($endtime)];
            $condition[] = ['paid_time','>=',strtotime($starttime)];
            $condition[] = ['paid_time','<=',strtotime($endtime)];
        }
        $view['list']      = BankBill::where($where)->order('id desc')->paginate(20, false, ['query' => ['input' => $input, 'starttime' => $starttime, 'endtime' => $endtime]]);
        $view['count']     = BankBill::where($where)->count();
        $view['money']     = BankBill::where($where)->sum('money');
        $view['input']     = $input;
        $view['starttime'] = $starttime;
        $view['endtime']   = $endtime;
        return view()->assign($view);
    } 

    /**
     * 客户提现
     */
    public function cash($types = 0){ 
        $keyword = trim(input('get.keyword','','htmlspecialchars'));
        $view['pending'] = BankCash::where(['member_miniapp_id' =>$this->member_miniapp_id,'state' => 0])->count();
        $view['pass']    = BankCash::where(['member_miniapp_id' =>$this->member_miniapp_id,'state' => 1])->count();
        $view['no_pass'] = BankCash::where(['member_miniapp_id' =>$this->member_miniapp_id,'state' => -1])->count();
        $condition['member_miniapp_id'] = $this->member_miniapp_id;
        if(!empty($keyword)){
            $condition['phone_uid'] = $keyword;
        }
        switch ($types) {
            case 1:
                $state = -1;
                break;
            case 2:
                $state = 1;
                break;
            default:
                $state = 0;
                break;
        }
        $condition['state'] = $state;
        $view['list']    =  BankCash::where($condition)->order('id desc')->paginate(20,false,['query' => ['types' => $types]]);
        $view['money']   = BankCash::where($condition)->sum('money');
        $view['keyword'] = $keyword;
        $view['types']   = $types;
        return view()->assign($view);
    }
    /**
     * 导出到Excel
     */
    public function cashExcel($types = 0){ 
        header("Content-type: text/plain");
        header("Accept-Ranges: bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=cash_".date('Y-m-d').".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        switch ($types) {
            case 1:
                $state = -1;
                break;
            case 2:
                $state = 1;
                break;
            default:
                $state = 0;
                break;
        }
        $condition['member_miniapp_id'] = $this->member_miniapp_id;
        $condition['state'] = $state;
        $view['list']   = BankCash::where($condition)->order('id desc')->select();
        $view['config'] = Config::where(['member_miniapp_id' => $this->member_miniapp_id])->find();
        return view()->assign($view);
    }

    /**
     * 充值积分
     * @return void
     */
    public function recharge(int $input){
        if(request()->isAjax()){
            $data = [
                'user_id'      => Request::param('uid/d'),
                'types'        => Request::param('types/d'),
                'safepassword' => Request::param('safepassword/s'),
                'shop_money'   => Request::param('shop_money/f'),
                'due_money'    => Request::param('due_money/f'),
                'miniapp_id'   => $this->member_miniapp_id,
            ];
            $validate = $this->validate($data,'Bank.recharge');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            //验证安全密码
            if(!password_verify(md5($data['safepassword']),$this->user->safe_password)) {
                return json(['code'=>0,'msg'=>"安全密码错误"]);
            }
            $due_money  = money($data['due_money']);
            $shop_money = money($data['due_money']);
            if($data['types'] > 0){  //提取
                if($due_money > 0){
                    AppBank::recharge($this->member_miniapp_id,$data['user_id'],-$due_money);
                    BankBill::add($this->member_miniapp_id,$data['user_id'],-$due_money,'平台扣除余额'.-$due_money);
                }
                if($shop_money > 0){
                    model('Bank')->rechargeShop($this->member_miniapp_id,$data['user_id'],-$data['shop_money']);
                    BankBill::add($this->member_miniapp_id,$data['user_id'],-(intval($data['shop_money']*100)),'平台扣除积分'.-money($data['shop_money']));
                }
            }else{ //充值
                if($data['due_money'] > 0){
                    model('Bank')->recharge($this->member_miniapp_id,$data['user_id'],$data['due_money']);
                    BankBill::add($this->member_miniapp_id,$data['user_id'],intval($data['due_money']*100),'奖励应付积分'.money($data['due_money']));
                }
                if($data['shop_money'] > 0){
                    model('Bank')->rechargeShop($this->member_miniapp_id,$data['user_id'],$data['shop_money']);
                    BankBill::add($this->member_miniapp_id,$data['user_id'],intval($data['shop_money']*100),'奖励购物积分'.money($data['shop_money']));
                }
            }
            return json(['code'=>200,'msg'=>"充提操作成功"]);
        }else{
            if($this->user->parent_id){
                $this->error('无权限,非【创始人】身份');
            }
            $view['uid'] = $input;
            $view['info'] = model('SystemUser')->get(['member_miniapp_id' => $this->member_miniapp_id,'id' => $input]);
            return view()->assign($view);
        }
    }
    /**
     * 客户审核
     */
    public function cashpass(){
        $id = Request::param('id/d');
        if(request()->isAjax()){
            $data = [
                'id'         => $id,
                'ispass'     => Request::param('ispass/d',0),
                'miniapp_id' => $this->member_miniapp_id,
                'realmoney'  => Request::param('realmoney/f')
            ];
            $validate = $this->validate($data,'Bank.cash');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result = AppBank::isPass($data);
            return json($result);
        }else{
            $cash   = BankCash::where(['member_miniapp_id' => $this->member_miniapp_id,'id' => $id])->find();
            if(empty($cash)){
                $this->error('未找到到所属内容');
            }   
            $config   = Config::where(['member_miniapp_id' => $this->member_miniapp_id])->find();
            $view['yescash']  = money($cash->money-$cash->money*$config->tax/100);
            $view['config']   = $config;
            $view['cash']     = $cash;
            $view['bankinfo'] = BankInfo::where(['member_miniapp_id' => $this->member_miniapp_id,'user_id' => $cash->user_id])->find();
            $view['bank']     = AppBank::where(['member_miniapp_id' => $this->member_miniapp_id,'user_id' => $cash->user_id])->find();
            return view()->assign($view);
        }
    }

    /**
     * @param int $types
     * @return \think\response\View
     * @throws \think\exception\DbException
     * 订单统计
     */
    public function statistics(){
        $starttime   = Request::param('starttime/s');
        $endtime     = Request::param('endtime/s');
        $condition[] = ['member_miniapp_id', '=', $this->member_miniapp_id];
        $where[]     = ['member_miniapp_id', '=', $this->member_miniapp_id];
        if ($starttime) {
            $condition[] = ['update_time', '>=', strtotime($starttime)];
            $where[]     = ['paid_time', '>=', strtotime($starttime)];
        }
        if ($endtime) {
            $condition[] = ['update_time', '<=', strtotime($endtime)];
            $where[]     = ['paid_time', '<=', strtotime($endtime)];
        }
        //商城统计
        $view['shop_count'] = Order::where($where)->where(['paid_at' => 1])->count(); //总数量
        $view['shop_no_express'] = Order::where($where)->where(['paid_at' => 1, 'express_status' => 0])->count();    //待发货
        $view['shop_express'] = $view['shop_count'] - $view['shop_no_express'];   //已发货
        $view['shop_payment'] = Order::where($where)->where(['paid_at' => 1])->sum('order_amount');  //总金额
        //活动订单
        $view['event_count'] = SaleOrder::where($where)->where(['paid_at' => 1])->count();  //总数量
        $view['event_no_express'] = SaleOrder::where($where)->where(['paid_at' => 1, 'express_status' => 0])->count();  //待发货
        $view['event_express'] = SaleOrder::where($where)->where(['paid_at' => 0, 'express_status' => 0])->count();  //已发货
        $view['event_payment'] = SaleOrder::where($where)->where(['paid_at' => 1])->sum('order_amount');  //总金额
        //寄卖统计
        $view['user_count'] = SaleUser::where($condition)->count();
        $view['user_pay_count'] = SaleUser::where($condition)->where(['is_rebate' => 1,'is_out' => 0])->count();  //已成交
        $view['user_pay_no_count'] = SaleUser::where($condition)->where(['is_rebate' => 0,'is_out' => 0])->count();  //未上架
        $view['user_money']        = SaleUser::where($condition)->sum('rebate'); //利润价
        //活动商品
        $view['sale_count']        = Sale::where($condition)->count(); //寄卖数量
        $view['sale_pay_count']    = Sale::where($condition)->where(['is_pay' => 1,'is_out' => 0])->count();
        $view['sale_pay_no_count'] = Sale::where($condition)->where(['is_pay' => 0,'is_out' => 0])->count();
        $view['sale_pay']          = Sale::where($condition)->where(['is_pay' => 1,'is_out' => 0])->sum('user_sale_price');
        $view['starttime']         = $starttime;
        $view['endtime']           = $endtime;
        return view()->assign($view);
    }

    
     /**
     * @param int $types
     * @return \think\response\View
     * @throws \think\exception\DbException
     * 财务统计
     */
    public function bankcount(){
        $starttime   = Request::param('starttime/s');
        $endtime     = Request::param('endtime/s');
        $condition[] = ['member_miniapp_id', '=', $this->member_miniapp_id];
        $view['income_money'] = AppBank::where($condition)->sum('income_money');  //累计流水
        $view['due_money']  = AppBank::where($condition)->sum('due_money');    //应付金额
        $view['shop_money'] = AppBank::where($condition)->sum('shop_money');    //购物积分
        $view['lack_money'] = AppBank::where($condition)->sum('lack_money');  //锁定金额
        $where[] = ['member_miniapp_id', '=', $this->member_miniapp_id];
        if ($starttime) {
            $condition[] = ['update_time', '>=', strtotime($starttime)];
            $where[]     = ['paid_time', '>=', strtotime($starttime)];
        }
        if ($endtime) {
            $condition[] = ['update_time', '<=', strtotime($endtime)];
            $where[]     = ['paid_time', '<=', strtotime($endtime)];
        }  
        $view['apply_money'] = BankCash::where(['state' => 0])->sum('money');  //待审
        $view['apply_pass_money']   = BankCash::where(['state' => 1])->sum('money');  //已通过
        //金额
        $view['transaction_money'] = BankBill::where($condition)->sum('money');
        $view['user_money']        = SaleUser::where($condition)->where(['is_rebate' => 0,'is_out' => 0])->sum('user_price'); //用户利润价
        $view['cost']              = Fees::where($condition)->sum('cost'); //成交总价
        $view['fees']              = Fees::where($condition)->sum('fees'); //服务费
        $view['tax']               = Fees::where($condition)->sum('tax'); //平台费
        $view['starttime']         = $starttime;
        $view['endtime']           = $endtime;
        return view()->assign($view);
    }
}