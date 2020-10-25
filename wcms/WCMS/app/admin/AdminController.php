<?php
/**
 * 权限管理
 * User: Administrator
 * Date: 2018/8/27
 * Time: 10:56
 */
class AdminController extends Action {


    private $_user_global;

    public function __construct()
    {

        if (empty($_COOKIE['openid'])) {
           $this->redirect("请先登录!","/index.php?anonymous/login",true);
        }
        $customerSer = new EncryptService();

        if(strpos($_COOKIE['openid'],"%")){
            $openid=urldecode($_REQUEST['openid']);
        }else{
            $openid=$_COOKIE['openid'];
        }

        $str = $customerSer->jiemi($openid);
        preg_match("#wcms\#(\d+)\#wcms#", $str, $rs);

        $memberSer = new MemberService();
        $this->_user_global = $memberSer->getMemberByUid($rs[1]);

        if($this->_user_global['status']<0){
            $this->sendNotice("账号异常", null, false);
        }


        if($this->_user_global['groupid']!=1){
            echo "权限不足,请等待管理员审核!";
            exit();
        }

        $this->view()->assign('user',$this->_user_global);
    }


    public function getUserInfo(){
        var_dump($this->_user_global);
    }

}