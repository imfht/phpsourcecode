<?php
/**
 *
 * Created by PhpStorm.
 * User: root
 * Date: 8/16/16
 * Time: 8:26 PM
 * Vsersion:2.0.0
 */
class UserModel extends Model{
    protected $tableName = 'users';

    public function userinfo($user_id){
        $info=$this->where(['user_id'=>$user_id])->find();
        return $info;
    }
}