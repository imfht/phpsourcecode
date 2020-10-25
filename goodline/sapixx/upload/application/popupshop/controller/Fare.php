<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 运费设置
 */
namespace app\popupshop\controller;
use app\common\controller\Manage;
use app\popupshop\model\Fare as AppFare;

class Fare extends Manage{

    public function initialize() {
        parent::initialize();
        $this->assign('pathMaps',[['name'=>'运费设置','url'=>url("popupshop/fare/index")]]);
    }

    /**
     * 运费管理
     */
    public function index(){
        $view['info'] =  AppFare::where(['member_miniapp_id' => $this->member_miniapp_id])->find();
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
                return enjson(0,$validate);
            }
            $rel = AppFare::where(['member_miniapp_id' => $this->member_miniapp_id])->find();
            if(empty($rel)){
                $data['member_miniapp_id'] = $this->member_miniapp_id;
                $result = AppFare::insert($data);
            }else{
                $result = AppFare::update($data,['member_miniapp_id' => $this->member_miniapp_id]);
            }
            if($result){
                return enjson(200,'操作成功',['url' => url('popupshop/fare/index')]);
            }else{
                return enjson(0);
            }
        }
    }    
}