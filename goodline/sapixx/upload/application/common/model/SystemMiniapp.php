<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 
 * 会员管理 
 */
namespace app\common\model;
use think\Model;

class SystemMiniapp extends Model{

    protected $pk     = 'id';

    /**
     * 管理小程序
     * @param  array $param 
     */
    public static function edit(array $param){
        $data['types']         = $param['types'];
        $data['title']         = $param['title'];
        $data['view_pic']      = $param['view_pic'];
        $data['style_pic']     = json_encode($param['style_pic']);
        $data['version']       = $param['version'];
        $data['expire_day']    = $param['expire_day'];
        $data['sell_price']    = $param['sell_price'];
        $data['market_price']  = $param['market_price'];
        $data['is_manage']     = $param['is_manage'];
        $data['is_diyapp']     = $param['is_diyapp'];
        $data['is_wechat_pay'] = $param['is_wechat_pay'];
        $data['is_alipay_pay'] = $param['is_alipay_pay'];
        $data['miniapp_dir']   = $param['miniapp_dir'];
        $data['is_openapp']    = $param['is_openapp'];
        $data['template_id']   = $param['template_id'];
        $data['describe']      = $param['describe'];
        $data['qrcode']        = $param['qrcode'];
        $data['content']       = $param['content'];
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