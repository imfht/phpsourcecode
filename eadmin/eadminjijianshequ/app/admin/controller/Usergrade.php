<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Common as LogicCommon;


/**
 * 会员等级控制器
 */
class Usergrade extends AdminBase
{

    // 配置逻辑
    private static $commonLogic = null;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();

        self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class, 'Usergrade');
    }

    /**
     * 获取会员等级列表搜索条件
     */
    public function getWhere($data = [])
    {

        $where = [];

        !empty($data['search_data']) && $where['name|~'] = '%' . $data['search_data'] . '%';

        if (!is_administrator()) {


        }

        return $where;
    }

    /**
     * 会员列表
     */
    public function usergradeList()
    {

        $where = $this->getWhere($this->param);

        $clist = self::$commonLogic->getDataList($where, true, 'id desc');

        $this->assign('list', $clist['data']);
        $this->assign('scoretypelist', parse_config_attr(webconfig('scoretype_list')));
        $this->assign('page', $clist['page']);


        return $this->fetch('usergrade_list');
    }

    /**
     * 会员添加
     */
    public function usergradeAdd()
    {
        $this->assign('scoretypelist', parse_config_attr(webconfig('scoretype_list')));

        $data          = $this->param;
        $data['quanx'] = implode(',', $data['quanx']);

        IS_POST && $this->jump(self::$commonLogic->dataAdd($data));

        return $this->fetch('usergrade_add');
    }

    /**
     * 会员编辑
     */
    public function usergradeEdit()
    {


        $data          = $this->param;
        $data['quanx'] = implode(',', $data['quanx']);

        IS_POST && $this->jump(self::$commonLogic->dataEdit($data, ['id' => $this->param['id']]));
        $this->assign('scoretypelist', parse_config_attr(webconfig('scoretype_list')));
        $info       = self::$commonLogic->getDataInfo(['id' => $this->param['id']]);
        $info['qx'] = explode(',', $info['quanx']);
        if (in_array('1', $info['qx'])) {
            $info['one'] = 1;
        } else {
            $info['one'] = 0;
        }
        if (in_array('2', $info['qx'])) {
            $info['two'] = 1;
        } else {
            $info['two'] = 0;
        }

        $this->assign('info', $info);
        return $this->fetch('usergrade_edit');
    }

    /**
     * 会员批量删除
     */
    public function usergradeAlldel($ids = 0)
    {

        $this->jump(self::$commonLogic->dataDel(['id' => $ids], '删除成功', true));
    }

    /**
     * 会员删除
     */
    public function usergradeDel($id = 0)
    {

        $this->jump(self::$commonLogic->dataDel(['id' => $id], '删除成功', true));
    }
}
