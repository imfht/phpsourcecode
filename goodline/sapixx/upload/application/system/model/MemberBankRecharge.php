<?php
namespace app\system\model;
use think\Model;

class MemberBankRecharge extends Model{

    protected $pk = 'id';

    /**
     * 创建充值记录
     * @param [type] $data
     * @return void
     */
    public static function order($data){
        $memberBankRecharge = [
            'member_id'   => $data['member_id'],
            'create_time' => time(),
            'order_sn'    => $data['order_sn'],
            'money'       => $data['money'],
            'state'       => 0,
        ];
        return self::insert($memberBankRecharge);
    }

}