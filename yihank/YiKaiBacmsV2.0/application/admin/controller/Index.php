<?php
namespace app\admin\controller;

class Index extends Admin{
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info' => array(
                'name' => '管理首页',
                'description' => '站点运行信息',
            ),
            'menu' => array(
                array(
                    'name' => '首页',
                    'url' => url('index'),
                    'icon' => 'list',
                ),
            ),
        );
    }
    public function index(){
        //前台引导
        $this->assign('home_url',model('HomeUrl')->loadList());
        $this->assign('langList',model('Lang')->loadList());
        $this->assign('loginUserInfo',$this->loginUserInfo);
        return $this->fetch();
    }
    //控制台
    public function home(){
        //获取当天的年份
        $y = date("Y");
        //获取当天的月份
        $m = date("m");
        //获取当天的号数
        $d = date("d");
        //将今天开始的年月日时分秒，转换成unix时间戳(开始示例：2015-10-12 00:00:00)
        $todayTime= mktime(0,0,0,$m,$d,$y);

        $info=array();
        $info['user_count']=model('User')->countList();//会员总量
        $where_user['add_time']=['gt',$todayTime];
        $info['user_count_today']=model('User')->countList($where_user);//今日注册
        $info['content_count']=model('article/ContentArticle')->countList();//文章总量
        $where_content['time']=['gt',$todayTime];
        $info['content_count_today']=model('article/ContentArticle')->countll($where_content);//今日新增
        $this->assign('info',$info);
        return $this->fetch();
    }
    //后台菜单
    public function menu(){
        $list = model('admin/menu')->menuLoadlist();
        $this->assign('list',$list);
        return $this->fetch();
    }
}
