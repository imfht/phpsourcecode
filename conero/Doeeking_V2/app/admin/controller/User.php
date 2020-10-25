<?php
namespace app\admin\controller;
use think\Loader;
use think\Controller;
use think\Db;
class User extends Controller
{
    public function index()
    {
        $this->loadScript([
            'auth'=>'DEV','title'=>'Conero','js'=>['User/index'],'css'=>['User/index'],'bootstrap'=>true
        ]);
        $this->userList();
        return $this->fetch();
    }
    private function userList()
    {
        $db = Db::table('net_user');
        $bstp = $this->bootstrap($this->view);
        $page = $bstp->page_decode();
        $wh = $bstp->getSearchWhere();
        $bstp->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['user_name'=>'姓名','user_nick'=>'昵称','loginTimes'=>'登录次数','last_ip'=>'最近操作ip','last_date'=>'维护日期','on_date'=>'注册日期','admin'=>'类型']]);
        $data = $db->order('`last_date` desc,`loginTimes` desc')->where($wh)->page($page,30)->select();$count = Db::table('net_user')->where($wh)->count();
        $tr = '';
        $i = 1;
        foreach($data as $v){
            $tr .= '<tr><td>'.$i.'</td><td>'.$v['user_name'].'</td><td><a href="javascript:void(0)" title="修改该用户的登录密码" class="change_pswd">'.$v['user_nick'].'</a></td><td>'.$v['loginTimes'].'</td><td>'.$v['last_ip'].'</td><td>'.$v['last_date'].'</td><td>'.$v['on_date'].'</td><td>'.$v['admin'].'</td></tr>';
            $i++;
        }
        $this->assign('userTr',$tr);        
        $bstp->pageBar($count);
        //debugOut($data);
    }
    public function ajax()
    {
        if(!isset($_POST['item'])) go('/conero/admin/user.html');
        $item = $_POST['item'];
        $ret = '';
        switch($item){
            case 'index/change_pswd':// 日志保存     
                $data = [];
                $data['command'] = $this->_password($_POST['pswd'],$_POST['nick']);
                $ret = $this->croDb('net_user')->where('user_nick',$_POST['nick'])->update($data);
                if($ret) $ret = 'Y';
                else $ret = 'N';
                break;
        }
        echo $ret;die;
    }
}
