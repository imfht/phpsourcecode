<?php
namespace app\common\controller\index\wxapp;

use app\common\controller\IndexBase;

/**
 * 小程序购物车
 * @author Administrator
 *
 */
class Car extends IndexBase
{
    protected $model;
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'car');
    }
    
    /**
     * 列出我的购物车
     * @param unknown $type 为1的时候,列出选中的商品
     * @return \think\response\Json
     */
    public function index($type=null){
        $car_array = $this->model->getList($this->user['uid'],$type?1:null);
        $listdb = [];
        foreach($car_array AS $seller_uid=>$array){
            $shops = [];
            foreach($array AS $rs){
                $shops[] = $rs;
            }
            $listdb[] = [
                    'uid'=>$seller_uid, //商铺卖家资料
                    'icon'=>get_user_icon($seller_uid),
                    'shops'=>$shops,    //用户购买了该商家的所有商品
            ];
        }
        return $this->ok_js($listdb);
    }
    
    
    
    /**
     * 商品详情页,查询购物车状态
     * @param number $id 商品ID
     * @return \think\response\Json
     */
    public function getbyid($id=0){
        if (empty($this->user)) {
            return $this->err_js('你还没登录');
        }else{
            $mytotal = $this->model->where('uid',$this->user['uid'])->sum('num');  //我的购物车商品数
            if($mytotal){
                $info = getArray( $this->model->get(['shopid'=>$id]) );
                $type1 = $info['type1'] - 1;
                $type2 = $info['type2'] - 1;
                $type3 = $info['type3'] - 1;                
            }
            $data = [
                    'type1'=>$type1<0?0:$type1,
                    'type2'=>$type2<0?0:$type2,
                    'type3'=>$type3<0?0:$type3,
                    'num'=>$info['num']<1?1:$info['num'],
                    'mytotal'=>$mytotal,
            ];
        }
        return $this->ok_js($data);
    }
    
    /**
     * 购物车的相关操作
     * @param number $shopid 商品ID
     * @param string $type  操作类型
     * @param number $num   数量
     * @param number $type1 商品属性1
     * @param number $type2 商品属性2
     * @param number $type3 商品属性3
     * @return string
     */
    private function act($shopid=0,$type='',$num=0,$type1=0,$type2=0,$type3=0){
        if (!$shopid) {
            return 'fail';
        }elseif (!$this->user) {
            return 'fail';
        }
        
        $info = $this -> model -> where(['shopid'=>$shopid,'uid'=>$this->user['uid']]) -> find() ;

        if(!$info){    //购物车没有该商品的话,就直接加进去
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
     * 踢除一个商品
     * @param unknown $id 商品ID
     * @return \think\response\Json
     */
    public function delete_one($id=0){
        if ($this -> model ->where(['shopid'=>$id,'uid'=>$this->user['uid']]) -> delete()) {
            return $this->ok_js([],'删除成功');
        } else {
            return $this->err_js('删除失败');
        }
    }
    
    /**
     * 清空购物车
     * @return unknown
     */
    public function clear(){
        if($this -> model -> destroy(['uid'=>$this->user['uid']])){
            return $this->ok_js([],'清除成功');
        } else {
            return $this->err_js('清空失败');
        }
    }
    
    /**
     * 勾选商品
     * @param number $id 商品ID
     * @param number $choose 选择状态 0 或 1
     * @return \think\response\Json
     */
    public function check_one($id=0,$choose=0){
        if($this -> model ->where(['uid'=>$this->user['uid'],'shopid'=>$id])-> update(['ifchoose'=>$choose])){
            return $this->ok_js([],'选中成功');
        } else {
            return $this->err_js('选中失败');
        }
    }
    
    /**
     * 购物车的相关操作
     * @param number $id 商品ID
     * @param string $type  操作类型
     * @param number $total 数量
     * @return \think\response\Json|\app\shop\index\wxapp\unknown
     */
    public function change($id=0,$type='',$total=1){
        $code = 1;
        $msg = '操作失败';
        if($type=='change_num'){
            if($this->act($id,'change_num',$total)=='ok'){
                return $this->ok_js([],'修改成功');
            }
        }elseif($type=='del'){
            return $this->delete_one($id);
        }elseif($type=='clear'){
            return $this->clear();
        }elseif($type=='choose'){
            return $this->check_one($id,input('choose'));
        }
        
        return $this->err_js($msg); 
    }
    
    /**
     * 商品详情页 把商品 加入购物车
     * @param number $id 商品ID
     * @param number $num 商品数量
     * @param number $type1 属性1
     * @param number $type2 属性2
     * @param number $type3 属性3
     * @return \think\response\Json
     */
    public function add($id = 0 , $num = 1 , $type1 = 0 , $type2 = 0 , $type3 = 0){
        $msg = '加入失败';        
        if(empty($this->user)){
            $code = 1;
            $msg = '请先登录';
        }
        
        if($this->act($id,'add',$num,$type1,$type2,$type3)=='ok'){
            return $this->ok_js([],'操作成功');
        }
        
        return $this->err_js($msg);
    }
    
    
}













