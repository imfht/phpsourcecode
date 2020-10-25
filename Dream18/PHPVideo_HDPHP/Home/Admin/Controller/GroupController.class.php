<?php

/**
 * 会员组管理
 * Class GroupControl
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class GroupController extends AuthController
{
    //模型
    private $db;

    //构造函数
    public function __init()
    {
        parent::__init();
        $this->db = K('Group');

        // 站长权限检测
        /*if (!IS_WEBMASTER)
        {
            $this->error('没有操作权限');
        }*/
    }

    //角色列表
    public function index()
    {
        $this->assign('data', M('role')->where(array('admin'=> 0))->order('rid ASC')->all());
        $this->display();
    }

    /**
     * 添加角色
     */
    public function add()
    {
        if (IS_POST)
        {
            if ($aid = $this->db->addRole())
            {
                $this->success('添加成功', 'index');
            }
            $this->error($this->db->error);
        }
        $this->display();
    }

    /**
     * 编辑角色
     * @return [type] [description]
     */
    public function edit()
    {
        if (IS_POST)
        {
            if ($this->db->editRole($_POST))
            {
                $this->success('修改成功', 'index');
            }
            $this->error($this->db->error);
        }
        $rid = Q('rid', 0, 'intval');
        $this->assign('field', M('role')->find($rid));
        $this->display();
    }

    /**
     * 删除角色
     * @return [type] [description]
     */
    public function del()
    {
        if ($this->db->delRole()) {
            $this->success('删除成功');
        } else {
            $this->error($this->db->error);
        }
    }
}