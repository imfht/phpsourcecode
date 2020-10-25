<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace User\Api;
use User\Api\Api;
use User\Model\UcenterMemberModel;

class UserApi extends Api{
    /**
     * 构造方法，实例化操作模型
     */
    protected function _init(){
        $this->model = new UcenterMemberModel();
    }

    /**
     * 注册一个新用户
     * @param  string $username 用户名
     * @param  string $password 用户密码
     * @param  string $email    用户邮箱
     * @param  string $mobile   用户手机号码
     * @return integer          注册成功-用户信息，注册失败-错误编号
     */
    public function register($username,$nickname, $password, $email, $mobile = ''){
        return $this->model->register($username,$nickname, $password, $email, $mobile);
    }

    /**
     * 用户登录认证
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function login($username, $password, $type = 1){
        if(file_exists('./api/uc_login.lock')){
            include_once './api/uc_client/client.php';
            if(strtolower(UC_CHARSET) == 'gbk'){
                $username =  iconv('UTF-8', 'GBK', $username);
            }
            $uc_user = uc_user_login($username,$password,0);
            if($uc_user[0]==-2){
                return '密码错误';
            }
            elseif($uc_user[0]==-1){
                return '用户不存在，或者被删除';

            }
            elseif($uc_user[0]>0){
                if(strtolower(UC_CHARSET) == 'gbk'){
                    $uc_user[1] =  iconv('GBK', 'UTF-8', $uc_user[1]);
                }
                D('member')->where(array('uid'=>$uc_user[0]))->setField('nickname',$uc_user[1]);
                D('ucenter_member')->where(array('id'=>$uc_user[0]))->setField('username',$uc_user[1]);
                return $uc_user[0];
            }
        }else{
            if(UC_SYNC && $username != get_username(1)){
                return $this->ucLogin($username, $password);
            }
            return $this->model->login($username, $password, $type);
        }

    }

    public function ucLogin($username,$password){
        include_once './api/uc_client/client.php';
        //Ucenter 内数据
        $uc_user = uc_user_login($username,$password,0);
        //关联表内数据
        $uc_user_ref = tox_get_ucenter_user_ref('',$uc_user['0'],'');
        //登录
        if($uc_user_ref['uid'] && $uc_user_ref['uc_uid'] && $uc_user[0] > 0 ){
            return $uc_user_ref['uid'];
        }
        //本地帐号信息
        $tox_user = $this->model->getLocal($username,$password);
        // 关联表无、UC有、本地无的
        if( $uc_user[0] > 0 && !$tox_user['id'] ){
           $uid = $this->register($uc_user[1],$uc_user[1],$uc_user[2],$uc_user[3]);
            if($uid<=0){
                return A('Home/User')->showRegError($uid);
            }
            $result = tox_add_ucenter_user_ref($uid,$uc_user[0],$uc_user[1],$uc_user[3]);
            if(!$result){
                return '用户不存在或密码错误';
            }
            return $uid;
        }
        // 关联表无、UC有、本地有的
        if( $uc_user[0] > 0 && $tox_user['id'] > 0 ){
            $result = tox_add_ucenter_user_ref($tox_user['id'],$uc_user[0],$uc_user[1],$uc_user[3]);
            if(!$result){
                return '用户不存在或密码错误';
            }
            return  $tox_user['id'];
        }
        // 关联表无、UC无、本地有
        if( $uc_user[0] < 0 && $tox_user['id'] > 0 ){
            //写入UC
            $uc_uid = uc_user_register($tox_user['username'], $password, $tox_user['email'],'','', get_client_ip());
            if($uc_uid <= 0 ){
                return 'UC帐号注册失败，请联系管理员';
            }
            //写入关联表
            if( M('ucenter_user_link')->where(array('uid'=>$tox_user['id']))->find()){
                $result = tox_update_ucenter_user_ref($tox_user['id'],$uc_uid,$tox_user['username'],$tox_user['email']);
            }
            else{
                $result = tox_add_ucenter_user_ref($tox_user['id'],$uc_uid,$tox_user['username'],$tox_user['email']);
            }
            if(!$result){
                return '用户不存在或密码错误';
            }
            return $tox_user['id'];
        }

         //关联表无、UC无、本地无的
        return '用户不存在';

    }

    /**
     * 获取用户信息
     * @param  string  $uid         用户ID或用户名
     * @param  boolean $is_username 是否使用用户名查询
     * @return array                用户信息
     */
    public function info($uid, $is_username = false){
        return $this->model->info($uid, $is_username);
    }
    /**
     * 根据用户名和邮箱获取用户数据
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function lomi($username, $email){
        return $this->model->lomi($username, $email);
    }
    /**
     * 根据用户ID获取用户所以数据
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function reset($uid){
        return $this->model->reset($uid);
    }
    /**
     * 获取用户信息2
     * @param  string  $uid         用户ID或用户名
     * @param  boolean $is_username 是否使用用户名查询
     * @return array                用户信息
     */
    public function infos($regip){
        return $this->model->infos($regip);
    }
    /**
     * 检测用户名
     * @param  string  $field  用户名
     * @return integer         错误编号
     */
    public function checkUsername($username){
        return $this->model->checkField($username, 1);
    }

    /**
     * 检测邮箱
     * @param  string  $email  邮箱
     * @return integer         错误编号
     */
    public function checkEmail($email){
        return $this->model->checkField($email, 2);
    }

    /**
     * 检测手机
     * @param  string  $mobile  手机
     * @return integer         错误编号
     */
    public function checkMobile($mobile){
        return $this->model->checkField($mobile, 3);
    }

    /**
     * 更新用户信息
     * @param int $uid 用户id
     * @param string $password 密码，用来验证
     * @param array $data 修改的字段数组
     * @return true 修改成功，false 修改失败
     * @author huajie <banhuajie@163.com>
     */
    public function updateInfo($uid, $password, $data){
        if($this->model->updateUserFields($uid, $password, $data) !== false){
            $return['status'] = true;
        }else{
            $return['status'] = false;
            $return['info'] = $this->model->getError();
        }
        return $return;
    }
    /**
     * 重置用户密码2
     * @param int $uid 用户id
     * @param string $password 密码，用来验证
     * @param array $data 修改的字段数组
     * @return true 修改成功，false 修改失败
     * @author huajie <banhuajie@163.com>
     */
    public function updateInfos($uid, $data){
        if($this->model->updateUserFieldss($uid, $data) !== false){
            $return['status'] = true;
        }else{
            $return['status'] = false;
            $return['info'] = $this->model->getError();
        }
        return $return;
    }

    public function addSyncData(){
        return $this->model->addSyncData();
    }

}
