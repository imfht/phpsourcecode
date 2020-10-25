<?php
namespace App\Model;

use Kernel\config;

class AdminLoginLog extends Model
{
    public function insert($user_id)
    {
        $data=[];
        $data['admin_id']=$user_id;
        $data['ip'] =htmlspecialchars($_SERVER['REMOTE_ADDR']);
        $data['lang']=htmlspecialchars($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $data['browser']=htmlspecialchars($_SERVER['HTTP_USER_AGENT']);

        $insert_keys = implode(",", array_keys($data));
        $insert_values = "'".implode("','", array_values($data))."'";

        $query = "INSERT INTO admin_login_log ({$insert_keys}) VALUES ({$insert_values})";
        $status=$this->db()->exec($query);
        return $status;
    }
}