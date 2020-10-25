<?php
class UsersController extends AdminController {
    public function indexAction($page = 1){
        $parameters["order"] = "id desc";
        // $parameters['conditions'] = 'username = :username:';
        // $parameters['bind'] = array('username'=>'seiven');
        $page = $this->request->getParam('page', 1);
        $parameters['offset'] = intval($page - 1) * self::page_size;
        $parameters['conditions'] = array();
        $data = SystemUser::find('all', $parameters);
        $this->view->assign('data', $data);
        // 翻页
        $rowCount = SystemUser::count($parameters['conditions']);
        $paginator = new Paginator($rowCount, self::page_size);
        $this->view->assign('page', $paginator->show());
        // 加载参数
        $this->view->assign('parameters', $parameters);
    }
    /**
	 * set password
	 */
    public function setpwdAction(){}
    /**
	 * 保存管理员用户
	 */
    public function saveAction(){
        $id = $this->request->getParam('id');
        if($id) $this->view->assign('data', SystemUser::first($id));
        if($this->request->isPost()){
            $postData = array(
                'username'=> $this->request->getPost('username'),
                'password'=> $this->request->getPost('password'),
                'email'=> $this->request->getPost('email'),
                'phone'=> $this->request->getPost('phone'),
                'createtime'=> time(),
                'status'=> $this->request->getPost('status', 'int'),
                'groupid'=> $this->request->getPost('groupid', 'int'),
                'truename'=> $this->request->getPost('truename') 
            );
            
            if(empty($postData['groupid'])){
                $this->displayAjax(false, '请选择用户所属用户角色分组');
            }elseif(is_null($postData['password']) && !$id){
                // 新增无密码
                $this->displayAjax(false, '新增用户必须填入密码');
            }
            if(!empty($postData['password'])){
                $postData['salt'] = rand(100000, 999999);
                $postData['password'] = md5(md5($postData['password']) . $postData['salt']);
            }
            if($id){
                // 更新
                $user = SystemUser::first($id);
                unset($postData['username']);
                if(empty($postData['password'])){
                    // 新增无密码
                    unset($postData['password']);
                }
                if(!$user) $this->displayAjax(false, '您要更新的账户不存在!');
                $status = $user->update_attributes($postData);
            }else{
                // 新增
                // 判断账户是否存在
                $hasUser = SystemUser::count(array(
                    'username'=> $username 
                ));
                if($hasUser) $this->displayAjax(false, '用户已存在无法新增!');
                $user = new SystemUser($postData);
                $status = $user->save();
            }
            if($status == false) $this->displayAjax(false, join($user->getMessages(), '<br>'));
            $this->displayAjax(true, '', array(
                'redirect_url'=> '/Admin/Users/index' 
            ));
        }
        $this->view->assign('groups', SystemGroups::all());
    }
    public function delAction(){
        $id = $this->request->getParam('id');
        if($id){
            $roles = SystemUser::first($id);
            $roles->delete();
        }
        $this->displayAjax(true, '删除成功');
    }
}
