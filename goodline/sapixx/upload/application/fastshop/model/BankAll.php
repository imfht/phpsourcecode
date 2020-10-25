<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户银行表 Table<ai_fastshop_bank>
 */
namespace app\fastshop\model;
use think\Model;

class BankAll extends Model{
    
    protected $pk     = 'id';
    protected $table  = 'ai_fastshop_bank_all';
    protected $createTime = false;

      //用户
    public function user(){
        return $this->hasOne('app\common\model\SystemUser','id','uid');
    } 

    /**
     * 增加有多少流水
     */
    public static function add(int $app_id,int $uid,float $money){
        $info = self::where(['uid' => $uid])->find();
        if(empty($info)){
            $data['uid']               = $uid;
            $data['account']           = $money;
            $data['member_miniapp_id'] = $app_id;
            return self::insert($data);
        }else{
            $info->account = ['inc',$money];
            return $info->save();
        }
    }
}