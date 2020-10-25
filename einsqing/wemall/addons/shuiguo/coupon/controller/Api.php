<?php
namespace addons\common\coupon\controller;

use think\addons\Controller;

class Api extends Controller
{
    public function _initialize(){
        
        if (request()->isOptions()){
            abort(json(true,200));
        }
    }
    //可用优惠券列表
    public function couponList()
    {
        //过期分类
        x_model('AddonsCommonCouponMenu')->where('last_time','<=',date("Y-m-d H:i:s"))->update(['status' => -1]);
        $data['coupon'] = x_model('AddonsCommonCouponMenu')->where('status',1)->select();

        return json($info = ['data' => $data, 'msg' => '优惠券列表', 'code' => 1]);
    }
    //我的优惠券
    public function myCoupon()
    {   
        $user_id = action('api/BaseController/get_user_id',[]);
        $map = array();
        $map['user_id'] = $user_id;

        $status = input('?param.status') ? input('param.status') : 0;
        if($status == -1){
            $ids = x_model('AddonsCommonCouponMenu')->where('last_time','<=', date("Y-m-d H:i:s"))->value('id');
            if($ids){
                $map['coupon_menu_id']  = ['in',$ids];
            }
        }else{
            $map['status'] = $status;
        }

        $data['coupon'] = x_model('AddonsCommonCoupon')->with('menu')->where($map)->order('id desc')->select();

        return json($info = ['data' => $data, 'msg' => '我的优惠券', 'code' => 1]);
    }
    //兑换优惠券
    public function couponChange()
    {
        $user_id = action('api/BaseController/get_user_id',[]);
        $id = input('param.id');

        $coupon_config = x_model('AddonsCommonCouponMenu')->find($id);
        $user_score =  model('app\common\model\User')->where('id',$user_id)->value('score');
        if($user_score >= $coupon_config['score']){
            $map = array();
            $map['user_id'] = 0;
            $map['status'] = 0;
            $map['coupon_menu_id'] = $id;

            $coupon = x_model('AddonsCommonCoupon')->where($map)->select()->toarray();
            if($coupon){
                x_model('AddonsCommonCoupon')->where('id',$coupon[0]['id'])->update(['user_id' => $user_id]);
                x_model('AddonsCommonCouponChange')->create([
                    'user_id'  =>  $user_id,
                    'coupon_id' =>  $coupon[0]['id'],
                    'score'  =>  $coupon_config['score'],
                    'type'  =>  1
                ]);
                model('app\common\model\User')->where('id',$user_id)->setDec('score', $coupon_config['score']);
                return json(['data' => false, 'msg' => '兑换成功', 'code' => 1]);
            }else{
                return json(['data' => false, 'msg' => '本场优惠券已兑换完毕', 'code' => 0]);
            }
        }else{
            return json(['data' => false, 'msg' => '您的积分不足', 'code' => 0]);
        }
    }

    //添加我的优惠券
    public function addCoupon()
    {
        $user_id = action('api/BaseController/get_user_id',[]);
        $code = input('param.code');
        
        $coupon = x_model('AddonsCommonCoupon')->with('menu')->where('code',$code)->find();
        if($coupon['menu']['last_time'] <= date("Y-m-d H:i:s")){
            return json(['data' => false, 'msg' => '优惠券已过期', 'code' => 0]);
        }
        if($coupon){
            if($coupon['user_id']){
                return json(['data' => false, 'msg' => '此券已经被兑换过了', 'code' => 0]);
            }else{
                x_model('AddonsCommonCoupon')->where('code',$code)->update(['user_id' => $user_id]);
                $result = x_model('AddonsCommonCouponChange')->create([
                            'user_id'  =>  $user_id,
                            'coupon_id' =>  $coupon['id'],
                            'score'  =>  $coupon['menu']['score'],
                            'type'  =>  3
                        ]);
                $data['coupon'] = $coupon;
                if($result){
                    return json(['data' => $data, 'msg' => '兑换成功', 'code' => 1]);
                }else{
                    return json(['data' => false, 'msg' => '兑换失败', 'code' => 0]);
                }
            }
        }else{
            return json(['data' => false, 'msg' => '此券不存在', 'code' => 0]);
        }
    }
    // 获取活动设置
    public function basic_config(){
        x_model('AddonsCommonCouponActiveConfig')->where('last_time','<=', date("Y-m-d H:i:s"))->update(['status'=>1]);
        x_model('AddonsCommonCouponActiveConfig')->where('start_time','>=', date("Y-m-d H:i:s"))->update(['status'=>-1]);
        $config = x_model('AddonsCommonCouponActiveConfig')->with('file')->where(['status'=>0])->find(1);
        return json(['data'=>$config, 'msg'=>'活动设置', 'code'=>1]);
    } 
    // 获取随机优惠券
    public function get_coupon(){
        $user_id = action('api/BaseController/get_user_id',[]);
        // 判断用户是否存在
        x_model('AddonsCommonCouponUserLog')->user_exist($user_id);
        // 判断奖品数量
        $coupon = x_model('AddonsCommonCoupon')->where(array('user_id'=>0))->find();
        if(!$coupon){
            return json(['data'=>false,'msg'=>'奖品兑换完了','code'=>0]);
        }
        // 判断用户可抽奖次数
        $left_times = x_model('AddonsCommonCouponUserLog')->left_times($user_id);
        if(!$left_times){
            return json(['data'=>false,'msg'=>'没有机会了哦','code'=>0]);
        }
        $rand_num = rand(0,100);
        // 更新抽奖次数
        x_model('AddonsCommonCouponUserLog')->where(['user_id'=>$user_id])->update(['total_use'=>['exp','total_use+1'],'day_use'=>['exp','day_use+1']]);
        $active_config = x_model('AddonsCommonCouponActiveConfig')->find(1);
        if($rand_num<$active_config['probability']){
            
            x_model('AddonsCommonCoupon')->where(array('id'=>$coupon['id']))->update(['user_id'=>$user_id]);
            x_model('AddonsCommonCouponChange')->create([
                'user_id'   => $user_id,
                'coupon_id' => $coupon['id'],
                'score'     => 0,
                'type'      => 2
                ]);
            $result = x_model('AddonsCommonCoupon')->where(array('id'=>$coupon['id']))->find();
            return json(['data'=>$result,'msg'=>'中奖了','code'=>1]);
        }else{
            return json(['data'=>false,'msg'=>'没中奖', 'code'=>0]);
        }
    }
    // 查询剩余次数
    public function left_times(){
        $user_id = action('api/BaseController/get_user_id',[]);
        // 查询用户可抽奖次数
        $left_times = x_model('AddonsCommonCouponUserLog')->left_times($user_id);
        return json(['data'=>$left_times,'msg'=>'剩余抽奖次数','code'=>1]);
    }
    public function abc(){
        $menu = x_model('AddonsCommonCouponMenu')->find(['score'=>20]);
        halt($menu);
    }
}