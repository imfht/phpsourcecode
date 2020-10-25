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


class EventTypeController extends AdminController
{
    protected $eventTypeModel;
    function _initialize()
    {
        $this->eventTypeModel = D('Event/EventType');
        parent::_initialize();
    }

    public function index()
    {

        //显示页面
        $builder = new AdminTreeListBuilder();

        $tree = D('Event/EventType')->getTree(0, 'id,title,sort,pid,status');


        $builder->title('活动分类管理')
            ->buttonNew(U('EventType/add'))
            ->data($tree)
            ->display();
    }

    public function add($id = 0, $pid = 0)
    {
        if (IS_POST) {
            if ($id != 0) {
                $eventtype = $this->eventTypeModel->create();
                if ($this->eventTypeModel->save($eventtype)) {

                    $this->success('编辑成功。');
                } else {
                    $this->error('编辑失败。');
                }
            } else {
                $eventtype = $this->eventTypeModel->create();
                if ($this->eventTypeModel->add($eventtype)) {

                    $this->success('新增成功。');
                } else {
                    $this->error('新增失败。');
                }
            }

        } else {
            $builder = new AdminConfigBuilder();
            $eventtypes =$this->eventTypeModel->select();
            $opt = array();
            foreach ($eventtypes as $eventtype) {
                $opt[$eventtype['id']] = $eventtype['title'];
            }
            if ($id != 0) {
                $eventtype = $this->eventTypeModel->find($id);
            } else {
                $eventtype = array('pid' => $pid, 'status' => 1);
            }


            $builder->title('新增分类')->keyId()->keyText('title', '标题')->keySelect('pid', '父分类', '选择父级分类', array('0' => '顶级分类')+$opt)
                ->keyStatus()->keyCreateTime()->keyUpdateTime()
                ->data($eventtype)
                ->buttonSubmit(U('EventType/add'))->buttonBack()->display();
        }

    }

    public function operate($type = 'move', $from = 0)
    {
        $builder = new AdminConfigBuilder();
        $from = D('EventType')->find($from);

        $opt = array();
        $types = $this->eventTypeModel->select();
        foreach ($types as $event) {
            $opt[$event['id']] = $event['title'];
        }
        if ($type === 'move') {

            $builder->title('移动分类')->keyId()->keySelect('pid', '父分类', '选择父分类', $opt)->buttonSubmit(U('EventType/add'))->buttonBack()->data($from)->display();
        } else {

            $builder->title('合并分类')->keyId()->keySelect('toid', '合并至的分类', '选择合并至的分类', $opt)->buttonSubmit(U('EventType/doMerge'))->buttonBack()->data($from)->display();
        }

    }

    public function doMerge($id, $toid)
    {
        $effect_count=D('Event')->where(array('type_id'=>$id))->setField('type_id',$toid);
        D('EventType')->where(array('id'=>$id))->setField('status',-1);
        $this->success('合并分类成功。共影响了'.$effect_count.'个内容。',U('index'));
        //TODO 实现合并功能 issue
    }




    public function eventTypeTrash($page = 1, $r = 20)
    {
        //读取微博列表
        $map = array('status' => -1);
        $model = $this->eventTypeModel;
        $list = $model->where($map)->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('活动类型回收站')
            ->setStatusUrl(U('setStatus'))->buttonRestore()
            ->keyId()->keyText('title', '标题')->keyStatus()->keyCreateTime()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

}
