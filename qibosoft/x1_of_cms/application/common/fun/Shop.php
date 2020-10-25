<?php
namespace app\common\fun;
use think\Db;

/**
 * 商城用到一些方法
 * @author Administrator
 *
 */
class Shop{
    
    /**
     * 统计用户购物车里的商品数量, 可以统计所有件数, 也可以统计品种数量
     * @param string $all 为1 或 true的时候,统计所有件数, 默认只统计品种数量
     * @return number
     */
    public function car_num($all=false){
        $uid = login_user('uid');
        if (empty($uid)) {
            return 0;
        }
        if ($all) {
            $num = Db::name('shop_car')->where('uid',$uid)->sum('num');
        }else{
            $num = Db::name('shop_car')->where('uid',$uid)->count('num');
        }
        return intval($num);
    }
    
    /**
     * 列出用户购物车里的商品
     * @return number|unknown[]
     */
    public function car_title(){
        $uid = login_user('uid');
        if (empty($uid)) {
            return 0;
        }
        $array = [];
        $listdb = Db::name('shop_car')->where('uid',$uid)->order('update_time desc')->column(true);
        foreach ($listdb AS $rs){
            $shop = \app\shop\model\Content::getInfoByid($rs['shopid'],true);
            $shop['_car_'] = $rs;
            $array[] = $shop;
        }
        return $array;
    }
    
    /**
     * 统计用户购物车里需要支付的总金额
     * @return number
     */
    public function car_money(){
        $uid = login_user('uid');
        if (empty($uid)) {
            return 0;
        }        
        $obj =  get_model_class(config('system_dirname')?:'shop','car');

        $listdb = $obj->getList($uid,1);
        $money = 0;
        foreach ($listdb AS $uid=>$shop_array){
            foreach ($shop_array AS $rs){   //某个商家的多个商品
                $_shop[] = $rs['_car_']['shopid'] . '-' . $rs['_car_']['num']  . '-' . $rs['_car_']['type1'] . '-' .$rs['_car_']['type2'] . '-' .$rs['_car_']['type3'];
                $rs['_car_']['num'] || $rs['_car_']['num']=1;
                $money += self::get_price($rs,$rs['_car_']['type1']-1) * $rs['_car_']['num'];
            }
        }
        return $money;
    }
    
    /**
     * 统计某个用户购买过某个商品的总数量
     * @param number $id 商品ID
     * @param number $uid 指定用户UID
     * @param string $ifpay 是否已成功付款
     */
    public function buynum($id=0,$uid=0,$ifpay=true){
        if (empty($uid)) {
            $uid = login_user('uid');
            if (empty($uid)) {
                return 0;
            }
        }
        $map = ['uid'=>$uid,];
        $ifpay && $map['pay_status'] = 1;
        
        $table = (config('system_dirname')?:'shop').'_order';
        $listdb = Db::name($table)->where($map)->column(true);
        $num = 0;
        foreach($listdb AS $rs){
            if($rs['shopid']==$id){
                $num += $rs['shopnum']?:1;
                continue;
            }
            $detail = explode(',',$rs['shop']);
            foreach ($detail AS $vs){
                list($shopid,$shopnum) = explode('-',$vs);
                if ($shopid==$id) {
                    $num += $shopnum?:1;
                }
            }
        }
        return $num;
    }
    
    /**
     * 取得商品属性 , $key 为 null 的话,商品内容页使用,全部列出给用户选择 为具体数字的话,显示某一项的价格或名称
     * @param string $type              属性类型,可以是 type1 type2 type3 比如分别可以定义为尺寸\颜色\长短
     * @param array $info               商品主表的内容信息
     * @param unknown $key          为null的话,商品详情页使用,所有参数展示出来, 为数值的话,就是用户选中购买的具体类型
     * @param string $result_type    要取得属性的名称,还是价格,一般都是名称.
     * @return void|array|unknown[]|array[]
     */
    public static function type_get_title_price($type='type1',$info=[],$key=null,$result_type='title'){
        if (empty($info[$type])) {
            return ;
        }
        $array = json_decode($info['field_array'][$type]['value']?:$info[$type],true);  //数据库存放的格式是 ["红","黄","蓝"]
        foreach ($array AS $_key=>$_value){
            list($title,$price,$num) = explode('|',$_value);    //对于第一项可以定义价格与库存量 比如 ["大号|100","中号|80","小号|50"]
            if($key!==null){    //展示某一项的名称或价格
                if($key==$_key){
                    if($result_type=='title'){
                        return $title;
                    }elseif($result_type=='price'){
                        return $price;
                    }elseif($result_type=='num'){
                        return is_numeric($num)?intval($num):null;
                    }
                }
            }else{  //商品详情页,把所有都列出来,供选择
                $listdb[] = [
                    'title'=>$title,
                    'price'=>$price,
                    'num'=>is_numeric($num)?$num:null,
                ];
            }
        }
        if($key===null){
            return $listdb;
        }
    }
    
