<?php
namespace app\home\controller;
use think\Db;
use clt\Leftnav;
use think\Controller;
class Common extends Controller
{
    protected $pagesize;
    public function initialize()
    {
        $system = cache('System');
        $this->assign('config',$system);
        if($system['mobile']=='open'){
            if(isMobile()){
                $this->redirect('mobile/index/index');
            }
        }
        $userInfo='';
        if(session('user')){
            //用户信息
            $userInfo =Db::name('users')->alias('u')
                ->join('user_level ul','u.level = ul.level_id','left')
                ->field('u.*,ul.level_name as level')
                ->where('u.id',session('user.id'))
                ->find();
        }
        $this->assign('userInfo',$userInfo);

        $action = request()->action();
        $controller = request()->controller();
        $this->assign('action',($action));
        $this->assign('controller',strtolower($controller));
        define('MODULE_NAME',strtolower($controller));
        define('ACTION_NAME',strtolower($action));

        //导航
        $thisCat = Db::name('category')->where('id',input('catId'))->find();
        $this->assign('title',$thisCat['title']);
        $this->assign('keywords',$thisCat['keywords']);
        $this->assign('description',$thisCat['description']);
        //判断是否为单页面模型
        $hasCat = Db::name('field')->where(['moduleid'=>$thisCat['moduleid'],'type'=>'catid'])->find();
        define('DBNAME',strtolower($thisCat['module']));
        if($hasCat){
            define('ISPAGE',0);
        }else{
            define('ISPAGE',1);
        }
        $this->pagesize = $thisCat['pagesize']>0 ? $thisCat['pagesize'] : '';

        if($thisCat['pid'] ==0){
            $this->assign('pid',input('catId'));
            $this->assign('ptitle',$thisCat['title']);
        }else{
            $this->assign('ptitle',Db::name('category')->where('id',$thisCat['pid'])->value('title'));
            $this->assign('pid',$thisCat['pid']);
        }

        // 获取缓存数据
        $cate = cache('cate');
        if(!$cate){
            $column_one = Db::name('category')->where([['pid','=',0],['ismenu','=',1]])->order('sort')->select();
            $column_two = Db::name('category')->where('ismenu',1)->order('sort')->select();
            $tree = new Leftnav ();
            $cate = $tree->index_top($column_one,$column_two);
            cache('cate', $cate, 3600);
        }
        $this->assign('category',$cate);


        //友情链接
        $linkList = cache('linkList');
        if(!$linkList){
            $linkList = Db::name('link')->where('open',1)->order('sort asc')->select();
            cache('linkList', $linkList, 3600);
        }
        $this->assign('linkList', $linkList);
        //畅言
        $plugin = Db::name('plugin')->where(['code'=>'changyan'])->find();
        $this->changyan = unserialize($plugin['config_value']);
        $this->assign('changyan', $this->changyan);
        $this->assign('time', time());

    }
    //空操作
    public function _empty(){
        return $this->error('空操作，返回上次访问页面中...');
    }
}
