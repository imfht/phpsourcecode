<?php
namespace app\common\model;

use think\Model;

class Verify extends Model
{
    protected $tableName = 'verify';
    protected $autoWriteTimestamp = true;
    // 关闭自动写入update_time字段
    protected $updateTime = false;

    public function addVerify($account,$type,$uid=0)
    {
        $uid = $uid?$uid:is_login();
        if ($type == 'mobile' || (modC('EMAIL_VERIFY_TYPE', 0, 'USERCONFIG') == 2 && $type == 'email')) {
            $verify = create_rand(6, 'num');
        } else {
            $verify = create_rand(32);
        }

        $this->where(['account'=>$account,'type'=>$type])->delete();
        $data['verify'] = $verify;
        $data['account'] = $account;
        $data['type'] = $type;
        $data['uid'] = $uid;
        
        $res = $this->save($data);
        if(!$res){
            return false;
        }
        return $verify;
    }

    public function getVerify($id){
        $verify = $this->where(['id'=>$id])->value('verify');
        return $verify;
    }
    public function checkVerify($account,$type,$verify,$uid){
        $verify = $this->where(array('account'=>$account,'type'=>$type,'verify'=>$verify,'uid'=>$uid))->find();
        if(!$verify){
            return false;
        }
        $this->where(array('account'=>$account,'type'=>$type))->delete();
        $this->where(['create_time'=>['<=',get_some_day(1)]])->delete();

        return true;
    }

}