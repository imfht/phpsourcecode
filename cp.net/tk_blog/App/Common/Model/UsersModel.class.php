<?php
/**
 * 用户表模型
 */
namespace Common\Model;
use Think\Model;
class UsersModel extends Model {
    //自动验证
    protected $_validate = array(
        array('u_email', 'require', '邮箱不能为空！.^_^', 1 ),
        array('u_email','email','邮箱格式不正确！.^_^',1), //验证email字段格式是否正确
    );

    //用户注册
    public function do_Users(){
        $email = trim(I('post.u_email',0));
        $code = trim(I('post.code'));
        if (!$this->create())  return false;
        if (!check_verify_code($code)) {
            $this->error = '验证码错误.^_^';
            return false;
        }
        if ($this->where(array('u_email'=>$email))->count()) {
            $this->error = '该邮箱已被注册.^_^';
            return false;
        }
        //验证通过
        $user = getUserName($email);
        //生成数字
        $password = rand_string(6,1);
        $data=array(
            'uname' => $user,
            'u_email' => $email,
            'nickname' =>$user,
            'password' => encrypt_password($password),
            'last_login_ip' => get_client_ip(0,true),
            'add_time' => time(),
            'last_login_time' => time(),
            "user_type"=>2,//会员
        );
        $uid= $this->add($data);
        if ($uid) {
            $data['password'] = $password;
            $data['uid'] = $uid;
            return $data;
        } else {
            $this->error = '注册失败.^_^';
            return false;
        }

    }


    public function do_login(){
        $email = trim(I('post.u_email',0));
        $pwd = trim(I('post.password',0));
        if (!$this->create())  return false;
        if (!$pwd) {
            $this->error = '密码不能为空.^_^';
            return false;
        }
        //检测邮箱是否存在
        $oldData = $this->where(array('u_email'=>$email,'user_type'=>2))->find();
        if (!$oldData) {
            $this->error = '用户名或密码错误.^_^';
            return false;
        }
        if ($oldData['password'] != encrypt_password($pwd)) {
            $this->error = '用户名或密码错误.^_^';
            return false;
        }
        if ($oldData['is_black']) {
            $this->error = '用户已被锁定,请联系管理员.^_^';
            return false;
        }
        $data = array(
            'last_login_time' => time(),
            'last_login_ip' => get_client_ip(0,true),
        );
        if ($this->where(array('uid'=>$oldData['uid']))->save($data)) {
            $_SESSION['user'] = $oldData;
            return true;
        } else {
            $this->error = '登录失败.^_^';
            return false;
        }
    }

    /**
     * 查询所有数据并且显示分页
     * @param int $limit 每页显示多少条数据 默认显示10条
     * @return array
     */
    public function getListData($limit=10){
        $count = $this->count();   // 查询满足要求的总记录数
        $Page = new \Think\Page($count,$limit); // 实例化分页类 传入总记录数和每页显示的记录数
        //设置分页显示
        $Page->setConfig('prev','Prev');
        $Page->setConfig('next','Next');
        $show = $Page->show(); // 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $this->order('uid ASC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $result = array(
            'list' => $list,
            'page' => $show
        );
        return $result;
    }


    /**
     * 拉黑用户
     */
    public function execBlackData($uid){
        if (!$uid) return false;
        $where = array('uid'=>$uid,'user_type'=>2);
        $data = $this->where($where)->find();
        if ($data) {
            if ($this->where($where)->setField(array('is_black'=>1))) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * 修改密码
     */
    public function editPass(){
        $password = trim(I('post.password'));
        $oldPwd = trim(I('post.oldpwd'));
        $newPwd = trim(I('post.newPwd'));
        if (!$password || !$oldPwd || !$newPwd) {
            $this->error = '不允许提交空数据.^_^';
            return false;
        }
        if ($password != $newPwd) {
            $this->error = '二次密码输入不一致.^_^';
            return false;
        }
        $data = $this->where(array('uid'=>$_SESSION['admin_user']['uid'],'password'=>encrypt_password($oldPwd)))->getField('password');
        if ($data) {
            $i = $this->where(array('uid'=>$_SESSION['admin_user']['uid']))->setField(array('password'=>encrypt_password($password)));
            if ($i) {
                //清除session 重新登录
                unset($_SESSION['admin_user']);
                session_destroy();
                return true;
            } else {
                return false;
            }
        } else {
            $this->error = '原始密码输入错误.^_^';
            return false;
        }

    }


}