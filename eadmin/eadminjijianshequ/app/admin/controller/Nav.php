<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Common as LogicCommon;


/**
 * 前台导航控制器
 */
class Nav extends AdminBase
{


    // 配置逻辑
    private static $commonLogic = null;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();

        self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class, 'nav');
    }

    /**
     * 获取导航列表搜索条件
     */
    public function getWhere($data = [])
    {

        $where = [];

        $where['status|>='] = 0;

        !empty($data['search_data']) && $where['name|~'] = '%' . $data['search_data'] . '%';

        if (!is_administrator()) {


        }

        return $where;
    }

    /**
     * 导航列表
     */
    public function navList()
    {

        $where = $this->getWhere($this->param);

        $clist = self::$commonLogic->getDataList($where, true, 'id desc');

        $this->assign('list', $clist['data']);

        $this->assign('page', $clist['page']);

        return $this->fetch('nav_list');
    }

    /**
     * 导航添加
     */
    public function navAdd()
    {

        IS_POST && $this->jump(self::$commonLogic->dataAdd($this->param));

        return $this->fetch('nav_add');
    }

    /**
     * 导航编辑
     */
    public function navEdit()
    {

        IS_POST && $this->jump(self::$commonLogic->dataEdit($this->param, ['id' => $this->param['id']]));

        $info = self::$commonLogic->getDataInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('nav_edit');
    }

    /**
     * 导航批量删除
     */
    public function navAlldel($ids = 0)
    {

        $this->jump(self::$commonLogic->dataDel(['id' => $ids], '删除成功', true));
    }

    /**
     * 导航删除
     */
    public function navDel($id = 0)
    {

        $this->jump(self::$commonLogic->dataDel(['id' => $id], '删除成功', true));
    }

    /**
     * 导航状态更新
     */
    public function navCstatus($id = 0, $status)
    {

        $this->jump(self::$commonLogic->setDataValue(['id' => $id], 'status', $status));
    }
}
