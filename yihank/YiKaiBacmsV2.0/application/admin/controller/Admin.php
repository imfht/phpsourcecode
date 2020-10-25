<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

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
        if (empty(get_lang_id())){
            cookie('think_var', 'zh-cn');
        }
        // 检测用户登录
        define('ADMIN_ID',$this->isLogin());
        if( !ADMIN_ID && ( request()->module() <> 'admin' || request()->controller() <> 'Login' )){
            $this->redirect('admin/Login/index');
        }
        if(!(request()->module() == 'admin' && request()->controller() == 'Login')){
            //设置登录用户信息
            $this->loginUserInfo = model('admin/AdminUser')->getInfo(ADMIN_ID);
            //检测权限
            if (!input('post.')){
                $rs_pur=$this->checkPurview();//权限返回结果
                if ($rs_pur){
                    if (!request()->isAjax()){//如果不是ajax
                        echo "<script>alert('您没有权限访问此功能！');</script>";
                        if ($rs_pur['if_menu']=='2'){//是菜单
                            echo "<script>window.history.go(-1);</script>";
                        }
                    }else{
                        $tmp['status']=0;
                        $tmp['msg']='您没有权限访问此功能';
                        $tmp['url']='';
                        $tmp['data']='';
                        $tmp['render']='';
                        echo json_encode($tmp);
                    }
                    exit;
                }
            }
            //赋值当前菜单
            if(method_exists($this,'_infoModule')){
                $this->assign('infoModule',$this->_infoModule());
            }
        }
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
     * 用户权限检测
     */
    protected function checkPurview(){
        $check_status=array();
        if ($this->loginUserInfo['user_id'] == 1 || $this->loginUserInfo['group_id'] == 1) {
            return $check_status;
        }
        $basePurview = explode(',',$this->loginUserInfo['base_purview']);
        if (empty($basePurview)) {
            return $check_status;
        }
        $module=request()->module();
        $path_arr=explode('/',request()->path());
        if (count($path_arr)>2){
            $path=$path_arr[0].'_'.$path_arr[1].'_'.$path_arr[2];
            $param=implode('_',array_keys(request()->param()));//测试
            if (count($path_arr)>3){
                $path.='_'.$path_arr[3];
            }
            $excPurview=array(
                'admin_index_index',
                'admin_index_home',
                'admin_admin_api_index',
                'admin_admin_delcache'
            );
            if ($module!='api'){
                //排除的url
                $basePurview=array_merge($basePurview,$excPurview);
                //var_dump($menu_list);exit;
                if (!in_array($path,$basePurview)){

                    $where['url']=['neq',''];
                    $menu_list=Db::name('admin_menu')->where($where)->select();
                    if ($menu_list){
                        $menu_arr=array();
                        foreach ($menu_list as $key => $val) {
                            $url=explode('/',explode('.',$val['url'])[0]);
                            $menu_arr[]=$url[1].'_'.$url[2].'_'.$url[3];
                        }
                    }
                    $check_status['code']='0';//状态
                    $check_status['if_menu']='1';//是否菜单1是2不是
                    if (!in_array($path,$menu_arr)){
                        $check_status['if_menu']='2';//是否菜单1是2不是
                    }
                }
            }

        }
        return $check_status;
    }
    /*
     * 一键清空缓存
     */
    public function delcache() {
        $path=ROOT_PATH.'/runtime';
        delDirAndFile($path);
        return ajaxReturn(200,'缓存清除成功');
    }

    /**
     * 检查分类修改信息
     */
    public function parentCheck($classId='',$parentId='',$model=''){
        //获取变量
        if (empty($classId)){
            $classId = input('post.class_id');
        }
        if (empty($parentId)){
            $parentId = input('post.parent_id');
        }
        if (empty($model)){
            $model='kbcms/Category';
        }
        //判断空上级
        if(!$parentId){
            return true;
        }
        // 分类检测
        if ($classId == $parentId){
            return '不可以将当前栏目设置为上一级栏目';
        }
        $cat = model($model)->loadList(array(),$classId);
        if(empty($cat)){
            return true;
        }
        foreach ($cat as $vo) {
            if ($parentId == $vo['class_id']) {
                return '不可以将上一级栏目移动到子栏目';
            }
        }
        return true;
    }
}