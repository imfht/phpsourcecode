<?php
namespace app\green\controller;
use app\common\model\SystemUser;
use app\green\model\GreenJob;
use think\facade\Request;
use think\helper\Time;

class Job extends Common{

    public function initialize(){
        parent::initialize();
        $this->assign('pathMaps',[['name'=>'求职管理','url'=>url("green/job/index")]]);
    }

    /**
     * 列表
     */
    public function index(){
        $condition   = [];
        $time      = Request::param('time/d',0);
        $starttime = Request::param('starttime/s');
        $endtime   = Request::param('endtime/s');
        if($time){
            switch ($time) {
                case 2:
                    list($start, $end) = Time::yesterday();
                    break;
                case 30:
                    list($start, $end) = Time::month();
                    break;
                case 60:
                    list($start, $end) = Time::lastMonth();
                    break;
                default:
                    list($start, $end) = Time::today();
                    break;
            }
            $condition[] = ['create_time','>=',$start];
            $condition[] = ['create_time','<=',$end];
        }else{
            if($starttime){
                $condition[] = ['create_time','>=',strtotime($starttime)];
            }
            if($endtime){
                $condition[] = ['create_time','<=',strtotime($endtime)];
            }
        }
        $list = GreenJob::where($this->mini_program)->where($condition)->order('id desc')->paginate(20, false, ['query' => ['starttime' => $starttime, 'endtime' => $endtime, 'time' => $time]]);
        foreach ($list as $key => $value) {
            $list[$key]->user = SystemUser::where($this->mini_program)->where(['id' => $value->uid])->find();
        }
        $view['lists']     = $list;
        $view['time']      = $time;
        $view['starttime'] = $starttime;
        $view['endtime']   = $endtime;
        return view()->assign($view);
    }

    /**
     * @param int $id
     * @return \think\response\View
     * @throws \think\exception\DbException
     * 详情
     */
    public function detail(int $id = 0){
        $view['info']        = GreenJob::where($this->mini_program)->where(['id' => $id])->find();
        return view()->assign($view);
    }
}