<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户银行信息表 Table<ai_user_bank>
 */
namespace app\common\model;
use think\Model;
use think\facade\Validate;

class UserBank extends Model{
    
    protected $pk     = 'id';
    protected $autoWriteTimestamp = true;
    protected $createTime = false;
    
    /**
     * 用户信息关联
    * @return void
     */
    public function user(){
        return $this->hasOne('User','id','user_id');
    }

    //修改信息
    public static function editer(int $miniapp_id,int $uid,array $param){
        $info = self::where(['member_miniapp_id' => $miniapp_id,'user_id' => $uid])->find();
        $data['name']        = trim($param['name']);
        $data['bankname']    = trim($param['bankname']);
        $data['bankid']      = trim($param['bankid']);
        $data['idcard']      = trim($param['idcard']);
        $data['update_time'] = time();
        if(empty($info)){
            $data['member_miniapp_id'] = $miniapp_id;
            $data['user_id']           = $uid;
            return self::insert($data);
        }
        return self::where(['member_miniapp_id' => $miniapp_id,'user_id' => $uid])->update($data);
    } 
}