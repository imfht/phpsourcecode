<?php
namespace Home\Controller;
use Think\Controller;
class AclController extends Controller {
    public function _initialize(){
        if(session('hellomarkeradminlogin')==1){
             $this->adminLoginShow();
        }else{
             $this->redirect('Admin/index');
        }
   }
   public function adminLoginShow(){
    $userLoginFlag=1;
    $this->assign('adminLoginFlag',$userLoginFlag);
    $this->assign('adminsessionname',session('hellomarkeradminname'));
   }
}