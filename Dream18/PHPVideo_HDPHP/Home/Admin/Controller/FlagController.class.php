<?php
/**
 * 推荐位属性管理
 * Class ContentControl
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class FlagController extends AuthController
{
    //模型
    private $db;

    public function __init()
    {
        parent::__init();
    }

    /**
     * 属性列表
     */
    public function index()
    {
        $this->display();
    }

    /**
     * 删除属性
     */
    public function del()
    {
        $index = Q('number');
        if (empty($index)) {
            $this->error('参数错误');
        }
        if ($this->db->delFlag($index)) {
            $this->success('删除成功');
        } else {
            $this->error($this->db->error);
        }
    }

    /**
     * 修改属性
     */
    public function edit()
    {
        if (IS_POST) {
            if ($this->db->editFlag()) {
                $this->success('修改成功');
            } else {
                $this->error($this->db->error);
            }
        } else {
            $this->error('参数错误');
        }
    }

    /**
     * 添加属性
     */
    public function add()
    {
        if (IS_POST) {
            if ($this->db->addFlag()) {
                $this->success('添加成功');
            } else {
                $this->error($this->db->error);
            }
        } else {
            $this->display();
        }
    }

    /**
     * 更新缓存
     */
    public function updateCache()
    {
        if ($this->db->updateCache()) {
            $this->success('推荐位缓存更新成功！');
        }
    }
}