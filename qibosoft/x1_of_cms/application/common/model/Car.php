<?php
namespace app\common\model;
use think\Model;
use app\common\util\Shop AS ShopFun;


class Car extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table;// = '__FORM_MODULE__';
    
    //以下三项必须在这里先赋值，不然下面的重新定义table会不生效
    protected $autoWriteTimestamp = true;   // 自动写入时间戳
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $resultSetType = 'array';
    
    protected static $base_table;
    protected static $model_key;
    protected static $table_pre;
    
    protected static $content_model; //内容模型
    
    //为了调用initialize初始化,生成数据表前缀$model_key
    protected static function scopeInitKey(){}
    protected function initialize()
    {
        parent::initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        self::$model_key = $array[0][1];
        self::$base_table = $array[0][1].'_content';
        self::$table_pre = config('database.prefix');
        //字段表，带数据表前缀如qb_form_field
        $this->table = self::$table_pre.self::$model_key.'_car';        
        self::$content_model = get_model_class(self::$model_key,'content');
    }
    
    /**
     * 获取用户的购物车数据 , 商家UID是一维数组下标,购物车及商品在二维数组那里
     * @param number $uid 购买者的UID
     * @param unknown $choose_type 是否只获取选中要购买的商品
     * @param string $format 是否对商品数据进行显示转义
     * @return array
     */
    public static function getList($uid=0,$choose_type=null,$format=true){
        empty(self::$model_key) && self::InitKey();
        $map = [
                'uid'=>$uid,
        ];
        if($choose_type!==null){    //获取购物车中 选中或者是全部 商品
            $map['ifchoose'] = intval($choose_type);
        }
        
        $list_data = self::where($map)->order('update_time','desc')->column(true);  //用户的购物车数据
        //$field = [];
        foreach ($list_data AS $rs){
            $shop = self::$content_model->getInfoByid($rs['shopid'],$format);    //取得商品的详细数据
            if(empty($shop)){
                self::destroy($rs['id']);   //商品若不存在,就把购物车记录删除
                continue ;
            }
            unset($shop['content'],$shop['full_content']);
            $shop['picurl'] && $shop['picurl'] = tempdir($shop['picurl']);            

            ShopFun::car_get_price_type($shop,$rs);     //对价格与商品属性进行处理,得到实际商品属性的价格
            
            //为了后续方便扩展多商家店铺,把商家的UID做为一维数组下标
            $listdb[$shop['uid']][$rs['id']] = array_merge($shop,['_car_'=>$rs]);
        }
        return $listdb;
    }
    
    /**
     * 统计需要支付的总金额
     * @param number $uid
     * @param unknown $choose_type
     * @return array
     */
    public static function getMoney($uid=0){
        empty(self::$model_key) && self::InitKey();
        $map = [
                'uid'=>$uid,
                'ifchoose'=>1,
        ];
        $money = 0;
        $list_data = self::where($map)->column(true);  //用户的购物车数据
        foreach ($list_data AS $rs){
            $shop = self::$content_model->getInfoByid($rs['shopid'],false);
            if(empty($shop)){
                self::destroy($rs['id']);   //商品若不存在,就把购物车记录删除
                continue ;
            }
            $money += ShopFun::get_price($shop,$rs['type1']-1)*$rs['num'];
        }
        return $money;
    }

	
}