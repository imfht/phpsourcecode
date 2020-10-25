<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 小程序公共API服务
 */
namespace app\popupshop\controller\api\v1;
use app\popupshop\controller\api\Base;
use app\popupshop\model\SaleOrder;
use app\popupshop\model\SaleOrderCache;
use app\popupshop\model\SaleUser;
use app\popupshop\model\Bank;
use app\popupshop\model\BankBill;
use think\facade\Request;

class User extends Base{
    
    public function initialize() {
        parent::initialize();
        if(!$this->user){
            exit(json_encode(['code'=>401,'msg'=>'用户认证失败']));
        }
    }


    /**
     * 获取用户的套餐订单
     */
    public function saleOrder(){
        $param['page']  = Request::param('page/d');
        $param['types'] = Request::param('types/d');
        $param['sign']  = Request::param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $condition = [];
        $condition['member_miniapp_id'] = $this->miniapp_id;
        $condition['user_id']           = $this->user->id;
        $condition['is_del']            = 0;
        switch ($param['types']) {
            case 1:
                $condition['paid_at']    = 1;
                $condition['is_entrust'] = 0;
                break;
            case 2:
                $condition['paid_at']    = 1;
                $condition['is_entrust'] = 1;
                $condition['express_status'] = 0;
                break;
            case 3:
                $condition['paid_at']        = 1;
                $condition['is_entrust']     = 1;
                $condition['express_status'] = 1;
            default:
                $condition['paid_at']    = 1;
                break;
        }
        $order = SaleOrder::where($condition)->order('id desc')->paginate(10);
        if($order->isEmpty()){
            return enjson(204,'空内容');
        }
        $data = [];
        foreach ($order as $key => $value) {
            $data[$key] = SaleOrder::order_data($value);
        }
        return enjson(200,'成功',$data);
    }

    /**
     * 获取用户的订单
     */
    public function saleOrderReview(){
        $param['order_no'] = Request::param('order_no');
        $param['sign']     = Request::param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $validate = $this->validate($param,'Sale.saleOrderReview');
        if(true !== $validate){
            return enjson(403,$validate);
        }
        $condition = [];
        $condition['member_miniapp_id'] = $this->miniapp_id;
        $condition['user_id']           = $this->user->id;
        $condition['is_del']            = 0;
        $condition['order_no']          = $param['order_no'];
        $order = SaleOrder::where($condition)->find();
        if(empty($order)){
            return enjson(204,'空内容');
        }
        return enjson(200,'成功',SaleOrder::order_data($order));
    }

    /**
     * 申请退货
     */
    public function orderOut(){
        if (request()->isPost()) {
            $param['order_no'] = Request::param('order_no');
            $param['sign']     = Request::param('sign');
            $rel = $this->apiSign($param);
            if($rel['code'] != 200){
                return enjson(204,'签名失败');
            }
            $validate = $this->validate($param,'Sale.saleOrderReview');
            if(true !== $validate){
                return enjson(403,$validate);
            }
            $condition = [];
            $condition['member_miniapp_id'] = $this->miniapp_id;
            $condition['user_id']           = $this->user->id;
            $condition['paid_at']           = 1; //支付
            $condition['is_del']            = 0; //删除
            $condition['is_entrust']        = 0; //确认委托
            $condition['is_out']            = 0; //退货
            $condition['is_settle']         = 0; //结算
            $condition['order_no']          = $param['order_no'];
            $order = SaleOrder::where($condition)->find();
            if(empty($order)){
                return enjson(403,'当前订单不支持退货');
            }
            foreach ($order as $key => $value) {
                # code...
            }
            //更新订单状态
            $order->is_out = 1;
            $order->status = 1;
            $order->save();
            SaleOrderCache::where(['order_no' => $param['order_no']])->update(['is_out' => 1,'is_entrust' => 0]);
            //把资金退回账户
            $amount = $order->order_amount*6/1000;
            $fees = $amount < 0.01 ? 0.01 : $amount;
            $order_amount = (float)money($order->order_amount-$fees);
            Bank::setDueMoney($this->miniapp_id,$order->user_id,$order_amount);
            BankBill::add($this->miniapp_id,$order->user_id,$order_amount,'退货,扣除交易手续费￥'.$fees,0,$order->order_no);
            return enjson(200,'退货成功');
        }
    }
}