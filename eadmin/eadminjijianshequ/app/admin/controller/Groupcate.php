<?php

namespace app\admin\controller;

use app\common\logic\Common as LogicCommon;

/**
 * 小组分类控制器
 */
class Groupcate extends AdminBase
{

    private static $commonLogic = null;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();
        self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class, 'groupcate');
    }

    public function getWhere($data = [])
    {

        $where = [];

        $where['status|>='] = 0;


        if (!is_administrator()) {


        }

        return $where;
    }

    /**
     * 小组分类列表
     */
    public function groupcateList()
    {

        $where = $this->getWhere($this->param);

        $clist = self::$commonLogic->getDataList($where, true, 'id desc');

        $this->assign('list', $clist['data']);

        $this->assign('page', $clist['page']);

        return $this->fetch('groupcate_list');
    }

    /**
     * 小组分类添加
     */
    public function groupcateAdd()
    {

        IS_POST && $this->jump(self::$commonLogic->dataAdd($this->param));

        return $this->fetch('groupcate_add');
    }

    /**
     * 小组分类编辑
     */
    public function groupcateEdit()
    {

        IS_POST && $this->jump(self::$commonLogic->dataEdit($this->param, ['id' => $this->param['id']]));

        $info = self::$commonLogic->getDataInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);
        return $this->fetch('groupcate_edit');
    }

    /**
     * 小组分类批量删除
     */
    public function groupcateAlldel($ids = 0)
    {

        $this->jump(self::$commonLogic->dataDel(['id' => $ids], '删除成功', true));
    }

    /**
     * 小组分类删除
     */
    public function groupcateDel($id = 0)
    {

        $this->jump(self::$commonLogic->dataDel(['id' => $id], '删除成功', true));
    }

    /**
     * 导航状态更新
     */
    public function groupcateCstatus($id = 0, $status)
    {

        $this->jump(self::$commonLogic->setDataValue(['id' => $id], 'status', $status));
    }
}
