<?php
namespace app\index\controller;

use think\Db;
use think\Session;

class Index extends Common_base
{
    public function index()
    {
        //个人信息
        $user = Db::name('users')->alias('u')
            ->join('auth_group g', 'u.user_auth = g.id')
            ->where('u.id',Session::get('user_id'))
            ->field('u.user_name,u.user_nick,u.id as uid,g.title')
            ->find();
        $pcount = Db::name('purchase')->where('status', '>=', 0)->count();//共计订单

        $pshenc = Db::name('purchase')->where('status', '=', 2)->count();//生产订单

        $pchuhuo = Db::name('purchase')->where('status', '=', 5)->count();//完成订单

        $pqueren = Db::name('purchase')->where('affirm', '=', 0)->where('status', '=',0)->count();//待确认订单

        $pqrdjin = Db::name('purchase')->where('affirm', '=',1)->where('status', '=',0)->count();//待确认订金

        $pqrweikuan = Db::name('purchase')->where('affirm', '=', 1)->where('status', '=',3)->count();//待确认尾款

        $pqrchuku = Db::name('purchase')->where('affirm', '=', 1)->where('status', '=',4)->count();//待确认出库

        //获取当前年份
        $dateD = date('Y');
        //获取当前月份 $dateM = date('m');
        //获取月份的最后一天 date('t',strtotime('2017-8')) //31
        //循环当前查询的年份，每个月销售
        for ($i=1;$i<=12;$i++) {
            $dqy = $dateD.'-'.$i;  //月份
            $dqy1 = $dqy.'-01 0:0:0'; //月份第一天
            $dqy2 = $dqy.'-'. date('t',strtotime($dqy)) .' 23:59:59'; //月份最后一天
            $dateArr[] = Db::name('purchase')
                ->where('affirm',1)
                ->where('create_time', '>=',strtotime($dqy1))
                ->where('create_time', '<=', strtotime($dqy2))
                ->count();
        }

        $assign = [
            'title'  => '首页',
            'user'  => $user,
            'pcount' => $pcount,
            'pshenc' => $pshenc,
            'pchuhuo' => $pchuhuo,
            'pqueren' => $pqueren,
            'pqrdjin' => $pqrdjin,
            'pqrweikuan' => $pqrweikuan,
            'pqrchuku' => $pqrchuku,
            'dateD' => $dateD,
            'dateArr' => json_encode($dateArr),
        ];
        $this->assign($assign);
        return $this->fetch();
    }
}
