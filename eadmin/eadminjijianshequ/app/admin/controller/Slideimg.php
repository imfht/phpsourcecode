<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Common as LogicCommon;


/**
 * 前台导航控制器
 */
class Slideimg extends AdminBase
{


    // 配置逻辑
    private static $commonLogic = null;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();

        self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class, 'Slideimg');
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
    public function slideimgList()
    {

        $where = $this->getWhere($this->param);

        $clist = self::$commonLogic->getDataList($where, true, 'id desc', '', '', '', '', true);

        $this->assign('list', $clist['data']);

        $this->assign('page', $clist['page']);

        return $this->fetch('slideimg_list');
    }


    public function slideimgAdd()
    {

        IS_POST && $this->jump(self::$commonLogic->dataAdd($this->param, false));

        return $this->fetch('slideimg_add');
    }

    public function slideimgEdit()
    {

        IS_POST && $this->jump(self::$commonLogic->dataEdit($this->param, ['id' => $this->param['id']], false));

        $info = self::$commonLogic->getDataInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('slideimg_edit');
    }

    public function slideimgAlldel($ids = 0)
    {

        $this->jump(self::$commonLogic->dataDel(['id' => $ids], '删除成功', true));
    }

    public function slideimgDel($id = 0)
    {

        $this->jump(self::$commonLogic->dataDel(['id' => $id], '删除成功', true));
    }

    public function slideimgCstatus($id = 0, $status)
    {

        $this->jump(self::$commonLogic->setDataValue(['id' => $id], 'status', $status));
    }
}
