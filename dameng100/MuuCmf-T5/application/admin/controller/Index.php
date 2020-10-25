<?php
namespace app\admin\Controller;

use app\admin\controller\Admin;
use think\Db;

class Index extends Admin
{
    /**
     * 控制台首页
     * @return [type] [description]
     */
    public function index()
    {
        if(request()->isPost()){
            $count_day=input('post.count_day', config('COUNT_DAY'),'intval',7);
            if(Db::name('Config')->where(['name'=>'COUNT_DAY'])->setField('value',$count_day)===false){
                $this->error(lang('_ERROR_SETTING_').lang('_PERIOD_'));
            }else{
               cache('DB_CONFIG_DATA',null);
                $this->success(lang('_SUCCESS_SETTING_').lang('_PERIOD_'));
            }

        }else{
            
            $this->setTitle(lang('_INDEX_MANAGE_'));
            $this->getRegUser();
            $this->getActionLog();
            $this->getUserCount();
            $this->getOtherCount();
            return $this->fetch();
        }
    }

    private function getOtherCount(){
        
        //用户流失
        $lostList = model('CountLost')->getListByPage([],'create_time desc','*',$r=20);
        $this->assign('lostList',$lostList);
        //日活跃
        $today = date('Y-m-d 00:00',time());
        $startTime = strtotime($today." - 10 day");
        $endTime = strtotime($today);
        $startTime = strtotime(date('Y-m-d').' - 9 day');
        $activeList = model('CountActive')->getActiveList($startTime,time(),'day');
        $this->assign('activeList',json_encode($activeList));
        //周活跃
        $startTime = strtotime(date('Y-m-d').' - '.date('w').' day - 49 day');
        $weekActiveList = model('CountActive')->getActiveList($startTime,time(),'week');
        $this->assign('weekActiveList',json_encode($weekActiveList));
        //月活跃
        $startTime = strtotime(date('Y-m-01').' - 9 month');
        $monthActiveList = model('CountActive')->getActiveList($startTime,time(),'month');
        $this->assign('monthActiveList',json_encode($monthActiveList));
        //前8日留存率
        $startTime = strtotime($today." - 9 day");
        $endTime = strtotime($today." - 2 day");
        $remainList = model('CountRemain')->getRemainList($startTime,$endTime);
        $this->assign('remainList',$remainList);
        
        return true;
    }
    /**
     * 获取顶部块统计数据
     * @return [type] [description]
     */
    private function getUserCount(){

        $t = time();
        $start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
        $end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));

        $map['status']=1;
        $map['reg_time']=[['>=',$start],['<=',$end],'and'];
        $reg_users = Db::name('UcenterMember')->where($map)->count();

        unset($map['reg_time']);
        $map['last_login_time'] = [['>=',$start],['<=',$end],'and'];
        $login_users = Db::name('UcenterMember')->where($map)->count();
        $total_user = Db::name('UcenterMember')->count();
        $today_action_log = Db::name('ActionLog')->where('status=1 and create_time>=' . $start)->count();

        $count['today_user'] = $reg_users;
        $count['login_users'] = $login_users;
        $count['total_user'] = $total_user;
        $count['today_action_log'] = $today_action_log;
        
        $this->assign('count', $count);
    }
    /**
     * 最近N日用户增长
     * @return [type] [description]
     */
    private function getRegUser()
    {
        $today = date('Y-m-d', time());
        $today = strtotime($today);
        
        $week = [];
        $regMemeberCount = [];
        $count_day = config('COUNT_DAY');

        //每日注册用户
        for ($i = $count_day; $i--; $i >= 0) {
            $day = $today - $i * 86400;
            $day_after = $today - ($i - 1) * 86400;
            $week_map = [
                'Mon' => lang('_MON_'), 
                'Tue' => lang('_TUES_'), 
                'Wed' => lang('_WEDNES_'), 
                'Thu' => lang('_THURS_'), 
                'Fri' => lang('_FRI_'), 
                'Sat' => lang('_SATUR_'), 
                'Sun' => lang('_SUN_')
            ];
            $week[] = date('m月d日 ', $day) . $week_map[date('D', $day)];

            $map['status']=1;
            $map['reg_time']=[['>=',$day],['<=',$day_after],'and'];
            $user = Db::name('UcenterMember')->where($map)->count() * 1;
            $regMemeberCount[] = $user;
        }

        $regMember['days'] = $week;
        $regMember['data'] = $regMemeberCount;
        $regMember = json_encode($regMember);

        $this->assign('count_day',$count_day);
        $this->assign('regMember', $regMember);
    }

    /**
     * 最近N日用户行为数据
     * @return [type] [description]
     */
    private function getActionLog()
    {
        $today = date('Y-m-d', time());
        $today = strtotime($today);
        $count_day = 7;//默认一周

        $week = [];
        $actionLogData = [];
        //每日用户行为数量
        for ($i = $count_day; $i--; $i >= 0) {
            $day = $today - $i * 86400;
            $day_after = $today - ($i - 1) * 86400;
            $week_map = [
                'Mon' => lang('_MON_'), 
                'Tue' => lang('_TUES_'), 
                'Wed' => lang('_WEDNES_'), 
                'Thu' => lang('_THURS_'), 
                'Fri' => lang('_FRI_'), 
                'Sat' => lang('_SATUR_'), 
                'Sun' => lang('_SUN_')
            ];
            $week[] = date('m月d日 ', $day) . $week_map[date('D', $day)];

            $map['status']=1;
            $map['create_time']=[['>=',$day],['<=',$day_after],'and'];
            $user = Db::name('action_log')->where($map)->count() * 1;
            $actionLogData[] = $user;
        }

        $actionLog['days'] = $week;
        $actionLog['data'] = $actionLogData;
        $actionLog = json_encode($actionLog);

        $this->assign('actionLog', $actionLog);
    }

    public function debug()
    {
        return $this->fetch();
    }
}
