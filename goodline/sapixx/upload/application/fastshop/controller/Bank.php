<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 客户收益管理
 */
namespace app\fastshop\controller;
use app\common\controller\Manage;

class Bank extends Manage
{

    public function initialize()
    {
        parent::initialize();
        if(!model('auth')->getAuth($this->user->id,3)){
            $this->error('无权限,你非【财务管理员】');
        }
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
                $order = 'money desc';
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
        $keyword = trim(input('get.keyword','','htmlspecialchars'));
        if(!empty($keyword)){
            $condition['system_user.phone_uid'] = $keyword;
        }
        $condition['fastshop_bank.member_miniapp_id'] = $this->member_miniapp_id;
        $view['list']     = model('Bank')->view('fastshop_bank','*')->view('system_user','nickname','fastshop_bank.user_id = system_user.id')->where($condition)->order($order)->paginate(20);
        $view['keyword']  = $keyword;
        $view['types']    = $types;
        return view()->assign($view);
    }

     /**
     * 统计日志
     * @return void
     */
    public function counts(){
        $starttime = empty(input('get.starttime')) ? 0 : strtotime(input('get.starttime/s'));
        $endtime   = empty(input('get.endtime')) ? 0 : strtotime(input('get.endtime/s'));
        $whereorder = [];
        $entrust    = [];
        $rebate     = []; 
        if(!empty($starttime) && !empty($endtime)){
            if($starttime > $endtime){
                $this->error('开始日期不能大于结束日期');
            }
            $whereorder[] = ['order_starttime','>=',$starttime];
            $whereorder[] = ['order_starttime','<=',$endtime];
            $entrust[]    = ['create_time','>=',$starttime];
            $entrust[]    = ['create_time','<=',$endtime];  
            $rebate[]     = ['update_time','>=',$starttime];
            $rebate[]     = ['update_time','<=',$endtime];
        }
        $config = model('Config')->get(['member_miniapp_id' => $this->member_miniapp_id]);
        if(empty($config)){
            $proportion = 0;
        }else{
            if($config->reward_types == 1){
                $proportion = ($config->profit+$config->reward_nth+$config->reward_ratio)/100;
            }else{
                $proportion = ($config->profit+$config->reward_ratio+$config->platform_ratio)/100;
            }
        }
        $view['bank']           = model('order')->where($whereorder)->where(['member_miniapp_id' => $this->member_miniapp_id,'paid_at' => 1,'is_point' => 0])->sum('order_amount');
        $view['point']          = model('order')->where($whereorder)->where(['member_miniapp_id' => $this->member_miniapp_id,'paid_at' => 1,'is_point' => 1])->sum('order_amount');
        $view['order_wechat']   = model('order')->where($whereorder)->where(['member_miniapp_id' => $this->member_miniapp_id,'paid_at' => 1,'is_point' => 0])->count();
        $view['order_point']    = model('order')->where($whereorder)->where(['member_miniapp_id' => $this->member_miniapp_id,'paid_at' => 1,'is_point' => 1])->count();      
        $view['due_money']      = model('Bank')->where(['member_miniapp_id' => $this->member_miniapp_id])->sum('due_money');
        $view['shop_money']     = model('Bank')->where(['member_miniapp_id' => $this->member_miniapp_id])->sum('shop_money');
        $view['lack_money']     = model('Bank')->where(['member_miniapp_id' => $this->member_miniapp_id])->sum('lack_money');
        $view['alllack_money']  = model('BankCash')->where(['member_miniapp_id' => $this->member_miniapp_id,'state' => 1])->sum('money');
        $view['entrust']        = model('EntrustList')->where($entrust)->where(['member_miniapp_id' => $this->member_miniapp_id,'is_rebate' => 0])->count();
        $view['entrust_rebate'] = model('EntrustList')->where($rebate)->where(['member_miniapp_id' => $this->member_miniapp_id,'is_rebate' => 1,'is_diy' => 0])->count();
        $view['entrust_diy']    = model('EntrustList')->where($rebate)->where(['member_miniapp_id' => $this->member_miniapp_id,'is_rebate' => 1,'is_diy' => 1])->count();
        $view['entrust_money']  = model('EntrustList')->where($rebate)->where(['member_miniapp_id' => $this->member_miniapp_id,'is_rebate' => 1,'is_diy' => 0])->sum('rebate');
        $view['entrust_diy_money']  = model('EntrustList')->where($rebate)->where(['member_miniapp_id' => $this->member_miniapp_id,'is_rebate' => 1,'is_diy' => 1])->sum('rebate');
        $view['bonus_money']    = ($view['entrust_money']+$view['entrust_diy_money'])*$proportion;
        $view['starttime']      = $starttime;
        $view['endtime']        = $endtime;
        return view()->assign($view);
    }   

