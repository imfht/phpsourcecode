<?php
namespace app\green\controller;
use app\common\model\SystemUser;
use app\green\model\GreenUserLog;
use app\green\model\GreenUser;
use think\facade\Request;
use think\helper\Time;

class User extends Common{

    public function initialize(){
        parent::initialize();
        $this->assign('pathMaps',[['name'=>'用户管理','url'=>url("green/user/index")]]);
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
        $uid   = Request::param('uid/d');
        $where = !empty($uid) ? ['uid' => $uid] : [];
        $view['weight_sum'] = GreenUserLog::where($this->mini_program)->where($condition)->where($where)->sum('weight');
        if (!empty($uid)) {
            $condition[] = ['id', '=', $uid];
        }
        $view['user_sum'] = SystemUser::where($this->mini_program)->where($condition)->count();
        $list             = SystemUser::where($this->mini_program)->where($condition)->order('id desc')->paginate(20, false, ['query' => ['starttime' => $starttime, 'endtime' => $endtime, 'time' => $time]]);
        foreach ($list as $key => $value) {
            $list[$key]->user = GreenUser::where($this->mini_program)->where($condition)->where(['uid' => $value->id])->find();
        }
        $view['lists']     = $list;
        $view['uid']       = $uid;
        $view['time']      = $time;
        $view['starttime'] = $starttime;
        $view['endtime']   = $endtime;
        return view()->assign($view);
    }


    /**
     * 投递列表
     */
    public function userLog(int $id = 0){
        $view['lists']        = GreenUserLog::where($this->mini_program)->where(['uid' => $id])->order('create_time desc')->paginate(20, false, ['query' => ['id' => $id]]);
        return view()->assign($view);
    }

}