<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29
 * Time: 11:40
 */

namespace app\admin\controller;


use app\common\cron\CronExpression;
use think\Db;
use think\Exception;
use think\Request;

class CronTaskController extends AdminBaseController
{

    public function lists($model = '',$return = false)
    {
        $list = Db::view('CronTask','*')
            ->view('Cate','name as cate_name','CronTask.cate_id=Cate.id')
            ->order('CronTask.cate_id asc,CronTask.id asc')
            ->select();
        $this->tableSet($list);
    }

    public function edit($model = '')
    {
        $this->_check();
        $this->_assignCommons();
        return parent::edit($model);
    }

    public function add($model = '')
    {
        $this->_check();
        $this->_assignCommons();
        return parent::add($model);
    }

    private function _check(){

        $request = Request::instance();

        if($request->isPost()){
            $validate = validate('CronTask');
            if(!$validate->check($request->post())){
                $this->error($validate->getError());
            }
        }

    }

    private function _assignCommons(){
        $list = Db::name('cate')->select();
        $this->assign('cate_list',$list);
    }

    public function getNextRunTime(){
        $cron_expression = Request::instance()->param('cron_expression');
        if(empty($cron_expression)) $this->error('表达式不能为空');
        try{
            $time = CronExpression::getNextRunTime($cron_expression);
            $this->success('当前时间：'.date('Y-m-d H:i:s')."<br>".'下次执行：'.$time);
        }catch (Exception $e){
            $this->error($e->getMessage());
        }

    }

    public function help(){
        return $this->fetch();
    }

}