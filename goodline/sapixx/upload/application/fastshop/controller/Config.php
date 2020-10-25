<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商城配置
 */
namespace app\fastshop\controller;
use app\common\controller\Manage;
use app\fastshop\model\Config as Configs;
use think\facade\Request;

class Config extends Manage{


    public function initialize()
    {
        parent::initialize();
        if(!model('auth')->getAuth($this->user->id,0)){
            $this->error('无权限,你非【超级管理员】');
        }
        $this->assign('pathMaps',[['name' => '应用配置','url' => url("fastshop/config/index")]]);
    }

    /**
     *  应用配置
     * @return void
     */
    public function index(){
        if(request()->isAjax()){
            $data = [
                'shop_types'         => input('post.shop_types/d'),
                'regvip_price'       => input('post.regvip_price/d'),
                'regvip_level1_ratio'=> input('post.regvip_level1_ratio/d'),
                'regvip_level2_ratio'=> input('post.regvip_level2_ratio/d'),
                'reward_types'       => input('post.reward_types/d'),
                'reward_nth'         => input('post.reward_nth/d'),
                'reward_ratio'       => input('post.reward_ratio/d'),
                'tax'                => input('post.tax/d'),
                'profit'             => input('post.profit/d'),
                'shopping'           => input('post.shopping/d'),
                'num'                => input('post.num/a'),
                'much'               => input('post.much/a'),
                'ratio'              => input('post.ratio/a'),
                'platform_ratio'     => input('post.platform_ratio/d'),
                'platform_amout'     => input('post.platform_amout/d'),
            ];
            $validate = $this->validate($data,'config.save');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            //处理商品属性
            if(!empty($data['num']) && !empty($data['much'])){
                $ary = [];
                foreach ($data['num'] as $key => $value) {
                    $ary[$key]['num'] = intval($value);
                }
                foreach ($data['much'] as $key => $value) {
                    $ary[$key]['much'] = intval($value);
                }
                foreach ($data['ratio'] as $key => $value) {
                    $ary[$key]['ratio'] = $value > 100 ? 100 : intval($value);
                }
                $props = array_filter($ary);
                $data['rules'] = empty($props) ? '[]' : json_encode($props);
            }
            unset($data['num']);
            unset($data['much']);
            unset($data['ratio']);
            $rel = model('Config')->get(['member_miniapp_id' => $this->member_miniapp_id]);
            if(empty($rel)){
                $data['member_miniapp_id'] = $this->member_miniapp_id;
                $result =  model('Config')->save($data);
            }else{
                $result =  model('Config')->save($data,['member_miniapp_id' => $this->member_miniapp_id]);      
            }
            if($result){
                return json(['code'=>200,'data' => ['url' => url('fastshop/config/index')],'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $info = model('Config')->where(['member_miniapp_id' => $this->member_miniapp_id])->find();
            if(empty($info)){
                return $this->redirect(url('fastshop/config/setting'),302);
            }
            $info['rules'] = empty($info->rules) ? [] : json_decode($info->rules,true);
            $view['info']  = $info;
            return view()->assign($view);
        }
    }
    
/**
     *  应用配置
     * @return void
     */
    public function setting(){
        if(request()->isAjax()){
            $data = [
                'cycle'              => Request::param('cycle/d',0),
                'payment_type_shop'  => Request::param('payment_type_shop/d',0),
                'payment_point_shop' => Request::param('payment_point_shop/d',0),
                'payment_type'       => Request::param('payment_type/d',0),
                'payment_point'      => Request::param('payment_point/d',0),
                'lack_cash'          => Request::param('lack_cash/d',0),
                'shopping_name'      => Request::param('shopping_name/s','购物积分'),
                'amountlimit'        => Request::param('amountlimit/d',0),
                'day_ordernum'       => Request::param('day_ordernum/d',0),
                'sale_ordernum'      => Request::param('sale_ordernum/d',0),
                'old_users'          => Request::param('old_users/d',0),
                'is_priority'        => Request::param('is_priority/d',0),
                'lock_sale_day'      => Request::param('lock_sale_day/d',0),
                'num_referee_people' => Request::param('num_referee_people/d',0),
            ];
            $validate = $this->validate($data,'config.setting');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $rel = Configs::where(['member_miniapp_id' => $this->member_miniapp_id])->find();
            $data['lack_cash'] = $data['lack_cash']*100;
            if(empty($rel)){
                $data['member_miniapp_id'] = $this->member_miniapp_id;
                $result = Configs::insert($data);
            }else{
                $result = Configs::where(['id' => $rel->id])->update($data);      
            }
            if($result){
                return json(['code'=>200,'data' => ['url' => url('fastshop/config/setting')],'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $view['info']  = model('Config')->get(['member_miniapp_id' => $this->member_miniapp_id]);
            return view()->assign($view);
        }
    }

    /**
     *  应用配置
     * @return void
     */
    public function message(){
        if(request()->isAjax()){
            $data = [
                'message'            => input('post.message/s','','htmlspecialchars'),
                'member_miniapp_id'  => $this->member_miniapp_id,
            ];
            $validate = $this->validate($data,'config.message');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $rel = model('Config')->get(['member_miniapp_id' => $this->member_miniapp_id]);
            if(empty($rel)){
                $data['member_miniapp_id'] = $this->member_miniapp_id;
                $result =  model('Config')->save($data);
            }else{
                $result =  model('Config')->save($data,['member_miniapp_id' => $this->member_miniapp_id]);      
            }
            if($result){
                return json(['code'=>200,'data' => ['url' => url('fastshop/config/message')],'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            
            $view['info']  = model('Config')->get(['member_miniapp_id' => $this->member_miniapp_id]);
            return view()->assign($view);
        }
    } 

    /**
     * 
     *  应用配置
     * @return void
     */
    public function configs(){
        $info  = model('Config')->get(['member_miniapp_id' => $this->member_miniapp_id]);
        return json(['code'=>200,'msg'=>'成功','data'=>$info]);
    }
}