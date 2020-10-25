<?php

/**
 * 配置组管理
 * Class ConfigGroupController
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class ConfigGroupController extends AuthController
{
    /**
     * [$db 数据对象]
     * @var [type]
     */
    private $db;

    public function __init()
    {
        parent::__init();
        $this->db = K('ConfigGroup');
    }

    /**
     * 配置组列表
     */
    public function index()
    {
        //获取组列表
        $data = $this->db->getGroup(1);
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 添加组
     */
    public function add()
    {
        if (IS_POST)
        {
            if ($this->db->addConfigGroup())
            {
                $this->success('添加成功', 'index');
            }
            $this->error($this->db->error);
        }
        $this->display();
    }

    /**
     * @return [type] [修改配置组]
     */
    public function edit()
    {
        if (IS_POST)
        {
            if ($this->db->editConfigGroup())
            {
                $this->success('修改成功', 'index');
            }
            $this->error($this->db->error);
        }
        else
        {
            $cid = Q('cid', 0, 'intval');
            $field = $this->db->find($cid);
            if (!$field)
            {
                $this->error($this->db->error);
            }
            $this->assign("field", $field);
            $this->display();
        }
    }

    /**
     * 删除配置组
     */
    public function del()
    {
        if ($this->db->delConfigGroup())
        {
            $this->success('删除成功');
        }
        $this->error($this->db->error);
    }
}