    /**
     * 客户收益记录
     * @return void
     */
    public function top(){
        $view['list'] = model('BankAll')->where(['member_miniapp_id' => $this->member_miniapp_id])->order('account desc')->paginate(20);;
        return view()->assign($view);
    } 

    /**
     * 客户收益记录
     * @return void
     */
    public function logs(int $input = 0){
        $starttime = empty(input('get.starttime')) ? 0 : input('get.starttime/s');
        $endtime   = empty(input('get.endtime')) ? 0 : input('get.endtime/s');
        $where = [];
        $where[] = ['user_id','=',$input];
        $condition = [];
        $condition[] = ['user_id','=',$input];
        if(!empty($starttime) && !empty($endtime)){
            if($starttime > $endtime){
                $this->error('开始日期不能大于结束日期');
            }
            $where[] = ['update_time','>=',strtotime($starttime)];
            $where[] = ['update_time','<=',strtotime($endtime)];
            $condition[] = ['paid_time','>=',strtotime($starttime)];
            $condition[] = ['paid_time','<=',strtotime($endtime)];
        }
        $view['receipts']   = model('BankLogs')->where(['member_miniapp_id' => $this->member_miniapp_id])->where($where)->where('money','>',0)->sum('money');  //收入
        $view['expenses']   = model('BankLogs')->where(['member_miniapp_id' => $this->member_miniapp_id])->where($where)->where('money','<',0)->sum('money');  //支付
        $view['balance']    = abs($view['receipts'])-abs($view['expenses']);
        $view['wechat_pay']   = model('Order')->where(['member_miniapp_id' => $this->member_miniapp_id,'paid_at' => 1,'is_point' => 0])->where($condition)->sum('real_amount');
        $view['balance_pay']  = model('Order')->where(['member_miniapp_id' => $this->member_miniapp_id,'paid_at' => 1,'is_point' => 1])->where($condition)->sum('real_amount');
        $view['list']       = model('BankLogs')->where($where)->order('id desc')->paginate(20,false,['query'=>['input' =>$input,'starttime'=>$starttime,'endtime'=>$endtime]]);
        $view['input']      = $input;
        $view['starttime']  = $starttime;
        $view['endtime']    = $endtime;
        return view()->assign($view);
    } 

   /**
     * 客户收益记录
     * @return void
     */
    public function alllogs(){
        $starttime = empty(input('get.starttime')) ? 0 : input('get.starttime/s');
        $endtime   = empty(input('get.endtime')) ? 0 : input('get.endtime/s');
        $where = [];
        if(!empty($starttime) && !empty($endtime)){
            if($starttime > $endtime){
                $this->error('开始日期不能大于结束日期');
            }
            $where[] = ['update_time','>=',strtotime($starttime)];
            $where[] = ['update_time','<=',strtotime($endtime)];
        }
        $view['receipts']   = model('BankLogs')->where(['member_miniapp_id' => $this->member_miniapp_id])->where($where)->where('money','>',0)->sum('money');  //收入
        $view['expenses']   = model('BankLogs')->where(['member_miniapp_id' => $this->member_miniapp_id])->where($where)->where('money','<',0)->sum('money');  //支付
        $view['balance']    = $view['expenses']+$view['receipts'];
        $view['list']       = model('BankLogs')->where(['member_miniapp_id' => $this->member_miniapp_id])->where($where)->order('id desc')->paginate(20,false,['query'=>['starttime'=>$starttime,'endtime'=>$endtime]]);
        $view['starttime']  = $starttime;
        $view['endtime']    = $endtime;
        return view()->assign($view);
    } 

