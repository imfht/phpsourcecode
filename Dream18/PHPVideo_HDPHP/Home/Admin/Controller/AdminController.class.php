<?php

/**
 * 管理员管理模块
 * Class AdministratorControl
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class AdminController extends AuthController
{
    private $db;

    public function __init()
    {
        parent::__init();
        $this->db = K("Admin");
        /*if (!IS_WEBMASTER) {
            $this->error('没有操作权限');
        }*/
    }

    /**
     * 管理员列表
     */
    public function index()
    {
        $data = $this->db->where(array('admin'=> 1))->order("uid ASC")->all();
        $this->assign('data', $data);
        $this->display();
    }


    //添加管理员
    public function add()
    {
        if (IS_POST)
        {
            if ($this->db->addUser())
            {
                $this->success("添加成功！", 'index');
            }
            $this->error($this->db->error);
        }
        $this->role = M("role")->where('admin=1')->order("rid DESC")->all();
        $this->display();
    }

    //修改管理员
    public function edit()
    {
        if (IS_POST)
        {
            $uid = Q('uid', 0, 'intval');
            $_POST['uid'] = $uid;
            if ($this->db->editUser($_POST))
            {
                $this->success("修改成功！", 'index');
            }
            $this->error($this->db->error);
        }
        $uid = Q("request.uid", null, "intval");
        if ($uid)
        {
            //会员信息
            $this->field = $this->db->where(array('uid'=> $uid))->find();
            $this->role = M("role")->where(array('admin'=> 1))->order("rid DESC")->all();
            $this->display();
        }
    }


    //删除管理员
    public function del()
    {
        $uid = Q("POST.uid", 0, "intval");
        if ($this->db->delUser($uid))
        {
            $this->success('删除成功');
        }
    }

}
