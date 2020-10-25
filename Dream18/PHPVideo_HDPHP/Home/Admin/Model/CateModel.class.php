<?php

/**
 * 栏目管理模型
 * Class CategoryModel
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class CateModel extends ViewModel
{
    // 数据主表
    public $table = "cate";
    private $cid;
    //栏目缓存
    private $cate;
    //栏目类型
    private $categoryType = array(1 => '普通频道', 2 => '<span style="color:blue;">封面频道</span>', 3 => '<span style="color:red;">外链频道</span>');

    /**
     * 表单验证
     * @var array
     */
    public $validate = array(
        array('catname', 'nonull', '栏目名称不能为空', 2, 3),
        array('catname', 'maxlen:30', '栏目名不能超过30个字', 2, 3),
        array('catdir', 'nonull', '静态目录不能为空', 2, 3)
    );

    /**
     * 构造函数
     */
    public function __init()
    {
        $this->cate = S("cate");
        $this->cid = Q('cid', 0, 'intval');
    }

    /**
     * 添加栏目
     * @return bool
     */
    public function addCate()
    {
        if ($this->create())
        {
            $cid = $this->add();
            if ($cid)
            {
                //更新缓存
                return $this->updateCache();
            }
            $this->error = '栏目添加失败';
            return false;
        }
    }


    /**
     * 修改栏目
     * @return bool
     */
    public function editCate()
    {
        // 检测栏目是否存在
        if (!M('cate')->find($this->cid))
        {
            $this->error = '栏目不存在';
            return false;
        }
        if ($this->create())
        {
            if ($this->save())
            {
                return $this->updateCache();
            }
            $this->error = '修改栏目失败';
            return false;
        }
    }


    /**
     * 删除栏目
     * @param $cid
     * @return bool
     */
    public function delCate($cid)
    {
        if (!$cid || !isset($this->cate[$cid]))
        {
            $this->error = 'cid参数错误';
            return false;
        }

        /**
         * 获得子栏目
         */
        $cate = Data::channelList($this->cate, $cid);
        $cate[]['cid'] = $cid;
        foreach ($cate as $cat)
        {
            //删除栏目权限
            M("cate_access")->where(array('cid'=> $cat['cid']))->del();
            //删除栏目
            $this->del($cat['cid']);
        }
        // 更新缓存
        return $this->updateCache();
    }
    

    /*---------------------------------属性定义---------------------------------------*/


    /**
     * 更新栏目排序
     */
    public function updateOrder()
    {
        $list_order = Q("post.list_order", array());
        $db = M('cate');
        foreach ($list_order as $cid => $order) {
            $cid = intval($cid);
            $order = intval($order);
            $data = array("cid" => $cid, "catorder" => $order);
            $db->save($data);
        }
        //重建缓存
        return $this->updateCache();
    }

    /**
     * 更新栏目缓存
     * @return bool
     */
    public function updateCache()
    {
        $data = $this->order("catorder ASC,cid ASC")->all();
        // 缓存数据
        $cache = array();
        if ($data)
        {
            $data = Data::tree($data, "catname", "cid", "pid");
            foreach ($data as $cat)
            {
                // 封面与链接栏目添加disabled属性
                $cat["disabled"] = $cat["cattype"] != 1 ? 'disabled=""' : '';
                $cat['cat_type_name'] = $this->categoryType[$cat['cattype']];
                $cache[$cat['cid']] = $cat;
            }
        }
        if (S("cate", $cache))
        {
            return true;
        }
        $this->error = '更新缓存失败';
        return false;
    }
}