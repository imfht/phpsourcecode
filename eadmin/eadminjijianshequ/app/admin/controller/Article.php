<?php

namespace app\admin\controller;

use app\common\logic\Common as LogicCommon;

/**
 * 文章控制器
 */
class Article extends AdminBase
{

    /**
     * 文章逻辑
     */

    private static $commonLogic = null;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();

        self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class);
    }

    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {

        $where = [];

        $uid = is_login();

        if ($uid != SYS_ADMINISTRATOR_ID) {


        }

        if (!is_administrator()) {


        }

        return $where;
    }

    public function getcommondata()
    {


        $keyword_list = parse_config_array('keyword_list');

        $this->assign('keyword_list', $keyword_list);

        $where['status'] = 1;


        $groupcate_list = self::$commonLogic->setname('articlecate')->getDataList($where, true, 'pid asc,sort desc', false);


        $groupcate_list = $this->menuToSelect(list_to_tree(array_values($groupcate_list), 'id', 'pid', 'children'));


        $this->assign('groupcate_list', $groupcate_list);


    }

    /**
     * 文章列表
     */
    public function articleList()
    {

        $where = $this->getWhere($this->param);


        $clist = self::$commonLogic->setname('article')->getDataList($where, true, 'id desc', 0, '', '', '', true);

        $this->getcommondata();

        $this->assign('list', $clist['data']);

        $this->assign('page', $clist['page']);

        return $this->fetch('article_list');
    }

    /**
     * 文章添加
     */
    public function articleAdd()
    {


        $data = $this->param;

        $data['status'] = 1;

        $data['uid'] = is_login();

        $data['pid'] = 1;

        IS_POST && $this->jump(self::$commonLogic->setname('article')->dataAdd($data, true));

        $this->getcommondata();

        return $this->fetch('article_add');
    }

    /**
     * 文章编辑
     */
    public function articleEdit()
    {
        $info = self::$commonLogic->setname('article')->getDataInfo(['id' => $this->param['id']]);

        $info['content'] = htmlspecialchars_decode($info['content']);


        $data = $this->param;

        $data['pid'] = 1;

        IS_POST && $this->jump(self::$commonLogic->setname('article')->dataEdit($data, ['id' => $this->param['id']]));

        $this->getcommondata();


        $this->assign('info', $info);

        return $this->fetch('article_edit');
    }

    /**
     * 文章批量删除
     */
    public function articleAlldel($ids = 0)
    {

        $this->jump(self::$commonLogic->setname('article')->dataDel(['id' => $ids], '删除成功', true));
    }

    /**
     * 文章删除
     */
    public function articleDel($id = 0)
    {

        $this->jump(self::$commonLogic->setname('article')->dataDel(['id' => $id], '删除成功', true));
    }

    /**
     * 文章状态更新
     */
    public function articleCstatus($id = 0, $status, $field)
    {

        $this->jump(self::$commonLogic->setname('article')->setDataValue(['id' => $id], $field, $status));
    }

}
