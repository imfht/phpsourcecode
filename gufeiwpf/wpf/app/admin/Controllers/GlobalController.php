<?php
namespace Wpf\App\Admin\Controllers;
class GlobalController extends \Wpf\App\Admin\Common\Controllers\CommonController{

    public function loginAction(){

        if($this->request->isPost()){
            
            $this->view->disable();
            
            $model = new \Wpf\App\Admin\Models\AdminMember();
            
            
            $username = $this->request->getPost("username");
            $password = $this->request->getPost("password");
            
            $uid = $model->checkLogin($username,$password);
            if(0 < $uid){
               $this->success('登录成功！', $this->url->get('Index/index'));
            }else { //登录失败
                switch($uid) {
                    case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
                    case -2: $error = '密码错误！'; break;
                    default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
                }
                $this->error($error);
            }
            
        }else{
            
            if($this->isLogin()){
                $this->response->redirect("Index/index")->getHeaders()->send();
                exit;
            }

            $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
            $this->headercss
                ->addCss("theme/assets/admin/pages/css/login.css"); 
                
            $this->footerjs
                ->addJs("theme/assets/global/plugins/jquery-validation/js/jquery.validate.min.js")
                ->addJs("theme/assets/global/plugins/jquery-validation/js/localization/messages_zh.min.js")
                ->addJs("js/jquery.form/jquery.form.js");
        }
        
        
    }
    
    public function logoutAction(){
        if($this->isLogin()){
            $model = new \Wpf\App\Admin\Models\AdminMember();
            $model->logout();
            $this->success('退出成功！', $this->url->get('Global/login'));
        } else {
            $this->response->redirect("Global/index")->getHeaders()->send();
            exit;
        }
    }

}