<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-11
 * Time: PM5:41
 */

namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;


class EventController extends AdminController
{
    protected $eventModel;

    function _initialize()
    {
        $this->eventModel = D('Event/Event');
        parent::_initialize();
    }
    public function config()
    {
        $admin_config = new AdminConfigBuilder();
        $data = $admin_config->handleConfig();

        $admin_config->title('活动基本设置')
            ->keyBool('NEED_VERIFY', '创建活动是否需要审核','默认无需审核')
            ->buttonSubmit('', '保存')->data($data);
        $admin_config->display();
    }
    public function event($page = 1, $r = 10)
    {
        //读取列表
        $map = array('status' => 1);
        $model = $this->eventModel;
        $list = $model->where($map)->page($page, $r)->select();
        unset($li);
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();

        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';

        $builder->title('内容管理')
            ->setStatusUrl(U('setEventContentStatus'))->buttonDisable('', '审核不通过')->buttonDelete()->button('设为推荐', array_merge($attr, array('url' => U('doRecommend', array('tip' => 1)))))->button('取消推荐', array_merge($attr, array('url' => U('doRecommend', array('tip' => 0)))))
            ->keyId()->keyLink('title', '标题', 'Event/Index/detail?id=###')->keyUid()->keyCreateTime()->keyStatus()->keyMap('is_recommend', '是否推荐', array(0 => '否', 1 => '是'))
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * 设置推荐or取消推荐
     * @param $ids
     * @param $tip
     * autor:xjw129xjt
     */
    public function doRecommend($ids, $tip)
    {
        D('Event')->where(array('id' => array('in', $ids)))->setField('is_recommend', $tip);
        $this->success('设置成功', $_SERVER['HTTP_REFERER']);
    }

    /**
     * 审核页面
     * @param int $page
     * @param int $r
     * autor:xjw129xjt
     */
    public function verify($page = 1, $r = 10)
    {
        //读取列表
        $map = array('status' => 0);
        $model = $this->eventModel;
        $list = $model->where($map)->page($page, $r)->select();
        unset($li);
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';
        $builder->title('审核内容')
            ->setStatusUrl(U('setEventContentStatus'))->buttonEnable('', '审核通过')->buttonDelete()
            ->keyId()->keyLink('title', '标题', 'Event/Index/detail?id=###')->keyUid()->keyCreateTime()->keyStatus()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * 设置状态
     * @param $ids
     * @param $status
     * autor:xjw129xjt
     */
    public function setEventContentStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        if ($status == 1) {
            foreach ($ids as $id) {
                $content = D('Event')->find($id);
                D('Common/Message')->sendMessage($content['uid'], "管理员审核通过了您发布的内容。现在可以在列表看到该内容了。", $title = '专辑内容审核通知', U('Event/Index/detail', array('id' => $id)), is_login(), 2);
                /*同步微博*/
                $user = query_user(array('username', 'space_link'), $content['uid']);
                $weibo_content = '管理员审核通过了@' . $user['username'] . ' 的内容：【' . $content['title'] . '】，快去看看吧：' . "http://$_SERVER[HTTP_HOST]" . U('Event/Index/detail', array('id' => $content['id']));
                $model = D('Weibo/Weibo');
                $model->addWeibo(is_login(), $weibo_content);
                /*同步微博end*/
            }

        }
        $builder->doSetStatus('Event', $ids, $status);

    }

    public function contentTrash($page = 1, $r = 10)
    {
        //读取微博列表
        $map = array('status' => -1);
        $model = D('Event');
        $list = $model->where($map)->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('内容回收站')
            ->setStatusUrl(U('setEventContentStatus'))->buttonRestore()
            ->keyId()->keyLink('title', '标题', 'Event/Index/detail?id=###')->keyUid()->keyCreateTime()->keyStatus()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }
}
