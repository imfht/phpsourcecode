<?php

namespace app\admin\controller;

use app\common\logic\Common as LogicCommon;


class Topiccate extends AdminBase
{

    private static $commonLogic = null;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();
        self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class, 'topiccate');
    }

    public function getWhere($data = [])
    {

        $where = [];

        $where['status|>='] = 0;


        if (!is_administrator()) {


        }

        return $where;
    }

    public function topiccateList()
    {

        $where = $this->getWhere($this->param);

        $clist = self::$commonLogic->getDataList($where, true, 'id desc');

        $this->assign('list', $clist['data']);

        $this->assign('page', $clist['page']);

        return $this->fetch('topiccate_list');
    }

    public function topiccateAdd()
    {

        IS_POST && $this->jump(self::$commonLogic->dataAdd($this->param));

        return $this->fetch('topiccate_add');
    }

    public function topiccateEdit()
    {

        IS_POST && $this->jump(self::$commonLogic->dataEdit($this->param, ['id' => $this->param['id']]));

        $info = self::$commonLogic->getDataInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);
        return $this->fetch('topiccate_edit');
    }

    public function topiccateAlldel($ids = 0)
    {

        $this->jump(self::$commonLogic->dataDel(['id' => $ids], '删除成功', true));
    }

    public function topiccateDel($id = 0)
    {

        $this->jump(self::$commonLogic->dataDel(['id' => $id], '删除成功', true));
    }

    public function topiccateCstatus($id = 0, $status)
    {

        $this->jump(self::$commonLogic->setDataValue(['id' => $id], 'status', $status));
    }
}
