<?php
namespace app\admin\controller;
use think\Controller;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/21 0021
 * Time: 下午 4:37
 */
class Admin extends Controller{
    public function __construct(\think\Request $request){
        parent::__construct($request);
        /* 设置路由参数 */
    }
    //当任何函数加载时候  会调用此函数
    public function _initialize(){//默认的方法  会自动执行 特征有点像构造方法
        // 检测用户登录
        define('ADMIN_ID',$this->isLogin());
        if( !ADMIN_ID && ( request()->module() <> 'admin' || request()->controller() <> 'Login' )){
            $this->redirect(url('admin/Login/index'));
        }
        if(!(request()->module() == 'admin' && request()->controller() == 'Login')){
            //设置登录用户信息
            $this->loginUserInfo = model('admin/AdminUser')->getInfo(ADMIN_ID);
            //检测权限
            $this->checkPurview();
            //赋值当前菜单
            if(method_exists($this,'_infoModule')){
                $this->assign('infoModule',$this->_infoModule());
            }
        }
    }
    /**
     * 用户权限检测
     */
    protected function checkPurview(){
        header("Content-type:text/html;charset=utf-8");
        if ($this->loginUserInfo['user_id'] == 1 || $this->loginUserInfo['group_id'] == 1) {
            return true;
        }
        $basePurview = unserialize($this->loginUserInfo['base_purview'])?unserialize($this->loginUserInfo['base_purview']):array();
        $purviewInfo = service(request()->module(),'Purview','getAdminPurview');

        if (empty($purviewInfo)) {
            return true;
        }
        //var_dump($purviewInfo);
        $controller = @$purviewInfo[request()->controller()];
        if (empty($controller['auth'])) {
            return true;
        }
        $action = @$controller['auth'][request()->action()];
        if (empty($action)) {
            return true;
        }
        $current = request()->module() . '_' . request()->controller();
        //var_dump($basePurview);
        //var_dump($current);exit;
        if (!in_array($current, (array) $basePurview)) {
            $this->error('您没有权限访问此功能！');
        }
        $current = request()->module() . '_' . request()->controller() . '_' . request()->action();
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
    /*
     * 一键清空缓存
     */
    public function delcache() {
        $path=ROOT_PATH.'/runtime';
        delDirAndFile($path);
        $this->success('缓存清除成功');
    }
}