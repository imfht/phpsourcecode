<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-1-26
 * Time: 下午4:29
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */

namespace Common\Model;

use Think\Model;

class VerifyModel extends Model
{
    protected $tableName = 'verify';
    protected $_auto = array(array('create_time', NOW_TIME, self::MODEL_INSERT));



    public function addVerify($account,$type,$uid=0)
    {
        $uid = $uid?$uid:is_login();
        if ($type == 'mobile' || (modC('EMAIL_VERIFY_TYPE', 0, 'USERCONFIG') == 2 && $type == 'email')) {
            $verify = create_rand(6, 'num');
        } else {
            $verify = create_rand(32);
        }
        $this->where(array('account'=>$account,'type'=>$type))->delete();
        $data['verify'] = $verify;
        $data['account'] = $account;
        $data['type'] = $type;
        $data['uid'] = $uid;
        $data = $this->create($data);
        $res = $this->add($data);
        if(!$res){
            return false;
        }
        return $verify;
    }

    public function getVerify($id){
        $verify = $this->where(array('id'=>$id))->getField('verify');
        return $verify;
    }
    /**
     * 验证验证码
     * @param  text  $account 账号 如：59262424@qq.com、18618380435
     * @param  text  $type    账号类型 email或mobile
     * @param  intval  $verify  验证码
     * @param  integer $uid     用户id
     * @return bool           返回布尔
     */
    public function checkVerify($account,$type,$verify,$uid=0){
        $verify = $this->where(array('account'=>$account,'type'=>$type,'verify'=>$verify,'uid'=>$uid))->find();
        if(!$verify){
            return false;
        }
        $this->where(array('account'=>$account,'type'=>$type))->delete();
        //$this->where('create_time <= '.get_some_day(1))->delete();

        return true;
    }

}















