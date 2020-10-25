<?php
/**
 * Created by JetBrains PhpStorm.
 * User: taotao
 * Date: 14-5-25
 * Time: 下午5:36
 * To change this template use File | Settings | File Templates.
 */
namespace Admin\Model;
use Think\Model;
class UserModel extends Model{
    protected $_validate = array(
        array('username','require','用户名不能为空。'),
        array('username','','用户名已经存在！',0,'unique',1),
        array('password','require','密码不能为空。'),
        array('confirm_password','password','确认密码不正确',0,'confirm'),
        array('email','require','邮箱不能为空'),
        array('email','email','邮箱格式不符合要求。'),
        array('email','','邮箱已经存在！',0,'unique',1),
    );
    protected $_auto = array (
        array('password','md5',3,'function') , // 对password字段在新增和编辑的时候使md5函数处理
        array('reg_time','time',1,'function'),
    );
}