<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 订单逻辑
 */
namespace app\fastshop\widget;
use think\facade\Cookie;

class Order{

      /**
     * 保存订单
     * @param  array   $item [计算参数]
     * @return array         商品价格信息
     */
    public function saveOrder(array $data){
        $order['order_no']          = order_no();
        $order['user_id']           = $data['user_id'];
        $order['sale_id']           = $data['sale_id'];
        $order['member_miniapp_id'] = $data['member_miniapp_id'];
        $order['payment_id']        = $data['payment_id'];
        $order['real_amount']       = $data['real_amount'];
        $order['real_freight']      = $data['real_freight'];
        $order['order_amount']      = $data['order_amount'];
        $order['express_name']      = $data['express_name'];
        $order['express_phone']     = $data['express_phone'];
        $order['express_address']   = $data['express_address'];
        $order['paid_at']           = $data['paid_at'];
        $order['is_fusion']         = $data['is_fusion']; //订单类型
        if($data['paid_at'] == 1){
            $order['paid_time']     = time();
            $order['paid_no']       = $order['order_no'];
        }
        $order['status']            = 0;
        $order['is_del']            = 0;
        $order['express_status']    = 0;
        $order['order_starttime']   = time();
        $rel = model('fastshop/Order')->insertGetId($order);
        return empty($rel) ? false : $order['order_no'];
    }

    /**
     * 计算运费多少钱
     * @param  array   $item [计算参数]
     * @return array         商品价格信息
     */
    public function realAmount(array $item,$miniapp_id){
        $fare  = model('fastshop/fare')->get(['member_miniapp_id' => $miniapp_id]);
        $real_amount  = $item['sale_price'];   //商品总价
        $real_freight = 0;   //运费总价
        $weight = $item['weight'];  //商品重量
        if($weight <= $fare['first_weight'] || 0 == $fare['second_weight']){
            $real_freight  = $fare['first_price'];
        }else{
            $weight = $weight - $fare['second_weight'];
            $real_freight  = $fare['first_price'] + ceil($weight/$fare['second_weight']) * $fare['second_price'];
        }            
        $data['real_amount']  = money($real_amount);   //商品价格
        $data['real_freight'] = money($real_freight);  //运费
        $data['order_amount'] = money($real_freight+$real_amount);  //商品总价+运费
        return $data;
    }

    /**
     * 读取订单的赠品价格和所属产品图片数据
     */
    public function gift(array $gift){
        $gift_id = array_column($gift,'item_id');
        $list = model('fastshop/item')->field('id,name,img,imgs,content,weight')->whereIn('id',$gift_id)->select()->toArray();
        $gift_data = [];
        foreach ($gift as $k => $v) {
            $gift_value['item_id']       = $v['item_id'];
            $gift_value['sale_price']    = money($v['sale_price']/100);
            $gift_value['market_price']  = money($v['market_price']/100);
            foreach ($list as $value) {
                if ($v['item_id'] == $value['id']) {
                    $gift_data[$k] = array_merge($value,$gift_value);
                    $gift_data[$k]['img']     = $value['img']."?x-oss-process = style/auto";
                    $gift_data[$k]['imgs']    = json_decode($value['imgs'],true);
                    $gift_data[$k]['content'] = $value['content'];
                }
            }
        }
        return $gift_data; 
    }

    /**
     * 指定成交(后台)
     * @param [type] $array
     * @param [type] $uid
     * @return void
     */
    public function userGift($array,$uid){
        $where_entrust['member_miniapp_id'] = $array['member_miniapp_id'];
        $where_entrust['item_id']           = $array['item_id'];
        $where_entrust['is_rebate']         = 0;
        $where_entrust['is_under']          = 0;  //1下架
        return model('EntrustList')->where($where_entrust)->where(['user_id' => $uid])->find(); 
    }

    /**
     * 交易顺序
     */
    public function goodGift($array,$uid,$config){
        $where_entrust['member_miniapp_id'] = $array['member_miniapp_id'];
        $where_entrust['item_id']           = $array['item_id'];
        $where_entrust['is_rebate']         = 0;  //未成交
        $where_entrust['is_under']          = 0;  //1下架
        /**
         * is_priority 0 市场优先   1客户优先（已关闭配置）
         */
        $entrust = [];
        if($config['is_priority'] == 1){
            $level = model('SystemUserLevel')->field('parent_id')->where(['user_id' => $uid,'level' => 1])->find();
            if (!empty($level)) {
                $entrust = model('EntrustList')->where($where_entrust)->where(['user_id' => $level->parent_id])->order('id asc')->find(); 
            }
        }
        //市场优先
        if (empty($entrust)) {
            $entrust = model('EntrustList')->where($where_entrust)->order('id asc')->find();
        }
        return $entrust;
    }
    

