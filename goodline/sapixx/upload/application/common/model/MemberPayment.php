<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 
 * 用户支付配置表 Table<ai_member_payment>
 */
namespace app\common\model;
use think\Model;
use think\facade\Env;

class MemberPayment extends Model{

    protected $pk = 'id';

   /**
     * 获取配置参数
     * @param  array 数据
     * @return bool
     */
    public static function config(int $miniapp_id,string $apiname){
        $rel = self::field('config')->where(['member_miniapp_id' => $miniapp_id,'apiname' => $apiname])->find();
        if(empty($rel)){
          return false;
        }
        $config = json_decode($rel['config'],true);
        $config['cert_path'] = empty($config['cert_path'])? '': Env::get('runtime_path').'cert'.DS.$miniapp_id.DS.$config['cert_path'];
        $config['key_path']  = empty($config['key_path'])? '': Env::get('runtime_path').'cert'.DS.$miniapp_id.DS.$config['key_path'];
        return $config;
    }
}