<?php
namespace app\common\controller\index;

use app\common\controller\IndexBase; 
use app\common\traits\AddEditList;

/**
 * 商城购物车
 * @author Administrator
 *
 */
abstract class Car extends IndexBase
{
    use AddEditList;
    protected $model;
    protected $topic_model;
    protected $form_items = [
            ['text', 'linkman', '联系人'],
            ['text', 'telphone', '联系电话'],
            //['image', 'picurl', '分享图片'],
            ['radio', 'ifolpay', '支付类型', '', [1 => '在线付款', 0 => '货到付款'], 1],
    ];
    
    public function edit(){
        die('出错了!');
    }
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'car');
        $this->topic_model = get_model_class($dirname,'content');
    }
    
    protected function check_status($shopid=0,$num=1,$type1='',$type2='',$type3=''){
        if (!$shopid) {
            return '商品id不存在';
        }
        if (!$this->user) {
            return '你还没登录';
        }
        $shop = $this->topic_model->getInfoByid($shopid);
        if($shop['end_time']){
            if (!is_numeric($shop['end_time'])) {
                $shop['end_time'] = strtotime($shop['end_time']);
            }
            if ($shop['end_time']<time()) {
                return '已经过期了!';
            }
        }
        if($shop['begin_time']){
            if (!is_numeric($shop['begin_time'])) {
                $shop['begin_time'] = strtotime($shop['begin_time']);
            }
            if ($shop['begin_time']>time()) {
                return '活动还没有开始!';
            }
        }
        
        if( $type1>0 && fun('shop@get_num',$shop,$type1-1)!==null ){
            if(fun('shop@get_num',$shop,$type1-1)-$num<0){
                return $num>1?'该类型库存不足!':'该类型没库存了';
            }
        }elseif(isset($shop['num'])){
            if ($shop['num']<1) {
                return '很抱歉,库存为0,无法购买!';
            }elseif ($num>$shop['num']) {
                return '购买数量,不能超过库存量!';
            }
        }
        
        return true;
    }
    
    /**
     * 加入购物车 如果没有就新增加,如果有的话,就进行修改 功能强于 change_num() 方法
     */
    public function add($shopid=0,$num=1,$type1='',$type2='',$type3=''){
        $result = $this->check_status($shopid,$num,$type1,$type2,$type3);
        if ($result!==true) {
            return $this->err_js($result);
        }
        if(config('car_one')===true){   //购物车只保留一件商品
            $this -> model -> where(['shopid'=>['<>',$shopid],'uid'=>$this->user['uid']]) ->delete();
        }
        $info = $this -> model -> where(['shopid'=>$shopid,'uid'=>$this->user['uid']]) -> find() ;
        if(!$info){    //购物车没有的话,就直接加进去
            $num<1 && $num=1;
            $data = [
                    'shopid'=>$shopid,
                    'uid'=>$this->user['uid'],
                    'type1'=>$type1,
                    'type2'=>$type2,
                    'type3'=>$type3,
                    'num'=>$num,
            ];
            if ($this -> model -> create($data)) {
                return $this->ok_js();
            } else {
                return $this->err_js('插入数据失败');
            }
        }else{  //购物车里边存在的,就进行修改
             $data = [
                        'id'=>$info['id'],
                        'type1'=>$type1,
                        'type2'=>$type2,
                        'type3'=>$type3,
                        'num'=>$num,
             ];
            if ($this -> model -> update($data)) {
                return $this->ok_js();
            } else {
                return $this->err_js('插入数据失败');
            }
        }
    }
    
    /**
     * 删除购物车里的商品
     * @param number $shopid
     * @return unknown
     */
    public function delete($shopid=0){        
        if (!$this->user) {
            return $this->err_js('你还没登录');
        }
        $info = $this -> model -> where(['shopid'=>$shopid,'uid'=>$this->user['uid']]) -> find() ;        
        if ($info['id'] && $this -> model -> destroy($info['id'])) {
            return $this->ok_js();
        } else {
            return $this->err_js('删除失败');
        }
    }
    
    /**
     * 设置购物车里的商品购买还是不购买
     * @param number $shopid
     * @param number $ck
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function choose($shopid=0,$ck=1){
        $result = $this->check_status($shopid);
        if ($result!==true) {
            return $this->err_js($result);
        }
        $info = $this -> model -> where(['shopid'=>$shopid,'uid'=>$this->user['uid']]) -> find() ;
        $data = [
                'id'=>$info['id'],
                'ifchoose'=> $ck ? 1 : 0,
        ];
        if ($this -> model -> update($data)) {
            return $this->ok_js();
        } else {
            return $this->err_js('更新失败');
        }
    }
    
    /**
     * 更改数量 此方法可以被 add() 取代.
     * @param number $shopid
     * @param number $num
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function change_num($shopid=0,$num=1){
        $result = $this->check_status($shopid);
        if ($result!==true) {
            return $this->err_js($result);
        }elseif ($num<1){
            return $this->err_js('数量不能小于1');
        }
        $info = $this -> model -> where(['shopid'=>$shopid,'uid'=>$this->user['uid']]) -> find() ;
        $data = [
                'id'=>$info['id'],
                'num'=> intval($num),
        ];
        if ($this -> model -> update($data)) {
            return $this->ok_js();
        } else {
            return $this->err_js('更新失败');
        }
    }
    
    /**
     * 购物车相关操作
     * @param number $shopid
     * @param string $type
     * @return string
     */
    public function act($shopid=0,$type=''){
        if (!$shopid) {
            return 'fail';
        }
		if (!$this->user) {
            return 'fail';
        }
        
        if(config('car_one')===true){   //购物车只保留一件商品
            $this -> model -> where(['shopid'=>['<>',$shopid],'uid'=>$this->user['uid']]) ->delete();
        }
        
        $info = $this -> model -> where(['shopid'=>$shopid,'uid'=>$this->user['uid']]) -> find() ;
        $num = intval(input('num'));
        $type1 = intval(input('type1'));
        $type2 = intval(input('type2'));
        $type3 = intval(input('type3'));
        if ($type=='del') { //踢出购物车
            //$shopids = is_array($shopid)?$shopid:[$shopid];
            if ($this -> model -> destroy($info['id'])) {
                return 'ok';
            } else {
                return 'fail';
            }
        }
        
        $result = $this->check_status($shopid,$num,$type1,$type2,$type3);
        if ($result!==true) {
            return $this->err_js($result);
        }
        
        if(!$info){    //购物车没有的话,就直接加进去
            $num<1 && $num=1;
            $data = [
                    'shopid'=>$shopid,
                    'uid'=>$this->user['uid'],
                    'type1'=>$type1,
                    'type2'=>$type2,
                    'type3'=>$type3,
                    'num'=>$num,
            ];            
            if ($this -> model -> create($data)) {
                return 'ok';
            } else {
                return 'fail';
            }
        }else{
            
            if($type=='plus'){     //购物车页面简单的加减数据
                $_num = $info['num']+intval($num);  //$num可以是负数
                if($_num<1){
                    $_num = 0;
                }
                $data = [
                        'id'=>$info['id'],
                        'num'=>$_num,
                ];
            }elseif($type=='change_num'){   //直接修改购买数量
                $data = [
                        'id'=>$info['id'],
                        'num'=> intval($num),
                ];
            }elseif($type=='choose'){   //是否选中 下单
                $data = [
                        'id'=>$info['id'],
                        'ifchoose'=> input('ck')==1 ? 1 : 0,
                ];
            }else{
                $data = [
                        'id'=>$info['id'],
                        'type1'=>$type1,
                        'type2'=>$type2,
                        'type3'=>$type3,
                        'num'=>$num,
                ];
            }
            if ($this -> model -> update($data)) {
                return 'ok';
            } else {
                return 'fail';
            }
        }
    }
    
    /**
     * 购物车查看页
     * @return mixed|string
     */
    public function index() {
        $listdb = $this->model->getList($this->user['uid']);
        $this->assign('listdb', $listdb);
        return $this ->fetch();
    }
    
    /**
     * 统计购物车里边的详细信息
     * @param number $uid
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function getcar($uid=0){
        if (empty($uid)) {
            $uid = $this->user['uid'];
        }
        $listdb = $this->model->getList($uid);
        if (empty($listdb)) {
            return $this->err_js('购物车里没有任何商品');
        }else{
            $array = [];
            $array['shop_num'] = 0; //商品种类总个数
            $array['num'] = 0;  //总个数,某个商品可能存在多个
            $array['money'] = 0; //总共需要支付的金额
            foreach($listdb AS $shop_uid=>$ar){
                foreach($ar AS $id=>$rs){
                    $array['shop'][] = array_merge($rs,['shop_uid'=>$shop_uid]);
                    $array['num'] += $rs['_car_']['num'];
                    $array['shop_num']++;
                    $array['money'] += fun('shop@get_price',$rs,$rs['_car_']['type1']-1)*$rs['_car_']['num'];
                }
            }
            return $this->ok_js($array);
        }
    }
    
    
}
