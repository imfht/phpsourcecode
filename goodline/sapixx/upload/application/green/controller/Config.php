<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 应用配置
 */
namespace app\green\controller;
use app\green\model\GreenConfig;

class Config extends Common{

    public function initialize(){
        parent::initialize();
        if(!$this->founder){
            $this->error('您无权限操作');
        }
    }
   
    //文案配置
    public function index(){
        $info =  GreenConfig::where(['member_miniapp_id' => $this->member_miniapp_id])->find();
        if(request()->isAjax()){
            $data = [
                'shore_img'  => $this->request->param('shore_img/s'),
                'shore_text' => $this->request->param('shore_text/s')
            ];
            if(empty($info)){
                $info = new GreenConfig;
                $info->member_miniapp_id = $this->member_miniapp_id;
            }
            $info->config = json_encode($data);
            $info->service_telephone = $this->request->param('service_telephone/s');
            $info->is_wechat_touser  = $this->request->param('is_wechat_touser/d');
            $result = $info->save();
            if($result){
                return enjson(200,'操作成功',['url' => url('config/index')]);
            }else{
                return enjson(0,'操作失败');
            }
        }else{
            $view['pathMaps'] = [['name' =>' 文案配置','url' => url("config/index")]];
            $view['config']   = empty($info->config) ? '' : json_decode($info->config,true);
            $view['info']     = $info;
            return view()->assign($view);
        }
    }

    /**
     * 投递指南
     * @return void
     */
    public function help(){
        if(request()->isAjax()){
            $data['help'] = $this->request->param('help/s');
            $result =  GreenConfig::configs($data,$this->member_miniapp_id);
            if($result){
                return enjson(200,'操作成功',['url' => url('config/help')]);
            }else{
                return enjson(0,'操作失败');
            }
        }else{
            $view['config']   = GreenConfig::getConfig($this->member_miniapp_id);
            $view['pathMaps']= [['name' =>' 投递指南','url' => url("config/help")]];
            return view()->assign($view);
        }
    }
}