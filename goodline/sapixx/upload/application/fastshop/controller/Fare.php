<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 运费设置
 */
namespace app\fastshop\controller;
use app\common\controller\Manage;
use think\facade\Config;

class Fare extends Manage{

    public function initialize() {
        parent::initialize();
        if(!model('auth')->getAuth($this->user->id,0)){
            $this->error('无权限,你非【超级管理员】');
        }
        $this->assign('pathMaps',[['name'=>'运费设置','url'=>url("fastshop/fare/index")]]);
    }

    /**
     * 运费管理
     */
    public function index(){
        $view['info'] = model('Fare')->get(['member_miniapp_id' => $this->member_miniapp_id]);
        return view('index',$view);
    }

    /**
     * 编辑/保存
     */
    public function save(){
        if(request()->isAjax()){
            $data = [
                'first_weight'  => input('post.first_weight/d'),
                'first_price'   => input('post.first_price/d'),
                'second_weight' => input('post.second_weight/d'),
                'second_price'  => input('post.second_price/d'),
            ];
            $validate = $this->validate($data,'fare.save');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $rel = model('Fare')->get(['member_miniapp_id' => $this->member_miniapp_id]);
            if(empty($rel)){
                $data['member_miniapp_id'] = $this->member_miniapp_id;
                $result = model('Fare')->insert($data);
            }else{
                $result = model('Fare')->save($data,['member_miniapp_id' => $this->member_miniapp_id]);                
            }
            if($result){
                return json(['code'=>200,'data' => ['url' => url('fastshop/fare/index')],'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }
    }

    
    /**
     * 计算运费多少钱
     * @param  array   $item [计算参数]
     * @return array         商品价格信息
     */
    public static function realAmount($item,$member_miniapp_id){
        $fare  = self::where(['member_miniapp_id' => $member_miniapp_id])->find();
        $real_amount  = 0;   //商品总价
        $real_freight = 0;   //运费总价
        $total        = 0;   //单SKU运费
        foreach($item as $value){
            $real_amount += $value['amount'];
            $weight      = $value['weight'] * $value['num'];
            if($weight <= $fare['first_weight'] || 0 == $fare['second_weight']){
                $total  = $fare['first_price'];
            }else{
                $weight = $weight - $fare['second_weight'];
                $total  = $fare['first_price'] + ceil($weight/$fare['second_weight']) * $fare['second_price'];
            }
            $real_freight += $total;
        }        
        $data['real_amount']  = money($real_amount);   //商品价格
        $data['real_freight'] = money($real_freight);  //运费
        $data['order_amount'] = money($real_freight+$real_amount);  //商品总价+运费
        return $data;
    }
}