<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Common as LogicCommon;


/**
 * 评论控制器
 */
class Comment extends AdminBase
{

    /**
     * 评论逻辑
     */

    private static $commonLogic = null;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();

        self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class, 'comment');

    }

    /**
     * 获取评论列表搜索条件
     */
    public function getWhere($data = [])
    {

        $where = [];

        !empty($data['search_data']) && $where['name'] = ['like', '%' . $data['search_data'] . '%'];

        if (!is_administrator()) {


        }

        return $where;
    }

    /**
     * 评论列表
     */
    public function commentList()
    {

        $where = $this->getWhere($this->param);


        $clist = self::$commonLogic->getDataList($where, 'm.*,user.username,topic.title', 'm.id desc', 0, [['user|user', 'm.uid=user.id'], ['topic|topic', 'm.fid=topic.id']], '', '', false, 'm');

        $this->assign('list', $clist['data']);

        $this->assign('page', $clist['page']);


        return $this->fetch('comment_list');
    }

    /**
     * 评论编辑
     */
    public function commentEdit()
    {
        IS_POST && $this->jump(self::$commonLogic->dataEdit($this->param, ['id' => $this->param['id']]));

        $info = self::$commonLogic->getDataInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);


        return $this->fetch('comment_edit');
    }

    /**
     * 评论批量删除
     */
    public function commentAlldel($ids = 0)
    {

        $fidsarr = self::$commonLogic->getDataColumn(['id' => $ids], 'fid');

        self::$datalogic->setname('topic')->setIncOrDec(['id' => $fidsarr], 'reply', 1, '-');

        self::$commonLogic->dataDel(['pid' => $ids], '删除成功', true);


        foreach ($ids as $k => $v) {
            homeaction_log(MEMBER_ID, 13, $v);
        }

        $this->jump(self::$commonLogic->dataDel(['id' => $ids], '删除成功', true));
    }

    /**
     * 评论删除
     */
    public function commentDel($id = 0)
    {
        $info = self::$commonLogic->getDataInfo(['id' => $id]);

        self::$datalogic->setname('topic')->setIncOrDec(['id' => $info['fid']], 'reply', 1, '-');

        self::$commonLogic->dataDel(['pid' => $id], '删除成功', true);

        homeaction_log(MEMBER_ID, 13, $id);

        $this->jump(self::$commonLogic->dataDel(['id' => $id], '删除成功', true));
    }

}
