<?php
namespace app\mobile\controller;
use think\Db;
use clt\Leftnav;
use think\Controller;
class Common extends Controller
{
    public function initialize()
    {
        if(!isMobile()){
            $this->redirect('home/index/index');
        }
        $sys = cache('System');
        $this->assign('sys',$sys);

        //导航
        $cate = cache('cate');
        if(!$cate){
            $column_one = Db::name('category')->where([['pid','=',0],['ismenu','=',1]])->order('sort')->select();
            $column_two = Db::name('category')->where('ismenu',1)->order('sort')->select();
            $tree = new Leftnav ();
            $cate = $tree->index_top($column_one,$column_two);
            cache('cate', $cate, 3600);
        }
        $this->assign('category',$cate);

        //二级导航
        $thisCat = Db::name('category')->where('id',input('catId'))->find();
        $this->assign('title',$thisCat['title']);
        $this->assign('keywords',$thisCat['keywords']);
        $this->assign('description',$thisCat['description']);
        define('DBNAME',strtolower($thisCat['module']));
        $this->pagesize = $thisCat['pagesize']>0 ? $thisCat['pagesize'] : '';

        //广告
        $adList = cache('adList');
        if(!$adList){
            $adList = Db::name('ad')->where(['type_id'=>1,'open'=>1])->order('sort asc')->limit('4')->select();
            cache('adList', $adList, 3600);
        }
        $this->assign('adList', $adList);

        //畅言
        $plugin = db('plugin')->where(['code'=>'changyan'])->find();
        $this->changyan = unserialize($plugin['config_value']);
        $this->assign('changyan', $this->changyan);
    }
    //空操作
    public function _empty(){
        return $this->error('空操作，返回上次访问页面中...');
    }
}
