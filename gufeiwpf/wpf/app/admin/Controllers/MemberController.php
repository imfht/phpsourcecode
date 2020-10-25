<?php
namespace Wpf\App\Admin\Controllers;

class MemberController extends \Wpf\App\Admin\Common\Controllers\CommonController{
    public $_model;
    
    public function initialize(){
        parent::initialize();
        
    }
    
    public function onConstruct(){
        parent::onConstruct();
        
        $this->_model = new \Wpf\App\Admin\Models\AdminMember();
    }
    
    public function indexAction(){

        $this->headercss
            ->addCss("theme/assets/global/plugins/select2/select2.css")
            ->addCss("theme/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css");
        
        $this->footerjs
            ->addJs("theme/assets/global/plugins/select2/select2.min.js")
            ->addJs("theme/assets/global/plugins/datatables/media/js/jquery.dataTables.min.js")
            ->addJs("theme/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js");
        
        $list = $this->_model->find()->toArray();

        
        int_to_string($list);
        $this->view->setVar('list', $list);
        $this->view->setVar('meta_title','后台用户信息');
    }
    
    public function addAction(){
        if($this->request->isPost()){
            $username = $this->request->getPost("username","string","");
            $password = $this->request->getPost("password","string","");
            $repassword = $this->request->getPost("repassword","string","");
            $email = $this->request->getPost("email","string","");
            
            
            /* 检测密码 */
            if($password != $repassword){
                $this->error('密码和重复密码不一致！');
            }

            /* 调用注册接口注册用户 */
            $User = $this->_model;
            $uid = $User->register($username, $password, $email);
            if(0 < $uid){ //注册成功
                $this->success('用户添加成功！',$this->url->get("Member/index/"));
            } else { //注册失败，显示错误信息
                $this->error("添加用户失败");
            }
        }else{
            $this->view->pick("Member/edit");
        }
    }
    
    public function changeStatusAction($method=null){
        //$id = array_unique((array)I('id',0));
        
        $method = $this->request->getQuery("method","string","");
        $id = $this->request->get("id");
        
        if(is_numeric($id)){
            $id = array($id);
        }

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        
        
        if(array_intersect($this->config->ADMIN_ADMINISTRATOR->toArray(),$id)){
            $this->error("不允许对超级管理员执行该操作!");
        }
        
        $id = implode(",",array_unique($id));
        
        $where = "id in ({$id})";
        $list = $this->_model->find($where);
        
        switch ( strtolower($method) ){
            case 'forbiduser':
                
                $list->update(array(
                    "status" => 0
                ));
                break;
            case 'resumeuser':
                $list->update(array(
                    "status" => 1
                ));
                break;
            case 'deleteuser':
                $list->delete();
                break;
            default:
                $this->error('参数非法');
        }
        $this->success("操作成功");
    }
    
    public function updatePasswordAction(){
        if($this->request->isPost()){
            $uid        =   ADMIN_UID;
            if(! $password   =   $this->request->getPost("old","string","")){
                $this->error('请输入原密码');
            }
            
            if(! $data['password'] = $this->request->getPost("password","string","")){
                $this->error('请输入新密码');
            }
            
            if(! $data['repassword'] = $this->request->getPost("repassword","string","")){
                $this->error('请输入确认密码');
            }
            
            if($data['password'] !== $data['repassword']){
                $this->error('您输入的新密码与确认密码不一致');
            }
            if($this->_model->verifyUser($uid,$password)){
                $user = $this->_model->getInfo($uid);
                $user->password = crypt_md5($data['password']);
                $user->save();
                $this->success("修改密码成功！");
            }else{
                $this->error("验证出错：密码不正确！");
            }
            
            
        }else{
            $this->view->setVar('meta_title','修改密码');
        }
        
    }
    
}
