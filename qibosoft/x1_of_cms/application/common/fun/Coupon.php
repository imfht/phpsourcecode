<?php
namespace app\common\fun;
use think\Db;
//use app\quan\model\Content AS ContentModel;

/**
 * 代金券
 *
 */
class Coupon{
    
    /**
     * 获取指定用户的可用代金券
     * $tag,$both这两项不做设置(即用默认的值),就只获取通用代金券 
     * 若设置$both为true的话代表同时获取通用及某类券
     * @param number $uid 用户UID
     * @param number $money 当前产品金额
     * @param number $shoper 对应商家的UID
     * @param string $tag 指定某类商品才能用的优惠券
     * @param string $both 默认只取某类或者是通用,设置为true的话,就两种一起获取
     * @return array
     */
    public static function get_list($uid=0,$money=0,$shoper=0,$tag='',$both=false){
        if (empty(modules_config('coupon'))) {
            return ;
        }
        $map = [
            'uid'=>$uid,
            'min_money'=>['<=',$money],
            'receive_status'=>0,
            'pay_status'=>1,
            'expiry_date'=>['>',time()],
        ];
        if ($shoper) {
            $map['shop_uid'] = $shoper;
        }
        if ($both!=false && $tag!='') {
            $map['coupon_tag'] = [
                ['=',$tag],
                ['=',''],
                'or'
            ];
        }else{
            $map['coupon_tag'] = $tag;
        }
        $listdb = Db::name('coupon_order')->where($map)->order('quan_money','asc')->column(true);
        foreach ($listdb AS $key=>$rs){
            if($rs['quan_money']>$rs['min_money']){
                $rs['quan_money'] = $rs['min_money']; //券的值不能大于本次消费金额
                $listdb[$key] = $rs;
            }
        }
        return $listdb;
    }
    
    /**
     * 获取具体某条
     * @param number $id
     * @return array|\think\db\false|PDOStatement|string|\think\Model
     */
    public static function get_info($id=0){
        $map = [
            'id'=>$id,
        ];
        $info = Db::name('coupon_order')->where($map)->find();
        return $info;
    }
    
    /**
     * 消费掉
     * @param number $id
     */
    public static function take_off($id=0){
        $map = [
            'id'=>$id,
        ];
        Db::name('coupon_order')->where($map)->update([
            'receive_status'=>1,
            'receive_time'=>time(),
        ]);
    }
}