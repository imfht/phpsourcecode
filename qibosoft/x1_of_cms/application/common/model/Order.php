<?php
namespace app\common\model;

use think\Model;
use app\common\model\User AS UserModel;
use app\common\util\Shop AS ShopFun;

class Order extends Model
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
        $this->table = self::$table_pre.self::$model_key.'_order';
        self::$content_model = get_model_class(self::$model_key,'content');
    }
    
    /**
     * 某条订单里的商品信息
     * @param unknown $shops
     * @return void[]|array[]|\think\db\false[]|\app\common\model\PDOStatement[]|string[]|\think\Model[]
     */
    protected static function getshop($shops){
        $listdb = [];
        $detail = explode(',', $shops);
        foreach ($detail AS $value){
            if (empty($value)) {
                continue;
            }
            list($shpid,$num,$type1,$type2,$type3) = explode('-', $value);
            $shopdb = self::$content_model->getInfoByid($shpid,true);
            unset($shopdb['content'],$shopdb['full_content']);
            //对价格与商品属性进行处理
            ShopFun::car_get_price_type($shopdb,[
                    'num'=>$num,
                    'type1'=>$type1,
                    'type2'=>$type2,
                    'type3'=>$type3,
            ]);
            $listdb[] = $shopdb;
        }
        return $listdb;
    }
    
    /**
     * 只获取一条订单信息,一般用在查看详情使用
     * @param unknown $id
     * @return void[]|array[]|\think\db\false[]|\app\common\model\PDOStatement[]|string[]|\think\Model[]
     */
    public function getInfo($id){
        $info = getArray( $this->find($id) );
        if ($info){
            $info['shop_db'] = $this->getshop($info['shop']);
            return $info;
        }
    }
    
    //订单列表,带分页
    public  function getList($map=[],$rows=20){
        $data_list = self::where($map)->order('id','desc')->paginate($rows,false,['query'=>input('get.')]);
        $data_list->each(function($rs,$key){
            $rs['shop_db'] = [];
            if($rs['shop']!=''){
                $rs['shop_db'] = static::getshop($rs['shop']);
            }
            return $rs;
        });
        return $data_list;
    }
    
    /**
     * 标签取数据
     * @param array $tag_array
     * @return unknown
     */
    public static function get_label($tag_array=[]){
        $map = [];
        $cfg = unserialize($tag_array['cfg']);
        $rows = $cfg['rows']?:10;
        if($cfg['where']){  //用户自定义的查询语句
            $_array = fun('label@where',$cfg['where'],$cfg);
            if($_array){
                $map = array_merge($map,$_array);
            }
        }
        $data_list = self::where($map)->order('id','desc')->paginate($rows);
        return $data_list;
    }
    

    /**
     * 后台支付后,进行确认付款审核处理
     * @param string $ids 多个订单的话,每个订单ID用逗号隔开,不同的商家会生成不同的订单
     * @return boolean
     */
    public static function pay($ids=''){
        $array = explode(',',$ids);
        $check = 0;
        foreach ($array AS $id){
            $info = self::get($id);
            if (empty($info)) {
                continue;
            }
            if ($info['pay_status']==1) {  //已支付
                $check++;
                continue;   //不要再执行下面的
            }
            $user = UserModel::get_info($info['uid']);
            if($info['pay_money']>0 && $user['rmb']<$info['pay_money']){    //钱不够扣,终止以下所有操作
                return false;
            }
                        
            self::update([
                    'id'=>$id,
                    'pay_status'=>1,
                    'pay_time'=>time(),
            ]);
            
            static::success_pay($info);    //支付成功,执行相关操作,比如资金变动
            
            $check++;
            
        }
        
        if ($check) {
            return true;
        }        
    }
    
    /**
     * 支付成功,资金变动 , 也可以增加消息通知
     * @param array $order_info 订单信息,不是商品信息
     */
    protected static function success_pay($order_info=[]){
        $money = abs($order_info['pay_money']);
        if ($money>0) {
            //购买者扣款
            add_rmb($order_info['uid'],-$money,0,'购物消费');
            
            //商家入帐
            add_rmb($order_info['shop_uid'],$money,0,'销售商品');
        }        
        
        static::send_msg($order_info);
        
        hook_listen('order_have_pay',$order_info,$array=['dirname'=>self::$model_key]);
        get_hook('order_have_pay',$data=[],$order_info,$array=['dirname'=>self::$model_key],$use_common=true,self::$model_key);   //钩子扩展
    }
    
    /**
     * 支付成功,消息通知
     * @param array $order_info 订单信息,不是商品信息
     */
    protected static function send_msg($order_info=[]){
        //preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        //$dirname = $array[0][1];
        $dirname = self::$model_key;
        $title = '恭喜你,成功交易了一笔订单';
        $content = $title.'，<a href="'.get_url( murl($dirname.'/kehu_order/show',['id'=>$order_info['id']]) ).'">点击查看详情</a>';
        $webdb = config('webdb.M__'.$dirname);
        if(!isset($webdb['pay_order_msg_hy']) || $webdb['pay_order_msg_hy']){
            send_msg($order_info['shop_uid'],$title,$content);
        }
        if(!isset($webdb['pay_order_wx_hy']) || $webdb['pay_order_wx_hy']){
            send_wx_msg($order_info['shop_uid'], $content);
        }
        if($webdb['pay_order_sms_hy']){
            send_sms($order_info['shop_uid'], $title);
        }
    }
	
}