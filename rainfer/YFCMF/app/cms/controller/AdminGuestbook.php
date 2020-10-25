<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------
namespace app\cms\controller;

use app\common\model\Common as CommonModel;
use app\common\widget\Widget;
use think\facade\Validate;
use app\admin\controller\Base;

class AdminGuestbook extends Base
{
    protected $model = null;

    public function initialize()
    {
        parent::initialize();
        $model = new CommonModel();
        $this->model = $model->setTable(config('database.prefix') . 'guestbook')->setPk('id');
    }

    /*
     * 留言列表
     * @author rainfer <81818832@qq.com>
     */
    public function guestbookIndex()
    {
        //表格字段
        $fields = [
            ['title' => 'ID', 'field' => 'id'],
            ['title' => '留言人名', 'field' => 'name'],
            ['title' => '留言邮箱', 'field' => 'email'],
            ['title' => '留言内容', 'field' => 'content'],
            ['title' => '留言时间', 'field' => 'create_time', 'type' => 'date']
        ];
        //主键
        $pk     = 'id';
        $delall = url('guestbookAlldel');
        $list   = $this->model->order('status,create_time desc')->paginate(config('paginate.list_rows'));
        $page   = $list->render();
        $page   = preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)", "<a href='javascript:ajax_page($1);'>$2</a>", $page);
        $data   = $list->items();
        foreach ($data as &$value) {
            $value['reply_href'] = url('guestbookReply', ['id' => $value['id']]);
        }
        //右侧操作按钮
        $right_action = [
            'reply'  => [
                'condition' => ['status', '=', 1],
                'true'      => ['field' => 'reply_href', 'title' => '回复', 'icon' => 'ace-icon fa fa-envelope-o bigger-130', 'class' => 'green', 'is_pop' => 1],
                'false'     => ['field' => 'reply_href', 'title' => '回复', 'icon' => 'ace-icon fa fa-envelope bigger-130', 'class' => 'red', 'is_pop' => 1]
            ],
            'delete' => url('guestbookDel')
        ];
        //实例化表单类
        $widget = new Widget();
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

    /*
     * 留言回复返回数据
     * @author rainfer <81818832@qq.com>
     */
    public function guestbookReply()
    {
        $id        = input('id');
        $guestbook = $this->model->find($id);
        //实例化表单类
        $widget = new Widget();
        return $widget
            ->addText('id', '', $guestbook['id'], '', '', 'hidden')
            ->addText('toname', '回复名字', $guestbook['name'], '', 'readonly="readonly"')
            ->addText('toemail', '回信地址', $guestbook['email'], '', 'readonly="readonly"')
            ->addUeditor('replycontent', '回复内容', '', '', [], 'col-xs-11')
            ->setUrl(url('guestbookDoreply'))
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }

    /*
     * 留言回复返回数据
     * @author rainfer <81818832@qq.com>
     */
    public function guestbookDoreply()
    {
        $email   = input('toemail');
        $name    = input('toname');
        $id      = input('id');
        $content = htmlspecialchars_decode(input('replycontent'));
        $content = str_replace('"/data/upload/', '"' . get_host() . '/data/upload/', $content);
        $validate = new Validate();
        $check   = $validate->checkRule($email, 'must|email');
        if ($check === false) {
            $this->error('', 'guestbookIndex', ['is_frame' => 1]);
        } else {
            $send_result = sendMail($email, "Reply:" . $name, $content);
            if ($send_result['error']) {
                $this->error('邮箱设置不正确或对方邮箱地址不存在', 'guestbookIndex', ['is_frame' => 1]);
            } else {
                $rst = $this->model->where('id', $id)->setField('status', 1);
                if ($rst !== false) {
                    $this->success('回复留言成功', 'guestbookIndex', ['is_frame' => 1]);
                } else {
                    $this->error('回复留言失败', 'guestbookIndex', ['is_frame' => 1]);
                }
            }
        }
    }

    /*
     * 留言删除
     * @author rainfer <81818832@qq.com>
     */
    public function guestbookDel()
    {
        $id  = input('id');
        $rst = $this->model->where('id', $id)->delete();
        if ($rst !== false) {
            $this->success('留言删除成功', 'guestbookIndex');
        } else {
            $this->error('留言删除失败', 'guestbookIndex');
        }
    }

    /*
     * 留言删除(全选)
     * @author rainfer <81818832@qq.com>
     */
    public function guestbookAlldel()
    {
        $ids = input('ids/a');
        if (empty($ids)) {
            $this->error("请选择删除留言", 'guestbookIndex');
        }
        if (is_array($ids)) {
            $where = 'id in(' . implode(',', $ids) . ')';
        } else {
            $where = 'id=' . $ids;
        }
        $rst = $this->model->where($where)->delete();
        if ($rst !== false) {
            $this->success("留言删除成功！", 'guestbookIndex');
        } else {
            $this->error("删除留言失败！", 'guestbookIndex');
        }
    }
}
