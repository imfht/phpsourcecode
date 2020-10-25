<?php
namespace app\common\fun;


class Weixin{
    
    /**
     * 根据订单号查询付款与否,第二项不为true的时候,返回的结果有可能是数据,也有可能是字符串. 
     * 只有当数组并且 $array['ispay']===true时,必须是三个等号来判断,才是支付成功,此时还会返回微信的订单号 $array['s_orderid'] 不存在这个值就付款不成功
     * @param string $out_trade_no 订单号
     * @param string $only_check_ispay 是否仅仅查询是否付款成功
     * @return boolean|unknown
     */
    public static function check_order($out_trade_no='',$only_check_ispay=false){
        $result = include(ROOT_PATH.'plugins/weixin/api/order_query.php');
        if($only_check_ispay==true){
            if(is_array($result) && $result['ispay']===true){
                return true;
            }else{
                return false;
            }
        }else{
            return $result;
        }        
    }
    
}