<?php

/**
 *@author wolf  [Email: 116311316@qq.com]
 *@since 2011-07-20  2014-08-04
 *@version 3.0 第3次简化
 */
class MemberadminController extends AdminController
{

    static $service;
    // 配置用户列表 导入
    public function getAllMember ()
    {

        $rs=self::getMemberService()->getAllMember();
        $this->view()->assign('rs',$rs);
        $this->view()->display('file:member/member.html');
    }
    

    public function removeMemberByUid(){
        $rs=self::getMemberService()->removeMemberByUid($_POST['uid']);
        $this->sendNotice("删除成功",null,true);
    }


    public function edit(){
        $rs=self::getMemberService()->getMemberByUid($_GET['uid']);
        $this->view()->assign('rs',$rs);
        $this->view()->display("file:member/edit.html");
    }

    public function saveMemberByUid(){
        self::getMemberService()->saveMemberByUid($_POST);
        $this->redirect("SUCCESS","/index.php?memberadmin/getallmember");
    }


    public static function getMemberService ()
    {
        if (self::$service == null) {
            self::$service = new MemberService();
        }
        return self::$service;
    }
}