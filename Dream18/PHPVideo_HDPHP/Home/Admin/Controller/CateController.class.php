<?php

/**
 * 频道管理模块
 * Class CategoryController
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class CateController extends AuthController
{
    // 数据私有对象
    private $cate, $db, $cid;

    /**
     * 构造函数
     */
    public function __init()
    {
        parent::__init();
        $this->cate = S("cate");
        $this->db = K('Cate');
        $this->cid = Q('cid', 0, 'intval');
    }

    /**
     * 显示频道列表
     * @return [type] [description]
     */
    public function index()
    {
        $this->assign("cate", $this->cate);
        // 视图显示
        $this->display();
    }



    /**
     * [add 添加频道到表]
     */
    public function add()
    {
        //添加频道
        if (IS_POST)
        {
            if ($this->db->addCategory())
            {
                $this->success('添加频道成功', 'index');
            }
            $this->error($this->db->error);
        }

        // 分配模板数据
        $this->assign('cate', $this->cate);
        $this->display();
    }


    /**
     * 修改频道到表
     * @return [type] [description]
     */
    public function edit()
    {
        if (IS_POST)
        {
            if ($this->db->editCategory())
            {
                $this->success('修改频道成功', 'index');
            }
            $this->error($this->db->error);
        }
        else
        {
            if (!$this->cid || !isset($this->cate[$this->cid]))
            {
                $this->error('频道不存在');
            }

            $cache = $this->cate;
            $cate = $cache[$this->cid];
            foreach ($cache as $n => $cat)
            {
                //父频道select状态
                $selected = $cate['pid'] == $cat['cid'] ? 'selected=""' : '';
                //子频道disabled
                $disabled = Data::isChild($this->cate, $cat['cid'], $this->cid) || $this->cid == $cat['cid'] ? 'disabled=""' : '';
                $cache[$n]['selected'] = $selected;
                $cache[$n]['disabled'] = $disabled;
            }

            // 分配模板数据
            $this->assign('field', $cate);
            $this->assign('cate', $cache);
            $this->display();
        }
    }


    /**
     * 删除频道
     */
    public function del()
    {
        if ($this->db->delCategory($this->cid))
        {
            $this->success('删除频道成功');
        }
        $this->error($this->db->error);
    }


    /*--------------------------------------属性定义---------------------------------------*/

    /**
     * 频道排序
     */
    public function updateOrder()
    {
        if ($this->db->updateOrder())
        {
            $this->success('排序成功');
        }
    }


    /**
     * 频道名称转拼音静态目录
     */
    public function dir_to_pinyin()
    {
        $dir = String::pinyin(Q("catname"));
        $pid = Q('pid', 0, 'intval');
        if ($pid)
        {
            echo $this->cate[$pid]['catdir'] . '/' . $dir;
        }
        else
        {
            echo $dir;
        }
        exit;
    }


    /**
     * 选择模板
     */
    public function Theme()
    {
        if (!$dir = Q('dir'))
        {
            $dir = 'Theme/' . C('WEB_STYLE');
        }

        $file = Dir::tree($dir, 'html');
        $this->assign('id', Q('id'));
        $this->assign('file', $file);
        $this->display();
    }

    /**
     * 更新频道缓存
     */
    public function updateCache()
    {
        if ($this->db->updateCache())
        {
            $this->success('更新频道缓存成功！');
        }
        else
        {
            $this->error($this->db->error);
        }
    }

}
