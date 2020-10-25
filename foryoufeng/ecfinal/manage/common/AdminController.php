<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/3/16
 * Time: 6:24 PM
 */
defined('UTILS')   or define('UTILS',ROOT_PATH.'includes/common/utils/');
include_once UTILS.'FileUpload.php';//文件上传
include_once UTILS.'Page.php';//分页类
include_once UTILS.'Constant.php';//所有常量的定义
include_once UTILS.'Functions.php';//所有常用函数的工具类
include_once UTILS.'Db.php';//数据库连接器
include_once dirname(UTILS).'/Model.php';//模型层
class Base{
    protected $model;
    protected $smarty;
    protected $user_id;
    protected $config;
    protected $type;
    public function __construct($smarty)
    {
        $this->init();
        $this->config=load_config();//加载配置文件
        $this->smarty=$smarty;
        $this->smarty->template_dir   = ROOT_PATH . 'manage/views/html';
    }
    // 回调方法 初始化模型
    protected function init() {}

    /**
     *
     * @param $data
     */
    protected function ajax($data){
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
    }

    /**
     *
     * @param $file
     */
    public function display($file){
        $this->smarty->display($file,$this->type.$file);
    }

    /**
     * @param $name
     * @param $value
     */
    public final function assign($name,$value){
        $this->smarty->assign($name,$value);
    }
    /**
     * ajax返回成功的消息
     * @param $data
     */
    protected final function success($data,$msg='success'){
        $res = array('code' => 1, 'msg' => $msg, 'content' => $data);
        $this->ajax($res);
    }

    /**
     * ajax返回错误的消息
     * @param string $msg
     */
    protected final function error($msg='error'){
        $res = array('code' => 0, 'msg' => $msg, 'content' => null);
        $this->ajax($res);
    }
    protected function check_auth($authz){
        if(!$this->check_action($authz)){
            $this->message('权限不足');
        }
    }
    /**
     * 检查管理员权限
     *
     * @access  public
     * @param   string  $authz
     * @return  boolean
     */
    private function check_action($authz)
    {
        return (preg_match('/,*'.$authz.',*/', session('action_code')) || session('action_code') == 'all');
    }
}
abstract class AdminsController extends Base{
    use FileUpload;

    //覆盖父类方法
    public function __construct($smarty)
    {
        parent::__construct($smarty);
        $controller=I('c','home');
        $action=I('a','index');
        $auth=$controller.'_'.$action;
        if(!session('admin_id')){
            $redirect='?c='.$controller.'&a='.$action;
            redirect('?c=login&redirect='.urlencode($redirect));
        }else{
            //以特定的类型开头的方法不进行权限检查
            if(strpos($action,'noauth_')===false){
                $this->check_auth($auth);
            }
        }
        $this->assign('c',$controller);
        $this->assign('a',$action);
        $this->assign('auth_menu',$auth);
        //授权用户进行访问
        $this->assign('config',$this->config);
        $this->assign('admin_name',session('admin_name'));
        $this->assign('version','1.0.0');
        $this->assign('nav_lists',$this->model->common_nav_list());/*分类显示*/
    }


    /**
     * 跳转
     * @param $url
     */
    protected final function redirect($url){
        header("Location:".$url);
        exit(0);
    }


    /**
     * 提示消息
     * @param $message
     */
    protected function message($message){
        $links[0]['text'] = '返回';
        $links[0]['href'] = 'javascript:history.go(-1)';
        $this->assign('msg_detail',  $message);
        $this->assign('msg_type',    0);
        $this->assign('links',       $links);
        $this->assign('default_url', $links[0]['href']);
        $this->assign('auto_redirect', true);
        $this->display('sys_msg.html');
        exit(0);
    }
    public function ajax_view($tpl){
        $lists=$this->get_list();
        $this->assign('lists',$lists['lists']);
        $this->assign('filter',$lists['filter']);
        $this->success($this->smarty->fetch($tpl));
    }
    public function noauth_export(){
        $filename = date('YmdHis').".csv";
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        $data=$this->model->export();
        $result=utf_to_gbk(implode(',',$data['title'])."\n");
        $count=count($data['data']);
        for($i=0;$i<$count;$i++){
            $str='';
            foreach($data['title'] as $k=>$v){
                $str.=utf_to_gbk($data['data'][$i][$k]).',';
            }
            $result.=trim($str,',')."\n";
        }
        echo $result;
    }

}
abstract  class Admin extends AdminsController{
    //模型对应的名称并显示相应的视图
    protected $modelName;
    public function __construct($smarty)
    {
        //获取对象名称来实例化对应的模型
        if(!$this->modelName){
            $this->modelName=strtolower(get_class($this));
        }
        $model=M($this->modelName);
        $this->model=$model;

        parent::__construct($smarty);
    }

    /**
     * 首页展示数据
     */
    public function index()
    {
        $data=$this->get_list();
        $this->assign('lists',    $data['lists']);
        $this->assign('filter',       $data['filter']);
        $this->assign('full_page',    1);
        $this->display($this->modelName.'/'.$this->modelName.'.html');
    }
    /**
     * ajax查询
     */
    public function query()
    {
        $this->ajax_view($this->model->getModelName().'/'.$this->model->getModelName().'.html');
    }

    /**
     * 获取数据结果集展示
     * @return mixed
     */
    abstract function get_list();

    /**
     * 删除数据
     */
    public function destroy()
    {
        if($_POST){
            $id=intval(I('id',0));
            if($this->model->destroy($id)){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
    }

    /**
     * 显示数据
     */
    public function show()
    {
        $id=I('id',0);
        $data=$this->model->show($id);
        if(IS_AJAX){
            if($data){
                $this->success($data);
            }else{
                $this->error('没有数据');
            }
        }else{
            if(!$data){
                $this->message('没有数据');
            }else{
                $this->assign('data',    $data);
                $this->display($this->model->getModelName().'/'.$this->model->getModelName().'_show.html');
            }
        }

    }

}