    /**
     * 客户提现
     */
    public function cash($types = 0){ 
        $keyword = trim(input('get.keyword','','htmlspecialchars'));
        if(!empty($keyword)){
            $condition['user.phone_uid'] = $keyword;     
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
        $view['list']    = model('BankCash')->lists($this->member_miniapp_id,$condition)->paginate(20,false,['query' => ['types' => $types]]);;
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
        $condition['state'] = $state;
        $view['list']   = model('BankCash')->lists($this->member_miniapp_id,$condition)->select();
        $view['config'] = model('Config')->get(['member_miniapp_id' => $this->member_miniapp_id]);
        return view()->assign($view);
    }

    /**
     * 充值积分
     * @return void
     */
    public function recharge(int $input){
        if(request()->isAjax()){
            if($this->user->parent_id){
                return json(['code'=>0,'msg'=>'无权限,非【创始人】身份']);
            }
            $data = [
                'user_id'      => input('post.uid/d'),
                'types'        => input('post.types/d'),
                'safepassword' => input('post.safepassword/s'),
                'shop_money'   => input('post.shop_money/f'),
                'due_money'    => input('post.due_money/f'),
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
            if($data['types'] > 0){  //提取
                if($data['due_money'] > 0){
                    model('Bank')->recharge($this->member_miniapp_id,$data['user_id'],-$data['due_money']);
                    model('BankLogs')->add($this->member_miniapp_id,$data['user_id'],-(intval($data['due_money']*100)),'提取应付积分'.-money($data['due_money']));
                }
                if($data['shop_money'] > 0){
                    model('Bank')->rechargeShop($this->member_miniapp_id,$data['user_id'],-$data['shop_money']);
                    model('BankLogs')->add($this->member_miniapp_id,$data['user_id'],-(intval($data['shop_money']*100)),'提取购物积分'.-money($data['shop_money']));
                }
            }else{ //充值
                if($data['due_money'] > 0){
                    model('Bank')->recharge($this->member_miniapp_id,$data['user_id'],$data['due_money']);
                    model('BankLogs')->add($this->member_miniapp_id,$data['user_id'],intval($data['due_money']*100),'奖励应付积分'.money($data['due_money']));
                }
                if($data['shop_money'] > 0){
                    model('Bank')->rechargeShop($this->member_miniapp_id,$data['user_id'],$data['shop_money']);
                    model('BankLogs')->add($this->member_miniapp_id,$data['user_id'],intval($data['shop_money']*100),'奖励购物积分'.money($data['shop_money']));
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
    public function cashpass(int $id){
        if(request()->isAjax()){
            $data = [
                'id'         => input('post.id/d'),
                'ispass'     => input('post.ispass/d'),
                'miniapp_id' => $this->member_miniapp_id,
                'realmoney'  => input('post.realmoney/f')
            ];
            $validate = $this->validate($data,'Bank.cash');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result = model('Bank')->ispass($data);
            return json($result);
        }else{
            $config = model('Config')->get(['member_miniapp_id' => $this->member_miniapp_id]);
            $cash   = model('BankCash')->finds(['fastshop_bank_cash.member_miniapp_id' => $this->member_miniapp_id,'fastshop_bank_cash.id' => $id]);
            if(empty($cash)){
                $this->error('未找到到所属内容');
            }   
            $bankinfo = model('BankInfo')->finds(['ai_fastshop_bank_info.member_miniapp_id' => $this->member_miniapp_id,'ai_fastshop_bank_info.user_id' => $cash->user_id]);
            $yescash = $cash->money-$cash->money*($config->tax/100);
            $view['yescash']  = money($yescash/100);
            $view['config']   = $config;
            $view['cash']     = $cash;
            $view['bankinfo'] = $bankinfo;
            $view['bank']     = model('Bank')->get(['user_id' => $cash->user_id]);
            return view()->assign($view);
        }
    }
}