    /**
     * 取得商品的实际价格,商品第一个属性1可以自定商品价格
     * 务必注意: 第二项,取得商品的实际价格,因为属性1可以定义价格, 如果从购物车取出的数据,数组下标要减1,因为购物车入库时加了1
     * @param array $info 商品信息
     * @param number $key 用户选中属性1的具体某项 如果从购物车取出的数据,数组下标要减1,因为购物车入库时加了1
     */
    public static function get_price($info=[],$key=0){
        $value = self::type_get_title_price('type1',$info,$key,'price');
        if($value>0){
            return $value;
        }else{
            if (isset($info['vip_price']) && $info['vip_price']>0) {    //存在VIP价格
                $webdb = config('webdb');
                $gid = login_user('groupid');
                if ($webdb['group_vip_price'] && in_array($gid, $webdb['group_vip_price'])) {
                    return $info['vip_price'];  //享受VIP价
                }
            }
            
            return $info['price'];
        }
    }
    
    /**
     * 获取库存量
     * @param array $info 商品信息
     * @param number $key 第几项的库存
     * @param number $num 是否重新设置库存量
     * @return void|array|\app\common\fun\unknown[]
     */
    public static function get_num($info=[],$key=0,$num=null){
        if ($num===null) {            
            return $key<0 ? null : self::type_get_title_price('type1',$info,$key,'num');
        }else{
            $array = json_decode($info['field_array']['type1']['value']?:$info['type1'],true);  //数据库存放的格式是 ["红","黄","蓝"]
            foreach ($array AS $_key=>$_value){
                $detail = explode('|',$_value);    //对于第一项可以定义价格与库存量 比如 ["大号|100","中号|80","小号|50"]
                if($key==$_key){
                    $detail[2] = $num;
                    $array[$_key] = implode('|', $detail);
                }
            }
            return json_encode($array);
        }
    }
    
    /**
     *  取得购物车里某一个商品,用户选中的属性,比如哪个型号或尺寸,及具体的价格
     * @param array $info 商品信息, 是引用参数,可以改变里边的值,这里是加数组
     * @param array $choose 用户选中的参数,num是购买了多少项,type1 就是第一项选中的哪个, type2就是第二项选中的哪个 type3就是第三项选中的哪个
     */
    public static function car_get_price_type($info=[],$choose=['num'=>1,'type1'=>0,'type2'=>0,'type3'=>0]){
        static $field_array = [];
        $_info = [];
        $_info['_price'] = self::get_price($info,$choose['type1']-1);     //取得商品的实际价格,因为属性1可以定义价格, 另外数组下标要减1,因为购物车入库时加了1
        $_info['_num'] = intval($choose['num']); //购买数量
        
        //得到用户购买的是什么颜色型号 , 用户选中的类型key要减1,因为入库前加过1 数组下标从0开始的,
        $choose['type1'] && $_info['_type1'] = self::type_get_title_price('type1',$info,$choose['type1']-1,'title');
        $choose['type2'] && $_info['_type2'] = self::type_get_title_price('type2',$info,$choose['type2']-1,'title');
        $choose['type3'] && $_info['_type3'] = self::type_get_title_price('type3',$info,$choose['type3']-1,'title');
        
        //得到尺寸 颜色 型号 的真实叫法 如果用户没选择,或者是商品都不存在这项信息,就没必要去取出来        
        if(empty($field_array[$info['mid']])){                              //这里用数组是考虑有可能会存在不同的模型
            $field_array[$info['mid']] = get_field($info['mid']);     //取得模型的所有字段的数据
        }
        $field = $field_array[$info['mid']];
        
        $info['_type1'] && $_info['_type1_title'] = $field['type1']['title'];
        $info['_type2'] && $_info['_type2_title'] = $field['type2']['title'];
        $info['_type3'] && $_info['_type3_title'] = $field['type3']['title'];
        
        $info = array_merge($info,$_info);
        return $info;
    }
    
    /**
     * 我的分类
     * @param number $uid
     */
    public static function mysort($uid=0){
        if (empty($uid)) {
            $uid = login_user('uid');
        }
        $map = [
                'uid'=>$uid,
        ];
        
        return Db::name('shop_mysort')->where($map)->column(true);
    }
    
}