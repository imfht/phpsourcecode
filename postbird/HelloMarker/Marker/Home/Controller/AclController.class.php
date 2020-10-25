<?php
namespace Home\Controller;
use Think\Controller;
class AclController extends Controller {
    public function _initialize(){
        if(session('hellomarkeruserlogin')==1){
             $this->userLoginShow();
        }else{
            if(cookie('hellomarkercookieflag')==1) {
                if(strlen(cookie('hellomarkerusername'))>1){
                    if(session('hellomarkeruserlogin')==1){

                        $this->userLoginShow();
                    }else{
                        session('hellomarkeruserlogin',1);
                        session('hellomarkerusername',cookie('hellomarkerusername'));
                        session('hellomarkerusernickname',cookie('hellomarkerusernickname'));
                        session('hellomarkeruserid',cookie('hellomarkeruserid'));
                        $this->userLoginShow();   
                    }
                }else{
                    $this->userNoLoginShow();
                }
             }else{
                $this->userNoLoginShow();
           }
        }
   }
   public function userNoLoginShow(){
    $userLoginFlag=0;
    $this->assign('userLoginFlag',$userLoginFlag);
    $this->assign('usersessionid',0);
    $this->assign('usersessionname',0);
   }
   public function userLoginShow(){
    $userLoginFlag=1;
    $this->assign('userLoginFlag',$userLoginFlag);
    $this->assign('usersessionid',session('hellomarkeruserid'));
    $this->assign('usersessionname',session('hellomarkerusername'));
   }
}