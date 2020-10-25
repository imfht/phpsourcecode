<?php
class UserModel extends PT_Model{

    public function checkInfo($username,$password) {
        if ($info=$this->db('user')->where(array('name'=>$username))->field('id,password,salt')->find()){
            if ($info['password']==md5(md5($password).$info['salt'])){
                return $info['id'];
            }
        }
        return false;
    }
}