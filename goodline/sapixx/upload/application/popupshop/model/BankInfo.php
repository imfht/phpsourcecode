<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户银行表 Table<ai_popupshop_bank_info>
 */
namespace app\popupshop\model;
use think\Model;
use think\facade\Validate;

class BankInfo extends Model{
    
    protected $pk     = 'id';
    protected $table  = 'ai_popupshop_bank_info';
    protected $autoWriteTimestamp = true;
    protected $createTime = false;
    
    //用户
    public function user(){
        return $this->hasOne('app\common\model\SystemUser','id','user_id');
    }
    
    //修改信息
    public static function editer(int $miniapp_id,int $uid,array $param){
        $info = self::where(['member_miniapp_id' => $miniapp_id,'user_id' => $uid])->find();
        $data['name']              = $param['name'];
        $data['bankname']          = $param['bankname'];
        $data['bankid']            = $param['bankid'];
        $data['idcard']            = $param['idcard'];
        $data['update_time'] = time();
        if(empty($info)){
            $data['member_miniapp_id'] = $miniapp_id;
            $data['user_id']           = $uid;
            return self::insert($data);
        }
        return self::where(['member_miniapp_id' => $miniapp_id,'user_id' => $uid])->update($data);
    }
}