    /**
     * 计算商品托买利润
     * @param integer $miniapp_id  //来自哪个小程序
     * @param [type]  $order_no    //订单号(当订单号是0的时候本$sale_id参数作为商品ID)
     * @param integer $sale_id     //来自哪个活动(当订单号是0的时候本参数作为商品ID)
     * @return void   //成功返回订单委托金额
     */
    public function rebate(int $miniapp_id,$order_no,int $item_id,int $uid,$config){
        $item = [];
        if(empty($order_no)){
            $item[] = $item_id;
        }else{
            $order  = model('OrderCache')->field('item_id,order_no,entrust,fusion_state,gift')->where(['order_no' => $order_no])->find();
            if(empty($order)){
                return;
            }
            if($order->order->is_fusion){ //买二送一
                $item = array_column(json_decode($order->gift,true),'item_id');
            }else{
                $item[] = $order->item_id;
            }
        }
        //计算利润
        foreach ($item as $id) {
            $where_entrust['member_miniapp_id'] = $miniapp_id;
            $where_entrust['item_id']           = $id;
            $where_entrust['is_rebate']         = 0;
            //成交判断
            if(empty($order_no) && $uid > 0){
                $entrust = self::userGift($where_entrust,$uid);
            }else{
                $entrust = self::goodGift($where_entrust,$uid,$config);
            }
            $is_diy = empty($order_no) ? 1 : 0;
            if (!empty($entrust)){
                //读取委托产品（聚变还是裂变）本金（分）
                $order_amount = $entrust->is_fusion ? $entrust->entrust_price : $entrust->entrust_price/2;  
                $cash_fee = abs($entrust['entrust_price']*($config->profit/100));  //计算利润
                $income = $cash_fee + $order_amount; //利润+本金
                //增加购物积分和应付积分
                if($order_amount > 0){
                    $shop_money = intval($cash_fee*($config->shopping/100));   //购物积分（利润的百分之多少）
                    $due_money  = intval($cash_fee-$shop_money+$order_amount); //应付(本金+利润)
                    model('Bank')->due_up($miniapp_id,$entrust->user_id,$due_money,$shop_money);
                    model('Bank')->isProfit($entrust->user_id,money($cash_fee/100));  //增加净收入(需要传入元)
                    $income = $cash_fee + $order_amount; //委托销售总收益(分)
                    model('BankLogs')->add($miniapp_id,$entrust->user_id,$income,money($income/100).'成交积分');
                }
                model('EntrustList')->where(['id' => $entrust->id])->update(['is_diy'=> $is_diy,'is_rebate'=>1,'is_under'=>1,'update_time'=>time(),'rebate' => $income]);
                model('entrust')->where(['item_id' => $item_id])->setDec('gite_count',1);//库存减1
            }
        }
        return empty($item_id) ? false : true;
    }

    /**
     * 抢购积分支付
     * @param integer $miniapp_id   来源小程序
     * @param integer $uid  用户ID
     * @param float $cash_fee  (分)
     * @param [type] $config  系统配置
     * @return void
     */
    public function pointPay(int $miniapp_id,int $uid,float $cash_fee,$config){
        $payment_point = $config->payment_point/100;
        $point = intval(($cash_fee*$payment_point)*100);  //积分支付
        $info  = model('Bank')->where(['member_miniapp_id' => $miniapp_id,'user_id' => $uid])->find();
        if(empty($info)){
            return;
        }
        if($config->payment_type == 1){
            $info->due_money   =  ['dec',$point];
            $info->money       =  ['dec',$point];
        }else{
            $info->shop_money  =  ['dec',$point];
            $info->money       =  ['dec',$point];
        }
        $info->update_time = time();
        return $info->save();
    }

    /**
     * 商城积分支付
     * @param integer $miniapp_id   来源小程序
     * @param integer $uid  用户ID
     * @param float $cash_fee  (分)
     * @param [type] $config  系统配置
     * @return void
     */
    public function shopPointPay(int $miniapp_id,int $uid,float $cash_fee){
        $config  = model('Config')->get(['member_miniapp_id' => $miniapp_id]);
        $payment_point = $config['payment_point_shop']/100;
        $point = intval(($cash_fee*$payment_point)*100);  //积分支付
        $info = model('Bank')->where(['member_miniapp_id'=>$miniapp_id,'user_id' => $uid])->find();
        if(empty($info)){
            return;
        }
        if($config['payment_type_shop'] == 1){
            $info->due_money   =  ['dec',$point];
            $info->money       =  ['dec',$point];
        }else{
            $info->shop_money  =  ['dec',$point];
            $info->money       =  ['dec',$point];
        }
        $info->update_time = time();
        return $info->save();
    }
}