<?php
namespace app\admin\controller;

use app\admin\controller\Admin;
use think\Db;

class Count extends Admin{

    protected $countModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->assign('now_table',request()->action());
    }

    /**
     * 流失率统计
     */
    public function lost($r=10)
    {
        if(request()->isPost()){
            $aLostLong=input('post.lost_long',30,'intval');
            if($aLostLong>=1){
                if(Db::name('Config')->where(array('name'=>'LOST_LONG'))->setField('value',$aLostLong)===false){
                    $this->error("设置失败！");
                }else{
                    cache('DB_CONFIG_DATA',null);
                    $this->success("设置成功！");
                }
            }
        }else{
            $day = config('LOST_LONG',30);
            $this->assign('lost_long',$day);

            $lostList = model('CountLost')->getListByPage([],'create_time desc','*',$r=20);
            $page = $lostList->render();
            
            $this->assign('lostList',$lostList);
            $this->assign('page', $page);
            $this->setTitle('流失率统计');

            return $this->fetch();
        }
    }

    /**
     * 留存率统计
     */
    public function remain()
    {
        if(request()->isPost()){
            $aStartTime = input('post.startDate','','text');
            $aEndTime = input('post.endDate','','text');

            if($aStartTime == '' || $aEndTime == ''){
                $this->error('请选择时间段!');
            }

            $startTime = strtotime($aStartTime);
            $endTime = strtotime($aEndTime);
            $remainList = model('CountRemain')->getRemainList($startTime,$endTime);
            $this->assign('remainList',$remainList);
            $html = $this->fetch('count/_remain_data');
            return $this->fetch($html);
        }else{
            $today = date('Y-m-d 00:00',time());
            $startTime = strtotime($today." - 9 day");
            $endTime = strtotime($today." - 2 day");
            $remainList = model('CountRemain')->getRemainList($startTime,$endTime);
            $options = array('startDate'=>time_format(strtotime($today." - 9 day"),"Y-m-d"),'endDate' => time_format(strtotime($today." - 2 day"),"Y-m-d"));
            $this->assign('options',$options);
            $this->assign('remainList',$remainList);
            $this->setTitle('留存率统计');
            
            return $this->fetch();
        }
    }

    /**
     * 活跃用户统计
     */
    public function active()
    {
        if(request()->isPost()){
            $aType = input('post.type','day','text');
            $aStartTime = input('post.startDate','','text');
            $aEndTime = input('post.endDate','','text');
            if($aStartTime == ''||$aEndTime == ''){
                $this->error('请选择时间段!');
            }
            $startTime = strtotime($aStartTime);
            $endTime = strtotime($aEndTime);
            if(!in_array($aType,array('week','month','day'))){
                $aType = 'day';
            }
            $activeList = model('CountActive')->getActiveList($startTime,$endTime,$aType);
            $activeList['status'] = 1;

            return json($activeList);

        }else{
            $aType = input('get.type','day','text');
            switch($aType){
                case 'week':
                    $startTime = strtotime(date('Y-m-d').' - '.date('w').' day - 91 day');
                    break;
                case 'month':
                    $startTime = strtotime(date('Y-m-01').' - 9 month');
                    break;
                default:
                    $aType = 'day';
                    $startTime = strtotime(date('Y-m-d').' - 9 day');
            }

            $this->assign('type',$aType);
            $options=array('startDate'=>time_format($startTime,"Y-m-d"),'endDate'=>time_format(time(),"Y-m-d"));
            $this->assign('options',$options);
            $activeList = model('CountActive')->getActiveList($startTime,time(),$aType);
            $this->assign('activeList',json_encode($activeList));
            
            $this->setTitle('活跃用户统计');
            return $this->fetch();
        }
    }

    /**
     * 设置活跃度绑定的行为
     */
    public function setActiveAction()
    {
        if(request()->isPost()){
            $aActiveAction = input('post.active_action',3,'intval');
            
            if(Db::name('Config')->where(['name' => 'COUNT_ACTIVE_ACTION'])->setField('value',$aActiveAction)===false){
                $this->error("设置失败！");
            }else{
                cache('DB_CONFIG_DATA',null);
                $this->success("设置成功！");
            }
        }else{
            $map['status'] = 1;
            $actionList = model('common/Action')->getAction($map);
            $this->assign('action_list',$actionList);
            $nowAction = config('COUNT_ACTIVE_ACTION');
            //dump($nowAction);exit;
            $this->assign('now_active_action',$nowAction);
            $this->setTitle('设置活跃度绑定的行为');

            return $this->fetch('set_active_action');
        }
    }

} 