<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 获取配置
 */
namespace app\green\controller\api\v1;
use app\green\controller\api\Base;
use app\green\model\GreenAdwords;
use app\green\model\GreenConfig;

class Config extends Base{

     /**
     * 获取应用配置
     **/
    public function index(){
        $info   = GreenConfig::getConfig($this->miniapp->id);
        $config = $info->config ? json_decode($info->config) : [];
        $config['service_telephone']  = $info->service_telephone ??'';
        $config['shore_img']          = $config->shore_img ??'';
        $config['shore_text']         = $config->shore_text ??'';
        $config['help']               = $info->help ? str_replace('<img', '<img class="img" style="max-width:100%;height:auto"',dehtml($info->help)):'';
        $config['icon']               = GreenAdwords::where(['member_miniapp_id' => $this->miniapp_id])->order('sort desc,id desc')->select()->toArray();
        return enjson(200,'应用配置',$config);
    }
}