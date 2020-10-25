<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 后台基类控制器
 * 
 * @author 牧羊人
 * @date 2018-12-08
 */
namespace app\admin\controller;
use app\common\controller\BaseController;
use app\admin\model\AdminModel;
class AdminBaseController extends BaseController
{
    // 模型、服务
    protected $model,$service;
    // 登录信息
    protected $adminId,$adminInfo;
    // 权限
    protected $system_auth;
    // 请求参数
    protected $param;
    
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-12-10
     */
    public function __construct()
    {
        parent::__construct();
        
        //初始化配置
        $this->initConfig();
        
        //检查登录
        $this->checkLogin();
        
        //权限验证
        $this->checkAuth();
        
    }
    
    /**
     * 初始化操作
     * 
     * @author 牧羊人
     * @date 2018-12-08
     * (non-PHPdoc)
     * @see \think\Controller::_initialize()
     */
    public function _initialize()
    {
        parent::_initialize();

        //初始化
        $this->assign('adminInfo', $this->adminInfo);
        
        // 请求参数
        $this->param = $this->request->param();
        
        // 分页基础默认值
        define('PERPAGE', isset($this->param['limit']) ? $this->param['limit'] : 20);
        define('PAGE', isset($this->param['page']) ? $this->param['page'] : 1);
    }
    
    /**
     * 空操作捕捉
     * 
     * @author 牧羊人
     * @date 2018-12-08
     */
    public function _empty()
    {
        return $this->render("public/403");
    }
    
    /**
     * 初始化配置
     * 
     * @author 牧羊人
     * @date 2018-12-08
     */
    public function initConfig()
    {
        //TODO...
    }
    
    /**
     * 检查登录
     * 
     * @author 牧羊人
     * @date 2018-12-08
     */
    public function checkLogin()
    {
        $noLoginActs = ['Login'];
        if(session('adminId')==null && !in_array($this->request->controller(), $noLoginActs)) {
            $this->redirect('/Login/index');
            exit;
        }

        //获取用户ID
        $this->adminId = session('adminId');
        $this->assign("adminId" , $this->adminId);
        
        //查询基本信息
        $adminMod = new AdminModel();
        $this->adminInfo = $adminMod->getInfo($this->adminId);
        $this->assign('adminInfo', $this->adminInfo);
        $this->system_auth = $this->adminInfo['adminAuth'];
    }
    
    /**
     * 权限认证
     * 
     * @author 牧羊人
     * @date 2018-12-08
     */
    public function checkAuth()
    {
        //访问权限判断
        if(!in_array($this->request->controller(),['Login','Index'])) {
        
            $reqestUrl = "/".$this->request->controller()."/" . $this->request->action();
            $funcInfo = db('menu')->where([
                'type'      =>4,
                'url'       =>$reqestUrl,
                'mark'      =>1
            ])->find();
            if(!$funcInfo) {
                if(IS_POST || IS_GET) {
                    return message('暂无操作权限',false);
                }
                $this->render('Public/404');
                exit;
            }
            $funcArr = $this->system_auth[$funcInfo['parent_id']];
            $funcList = [];
            if(is_array($funcArr)) {
                $keys = array_values($funcArr);
                $map['id'] = array('in',$keys);
                $funcList = db('menu')->where($map)->column('auth');
            }
            if(!in_array($funcInfo['auth'], $funcList)) {
                if(IS_POST) {
                    return message('暂无操作权限',false);
                }
                $this->render('Public/404');
                exit;
            }
            $this->assign('funcList',$funcList);
        
        }
    }
    
    /**
     * 获取导航栏菜单
     * 
     * @author 牧羊人
     * @date 2018-12-08
     */
    public function getNavbar()
    {
        
    }
    
    /**
     * 获取面包屑
     * 
     * @author 牧羊人
     * @date 2018-12-08
     */
    public function getCrumb() 
    {
        
    }
    
    /**
     * 控制器基类默认入口
     * 
     * @author 牧羊人
     * @date 2018-12-08
     */
    public function index($data=[])
    {
        if($this->request->isPost()) {
            $message = $this->service->getList();
            return $message;
        }
        foreach ($data as $key=>$val) {
            $this->assign($key,$val);
        }
        return $this->render();
    }
    
    /**
     * 添加或编辑基类默认入口
     * 
     * @author 牧羊人
     * @date 2018-12-08
     */
    public function edit($data=[])
    {
        if(IS_POST) {
            $message = $this->service->edit();
            return $message;
        }
        $id = input("get.id",0);
        if($id) {
            $info = $this->model->getInfo($id);
        }else{
            foreach ($data as $key=>$val) {
                $info[$key] = $val;
            }
        }
        $this->assign('info',$info);
        return $this->render();
    }
    
    /**
     * 详情基类默认入口
     * 
     * @author 牧羊人
     * @date 2018-12-08
     */
    public function detail()
    {
        if(IS_POST) {
            $message = $this->service->edit();
            return $message;
        }
        $id = input("get.id",0);
        if($id) {
            $info = $this->model->getInfo($id);
            $this->assign('info',$info);
        }
        return $this->render();
    }
    
    /**
     * 删除单条数据
     * 
     * @author 牧羊人
     * @date 2018-12-12
     */
    function drop()
    {
        if(IS_POST) {
            return message("演示版不提供删除功能",false);
            $id = input('post.id');
            $info = $this->model->getInfo($id);
            if($info) {
                $result = $this->model->drop($id);
                if($result!==false) {
                    return message();
                }
            }
            return message($this->model->getError(),false);
        }
    }
    
    /**
     * 批量删除
     * 
     * @author 牧羊人
     * @date 2018-12-12
     */
    function batchDrop()
    {
        if(IS_POST) {
            return message("演示版不提供删除功能",false);
            $ids = explode(',', $_POST['id']);
            $changeAct = $_POST['changeAct'];
            if($changeAct==0) {
                //删除
                $num = 0;
                foreach ($ids as $key => $val) {
                    $res = $this->model->drop($val);
                    if($res!==false) $num++;
                }
                return message('本次共选择' . count($ids) . "个条数据,删除" . $num . "个");
            }else if($changeAct==1){
                //重置缓存
                foreach ($ids as $key => $val){
                    $this->model->_cacheReset($val);
                }
                return message('重置缓存成功！');
            }
            return message($this->model->getError(),false);
        }
    }
    
    /**
     * 模板渲染
     * 
     * @author 牧羊人
     * @date 2018-12-08
     */
    public function render($tpl="", $data=[])
    {
        return $this->fetch($tpl, $data);
    }
    
}