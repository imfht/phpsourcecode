<?php
/**
 * 用户管理控制器
 * @author 楚羽幽 <houdunwangxj@gmail.com>
 */
class UserController extends AuthController
{
    private $db;

    public function __init()
    {
        parent::__init();
        $this->db = K("User");
    }

    /**
     * 用户列表
     * @return [type] [description]
     */
    public function index()
    {
        $page =  new page($this->db->count(),15);
        $this->page = $page->show();
        $data = $this->db->limit($page->limit())->order("uid ASC")->all();
        $this->assign('data', $data);
        $this->display();
    }


    /**
     * 添加用户
     */
    public function add()
    {
        if (IS_POST)
        {
            if ($this->db->addUser())
            {
                $this->success("用户添加成功！", 'index');
            }
            $this->error($this->db->error);
        }
        $this->role = M("role")->where(array('admin'=> 0))->order("rid DESC")->all();
        $this->display();
    }

    /**
     * 修改用户
     * @return [type] [description]
     */
    public function edit()
    {
        if (IS_POST)
        {
            if ($this->db->editUser())
            {
                $this->success("用户修改成功", 'index');
            }
            $this->error($this->db->error);
        }
        $uid = Q("uid", 0, "intval");
        if ($uid)
        {
            $field = $this->db->where(array('uid'=> $uid))->find();
            $role = M("role")->order("rid DESC")->all();
            $this->assign('field', $field);
            $this->assign('role', $role);
            $this->display();
        }
    }

    /**
     * 删除用户
     * @return [type] [description]
     */
    public function del()
    {
        if (IS_POST)
        {
            if ($this->db->delUser())
            {
                $this->success('删除成功');
            }
            $this->error = $this->db->error;
        }
        $this->assign('field', M('user')->find(Q('uid')));
        $this->display();
    }


    /*-------------------------------------------------------------属性定义--------------------------------------------------------------------*/

    // 上传头像
    public function avatar()
    {
        $uid = Q('uid', 0, 'intval');
        $avatar = $this->db->where(array('uid'=> $uid))->field('avatar')->find();
        p($avatar);exit;
    }
}