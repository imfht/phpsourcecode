<?php

class userModel extends RelationModel
{
    protected $_validate = array(
        array('username', 'require', '{%username_require}'), //不能为空
        array('repassword', 'password', '{%inconsistent_password}', 0, 'confirm'), //确认密码
        array('email', 'email', '{%email_error}'), //邮箱格式
        array('username', '1,20', '{%username_length_error}', 0, 'length', 1), //用户名长度
       
        array('username', '', '{%username_exists}', 1, 'unique', 1), //检测重复
    );

    protected $_auto = array(
        array('password','webmd5',1,'function'), //密码加密
        array('add_time','time',1,'function'), //注册时间
    );
    protected $_link = array(
        //关联角色
        'rolename' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'user_role',
            'foreign_key' => 'roleid',
            'parent_key' => 'id',
            'mapping_fields'=>'name',
            'auto_prefix' => true
        ),
        'userinfo' => array(
            'mapping_type' => HAS_ONE,
            'class_name' => 'userinfo',
            'foreign_key' => 'uid',
            'parent_key' => 'uid',
            'auto_prefix' => true
        )
    );
    /**
     * 修改用户名
     */
    public function rename($map, $newname) {
        if ($this->where(array('username'=>$newname))->count('uid')) {
            return false;
        }
        $this->where($map)->save(array('username'=>$newname));
        $uid = $this->where(array('username'=>$newname))->getField('uid');
        return true;
    }

    public function name_exists($name, $id = 0) {
        $where = "username='" . $name . "' AND uid<>'" . $id . "'";
        $result = $this->where($where)->count('uid');
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function email_exists($email, $id = 0) {
        $where = "email='" . $email . "' AND uid<>'" . $id . "'";
        $result = $this->where($where)->count('uid');
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}