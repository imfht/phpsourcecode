<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户银行表 Table<ai_fastshop_bank_info>
 */
namespace app\fastshop\model;
use think\Model;
use think\facade\Validate;

class BankInfo extends Model{
    
    protected $pk     = 'id';
    protected $table  = 'ai_fastshop_bank_info';
    protected $autoWriteTimestamp = true;
    protected $createTime = false;
    
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

    //修改信息
    public function finds(array $param){
        return self::view('ai_fastshop_bank_info','*')->view('system_user','nickname,phone_uid','ai_fastshop_bank_info.user_id = system_user.id','left')->where($param)->find();
    }   
}