<?php

namespace app\admin\controller;

use app\common\logic\Common as LogicCommon;

/**
 * 文章分类控制器
 */
class Articlecate extends AdminBase
{

    /**
     * 文章分类逻辑
     */

    private static $commonLogic = null;
    private static $menuSelect = [];

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();

        self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class, 'Articlecate');

    }

    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {

        $where = [];

        $where = empty($this->param['pid']) ? ['pid' => 0] : ['pid' => $this->param['pid']];

        if (!is_administrator()) {


        }
        $this->assign('pid', $where['pid']);

        return $where;
    }

    /**
     * 文章分类列表
     */
    public function articlecateList()
    {

        $where = $this->getWhere($this->param);

        $clist = self::$commonLogic->getDataList($where, true, 'id desc', 0, '', '', '', true);

        $this->assign('list', $clist['data']);

        $this->assign('page', $clist['page']);

        return $this->fetch('articlecate_list');
    }

    /**
     * 文章分类添加
     */
    public function articlecateAdd()
    {

        if (IS_POST) {

            $data = $this->setPidstrAttr($this->param);

            $this->jump(self::$commonLogic->dataAdd($data));
        }

        $where['status'] = 1;

        $groupcate_list = self::$commonLogic->getDataList($where, true, 'pid asc,sort desc', false);

        $groupcate_list = $this->menuToSelect(list_to_tree(array_values($groupcate_list), 'id', 'pid', 'children'));

        $this->assign('groupcate_list', $groupcate_list);

        return $this->fetch('articlecate_add');
    }

    /**
     * 文章分类编辑
     */
    public function articlecateEdit()
    {
        $info = self::$commonLogic->getDataInfo(['id' => $this->param['id']]);

        IS_POST && $this->jump(self::$commonLogic->dataEdit($this->param, ['id' => $this->param['id']], true));

        $where['status'] = 1;

        $groupcate_list = self::$commonLogic->getDataList($where, true, 'pid asc,sort desc', false);

        $groupcate_list = $this->menuToSelect(list_to_tree(array_values($groupcate_list), 'id', 'pid', 'children'));

        $this->assign('groupcate_list', $groupcate_list);

        $this->assign('info', $info);

        return $this->fetch('articlecate_edit');
    }

    public function setPidstrAttr($data)
    {
        $info = self::$commonLogic->getDataInfo(['id' => $data['pid']]);

        if (!$data['pid']) {

            $data['pidstr'] = 0;

        } else {

            $data['pidstr'] = $info['pidstr'] . '|' . $data['pid'];

        }


        return $data;
    }

    /**
     * 文章分类批量删除
     */
    public function articlecateAlldel($ids = 0)
    {

        $this->jump(self::$commonLogic->dataDel(['id' => $ids], '删除成功', true));
    }

    /**
     * 文章分类删除
     */
    public function articlecateDel($id = 0)
    {

        $this->jump(self::$commonLogic->dataDel(['id' => $id], '删除成功', true));
    }

}
