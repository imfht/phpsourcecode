<?php
/**
 * 后台RBAC角色管理
 * Class RoleControl
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class RoleController extends AuthController
{
    //模型
    private $db;

    //构造函数
    public function __init()
    {
        parent::__init();
        $this->db = K('Role');
    }

    /**
     * 角色列表
     * @return [type] [description]
     */
    public function index()
    {
        $AdminRole = $this->db->where(array('admin'=> 1))->all();
        $this->assign('data', $AdminRole);
        $this->display();
    }

    /**
     * 添加角色
     */
    public function add()
    {
        if (IS_POST)
        {
            if ($this->db->addRole())
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
        if (IS_POST) {
            if ($this->db->editRole()) {
                $this->success('修改成功', 'index');
            } else {
                $this->error($this->db->error);
            }
        }
        $rid = Q('rid', 0, 'intval');
        if ($rid)
        {
            $this->assign('field', M('role')->find($rid));
            $this->display();
        }
    }

    /**
     * 删除角色
     * @return [type] [description]
     */
    public function del()
    {
        if ($this->db->delRole())
        {
            $this->success('删除角色成功！', 'index');
        }
        $this->error('参数错误');
    }
}
