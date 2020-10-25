<?php
namespace app\admin\Model;

use think\Model;
use think\Db;

class CountActive extends Model
{
    public function _initialize()
    {
        parent::_initialize();
    }

/**
     * 每日执行活跃用户统计
     */
    public function activeCount()
    {
        $activeAction = config('COUNT_ACTIVE_ACTION');
        if(!$activeAction){
            $activeAction = 3;
        }
        $time = strtotime(time_format(time(),'Y-m-d'));

        $day_data = $this->_dayActiveCount($activeAction,$time);
        
        $have = $this->where(['date'=>$day_data['date']])->find();

        if(!$have){
            $this->save($day_data);
        }else{
            $this->save($day_data,['id'=>$have['id']]);
        }
        
        if(date('w',$time) === '0'){
            $week_data = $this->_weekActiveCount($activeAction,$time);
            $have = $this->where('date',$week_data['date'])->find();
            if(!$have){
                $this->save($week_data);
            }else{
                $this->save($week_data,['id'=>$have['id']]);
            }
        }

        $month_data = $this->_monthActiveCount($activeAction,$time);

        $have = $this->where('date',$month_data['date'])->find();
        if(!$have){
            $this->save($month_data);
        }else{
            $this->save($month_data,['id'=>$have['id']]);
        }

        return true;
    }

    /**
     * 每日活跃度统计
     * @param $action
     * @param $today
     * @return mixed
     */
    private function _dayActiveCount($action,$today)
    {
        $startTime = $today - 24*60*60;
        $map['action_id'] = $action;
        $map['create_time'] = ['between',[$startTime,$today-1]];

        $users_num = Db::name('action_log')->where($map)->count();
        $data['num'] = $users_num;
        $data['type'] = 'day';
        $data['date'] = $startTime;
        return $data;
    }

    /**
     * 每周活跃度统计
     * @param $action
     * @param $today
     * @return mixed
     */
    private function _weekActiveCount($action,$today)
    {
        $startTime = $today-7*24*60*60;
        $map['action_id'] = $action;
        $map['create_time'] = ['between',[$startTime,$today-1]];
        $users_num = Db::name('action_log')->where($map)->count();
        $data['num'] = $users_num;
        $data['type'] = 'week';
        $data['date'] = $startTime+7;//周统计date偏移7，实现date唯一
        return $data;
    }

    /**
     * 每月活跃度统计
     * @param $action
     * @param $today
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    private function _monthActiveCount($action,$today)
    {
        $startTime = strtotime(date('Y-m-01 00:00:00',strtotime('-1 month')));

        $map['action_id'] = $action;
        $map['create_time'] = ['between',[$startTime,$today-1]];
        $users_num = Db::name('action_log')->where($map)->count();
        $data['num'] = $users_num;
        $data['type'] = 'month';
        $data['date'] = $startTime+30;//月统计date偏移30，实现date唯一
        return $data;
    }

    /**
     * 获取活跃度列表
     * @param $startTime 开始时间
     * @param $endTime 结束时间
     * @param string $type
     * @return array
     */
    public function getActiveList($startTime,$endTime,$type='day')
    {
        switch($type){
            case 'week':
                $startTime = strtotime(date('Y-m-d',$startTime).' - '.date('w',$startTime).' day');
                break;
            case 'month':
                $startTime = strtotime(date('Y-m-01',$startTime));
                break;
            default:;
        }
        $map['type'] = $type;
        $map['date'] = ['between',$startTime.','.$endTime];
        $list = $this->where($map)->select();
        $list = collection($list)->toArray();
        $list = $this->_initActiveList($list,$startTime,$endTime,$type);
        return $list;
    }

    /**
     * 格式化活跃度数据
     * @param $list
     * @param $startTime
     * @param $endTime
     * @param $type 类型
     * @return array
     */
    private function _initActiveList($list,$startTime,$endTime,$type)
    {
        switch($type){
            case 'day':
                $away = 0;
                $range = ' + 1 day';
                $format = 'Y-m-d';
                if(strtotime(date('Y-m-d'))<=$endTime){//今日实时统计
                    $next = strtotime(time_format(time(),'Y-m-d').$range);
                }
            break;
            case 'week':
                $away=7;//周统计date偏移7，实现date唯一
                $range=' + 7 day';
                $format='W周(m-d)';
                if(strtotime(date('Y-m-d').' - '.date('w').' day')<=$endTime){//本周实时统计
                    $next = strtotime(date('Y-m-d').' - '.date('w').' day + 7 day');
                }
            break;
            case 'month':
                $away = 30;//月统计date偏移30，实现date唯一
                $range = ' + 1 month';
                $format= 'Y-m';
                if(strtotime(date('Y-m-01'))<=$endTime){//本月实时统计
                    $next = strtotime(time_format(time(),'Y-m-01').$range);
                }
            break;
            default:;
        }
        if($next){
            $activeAction = config('COUNT_ACTIVE_ACTION',null,3);
            $function = '_'.$type.'ActiveCount';
            $list['now']=$this->$function($activeAction,$next);
        }
        $lost = array();

        $hasDate = array_column($list,'date');
        $date = $startTime+$away;
        do{
            if(!in_array($date,$hasDate)){
                $lost[] = array('type'=>$type,'date'=>$date,'num'=>0,'total'=>'0');
            }
            $date = strtotime(time_format($date,'Y-m-d').$range)+$away;
        }while($date<=$endTime);
        if(count($lost)&&count($list)){
            $list = array_merge($lost,$list);
        }else if(count($lost)){
            $list = $lost;
        }
        $list = list_sort_by($list,'date');

        foreach($list as $val){
            $labels[] = date($format,$val['date']);
            $num[] = $val['num'];
        }
        unset($val);
        $resultList = array(
            'labels' => $labels,
            'datas' => array(
                'num' => $num
            )
        );
        return $resultList;
    }

    /**
     * 根据条件获取数据
     *
     * @param      <type>  $map    The map
     *
     * @return     <type>  The data by map.
     */
    public function getDataByMap($map)
    {
        $data = $this->where($map)->find();

        return $data;
    }
} 