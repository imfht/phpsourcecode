<?php
namespace Model;
use HY\Model;
!defined('HY_PATH') && exit('HY_PATH not defined.');
class User extends Model {
    public function read($id){
        //{hook m_user_read_1}
        return $this->find('*',array('id'=>$id));
    }
    //通过用户名获取用户数据
    public function user_read($user){
        //{hook m_user_user_read_1}
        return $this->find("*",array('user'=>$user));
    }
    //通过邮箱获取用户数据
    public function email_read($email){
        //{hook m_user_email_read_1}
        return $this->find("*",array('email'=>$email));
    }
    //判断账号是否存在  通过 ID
    public function is_id($id){
        //{hook m_user_is_id_1}
        return $this->has(array('id'=>$id));
    }
    //判断账号是否存在 通过用户名
    public function is_user($user){
        //{hook m_user_is_user_1}
        return $this->has(array('user'=>$user));
    }
    public function is_email($email){
        //{hook m_user_is_email_1}
        return $this->has(array('email'=>$email));
    }

    //增加账号
    public function add_user($user,$pass,$email,$group = 2){
        //{hook m_user_add_user_1}
        $salt = substr(md5(mt_rand(10000000, 99999999).NOW_TIME), 0, 8);
        //{hook m_user_add_user_2}
        return $this->insert(array(
            'user'=>$user,
            'pass'=>L("User")->md5_md5($pass,$salt),
            'email'=>$email,
            'salt'=>$salt,
            'atime'=>NOW_TIME,
            'group'=>$group,
            'ctime'=>NOW_TIME,
        ));
    }
    // 通过id获得用户名
    public function id_to_user($id){
        //{hook m_user_id_to_user_1}
        return $this->find('user',array('id'=>$id));
    }
    // 通过用户名获取 id
    public function user_to_id($user){
        //{hook m_user_user_to_id_1}
        return $this->find('id',array('user'=>$user));
    }
    //更新值 默认 金钱+1
    public function update_int($id,$key='gold',$type="+",$size=1){
        //{hook m_user_update_int_1}
        $key .= ($type=='+') ? '[+]' : '[-]';
        //{hook m_user_update_int_2}
        $this->update(array(
            $key=>$size
        ),array(
            'id'=>$id
        ));
        //{hook m_user_update_int_3}
    }
     //获取用户头像
    public function avatar($user){
        //{hook m_user_avatar_1}
        $path = INDEX_PATH . 'upload/avatar/' . md5($user);
        $path1 = 'upload/avatar/' . md5($user);
        //{hook m_user_avatar_2}
        if(!file_exists($path.'-a.jpg'))
            return array(
                'a'=>'public/images/user.gif',
                'b'=>'public/images/user.gif',
                'c'=>'public/images/user.gif',
            );
        //{hook m_user_avatar_3}
        return array(
            "a"=>$path1."-a.jpg",
            "b"=>$path1."-b.jpg",
            "c"=>$path1."-c.jpg"
        );
    }
    //获取用户金币
    public function get_gold($id){
        //{hook m_user_get_gold_1}
        return $this->find("gold",array('id'=>$id));
    }

    //获取用户积分 
    public function get_credits($uid){
        //{hook m_user_get_credits_1}
        return $this->find('credits',array('id'=>$uid));
    }

    //设置用户组
    public function set_group($uid,$id){
        //{hook m_user_set_group_1}
        return $this->update(array('group'=>$id),array('id'=>$uid));
    }
    //获取用户当前用户组ID
    public function get_group($uid){
        //{hook m_user_get_group_1}
        return $this->find('group',array('id'=>$uid));
    }
    public function get_fans($uid){
        //{hook m_user_get_fans_1}
        return $this->find('fans',array('id'=>$uid));
    }
    public function get_follow($uid){
        //{hook m_user_get_follow_1}
        return $this->find('follow',array('id'=>$uid));
    }
    //{hook m_user_fun}
    
}
