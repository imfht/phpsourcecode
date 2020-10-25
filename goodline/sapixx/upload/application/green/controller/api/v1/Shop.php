<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商城管理
 */
namespace app\green\controller\api\v1;
use app\green\controller\api\Base;
use app\common\facade\WechatPay;
use app\common\model\SystemUserAddress;
use app\green\model\GreenShop;
use app\green\model\GreenUser;
use app\green\model\GreenOrder;
use think\facade\Request;

class Shop extends Base{
   
    /**
     * 读取某个分类下的商品列表
     * @param integer api 读取ID
     * @return json
     */
    public function list(){
        $data['page']    = Request::param('page',0);
        $data['sign']    = Request::param('sign');
        $rel = $this->apiSign($data);
        if($rel['code'] == 200){
            $condition[] = ['member_miniapp_id','=',$this->miniapp_id];
            $condition[] = ['is_sale','=',1];
            $condition[] = ['is_del','=',0];
            $result = GreenShop::where($condition)->field('id,name,points,note,img')->order('sort desc,id desc')->paginate(20,true)->toArray();
            if(empty($result['data'])){
                return enjson(204,'没有内容了');
            }
            return enjson(200,'成功',$result['data']);
        }
        return enjson($rel['code'],'签名验证失败');
    }

    
    /**
     * 读取单个商品信息
     * @param integer $id 商品ID
     * @return void
     */
    public function item(int $id){
        $param['id']   = Request::param('id',0);
        $param['sign'] = Request::param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] == 200){
            //查找商品SPU
            $item = GreenShop::where(['is_sale'=>1,'is_del' => 0,'id' => $id,'member_miniapp_id' => $this->miniapp_id])->field('id,name,note,points,img,imgs,content')->find();
            if(empty($item)){
                return json(['code'=>403,'msg'=>'没有内容']);
            }
            $data = [];
            $data['content']      = str_replace('<img', '<img class="img" style="max-width:100%;height:auto"',dehtml($item->content));
            $data['name']         = $item->name;
            $data['note']         = $item->note;
            $data['points']       = $item->points;
            $data['img']          = $item->img.'?x-oss-process = style/500';
            $data['imgs']         = json_decode($item->imgs,true);
            return enjson(200,'成功',$data);
        }
        return enjson(204,'签名错误');
    }


    //立即兑换
    public function dopay(){
        $this->isUserAuth();
        if (request()->isPost()) {
            $param['shop_id']  = $this->request->param('shop_id/d');
            $param['message']  = $this->request->param('message','');
            $param['address']  = $this->request->param('address/d');
            $param['ucode']    = $this->request->param('ucode','');
            $param['signkey']  = $this->request->param('signkey');
            $param['sign']     = $this->request->param('sign');
            $sign = $this->apiSign($param);
            if($sign['code'] != 200){
                return enjson($sign['code'],'签名验证失败');
            }
            //读取发货地址
            $address = SystemUserAddress::where(['user_id'=>$this->user->id,'id' =>$param['address']])->find();
            if(empty($address)){
                return enjson(403,'请选择收货地址');
            }
            //判断是否已下架
            $item = GreenShop::where(['id' => $param['shop_id'],'is_sale' => 1,'is_del' => 0])->field('id,points,name,note,img,imgs,content')->find();
            if(empty($item)){
                return enjson(403,'活动已下架');
            }
            if(time() > $item->end_time){
                return enjson(403,'活动已结束,还可以参加其它活动.');
            }
            //判断积分是否足够
            $bank = GreenUser::where(['member_miniapp_id'=>$this->miniapp_id,'uid' =>  $this->user->id])->find();
            if(empty($bank) || $bank->points < $item->points){
                return enjson(403,'积分不足够支付');
            }
            //读取订单
            $order = GreenOrder::where(['member_miniapp_id' => $this->miniapp_id,'user_id' =>$this->user->id,'shop_id' => $param['shop_id'],'is_del' => 0])->field('points,paid_at,order_no')->find();
            if(empty($order)){
                $order_no = $this->user->invite_code.order_no();
                $points   = $item->points;
                $is_new_order = true;
            }else{
                if($order->points < $item->points){
                    $order->is_del = 1;
                    $order->save();
                    return enjson(403,'订单已失效,请重新下单');
                }
                $order_no = $order->order_no;
                $points   = $order->points;
                $is_new_order = false;
            }
            //唤醒微信支付参数
            $payparm = [
                'openid'     => $this->user->miniapp_uid,
                'miniapp_id' => $this->miniapp_id,
                'name'       => $item->name,
                'order_no'   => $order_no,
                'total_fee'  => 1,
                'notify_url' => api(1,'green/notify/shop',$this->miniapp_id),
            ];
            $ispay = WechatPay::orderPay($payparm);
            if ($ispay['code'] == 0) {
                return enjson(403,$ispay['msg']);
            }
            //判断是否新订单
            $rel = true;
            if($is_new_order){
                $param['member_miniapp_id'] = $this->miniapp_id;
                $param['points']            = $points;
                $param['user_id']           = $this->user->id;
                $param['shop_cache']        = $item->toJson();
                $param['express_name']      = $address['name'];
                $param['express_phone']     = $address['telphone'];
                $param['express_address']   = $address['address'];
                $rel = GreenOrder::insertOrder($param,$order_no);
            }
            if(empty($rel)){
                return enjson(204,'购买商品失败');
            }
            return enjson(200,'成功',$ispay['data']);
        }
    }

    //重新支付
    public function reDopay(){
        $this->isUserAuth();
        if (request()->isPost()) {
            $param['order_no'] = $this->request->param('order_no/s','');
            $param['signkey']  = $this->request->param('signkey');
            $param['sign']     = $this->request->param('sign');
            $sign = $this->apiSign($param);
            if($sign['code'] != 200){
                return enjson($sign['code'],'签名验证失败');
            } 
            //读取订单
            $order = GreenOrder::where(['member_miniapp_id' => $this->miniapp_id,'user_id' =>$this->user->id,'order_no' => $param['order_no'],'paid_at'=>0,'is_del' => 0])->field('points,paid_at,order_no,shop_id')->find();
            if(empty($order)){
                return enjson(403,'订单已失效,请重新下单');
            }
            //判断是否已下架
            $item = GreenShop::where(['id' => $order->shop_id,'is_sale' => 1,'is_del' => 0])->field('id,points,name,note,img,imgs,content')->find();
            if(empty($item)){
                return enjson(403,'商品已经下架');
            }
            if($order->points < $item->points){
                $order->is_del = 1;
                $order->save();
                return enjson(403,'订单已失效,请重新下单');
            }
            //判断积分是否足够
            $bank = GreenUser::where(['member_miniapp_id'=>$this->miniapp_id,'uid' =>  $this->user->id])->find();
            if($bank->points < $item->points){
                return enjson(403,'积分不足够兑换');
            }
            //唤醒微信支付参数
            $payparm = [
                'openid'     => $this->user->miniapp_uid,
                'miniapp_id' => $this->miniapp_id,
                'name'       => $item->name,
                'order_no'   => $order->order_no,
                'total_fee'  => 1,
                'notify_url' => api(1,'green/notify/shop',$this->miniapp_id),
            ];
            $ispay = WechatPay::orderPay($payparm);
            if ($ispay['code'] == 0) {
                return enjson(403,$ispay['msg']);
            }
            return enjson(200,'成功',$ispay['data']);
        }
    }

    //立即兑换
    public function order(){
        $this->isUserAuth();
        $param['active']  = $this->request->param('active/d',0);
        $param['signkey'] = $this->request->param('signkey');
        $param['sign']    = $this->request->param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson($rel['code'],'签名验证失败');
        }
        $condition['user_id'] = $this->user->id;
        $condition['is_del']  = 0;
        switch ($param['active']) {
            case 1:
                $condition['paid_at'] = 0;
                break;
            case 2:
                $condition['paid_at'] = 1;
                $condition['express_status'] = 0;
                break;
            case 3:
                $condition['paid_at'] = 1;
                $condition['express_status'] = 1;
                break;
        }
        $order = GreenOrder::field('id,order_no,paid_at,paid_time,points,message,shop_id,express_status,shop_cache,amount,create_time,is_del')->where($condition)->paginate(10);
        if($order->isEmpty()){
            return enjson(204);
        }
        $data = [];
        $ids  = [];
        foreach ($order as $key => $value) {
            $data[$key] = $value;
            $data[$key]['shop_cache'] = json_decode($value['shop_cache']);
            if($value['paid_at']){
                $data[$key]['status_text'] = $value['express_status']?'已发货':'待发货';
                $data[$key]['end_paytime'] = 0;
            }else{
                $data[$key]['status_text'] = '待支付';
                $data[$key]['end_paytime'] = (($value['create_time'] + 60 * 10) - time()) * 1000;
            }
            if($value['paid_at'] == 0 && $data[$key]['end_paytime'] < 0){
                $ids[] = $value['id'];
                unset($data[$key]);
            }
        }
        //过期了更改了状态
        if(!empty($ids)){
            GreenOrder::where(['id' => $ids])->update(['is_del' => 1]);
        }
        if(empty($data)){
            return enjson(204);
        }
        return enjson(200,'成功',array_values($data));
    }

      
    /**
     * @param int $store_id
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 扫码店铺待核销订单列表
     */
    public function getOrder(){
        $this->isUserAuth();
        $param['id']      = $this->request->param('id/d',0);
        $param['signkey'] = $this->request->param('signkey');
        $param['sign']    = $this->request->param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson($rel['code'],'签名验证失败');
        }
        $condition['user_id'] = $this->user->id;
        $condition['id']      = $param['id'];
        $order = GreenOrder::where($condition)->find();
        if(empty($order)){
            return enjson(204);
        }
        $data = $order->toArray();
        $data['shop_cache'] = json_decode($order->shop_cache,true);
        if($data['paid_at']){
            $data['end_paytime'] = 0;
        }else{
            $data['end_paytime'] = (($order->create_time + 60 * 10) - time()) * 1000;
        }
        if($order->paid_at){
            $data['status_text'] = $order->express_status?'已发货':'待发货';
            $data['end_paytime'] = 0;
        }else{
            $data['status_text'] = '待支付';
            $data['end_paytime'] = (($order->create_time + 60 * 10) - time()) * 1000;
        }
        if($order->paid_at == 0 && $data['end_paytime'] < 0){
            $order->is_del = 1;
            $order->save();
        }
        $data['paid_time']   = date('Y-m-d H:i:s',$order->paid_time);
        $data['create_time'] = date('Y-m-d H:i:s',$order->create_time);
        return enjson(200,'成功',$data);
    }
    
}