<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 
 * 管理用户表
 */

namespace app\common\model;
use think\Model;
use util\Util;

class SystemAdmin extends Model{
    
    protected $pk    = 'id';

    /**
     * 添加或编辑用户
     * @param  array 数据
     * @return bool
     */
    public static function updateUser($param){
        $data['username']        = $param['username'];
        $data['about']           = $param['about'];
        $data['last_login_ip']   = Util::getIp();
        $data['last_login_time'] = time();
        $data['update_time']     = time();
        if(isset($param['id']) && $param['id'] > 0){
            if(!empty($param['password'])){
                $data['password']  = password_hash(md5($param['password']),PASSWORD_DEFAULT);
            }
        }else{
            $data['password']  = password_hash(md5($param['password']),PASSWORD_DEFAULT);
        }
        if(isset($param['id']) && $param['id'] > 0){
            return SystemAdmin::where('id',$param['id'])->update($data);
        }else{
            $data['create_time']  = time();
            return SystemAdmin::insert($data);
        }
    }

    /**
     * 判断登录用户
     * @access public
     * @return bool
     */
    public static function login($param){
        $result = SystemAdmin::where(['username' => $param['login_id'],'locks' => 0])->find();
        if($result){
            if(!password_verify(md5($param['login_password']),$result->getAttr('password'))) {
                return FALSE;
            }
            $result->last_login_time = time();
            $result->last_login_ip   = request()->ip();
            $result->save();
            return $result;
        }
        return FALSE;
    }


    /**
     * 修改我的密码
     * @access public
     */
    public static function upDatePasspowrd($param){
        $data = [
            'password' => password_hash(md5($param['password']),PASSWORD_DEFAULT),
            'about'    => $param['about']
        ];
        $where = ['id' => $param['login']['admin_id']];
        return SystemAdmin::where($where)->update($data);
    } 
}