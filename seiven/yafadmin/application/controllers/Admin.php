<?php
/**
 * 基础Controller
 * @author user
 *
 */
class AdminController extends BaseController {
    const admin_auth_session_key = 'auth_manager_user';
    const page_size = 20;
    public $user;
    private $allow_controllers = array(
        'Login',
        //'Ajax' 
    );
    /**
     * 检测是否登录
     */
    public function isLogin(){
        $user = $this->session->get(self::admin_auth_session_key);
        if($user){
            // 已登录
            return unserialize($user);
        }else{
            // 未登录
            if(!in_array($this->request->getControllerName(), $this->allow_controllers)){
                $this->redirect('/admin/login/index');
            }
        }
    }
    // 自动执行
    public function init(){
        parent::init();
        $this->user = $this->isLogin();
        // 检测权限
        if(!in_array($this->request->getControllerName(), $this->allow_controllers)){
            if(!SystemUser::checkRight('admin/' . $this->request->getControllerName() . '/' . $this->request->getActionName())){
                // 无权限
                $this->displayAjax(false, 'access denied');
            }
            // 加载菜单
            $allMenus = SystemMenus::getMenus();
            foreach($allMenus as $k => $v){
                if(isset($v['list']) && $v['list']){
                    foreach($v['list'] as $k2 => $v2){
                        if(!SystemUser::checkRight($v2['url'])) unset($allMenus[$k]['list'][$k2]);
                    }
                    if(isset($allMenus[$k]['list']) && empty($allMenus[$k]['list'])) unset($allMenus[$k]);
                }else{
                    // 无下级检测本身
                    if(!SystemUser::checkRight($v['url'])) unset($allMenus[$k]);
                }
            }
            // var_dump($allMenus);
            $this->view->assign('admin_menus', $allMenus);
        }
    }
    // ajax return
    protected function displayAjax($status = false, $message = '未知错误', $assignData = array()){
        $ajaxReturn = array(
            'status'=> $status,
            'message'=> $message,
            'data'=> $assignData 
        );
        die(json_encode($ajaxReturn));
    }
}