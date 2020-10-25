<?php
namespace Model;
use HY\Model;
!defined('HY_PATH') && exit('HY_PATH not defined.');
class Usergroup extends Model {
    // $id 用户组ID 返回权限数组
    public function read_json($id){
        //{hook m_usergroup_read_json_1}
        $json = $this->select("json",array(
            "id"=>$id
        ));
        //{hook m_usergroup_read_json_2}
        return json_decode($json,true);
    }

    
    public function id_to_name($id){
        //{hook m_usergroup_id_to_name_1}
        return $this->find("name",array(
            'id'=>$id
        ));
    }
    public function format(&$usergroup){
        //{hook m_usergroup_format_1}
        if(empty($usergroup))
            return;
        //{hook m_usergroup_format_2}
        $tmp = $usergroup;
        $usergroup = array();
        //{hook m_usergroup_format_3}
        foreach ($tmp as $k => $v) {
            $usergroup[intval($v['id'])] = $v;
        }
    }
    //{hook m_usergroup_fun}
}
