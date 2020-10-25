<?php
/**
 * 用户管理模型
 * Class UserModel
 */
class UserModel extends ViewModel
{
    public $table = "user";
    // 表关联
    public $view = array(
        'user' => array('_type' => "INNER"),
        'role' => array('_on' => 'user.rid=role.rid')
    );
    // 自动完成
    public $auto = array(
        // array(' 数据字段名 ',' 处理方法 ',' 方法类型 ',' 验证条件 ',' 处理时机 ')
        // 积分转整型
        array('credits', 'intval', 'function', 2, 3),
        // 密码自动加密
        array('password', 'md5', 'function', 2, 3),
        // 注册时间
        array('regtime', 'time', 'function', 2, 1),
        // 登录时间初始化
        array('logintime', 'time', 'function', 2, 1),
        // 注册IP
        array('regip', 'ip_get_client', 'function', 2, 1),
        // 登录IP初始化
        array('lastip', 'ip_get_client', 'function', 2, 1),
    );
    // 自动验证
    public $validate = array(
        array('username', 'nonull', '用户名不能为空', 2, 1),
        array('nickname', 'nonull', '昵称不能为空！', 2, 3),
        array('password', 'nonull', '密码不能为空', 2, 1),
        array('password', 'confirm:password_c', '密码不一致', 3, 3),
        array('email', 'email', '邮箱格式不正确', 3, 3),
        array('email', 'IsEmail', '邮箱已经存在', 3, 3)
    );


    /**
     * [addUser 添加帐号]
     */
    public function addUser()
    {
        if ($this->create())
        {
            $map['username'] = array('EQ', $this->data['username']);

            // 用户名检测
            if (M('user')->where($map)->find()) {
                $this->error = '「PHP联盟」温馨提示：用户名已经存在！';
                return false;
            }

            // 获取密码Key
            // $code = $this->getUserCode();

            // 数组重组
            // $this->data['code'] = $code;
            // $this->data['password'] = md5($this->data['password'] /*. $this->data['code']*/);
            // $this->data['nickname'] = $this->data['nickname'];
            // $this->data['regtime'] = time();
            // $this->data['logintime'] = time();
            // $this->data['regip'] = ip_get_client();
            // $this->data['lastip'] = ip_get_client();

            // 添加数据处理
            if ($this->add())
            {
                return true;
            }
            $this->error = '添加失败';
            return false;
        }
    }


    /**
     * 修改用户
     */
    public function editUser()
    {
        $uid = Q('uid', 0, 'intval');
        if ($this->create())
        {
            //没有添加密码时删除密码数据
            if (empty($_POST['password']))
            {
                unset($_POST['password']);
                unset($this->data['password']);

                unset($_POST['password_c']);
                unset($this->data['password_c']);
            }

            // 获取密码Key
            // $code = $this->getUserCode();
            
            // 数组重组
            // $this->data['code'] = $code;
            // $this->data['password'] = md5($this->data['password']/*. $code*/);
        
            // 更新数据处理
            if ($this->where(array('uid'=> $uid))->save())
            {
                return true;
            }
            $this->error = '修改失败';
        }
    }


    /**
     * 删除用户
     * @return mixed
     */
    public function delUser()
    {
        $uid = Q('uid', 0, 'intval');

        //删除文章
        if (Q('post.delcontent'))
        {
            $map = array(
                'uid' => array('EQ', $uid)
            );
            $ModelCache = S('model');
            foreach ($ModelCache as $model) {
                $contentModel = ContentModel::getInstance($model['mid']);
                $contentModel->where($map)->del();
            }
        }

        //评论表存在时删除评论
        if (Q('post.delcomment') && M()->tableExists('addon_comment')) {
            $map = array(
                'userid' => array('EQ', $uid)
            );
            M('addon_comment')->where($map)->del();
        }

        //删除附件
        if (Q('post.delupload')) {
            $map = array(
                'uid' => array('EQ', $uid)
            );
            M('upload')->where($map)->del();
        }

        //后台历史菜单
        M('menu_favorite')->where($map)->del();
        $map = array(
            'space_uid' => array('EQ', $uid),
            'guest_uid' => array('EQ', $uid),
            '_logic' => 'OR'
        );

        //删除访客
        M('user_guest')->where($map)->del();

        //日志记录
        $map = array(
            'uid' => array('EQ', $uid)
        );
        
        M('user_credits')->where($map)->del();
        if (M('user')->del($uid))
        {
            return true;
        }
        $this->error = '删除失败';
    }


    /*----------------------------------属性定义---------------------------------------*/

    /**
     * [IsEmail 邮箱验证]
     * @param [type] $name  [description]
     * @param [type] $value [description]
     * @param [type] $msg   [description]
     * @param [type] $arg   [description]
     */
    public function IsEmail($name, $value, $msg, $arg)
    {
        $uid = Q('uid', 0, 'intval');
        if ($uid)
        {
            $map['uid'] = array('NEQ', $uid);
        }
        $map['email'] = array('EQ', $value);
        if (M('user')->where($map)->find())
        {
            return $msg;
        }
        return true;
    }


    /**
     * 获取用户密码加密key
     * @return string
     */
    public function getUserCode()
    {
        return substr(md5(C("AUTH_KEY") . mt_rand(1, 1000) . time() . C('AUTH_KEY')), 0, 10);
    }
}