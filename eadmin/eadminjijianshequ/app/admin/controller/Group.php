<?php

namespace app\admin\controller;

use app\common\logic\Common as LogicCommon;

class Group extends AdminBase
{


    private static $commonLogic = null;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();

        self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class, 'group');
    }

    public function getWhere($data = [])
    {

        $where = [];


        !empty($data['pid']) && $where['pid'] = $data['pid'];
        if (!is_administrator()) {


        }

        return $where;
    }

    /**
     * 小组分类列表
     */
    public function groupList()
    {


        $where = $this->getWhere($this->param);

        $where['m.status|>='] = 0;

        $clist = self::$commonLogic->getDataList($where, 'm.*,user.nickname', 'm.id desc', 0, [['user|user', 'user.id=m.uid', 'LEFT']]);

        $this->assign('list', $clist['data']);

        $this->assign('page', $clist['page']);

        $groupcate_list = self::$datalogic->setname('groupcate')->getDataList(['status' => 1], true, 'id desc', false);
        if (!empty($groupcate_list)) {
            foreach ($groupcate_list as $k => $v) {

                $lsarr[$v['id']] = $v;


            }
            $this->assign('groupcate_list', $lsarr);
        } else {
            $this->assign('groupcate_list', '');
        }


        $this->assign('pid', !empty($where['pid']) ? $where['pid'] : 0);


        return $this->fetch('group_list');
    }

    /**
     * 小组分类添加
     */
    public function groupAdd()
    {


        $obj = new Callback();


        $this->assign('groupcate_list', parent::$datalogic->setname('groupcate')->getDataList(['status' => 1], true, 'id desc', false));

        if (IS_POST) {
            $data             = $this->param;
            $data['describe'] = htmlspecialchars_decode($data['describe']);
            $data['name']     = htmlspecialchars_decode($data['name']);
            $this->jump(self::$commonLogic->dataAdd($data, false, '', '添加成功', $obj, 'groupadd_call_back'));
        }


        return $this->fetch('group_add');
    }


    /**
     * 小组分类编辑
     */
    public function groupEdit()
    {
        $info = self::$commonLogic->getDataInfo(['id' => $this->param['id']]);

        if (IS_POST) {
            $data             = $this->param;
            $data['name']     = htmlspecialchars_decode($data['name']);
            $data['describe'] = htmlspecialchars_decode($data['describe']);

            $this->jump(self::$commonLogic->dataEdit($data, ['id' => $data['id']]));
        }


        $this->assign('groupcate_list', parent::$datalogic->setname('groupcate')->getDataList(['status' => 1], true, 'id desc', false));

        $this->assign('info', $info);
        return $this->fetch('group_edit');
    }

    /**
     * 小组分类批量删除
     */
    public function groupAlldel($ids = 0)
    {

        //在帖子中找到这个话题的，删除相关关键词
        $namearr = self::$commonLogic->getDataColumn(['id' => $ids], 'name');
        foreach ($namearr as $key => $vo) {

            $topiclist = self::$datalogic->setname('topic')->getDataList(['gidtext|~' => $vo], true, 'id desc', false);

            foreach ($topiclist as $k => $v) {

                if ($v['gidtext']) {
                    $nn = explode(',', $v['gidtext']);
                    if ($nn = deletearray($nn, $vo)) {
                        $str = implode(',', $nn);
                        self::$datalogic->setname('topic')->setDataValue(['id' => $v['id']], 'gidtext', $str);


                    }

                }


            }

        }


        $this->jump(self::$commonLogic->dataDel(['id' => $ids], '删除成功', true));
    }

    /**
     * 小组分类删除
     */
    public function groupDel($id = 0)
    {
        //先删除相关帖子


        //在帖子中找到这个话题的，删除相关关键词
        $namearr = self::$commonLogic->getDataInfo(['id' => $id]);


        $topiclist = self::$datalogic->setname('topic')->getDataList(['gidtext|~' => $namearr['name']], true, 'id desc', false);

        foreach ($topiclist as $k => $v) {

            if ($v['gidtext']) {
                $nn = explode(',', $v['gidtext']);
                if ($nn = deletearray($nn, $namearr['name'])) {
                    $str = implode(',', $nn);
                    self::$datalogic->setname('topic')->setDataValue(['id' => $v['id']], 'gidtext', $str);


                }

            }


        }


        $this->jump(self::$commonLogic->dataDel(['id' => $id], '删除成功', true));
    }

    /**
     * 导航状态更新
     */

    public function groupCstatus($id = 0, $status, $field)
    {

        $this->jump(self::$commonLogic->setDataValue(['id' => $id], $field, $status));
    }
}
