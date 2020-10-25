<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Common as LogicCommon;


/**
 * 公告及消息控制器
 */
class Message extends AdminBase
{

    /**
     * 公告及消息逻辑
     */

    private static $commonLogic = null;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();

        self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class, 'message');
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
     * 公告及消息列表
     */
    public function messageList()
    {

        $where = $this->getWhere($this->param);

        $clist = self::$commonLogic->getDataList($where, true, 'id desc');

        $this->assign('list', $clist['data']);

        $this->assign('page', $clist['page']);


        return $this->fetch('message_list');
    }

    /**
     * 公告及消息添加
     */
    public function messageAdd()
    {

        IS_POST && $this->jump(self::$commonLogic->dataAdd($this->param, false));

        return $this->fetch('message_add');
    }

    /**
     * 公告及消息编辑
     */
    public function messageEdit()
    {
        IS_POST && $this->jump(self::$commonLogic->dataEdit($this->param, ['id' => $this->param['id']], false));

        $info = self::$commonLogic->getDataInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);
        return $this->fetch('message_edit');
    }

    /**
     * 公告及消息批量删除
     */
    public function messageAlldel($ids = 0)
    {

        $this->jump(self::$commonLogic->dataDel(['id' => $ids], '删除成功', true));
    }

    /**
     * 公告及消息删除
     */
    public function messageDel($id = 0)
    {

        $this->jump(self::$commonLogic->dataDel(['id' => $id], '删除成功', true));
    }
}
