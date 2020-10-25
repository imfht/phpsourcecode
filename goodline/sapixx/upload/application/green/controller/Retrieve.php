<?php
namespace app\green\controller;
use app\green\model\GreenRetrieve;
use think\facade\Request;
use think\helper\Time;

class Retrieve extends Common{

    public function initialize(){
        parent::initialize();
        $this->assign('pathMaps',[['name'=>'回收管理','url'=>url("green/retrieve/index")]]);
    }

    /**
     * 列表
     */
    public function index(int $operate_id = 0){
        $condition   = [];
        if($this->founder){
            if($operate_id > 0){
                $condition[] = ['operate_id','=',$operate_id];
            }
        }else{
            $condition[] = ['operate_id','=',$this->operate_id];
        }
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
        $list               = GreenRetrieve::where($this->mini_program)->where($condition)->order('id desc')->paginate(20, false, ['query' => ['starttime' => $starttime, 'endtime' => $endtime, 'time' => $time]]);
        $view['lists']      = $list;
        $view['time']       = $time;
        $view['operate_id'] = $operate_id;
        $view['starttime']  = $starttime;
        $view['endtime']    = $endtime;
        return view()->assign($view);
    }

    /**
     * 完成回收
     * @param integer $id 用户ID
     */
    public function finish(int $id){
        $result = GreenRetrieve::where($this->mini_program)->where(['id' => $id])->find();
        if(!$result){
            return enjson(0,'修改失败');
        }else{
            $result->state       = $result->state ? 0 : 1;
            $result->update_time = time();
            $result->save();
            return enjson(200, '操作成功');
        }
    }
}