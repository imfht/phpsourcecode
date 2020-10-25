<?php
namespace app\admin\controller;
use app\base\controller\BaseController;
/**
 * 后台公共类
 */
class AdminController extends BaseController {

    public function __construct()
    {
        parent::__construct();
        $this->init();
    }

    /**
     * 后台控制器初始化
     */
    protected function init(){
        //强制后台入口登录
        if (!defined('ADMIN_STATUS')) { 
            $this->error('请从后台入口重新登录！', false);
        }
        // 检测用户登录
        define('ADMIN_ID',$this->isLogin());
        if( !ADMIN_ID && ( APP_NAME <> 'admin' || CONTROLLER_NAME <> 'Login' )){
            $this->redirect(url('admin/Login/index'));
        }
        if(!(APP_NAME == 'admin' && CONTROLLER_NAME == 'Login')){
            //设置登录用户信息
            $this->loginUserInfo = target('admin/AdminUser')->getInfo(ADMIN_ID);
            //检测权限
            $this->checkPurview();
            //赋值当前菜单
            if(method_exists($this,'_infoModule')){
                $this->infoModule = $this->_infoModule();
            }
        }
    }

    /**
     * 用户权限检测
     */
    protected function checkPurview()
    {
        if ($this->loginUserInfo['user_id'] == 1 || $this->loginUserInfo['group_id'] == 1) {
            return true;
        }
        $basePurview = unserialize($this->loginUserInfo['base_purview']);
        $purviewInfo = service(APP_NAME,'Purview','getAdminPurview');
        if (empty($purviewInfo)) {
            return true;
        }
        $controller = $purviewInfo[CONTROLLER_NAME];
        if (empty($controller['auth'])) {
            return true;
        }
        $action = $controller['auth'][ACTION_NAME];
        if (empty($action)) {
            return true;
        }
        $current = APP_NAME . '_' . CONTROLLER_NAME;
        if (!in_array($current, (array) $basePurview)) {
            $this->error('您没有权限访问此功能！');
        }
        $current = APP_NAME . '_' . CONTROLLER_NAME . '_' . ACTION_NAME;
        if (!in_array($current, (array) $basePurview)) {
            $this->error('您没有权限访问此功能！');
        }
        return true;
    }

    /**
     * 检测用户是否登录
     * @return int 用户IP
     */
    protected function isLogin(){
        $user = session('admin_user');
        if (empty($user)) {
            return 0;
        } else {
            return session('admin_user_sign') == data_auth_sign($user) ? $user['user_id'] : 0;
        }
    }

    /**
     * 后台模板显示 调用内置的模板引擎显示方法，
     * @access protected
     * @param string $tpl 指定要调用的模板文件
     * @return void
     */
    protected function adminDisplay($tpl = '') {
        //复制当前URL
        $this->assign('self',__SELF__);
        $common = $this->display('app/admin/view/common',true);
        $tplArray = get_method_array($tpl);
        $tpl = 'app/'. strtolower($tplArray['app']) . '/view/' . strtolower($tplArray['controller']) . '/' . strtolower($tplArray['action']);
        $html = $this->display($tpl,true);
        echo str_replace('<!--common-->', $html, $common);
    }

    //分页结果显示
    protected function getPageShow($map = array(), $mustParams = array())
    {
        $pageArray = $this->pager;
        $html = '
        <ul class="pagination pagination-small">
          <li><a href="'.$this->createPageUrl($map,$mustParams,$pageArray['firstPage']).'">首页</a></li>
          <li><a href="'.$this->createPageUrl($map,$mustParams,$pageArray['prevPage']).'">上一页</a></li> ';
            foreach ($pageArray['allPages'] as $value) {
                if($value == 0){
                    continue;
                }
                if($value == $pageArray['page']){
                    $html .= '<li class="active">';
                }else{
                    $html .= '<li>';
                }
                $html .= '<a href="'.$this->createPageUrl($map,$mustParams,$value).'">'.$value.'</a></li> ';
           }
         $html .= '<li><a href="'.$this->createPageUrl($map,$mustParams,$pageArray['nextPage']).'">下一页</a></li>
          <li><a href="'.$this->createPageUrl($map,$mustParams,$pageArray['lastPage']).'">末页</a></li>
        </ul>';
        return $html;

    }
}