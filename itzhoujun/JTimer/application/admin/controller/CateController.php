<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/28
 * Time: 15:19
 */

namespace app\admin\controller;

use think\Db;
use think\Request;

class CateController extends AdminBaseController
{

    /**
     * @param string $model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function lists($model = '',$return = false)
    {
        $list = Db::name('cate')
            ->alias('c')
            ->join('__CRON_TASK__ ct', 'c.id = ct.cate_id', 'LEFT')
            ->group('c.id')
            ->field('c.id,c.name,count(ct.id) as count')
            ->select();
        $this->tableSet($list);
    }

    public function doDel($model = '')
    {
        $request = Request::instance();
        $id = $request->param('ids');
        $count = Db::name('cron_task')->where('cate_id',$id)->count();
        if($count > 0){
            $this->error('请先删除该分类下的所有任务');
        }

        parent::doDel($model);
    }


}