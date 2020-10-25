<?php
namespace app\common\util;

/**
 * 商城用到一些方法
 * @author Administrator
 *
 */
class Shop{
    
    /**
     * 取得商品属性 , $key 为 null 的话,商品内容页使用,全部列出给用户选择 为具体数字的话,显示某一项的价格或名称
     * @param string $type              属性类型,可以是 type1 type2 type3 比如分别可以定义为尺寸\颜色\长短
     * @param array $info               商品主表的内容信息
     * @param unknown $key          为null的话,商品详情页使用,所有参数展示出来, 为数值的话,就是用户选中购买的具体类型
     * @param string $result_type    要取得属性的名称,还是价格,一般都是名称.
     * @return void|array|unknown[]|array[]
     */
    public static function type_get_title_price($type='type1',$info=[],$key=null,$result_type='title'){
        return fun('shop@type_get_title_price',$type,$info,$key,$result_type);
    }
    
    /**
     * 取得商品的实际价格,商品第一个属性1可以自定商品价格
     * 务必注意: 第二项,取得商品的实际价格,因为属性1可以定义价格, 如果从购物车取出的数据,数组下标要减1,因为购物车入库时加了1
     * @param array $info 商品信息
     * @param number $key 用户选中属性1的具体某项 如果从购物车取出的数据,数组下标要减1,因为购物车入库时加了1
     */
    public static function get_price($info=[],$key=0){
        return fun('shop@get_price',$info,$key);
    }
    
    /**
     *  取得购物车里某一个商品,用户选中的属性,比如哪个型号或尺寸,及具体的价格
     * @param array $info 商品信息, 是引用参数,可以改变里边的值,这里是加数组
     * @param array $choose 用户选中的参数,num是购买了多少项,type1 就是第一项选中的哪个, type2就是第二项选中的哪个 type3就是第三项选中的哪个
     */
    public static function car_get_price_type(&$info=[],$choose=['num'=>1,'type1'=>0,'type2'=>0,'type3'=>0]){        
        $info = fun('shop@car_get_price_type',$info,$choose);
        return $info;
    }
    
}