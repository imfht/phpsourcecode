<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 */
class IndexController extends AdminController
{

    /**
     * 后台首页
     */
    public function index()
    {

        if (UID) {

            if(IS_POST){
                $count_day=I('post.count_day', C('COUNT_DAY'),'intval',7);
                if(M('Config')->where(array('name'=>'COUNT_DAY'))->setField('value',$count_day)===false){
                    $this->error(L('_ERROR_SETTING_').L('_PERIOD_'));
                }else{
                   S('DB_CONFIG_DATA',null);
                    $this->success(L('_SUCCESS_SETTING_').L('_PERIOD_'),'refresh');
                }

            }else{
                //获取本地版本号
                $version = file_get_contents('./Data/version.ini');

                
                $this->meta_title = L('_INDEX_MANAGE_');
                $this->assign('version',$version);
                $this->assign('count', $this->getUserCount());
                $this->getOtherCount();
                $this->display();
            }


        } else {
            $this->redirect('Public/login');
        }
    }

    private function getOtherCount(){
        $countModel=D('Count');
        list($lostList,$totalCount)=$countModel->getLostListPage($map=1,1,5);
        foreach($lostList as &$val){
            $val['date']=time_format($val['date'],'Y-m-d');
            $val['rate']=($val['rate']*100)."%";
        }
        unset($val);
        $this->assign('lostList',$lostList);

        $today=date('Y-m-d 00:00',time());
        $startTime=strtotime($today." - 10 day");
        $endTime=strtotime($today);

        $startTime=strtotime(date('Y-m-d').' - 9 day');
        $activeList=$countModel->getActiveList($startTime,time(),'day');
        $this->assign('activeList',json_encode($activeList));

        $startTime=strtotime(date('Y-m-d').' - '.date('w').' day - 49 day');
        $weekActiveList=$countModel->getActiveList($startTime,time(),'week');
        $this->assign('weekActiveList',json_encode($weekActiveList));

        $startTime=strtotime(date('Y-m-01').' - 9 month');
        $monthActiveList=$countModel->getActiveList($startTime,time(),'month');
        $this->assign('monthActiveList',json_encode($monthActiveList));

        $startTime=strtotime($today." - 9 day");
        $endTime=strtotime($today." - 2 day");
        $remainList=$countModel->getRemainList($startTime,$endTime);
        $this->assign('remainList',$remainList);
        return true;
    }

    private function getUserCount(){
        $today = date('Y-m-d', time());
        $today = strtotime($today);
        $count_day = C('COUNT_DAY', null, 7);
        $count['count_day'] = $count_day;
        for ($i = $count_day; $i--; $i >= 0) {
            $day = $today - $i * 86400;
            $day_after = $today - ($i - 1) * 86400;
            $week_map = array('Mon' => L('_MON_'), 'Tue' => L('_TUES_'), 'Wed' => L('_WEDNES_'), 'Thu' => L('_THURS_'), 'Fri' => L('_FRI_'), 'Sat' => '<strong>' . L('_SATUR_') . '</strong>', 'Sun' => '<strong>' . L('_SUN_') . '</strong>');
            $week[] = date('m月d日 ', $day) . $week_map[date('D', $day)];
            $user = UCenterMember()->where('status=1 and reg_time >=' . $day . ' and reg_time < ' . $day_after)->count() * 1;
            $registeredMemeberCount[] = $user;
            if ($i == 0) {
                $count['today_user'] = $user;
            }
        }
        $week = json_encode($week);
        $this->assign('week', $week);
        $count['total_user'] = $userCount = UCenterMember()->where(array('status' => 1))->count();
        $count['today_action_log'] = M('ActionLog')->where('status=1 and create_time>=' . $today)->count();
        $count['last_day']['days'] = $week;
        $count['last_day']['data'] = json_encode($registeredMemeberCount);
        $count['now_inline']=M('Session')->where(1)->count()*1;
        return $count;
    }

    /**
     * 保存用户统计设置
     */
    private function saveUserCount()
    {
        $count_day = I('post.count_day', C('COUNT_DAY'), 'intval', 7);
        if (M('Config')->where(array('name' => 'COUNT_DAY'))->setField('value', $count_day) === false) {
            $this->error(L('_ERROR_SETTING_') . L('_PERIOD_'));
        } else {
            S('DB_CONFIG_DATA', null);
            $this->success(L('_SUCCESS_SETTING_'), 'refresh');
        }
    }
    

}
