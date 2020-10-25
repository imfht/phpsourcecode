<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 
 * 用户管理 Table<ai_member>
 */
namespace app\common\model;
use think\Model;

class Miniapp extends Model{

    protected $pk = 'id';

    /**
     * 管理小程序
     * @param  array $param 
     */
    public static function edit(array $param){
        $data['types']         = trim($param['types']);
        $data['title']         = trim($param['title']);
        $data['view_pic']      = trim($param['view_pic']);
        $data['style_pic']     = json_encode($param['style_pic']);
        $data['version']       = trim($param['version']);
        $data['expire_day']    = trim($param['expire_day']);
        $data['sell_price']    = trim($param['sell_price']);
        $data['market_price']  = trim($param['market_price']);
        $data['is_manage']     = trim($param['is_manage']);
        $data['is_wechat_pay'] = trim($param['is_wechat_pay']);
        $data['is_alipay_pay'] = trim($param['is_alipay_pay']);
        $data['miniapp_dir']   = trim($param['miniapp_dir']);
        $data['is_openapp']    = trim($param['is_openapp']);
        $data['template_id']   = trim($param['template_id']);
        $data['describe']      = trim($param['describe']);
        $data['qrcode']        = trim($param['qrcode']);
        $data['content']       = trim($param['content']);
        $data['update_time']   = time();
        if(isset($param['id']) && $param['id'] > 0){
            return self::where('id',$param['id'])->update($data);
        }else{
            $data['create_time']  = time();
            return self::insert($data);
        }
    }

    /**
     * 锁定用户
     * @param integer $id
     */
    public static function lock(int $id){
        $result = self::where(['id' => $id])->find();
        $result->is_lock  = $result->is_lock ? 0 : 1;
        return $result->save();
    } 
}