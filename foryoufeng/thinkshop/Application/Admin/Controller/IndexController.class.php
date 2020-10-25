<?php
namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Controller;
class IndexController extends AdminController {
    //后台启动界面
    public function index(){
        //计算统计图日期
        $today = strtotime(date('Y-m-d', time())); //今天
        $start_date = I('get.start_date') ? I('get.start_date')/1000 : $today-14*86400;
        $end_date   = I('get.end_date') ? (I('get.end_date')+1)/1000 : $today+86400;
        $count_day  = ($end_date-$start_date)/86400; //查询最近n天
        $user_object = D('User');
        for($i = 0; $i < $count_day; $i++){
            $day = $start_date + $i*86400; //第n天日期
            $day_after = $start_date + ($i+1)*86400; //第n+1天日期
            $map['ctime'] = array(
                array('egt', $day),
                array('lt', $day_after)
            );
            $user_reg_date[] = date('m月d日', $day);
            $user_reg_count[] = (int)$user_object->where($map)->count();
        }

        $this->assign('start_date', date('Y年m月d日', $start_date));
        $this->assign('end_date', date('Y年m月d日', $end_date-1));
        $this->assign('count_day', $count_day);
        $this->assign('user_reg_date', json_encode($user_reg_date));
        $this->assign('user_reg_count', json_encode($user_reg_count));
        $this->assign('meta_title', "后台首页");
        $this->display();
    }
}