<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商城配置
 */
namespace app\popupshop\controller;
use app\common\controller\Manage;
use app\popupshop\model\Config as Configs;
use think\facade\Request;

class Config extends Manage{


    public function initialize()
    {
        parent::initialize();
        $this->assign('pathMaps',[['name' => '应用配置','url' => url("popupshop/config/index")]]);
    }

    /**
     *  应用配置
     * @return void
     */
    public function index(){
        if(request()->isAjax()){
            $data = [
                'is_wechat_touser'   => Request::param('is_wechat_touser/d',0),
                'tax'                => Request::param('tax/d'),
                'profit'             => Request::param('profit/d'),
                'cycle'              => Request::param('cycle/d',0),
                'lack_cash'          => Request::param('lack_cash/d',0),
                'lock_sale_day'      => Request::param('lock_sale_day/d',0),
                'num_referee_people' => Request::param('num_referee_people/d',0),
            ];
            $validate = $this->validate($data,'config.setting');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $rel = Configs::where(['member_miniapp_id' => $this->member_miniapp_id])->find();
            if(empty($rel)){
                $data['member_miniapp_id'] = $this->member_miniapp_id;
                $result = Configs::create($data);
            }else{
                $result = Configs::where(['member_miniapp_id' => $this->member_miniapp_id])->update($data);
            }
            if($result){
                return json(['code'=>200,'data' => ['url' => url('popupshop/config/index')],'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $view['info']  = Configs::where(['member_miniapp_id' => $this->member_miniapp_id])->find();
            return view()->assign($view);
        }
    }
}