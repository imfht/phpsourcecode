<?php
namespace app\center\Logic;
use app\center\Logic\Controller;
class User extends Controller{
    public function init(&$opts,$action){
        if($action == 'edit'){
            $js = $opts['js'];
            $js[] = 'index/user_edit';
            $opts['js'] = $js;
        }        
        //println($opts,getUrlBind('index'));
    }
    public function main()
    {
        $app = $this->app;
        $uInfo = uInfo();
        $user = $app->croDb('net_user')->where('user_code',$uInfo['code'])->find();
        // 注册时间长度计算
        $onDate = function($date){
            $d1 = date_create($date);
            $dt = date_diff($d1, date_create("now"));
            //return $dt;
            return ($dt->y).'年'.($dt->m).'月'.($dt->d).'天'.($dt->h).'小时'.($dt->i).'分钟'.($dt->s).'秒';
        };
        $login = $app->croDb('syslogin')->where('uid',$uInfo['uid'])->field('login_count,login_log')->find();
        $photo = $app->croDb('sys_file')->where(['user_code'=>$uInfo['code'],'file_use'=>'P0'])->value('url_name');
        if($photo) $photo = '<p class="text-right"><a href="javascript:void(0);"><img src="/conero/files/'.$photo.'" class="img-thumbnail" style="width: 140px; height: 140px;"></a></p>';
        else $photo = '';
        $html = $photo.'
            <div class="page-header">
                <h1>用户信息 <small>'.$uInfo['name'].'</small></h1>
            </div>
            <h4>基本信息</h4>
            <div class="alert alert-info" role="alert"><strong>邮箱</strong> '.$user['exmail_id'].'</div>            
            '.($user['cellph_id']? '<div class="alert alert-info" role="alert"><strong>联系电话</strong> '.$user['cellph_id'].'</div>':'').'
            <div class="alert alert-info" role="alert"><strong>用户类型</strong> '.adminDescrp($user['admin']).'</div>
            '.$this->info($uInfo['code']).'
            <div class="page-header">
                <h1>账号概述 <small>'.$onDate($user['on_date']).'['.$user['on_date'].']</small></h1>
            </div>
            <div class="well">
                <p class="bg-success">您累计共登录'.$login['login_count'].'次</p>
                <h4>登录日志</h4>
                '.$login['login_log'].'
                <a href="/Conero/center/index/edit/user.html" class="btn btn-default">编辑</a>
            </div>
        ';
        return $html;
    }
    private function info($code)
    {
        $html = '';
        $app = $this->app;       
        $info = $app->croDb('net_info')->where('user_code',$code)->find();
        if(is_array($info)){
            $html = '<div class="alert alert-info" role="alert"><strong>性别</strong> '.($info['gender'] == 'M'? '男':'女').'</div>'
                    .($info['birth']? '<div class="alert alert-info" role="alert"><strong>生日</strong> '.$info['birth'].'</div>':'')                                        
                    .($info['book']? '<div class="alert alert-info" role="alert"><strong>书籍</strong> '.$info['book'].'</div>':'')
                    .($info['city']? '<div class="alert alert-info" role="alert"><strong>城市</strong> '.$info['city'].'</div>':'')                    
                    .($info['adrss']? '<div class="alert alert-info" role="alert"><strong>地址</strong> '.$info['adrss'].'</div>':'')
                    .'<h4>个性签名</h4><div class="alert alert-success" role="alert">'.$info['sign'].'</div>'
                    .($info['interest']? '<h4>兴趣</h4><div class="alert alert-success" role="alert">'.$info['interest'].'</div>':'')
                    ;
        }
        return $html;
    }
    public function edit($view)
    {
        $this->viewInit($view);
        $formid = getUrlBind('user');

        $editParam = [
            'navbar'    => '<li><a href="/conero/center.html?user.html">个人中心</a></li>',
            'navActive' => $formid == 'acount'? '编辑.账户信息':'编辑.基本信息'
        ];
        $this->editPageParam($editParam);
        
        $app = $this->app;$code = uInfo('code');        
        $user = $app->croDb('net_user')->alias('a')->join('net_info b','a.user_code=b.user_code','left')->where('a.user_code',$code)->find();
        if($formid == 'acount') $this->edit_acount($user);
        else $this->edit_baseInfo();
        $view->assign([
            'formid' => $formid,
            'userParam' => $user
        ]);
        $this->form($view);
    }
    // 账号编辑
    protected function edit_acount($info)
    {
        $helper = [];$app = $this->app;$code = uInfo('code');
        $dt = date_create($info['on_date']);
        $dt2 = date_create('now');
        $helper['nickDiff'] = date_diff($dt, $dt2)->format('%a');
        if($helper['nickDiff'] > 60) $helper['nickDiff'] = $helper['nickDiff'];
        else unset($helper['nickDiff']);
        // 头像
        $photo = $app->croDb('sys_file')->where(['user_code'=>$code,'file_use'=>'P0'])->value('url_name');
        if($photo) $photo = '<p class="text-right"><a href="javascript:void(0);"><img src="/conero/files/'.$photo.'" class="img-thumbnail" style="width: 140px; height: 140px;"></a></p>';

        $this->assign([
            'helper' => $helper,
            'photo'  => empty($photo)? null:$photo
        ]);
    }
    // 基本信息
    protected function edit_baseInfo()
    {
        $app = $this->app;$code = uInfo('code');
        $helper = [];
        $hCityArr = $app->croDb('syslogin')->where('user_code=\''.$code.'\' and city is not null')->order('edittm desc')->field('city')->limit(5)->group('city')->select();
        $hCity = '';
        foreach($hCityArr as $v){
            $hCity .= '<a href="JavaScript:void(0);" class="set_city">'.$v['city'].'</a>';
        }
        $helper['city'] = $hCity;
        $this->view->assign('helper',$helper);
    }
    public function save()
    {
        println($_POST,empty($_GET),empty($_POST));
        //println($_POST? $_POST:$_GET);
        $data = $_POST? $_POST:$_GET;
        $formid = isset($data['formid'])? $data['formid']:null;
        $ret = '';
        if($formid == 'baseinfo'){// 基本信息
            ;
        }
    }
}