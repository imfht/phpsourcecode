<?php
/**
 * Created by PhpStorm.
 * User: zhoujun
 * Date: 2018/3/31
 * Time: 15:33
 */

namespace app\admin\controller;


use app\admin\model\CronTaskModel;
use think\Db;
use think\Request;

class CronTaskLogController extends AdminBaseController
{
    public function index()
    {
        $this->assign('cates',Db::name('cate')->select());
        return parent::index();
    }

    public function lists($model = '', $return = false)
    {
        $data = parent::lists($model, true);
        $cron_task = new CronTaskModel();
        foreach ($data['data'] as &$item){
            $item['cate_name'] = $cron_task->getBelongCateName($item['ct_id']);
            $item['remark'] = Db::name('cron_task')->where('id',$item['ct_id'])->value('remark');
        }
        $this->tableSet($data['data'],$data['count']);
    }

    protected function getWhere($model = '')
    {
        $request = Request::instance();

        $cmd = $request->param('cmd');
        $cate_id = $request->param('cate_id');
        $ct_id = $request->param('ct_id');

        if($cmd){
            $where['cmd'] = ['like',"%{$cmd}%"];
        }

        if($ct_id){
            $where['ct_id'] = $ct_id;
        }

        if($cate_id){
            $task_ids = Db::name('cron_task')->where('cate_id',$cate_id)->column('id');
            $where['ct_id'] = ['in',$task_ids];
        }
        return $where;
    }
}