<?php

namespace app\admin\controller;

use app\common\logic\Common as LogicCommon;

/**
 * 文档控制器
 */
class Topic extends AdminBase
{

    private static $commonLogic = null;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();
        self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class, 'topic');
    }

    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {

        $where = [];

        $where['m.status|>='] = 0;
        !empty($data['tid']) && $where['tid'] = $data['tid'];
        if (!is_administrator()) {


        }

        return $where;
    }

    /**
     * 文档列表
     */
    public function topicList()
    {

        $topiccatelist = self::$datalogic->setname('topiccate')->getDataList(['status' => 1], true, 'sort desc', false);
        $this->assign('topiccatelist', $topiccatelist);

        $where = $this->getWhere($this->param);

        $clist = self::$commonLogic->getDataList($where, 'm.*,user.nickname', 'm.id desc', 0, [['user|user', 'user.id=m.uid']]);

        $this->assign('list', $clist['data']);

        $this->assign('page', $clist['page']);

        return $this->fetch('topic_list');
    }

    /**
     * 文档编辑
     */
    public function topicEdit()
    {
        if (IS_POST) {
            $data            = $this->param;
            $data['content'] = htmlspecialchars_decode($data['content']);
            $this->jump(self::$commonLogic->dataEdit($data, ['id' => $data['id']]));
        }

        $topiccatelist = self::$datalogic->setname('topiccate')->getDataList(['status' => 1], true, 'sort desc', false);
        $this->assign('topiccatelist', $topiccatelist);

        $info = self::$commonLogic->getDataInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);
        return $this->fetch('topic_edit');
    }

    /**
     * 文档批量删除
     */
    public function topicAlldel($ids = 0)
    {

        //更新小组的帖子数量
        $tidsarr = self::$commonLogic->getDataColumn(['id' => $ids], 'gidtext');

        foreach ($tidsarr as $key => $vo) {

            if ($vo) {

                $nn = explode(',', $vo);
                foreach ($nn as $k => $v) {

                    self::$datalogic->setname('group')->setIncOrDec(['name' => $v], 'topiccount', 1, '-');


                }


            }


        }

        foreach ($ids as $k => $v) {
            homeaction_log(MEMBER_ID, 14, $v);
        }

        $this->jump(self::$commonLogic->dataDel(['id' => $ids], '删除成功', true));
    }

    /**
     * 文档删除
     */
    public function topicDel($id = 0)
    {
        $info = self::$commonLogic->getDataInfo(['id' => $id]);

        if ($info['gidtext']) {

            $nn = explode(',', $info['gidtext']);
            foreach ($nn as $k => $v) {

                self::$datalogic->setname('group')->setIncOrDec(['name' => $v], 'topiccount', 1, '-');


            }


        }

        homeaction_log(MEMBER_ID, 14, $id);


        $this->jump(self::$commonLogic->dataDel(['id' => $id], '删除成功', true));
    }

    /**
     * 文档状态更新
     */
    public function topicCstatus($id = 0, $status, $field)
    {

        $this->jump(self::$commonLogic->setDataValue(['id' => $id], $field, $status));
    }

    /**
     * 文档审核
     */
    public function topicSh($id = 0, $status, $field)
    {

        $this->jump(self::$commonLogic->setDataValue(['id' => $id], 'status', 1));
    }

    /**
     * 文档批量审核
     */
    public function topicAllSh($ids = 0)
    {

        $this->jump(self::$commonLogic->setDataValue(['id' => $ids], 'status', 1));
    }

    /**
     * 文档推送
     */
    public function yidong($ids = 0)
    {
        if (IS_POST) {

            $ids = explode('-', $this->param['ids']);
            $this->jump(self::$commonLogic->setDataValue(['id' => $ids], 'tid', $this->param['tid']));

        }
        $topiccatelist = self::$datalogic->setname('topiccate')->getDataList(['status' => 1], true, 'sort desc', false);
        $this->assign('topiccatelist', $topiccatelist);

        $this->assign('ids', $ids);
        return $this->fetch('topic_ts');

    }

}
