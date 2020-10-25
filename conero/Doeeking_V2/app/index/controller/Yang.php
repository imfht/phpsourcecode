<?php
// 2016年12月29日 星期四  作者简介
namespace app\index\controller;
use think\Controller;
class Yang extends Controller{
    public function index()
    {
        $this->loadScript([
            'title'=>'Joshua Conero Yang','require'=>['bootstrap']
        ]);
        $page = [
            'year'=>date('Y')
            ,'visit_count'=>$this->aboutVisit()
        ];
        $this->assign('page',$page);
        return $this->fetch();
    }
    // 信息 - 文章
    public function infor()
    {
        $this->loadScript([
            'title'=>'Joshua Conero Yang','require'=>['bootstrap']
        ]);
        $uid = isset($_GET['uid'])? base64_decode($_GET['uid']):null;
        if($uid){
            $uid = substr($uid,0,stripos($uid,'='));
            $data = $this->croDb('sys_infor')->where('no',$uid)->find();
            if($data){
                $data['loadsuccess'] = 'Y';
                $this->assign('data',$data);
                // 分页
                $pager = [];
                $previous = $this->croDb('sys_infor')->where('type=\'30\' and no <\''.$data['no'].'\'')->field('title,no')->limit(1)->find();
                if($previous) $pager['previous'] = '<li class="previous"><a href="/conero/index/yang/infor.html?uid='.base64_encode($previous['no'].'='.sysdate()).'" title="'.$previous['title'].'">&larr; 上一篇</a></li>';
                $next = $this->croDb('sys_infor')->where('type=\'30\' and no >\''.$data['no'].'\'')->field('title,no')->limit(1)->find();
                if($next) $pager['next'] = '<li class="next"><a href="/conero/index/yang/infor.html?uid='.base64_encode($next['no'].'='.sysdate()).'" title="'.$next['title'].'">下一篇 &rarr;</a></li>';
                if($pager) $this->assign('pager',$pager);
            }
                        
            //$uid = '688857455=2016-12-29 22:03:37';
            //println($uid,substr($uid,0,stripos($uid,'=')));
        }
        $page = [
            'year'=>date('Y')
            ,'visit_count'=>$this->aboutVisit()
        ];
        $this->assign('page',$page);
        return $this->fetch();
    }
}