<?php
namespace app\center\Logic;
use app\center\Logic\Controller;
class Login extends Controller{
    public function main()
    {
        $app = $this->app;
        $bstp = $app->bootstrap();
        $wh = $bstp->getSearchWhere('code');
        $count = $app->croDb('syslogin')->where($wh)->count();
        $html = $bstp->GridSearchForm(['__cols__'=>['login_count'=>'登录次数','login_ip'=>'登录IP','edittm'=>'时间','city'=>'地区'],'ipts'=>'<input type="hidden" name="login">']);
        $html .= $bstp->tableGrid([
                'cols'=>['登录次数','登录IP','时间','地区']],[
                'table'=>'syslogin','orderQuit'=>true,
                'cols'=>['login_count','login_ip','edittm','city']
            ],
            function($db){
                $bstp = $this->app->bootstrap();
                $page = $bstp->page_decode();
                $wh = $bstp->getSearchWhere('code');
                return $db->where($wh)->order('edittm desc')->page($page,30)->select();
        });
        $html .= $bstp->pageBar($count);
        return $html;
    }
}