<?php
namespace app\common\controller\index;

use app\common\controller\IndexBase; 
use app\common\util\Shop AS ShopFun;
use app\member\model\Address AS AddressModel;

/**
 * 下订单
 * @author Administrator
 *
 */
abstract class Order extends IndexBase
{    
    protected $order_model;
    protected $car_model;
    protected $content_model;
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->order_model = get_model_class($dirname,'order');
        $this->car_model = get_model_class($dirname,'car');
        $this->content_model = get_model_class($dirname,'content');
    }
    
    /**
     * 付款之后返回的页面
     * @param string $orders_id 订单ID,可能有多个订单
     * @param number $ispay 是否支付成功
     */
    public function endpay($orders_id = '',$ispay=1){
        if ($ispay==0) {
            $this->error('支付失败',murl('order/index'),[],3);
        }else{
            if($this->order_model->pay($orders_id)){
                $this -> success('支付成功', murl('order/index'));
            }else{
                $this->error('订单还在处理中...', murl('order/index'),[],3);
            }
        }        
    }
    
    /**
     * 检查字段
     * @param array $data
     * @param number $mid
     * @return string|boolean
     */
    protected function check_post_filed(&$data=[]){
        if ($this->request->isPost()){
            foreach(get_field(-1) AS $rs){
                if ($rs['ifmust']==1&&$data[$rs['name']]=='') {
                    return $rs['title'].'不能为空!';
                }
            }
        }
        return true;
    }
    
    /**
     * 创建某个商家的订单前做处理,可以改写 $data 内容
     * @param array $data 订单入库信息
     * @param array $shop_array 多个商品信息
     */
    protected function add_shoper_begin(&$data=[],$shop_array=[]){        
    }
    
    /**
     * 成功创建某个商家的订单后做处理
     * @param number $id 订单ID
     * @param array $shop_array 多个商品信息
     */
    protected function add_shoper_end($id=0,$shop_array=[]){
    }
    
    /**
     * 提交订单,还没进入付款页面
     * 在微信端,就用微信支付,否则就用支付宝支付
     * @return mixed|string
     */
    public function add() {
        
        $listdb = $this->car_model->getList($this->user['uid'],1);  //购物车数据
        
        if($this -> request -> isPost()){
            $data = $this -> request -> post();
            unset($data['pay_status'],$data['pay_money'],$data['fewmoney'],$data['few_ifpay'],$data['agree']);
            
            if (empty($this->get_order_field( current(current($listdb)) ))) {   //不存在主题自定义字段才处理
                $result = $this->check_post_filed($data);
                if ($result!==true) {
                    $this->error($result);
                }
            }
            
            $data = \app\common\field\Post::format_all_field($data,-1); //对一些特殊的自定义字段进行处理,比如多选项,以数组的形式提交的
            $order_ids = [];    //多条订单数据,多个商家就多个订单
            $car_ids = [];        //购买车里的id数据
            $car_db = [];        //购买车里的详细数据            
            
            $total_money = 0;   //需要支付的总金额
            foreach ($listdb AS $uid=>$shop_array){     //取每一个商家的数据生成一个订单,不能同家不能混在同一个订单
                $data['shop_uid'] = $uid;   //店主UID
                $_shop = [];
                $money = 0;     //每一个商家的所有货款
                foreach ($shop_array AS $rs){   //某个商家的多个商品
                    $_shop[] = $rs['_car_']['shopid'] . '-' . $rs['_car_']['num']  . '-' . $rs['_car_']['type1'] . '-' .$rs['_car_']['type2'] . '-' .$rs['_car_']['type3'];
                    $money += ShopFun::get_price($rs,$rs['_car_']['type1']-1)*$rs['_car_']['num'];
                    $car_ids[] = $rs['_car_']['id'];
                    $car_db[] = $rs['_car_'];
                }
                $data['totalmoney'] = $money;   //订单金额,实际需要支付的可能会少一点
                
                
                
                $cid = $data['cid'][$uid];      //代金券ID
                if ($cid>0) {
                    $coupon = fun('Coupon@get_list',$this->user['uid'],$money,$uid);    //通用券处理
                    if($coupon[$cid]){
                        $money -= $coupon[$cid]['quan_money'];    //抵扣券
                        if ($money<0) {
                            $money = 0;
                        }
                    }else{
                        $check_coupon = false;
                        foreach ($shop_array AS $rs){  //非通用券, 需要单独的对每一个商品进行判断,不能像上面的通用券批量处理
                            if($rs['coupon_tag']){      //存在非通用券标志
                                $c_tag = $rs['coupon_tag'];
                            }else{
                                $c_tag = config('system_dirname')."-".$rs['id'];
                            }
                            $_array = fun('Coupon@get_list',$this->user['uid'],fun('shop@get_price',$rs,$rs['_car_']['type1']-1),$uid,$c_tag);
                            if($_array[$cid]){
                                $money -= $_array[$cid]['quan_money'];    //抵扣券
                                if ($money<0) {
                                    $money = 0;
                                }
                                $check_coupon = true;
                                break;
                            }
                        }
                        if($check_coupon==false){
                            $cid = 0;
                        }                        
                    }
                }
                
                $data['shop'] = implode(',', $_shop);
                $data['order_sn'] = 's'.date('ymdHis').rands(3);      //订单号
                $data['pay_money'] = $money;    //需要支付的金额
                $total_money += $money;
                if (!empty($this -> validate)) {// 验证表单                    
                    $result = $this -> validate($data, $this -> validate);
                    if (true !== $result) $this -> error($result);
                }
                $data['uid'] = $this -> user['uid'];
                $data['create_time'] = time();
                $this->add_shoper_begin($data,$shop_array);     //扩展使用
                if ($result = $this->order_model->create($data)) {
                    $order_ids[] = $result->id;
                    $msg = '';
                    if($cid>0){
                        fun('coupon@take_off',$cid);    //标志优惠券已用
                        $msg = '，使用了一张面额 '.$coupon[$cid]['quan_money'].' 元的代金券，';
                    }
                    $this->send_msg($uid,$result->id,$shop_array,$msg);
                    $this->add_shoper_end($result->id,$shop_array);     //扩展使用
                }
            }
            
            $this->end_add($order_ids,$car_ids,$car_db);     //扩展使用
            
            $this->add_address($data);  //添加地址
            
            if (!empty($order_ids)) {
                $url = murl('order/index');
                if($total_money<0.01){
                    $this->order_model->pay(implode(',', $order_ids));
                }elseif ($data['ifolpay']==1 && $total_money>0) {
                    $order_ids = implode(',', $order_ids);
                    $url = post_olpay([
                                    'money'=>$total_money,
                                    //'money'=>'0.01',    //调试
                                    'return_url'=>url('endpay',['orders_id'=>$order_ids]),
                                    'banktype'=>'',//in_weixin() ? 'weixin' : 'alipay' , //在微信端,就用微信支付,否则就用支付宝支付
                                    'numcode'=>$data['order_sn'],
                                    'callback_class'=>mymd5('app\\'.config('system_dirname').'\\model\\Order@pay@'.$order_ids),
                            ]);
                }
                $this -> success('订单提交成功', $url,[],1);
            } else {
                $this -> error('订单提交失败');
            }
        }
        
        $info = [];
        $money_array = [];  //需要支付给每个商家的金额
        $total_money = 0;   //需要支付给所有商家的总金额
        foreach ($listdb AS $uid=>$shop_array){
            $money = 0;
            foreach ($shop_array AS $key=>$rs){   //某个商家的多个商品
                $info = $rs;
                $money += ShopFun::get_price($rs,$rs['_car_']['type1']-1)*$rs['_car_']['num'];
                if(!$rs['coupon_tag']){ //没有设置代金券标志的情况
                    $rs['coupon_tag'] = config('system_dirname')."-".$rs['id'];
                    $listdb[$uid][$key] = $rs;
                }
            }
            $total_money += $money;
            $money_array[$uid] = $money;
        }
        
        $this->assign('total_money',$total_money);
        $this->assign('money_array',$money_array);
        $this->assign('listdb',$listdb);
        $this->assign('shopdb',current(current($listdb))); //第一个商品信息
        
        $address = AddressModel::where('uid',$this->user['uid'])->order('often desc,id desc')->column(true);
        $this->assign('address',$address);
        
        $this->assign('f_array',$this->get_order_field($info)); //用户自定义表单字段,只适合于订单中只有一个商品的情况
        
        return $this ->fetch();
    }
    
    /**
     * 给标签调用
     * @param array $cfg
     * @return array
     */
    public function label($tag_array=[]){
        $cfg = unserialize($tag_array['cfg']);
        $info = $this->content_model->getInfoByid(intval($cfg['ids']));
        return [
            'info'=>$info,
            'f_array'=>$info ? $this->get_order_field($info) : [],
        ];
    }
    
    /**
     * 用户下单后,给商家发信息
     * @param number $shop_uid 商家UID
     * @param number $order_id 订单ID
     * @param array $shop 商品信息
     * @param string $msg 额外消息
     */
    protected function send_msg($shop_uid=0,$order_id=0,$shop=[],$msg=''){
        $shops = [];
        foreach($shop AS $rs){
            $shops[] = $rs['title'];
        }
        $title = '有客户 '.$this->user['username'].' 下单了 '.$msg.' ,订购的是:'.implode('、',$shops);
        $content = $title.'，<a href="'.get_url( murl('kehu_order/show',['id'=>$order_id]) ).'">点击查看详情</a>';
        if ( !isset($this->webdb['post_order_msg_hy'])||$this->webdb['post_order_msg_hy'] ) {
            send_msg($shop_uid,$title,$content);
        }
        if ( !isset($this->webdb['post_order_wx_hy'])||$this->webdb['post_order_wx_hy'] ) {
            send_wx_msg($shop_uid, $content);
        }
        if ( $this->webdb['post_order_sms_hy'] ) {
            send_sms($shop_uid, $title);
        }
    }
    
    /**
     * 添加地址
     * @param array $data
     */
    protected function add_address($data=[]){
        if (!isset($data['address_id']) || $data['address_id']) {
            return ;
        }
        $often = 1;
        if ( AddressModel::where('uid',$this->user['uid'])->where('often',1)->find() ) {
            $often = 0;
        }
        $array = [
                'uid'=>$this->user['uid'],
                'user'=>$data['linkman'],
                'telphone'=>$data['telphone'],
                'address'=>$data['address'],
                'often'=>$often,
        ];
        AddressModel::create($array);
    }
    
    /**
     * 成功提交订单,后续的扩展操作
     * @param array $order_ids 多条订单信息
     * @param array $car_ids 购物车ID数组
     * @param array $car_db 购物车详细信息数组数据
     */
    protected function end_add($order_ids=[],$car_ids=[],$car_db=[]){
        $this->car_model->destroy($car_ids);    //购买成功后,就把购买车的数据清掉
    }
    
    /**
     * 用户提交的表单自定义字段
     * @param array $info
     * @return void|string[][]|unknown[][]|mixed[][]
     */
    protected function get_order_field($info=[]){
        if (empty($info)||empty($info['order_filed'])) {
            return ;
        }
        $array = json_decode($info['order_filed'],true);
        if (empty($array)){
            return ;
        }
        $data = [];
        foreach($array AS $key=>$rs){
            if ($rs['type']=='select' || $rs['type']=='checkbox') {
                $detail = explode("\n",$rs['options']);
                $opt = [];
                foreach($detail AS $value){
                    $opt[$value] = $value;
                }
            }else{
                $opt='';
            }
            $data[] = [
                'type'=>$rs['type'],
                'name'=>'order_field_'.$key,
                'title'=>$rs['title'],
                'about'=>'',
                'options'=>$opt,
                'ifmust'=>$rs['must'],
                'customize'=>'customize',
            ];
        }
        return $data;
    }
    
}
