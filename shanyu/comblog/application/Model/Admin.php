<?php
namespace App\Model;

use Kernel\config;
use Kernel\Loader;
use App\Model\Admin as UserModel;

class Admin extends Model
{
    public function getDataByUsername($username)
    {
        $query="SELECT * FROM admin WHERE username='{$username}' LIMIT 1";
        $data=$this->db()->query($query)->fetch();
        return $data;
    }
    public function getDataByCookieToken($cookie_token)
    {
        $query="SELECT * FROM admin WHERE cookie_token='{$cookie_token}' LIMIT 1";
        $data=$this->db()->query($query)->fetch();
        return $data;
    }
    public function loginHandle($id,$data)
    {
        $update_data = [];
        foreach ($data as $k => $v) {
            $update_data[] = "{$k} = '{$v}'";
        }
        $update = implode(',', $update_data);
        $query = "UPDATE admin SET {$update} WHERE id = {$id}";
        $status=$this->db()->exec($query);
        return $status;
    }
    public function logoutHandle($id)
    {
        $query = "UPDATE admin SET session_id='',cookie_token='',cookie_expire=0 WHERE id = {$id}";
        $status=$this->db()->exec($query);
        return $status;
    }

    public function encryptPassword($password)
    {
        return md5(Config::instance()->get('encrypt').$password);
    }

    public function findOrCreate($username,$password)
    {
        $account = $this->getDataByUsername($username);
        if($account) return $account;

        $password = $this->encryptPassword($password);
        $query = "INSERT INTO admin (username,password) VALUES ('{$username}','{$password}')";
        $this->db()->exec($query);

        return $this->getDataByUsername($username);
    }
}