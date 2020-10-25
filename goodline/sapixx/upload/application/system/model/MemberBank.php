<?php
namespace app\system\model;
use think\Model;

class MemberBank extends Model{

    protected $pk = 'id';


    public function member(){
        return $this->hasOne('app\common\model\Member','id','member_id');
    }

    /**
     * 帐号余额
     */
    public function bank(){
        return self::view('member_bank','*')->view('member','username','member_bank.user_id = member.id')->order('id desc')->paginate(10);
    }

    /**
     * 查询账号金额够不够
     *
     * @param int $member_id
     * @param float $sellMoeny
     * @return void
     */
    public static function moneyJudge($member_id,$sellMoeny){
       $bank = self::where(['member_id' => $member_id])->find();
       if($bank){
           if($bank->money >= $sellMoeny){
               return false;
           }else{
               return true;
           }
       }else{
           return true;
       }
    }

    /**
     * 更改应用信息

     * @param int $member_id
     * @param float $money
     * @return void
     */
    public static function moneyUpdate($member_id,$money){
        $bank = MemberBank::where(['member_id' => $member_id])->find();
        if ($bank) {
            MemberBank::where(['member_id' => $member_id])->update(['money' => $money + $bank->money, 'update_time' => time()]);
        } else {
            $data = [
                'member_id'   => $member_id,
                'money'       => $money,
                'update_time' => time(),
            ];
            MemberBank::create($data);
        }
    }
}