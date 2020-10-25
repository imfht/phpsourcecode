<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Common as LogicCommon;


class Rzuser extends AdminBase
{

    // 配置逻辑
    private static $commonLogic = null;


    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();

        self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class, 'Rzuser');
    }

    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {

        $where = [];


        if (!is_administrator()) {


        }

        return $where;
    }

    /**
     * 文档列表
     */
    public function rzuserList()
    {

        $where = $this->getWhere($this->param);

        $clist = self::$commonLogic->getDataList($where, true, 'id desc');

        $this->assign('list', $clist['data']);

        $this->assign('page', $clist['page']);

        return $this->fetch('rzuser_list');
    }

    public function rzuserSh($id = 0, $status, $field)
    {

        $this->jump(self::$commonLogic->setDataValue(['id' => $id], 'status', 1));
    }


}
