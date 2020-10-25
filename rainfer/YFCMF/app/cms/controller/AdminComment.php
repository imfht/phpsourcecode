<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: rainfer <81818832@qq.com>
// +----------------------------------------------------------------------
namespace app\cms\controller;

use app\common\model\Common as CommonModel;
use app\common\widget\Widget;
use think\facade\Cache;
use app\admin\controller\Base;

class AdminComment extends Base
{
    protected $model = null;

    public function initialize()
    {
        parent::initialize();
        $model = new CommonModel();
        $this->model = $model->setTable(config('database.prefix') . 'comments')->setPk('id');
    }

    /**
     * 评论列表
     * @author rainfer <81818832@qq.com>
     * @throws
     */
    public function commentIndex()
    {
        $comments = $this->model->alias("a")->field('a.*,b.username')->join(config('database.prefix') . 'user b', 'a.uid =b.id')
            ->order('create_time desc')->paginate(config('paginate.list_rows'));
        $page     = $comments->render();
        $page     = preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)", "<a href='javascript:ajax_page($1);'>$2</a>", $page);
        $data     = $comments->items();
        //表格字段
        $fields       = [
            ['title' => 'ID', 'field' => 'id'],
            ['title' => '评论人', 'field' => 'username'],
            ['title' => '评论人邮箱', 'field' => 'email'],
            ['title' => '评论内容', 'field' => 'content'],
            ['title' => '状态', 'field' => 'status', 'type' => 'switch', 'url' => url('commentState'), 'options' => [0 => '未审核', 1 => '已审核']],
            ['title' => '评论时间', 'field' => 'create_time', 'type' => 'datetime']
        ];
        $pk           = 'id';
        $right_action = [
            'delete' => url('commentDel')
        ];
        $delall       = url('commentAlldel');
        $widget       = new Widget();
        if (request()->isAjax()) {
            return $widget
                ->form('table', $fields, $pk, $data, $right_action, $page, '', $delall, 1);
        } else {
            return $widget
                ->addtable($fields, $pk, $data, $right_action, $page, '', $delall)
                ->setButton()
                ->fetch();
        }
    }

    /**
     * 评论删除
     * @author rainfer <81818832@qq.com>
     */
    public function commentDel()
    {
        $id  = input('id');
        $rst = $this->model->where('id', $id)->find();
        if ($rst) {
            $rst = $this->del($id);
            if ($rst !== false) {
                $this->success('评论删除成功', 'commentIndex');
            } else {
                $this->error('评论删除失败', 'commentIndex');
            }
        } else {
            $this->error('评论不存在', 'commentIndex');
        }
    }

    /**
     * 全选删除
     * @author rainfer <81818832@qq.com>
     */
    public function commentAlldel()
    {
        $ids = input('ids/a');
        if (empty($ids)) {
            $this->error("请选择删除的评论", 'commentIndex');
        }
        if (!is_array($ids)) {
            $ids[] = $ids;
        }
        foreach ($ids as $id) {
            $this->del($id);
        }
        $this->success("评论删除成功", 'commentIndex');
    }

    /**
     * 评论审核/取消审核
     * @author rainfer <81818832@qq.com>
     */
    public function commentState()
    {
        $id           = input('id', 0, 'intval');
        $common_model = new CommonModel();
        $common_model = $common_model->setTable(config('database.prefix') . 'comments')->setPk('id');
        if (!$id) {
            $this->error('评论不存在', 'commentsIndex');
        }
        $status = $common_model->where('id', $id)->value('status');
        $status = $status ? 0 : 1;
        $common_model->where('id', $id)->setField('status', $status);
        $this->success($status ? '已审核' : '未审核', null, ['result' => $status]);
    }

    /**
     * 评论设置显示
     * @author rainfer <81818832@qq.com>
     */
    public function commentSetting()
    {
        $csys   = config('yfcmf.comment');
        $widget = new Widget();
        return $widget
            ->addSwitch('t_open', '是否开启评论', $csys['t_open'])
            ->addText('t_limit', '评论间隔时间', $csys['t_limit'], '单位(秒)', 'required', 'number', ['placeholder' => '请输入间隔时间'])
            ->setUrl(url('commentUpdate'))
            ->setAjax()
            ->fetch();
    }

    /**
     * 评论设置保存
     * @author rainfer <81818832@qq.com>
     */
    public function commentUpdate()
    {
        $t_open = input('t_open', 0) ? true : false;
        $t_limit = input('t_limit', 60, 'intval');
        $data    = [
            'comment' => [
                't_open'  => $t_open,
                't_limit' => $t_limit,
            ],
        ];
        $rst     = sys_config_setbyarr($data);
        Cache::clear();
        if ($rst) {
            $this->success('评论设置成功', 'commentSetting');
        } else {
            $this->error('评论设置失败', 'commentSetting');
        }
    }

    /**
     * 评论审核/取消审核
     * @author rainfer <81818832@qq.com>
     * @param int $id
     * @return mixed
     * @throws
     */
    private function del($id)
    {
        $ids = $this->model->getAllChilds([], $id, true, true);
        return $this->model->where('id', 'in', $ids)->delete();
    }
}
