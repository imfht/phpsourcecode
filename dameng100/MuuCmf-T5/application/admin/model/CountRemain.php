<?php
namespace app\admin\Model;

use think\Model;
use think\Db;

class CountRemain extends Model
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 每日执行留存率统计
     * @return bool
     */
    public function remainCount()
    {
        $date = date('Y-m-d 00:00',time());
        $date = time_format(strtotime($date." - 2 day"),'Y-m-d 00:00');
        $this->_doRemainCount($date,1);

        return true;
    }


     /**
     * @param null $date 统计日期
     * @param int $day 统计几日留存率（1~8）
     * @return bool
     */
    private function _doRemainCount($date,$day = 1)
    {
        //统计start
        $strDayTime = strtotime($date);
        $endDayTime = strtotime($date." + 1 day")-1;
        $strCountTime = strtotime($date." + ".$day." day");
        $endCountTime = strtotime($date." + ".($day+1)." day")-1;

        $data = null;
        $remain = $this->where(['date'=>$strDayTime])->find();
        if($remain){
            $data = $remain->toArray();
        }

        $map_reg['reg_time'] = ['between',[$strDayTime,$endDayTime]];
        $map_reg['status'] = 1;
        $regUids = model('Member')->where($map_reg)->field('uid')->select();
        $regUids = array_column($regUids,'uid');
        if(!$remain){
            $data['reg_num'] = count($regUids);
            $data['date'] = $strDayTime;
        }

        if(count($regUids)){
            $tag='LOGIN_ACTION_ID';
            $login_action_id = cache($tag);
            if($login_action_id === false){
                $login_action_id = Db::name('Action')->where(['name'=>'user_login','status'=>1])->value('id');
                cache($tag,$login_action_id);
            }

            $map_login['action_id'] = $login_action_id;//用户登录行为id
            $map_login['user_id'] = ['in',$regUids];
            $map_login['create_time'] = ['between',[$strCountTime,$endCountTime]];
            $loginUids = Db::name('action_log')->where($map_login)->field('user_id')->select();
            $loginUids = array_column($loginUids,'user_id');
            $loginCount = count(array_unique($loginUids));
        }else{
            $loginCount = 0;
        }
        $data['day'.$day.'_num'] = $loginCount;
        cache('DAY_'.$day,$data);

        if($remain){//更新
            $this->save($data,['id'=>$data['id']]);
        }else{//新增
            $this->save($data);
        }
        //统计end
        //dump($day);
        if($day == 8){
            return true;
        }
        //下面执行前一天的统计
        $date = time_format(strtotime($date." - 1 day"),'Y-m-d 00:00');
        $day = $day+1;
        $this->_doRemainCount($date,$day);

        return true;
    }

    /**
     * 留存率数据查询
     * @param $strTime 开始日期（时间戳）
     * @param $endTime 结束日期（时间戳）
     * @return array
     */
    public function getRemainList($strTime,$endTime)
    {
        $map['date'] = array('between',array($strTime,$endTime));
        $list = $this->where($map)->select();
        $list = collection($list)->toArray();
        $list = $this->_initRemainList($list);
        
        return $list;
    }

    /**
     * 格式化留存率数据
     * @param $list
     * @return mixed
     * @author 大蒙 <59262424@qq.com> 重写
     */
    private function _initRemainList($list)
    {
        $date = date('Y-m-d 00:00',time());
        $special = [
            strtotime($date.' - 2 day')=>1,
            strtotime($date.' - 3 day')=>2,
            strtotime($date.' - 4 day')=>3,
            strtotime($date.' - 5 day')=>4,
            strtotime($date.' - 6 day')=>5,
            strtotime($date.' - 7 day')=>6,
            strtotime($date.' - 8 day')=>7,
            strtotime($date.' - 9 day')=>8
        ];

        $max = 0;

        foreach($list as &$val){
            $total = 0;

            if($val['date'] > strtotime($date.' - 2 day')){
                continue;
            }else if($val['date'] < strtotime($date.' - 9 day')){
                $val['day'] = [
                    $val['day1_num'],$val['day2_num'],$val['day3_num'],$val['day4_num'],$val['day5_num'],$val['day6_num'],$val['day7_num'],$val['day8_num']
                ];
            }else{
                $num = $special[$val['date']];
                for($i=1;$i<=$num;$i++){
                    $val['day'][] = $val['day'.$i.'_num'];
                }
            }

            $val['date_str'] = time_format($val['date'],'y-m-d');
            
            foreach($val['day'] as &$day){
                
                if($day != 0){
                    $day = [
                        'num' => $day,
                        'value' => $day/$val['reg_num']
                    ];
                    $total += $day['value'] + 0.0499;
                }else{
                    $day = [
                        'num' => 0,
                        'value' => 0
                    ];
                    $total += 0.0499;
                }
            }

            if($total > $max){
                $max = $total;
            }
            unset($day);
        }
        unset($val);

        //$minWidth = sprintf("%.2f",substr(sprintf("%.3f", 0.0499/$max * 100), 0, -2));
        $minWidth = sprintf("%.2f",100/8);
        
        foreach($list as &$val){
            foreach($val['day'] as &$day){
                if($day['num'] == 0){
                    $day['value'] = '0%';
                    $day['width'] = $minWidth.'%';
                }else{
                    $width = ($day['value']/$max) * 100 + $minWidth;
                    $width = sprintf("%.2f",substr(sprintf("%.3f", $width), 0, -2)).'%';
                    $day['value'] = round($day['value'] * 100,2).'%';
                    //$day['width'] = $width;
                    $day['width'] = $minWidth.'%';
                }
            }
            unset($day);
        }
        unset($val);
        if(count($list)>15){
            $list=list_sort_by($list,'date','desc');
        }else{
            $list=list_sort_by($list,'date','asc');
        }
        return $list;
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