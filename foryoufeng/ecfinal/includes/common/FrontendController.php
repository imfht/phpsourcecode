<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/3/16
 * Time: 6:24 PM
 */
defined('IN_ECS')   or define('IN_ECS',true);
include_once dirname(dirname(LIB)).'/includes/init.php';
defined('UTILS')   or define('UTILS',ROOT_PATH.'includes/common/utils/');
include_once UTILS.'FileUpload.php';//文件上传
include_once UTILS.'Page.php';//分页类
include_once UTILS.'Constant.php';//所有常量的定义
include_once UTILS.'Functions.php';//所有常用函数的工具类
include_once UTILS.'Restful.php';//方法规范
include_once UTILS.'Db.php';//数据库连接器
include_once LIB.'Model.php';//模型层
class Base{
    //模型对象
    protected $model;
    //模型对应的名称并显示相应的视图
    protected $modelName;
    //smarty
    protected $smarty;
    //网站配置文件
    protected $config;
    //
    protected $nologion=array('code' => 403, 'msg' => '请登陆', 'content' => null);
    //用户信息
    protected $user_info;
    //用户id
    protected $user_id;
    public function __construct($smarty)
    {
        $this->smarty=$smarty;
        $this->config=load_config();//加载配置文件
        $this->smarty->template_dir   = ROOT_PATH . '/themes/new/html';
        //前台页面加入缓存
        $this->smarty->caching = true;
        $this->smarty->direct_output = false;
        $this->smarty->cache_lifetime = 3600*24;  //缓存时间

        //网站配置信息
        $this->smarty->assign('config',$this->config);
        $this->smarty->assign('WWW',WWW);
        //获取对象名称来实例化对应的模型
        if($this->modelName){
            $model=M($this->modelName);
        }else{
            $model=M(strtolower(get_class($this)));
        }
        $this->model=$model;

        //获取当前时间
        $this->nowtime();

        //用户数据
        $this->user_id=session('user_id');
        if($this->user_id){
            $this->user_info=M('user')->userinfo($this->user_id);
            //显示用户信息
            $this->assign('user_info',$this->user_info);
        }
        //需要进行初始化的操作可以执行
        $this->init();
    }
    // 回调方法 初始化模型
    protected function init() {}
    public final function assign($name,$value){
        $this->smarty->assign($name,$value);
    }

    protected final function ajax($data){
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
    }
    /**
     * 判断文件是否被缓存
     * @param string $tpl
     * @return mixed true |false
     */
    protected function is_cached($tpl = ''){
        if(!$tpl){
            exit('程序错误');
        }
        return $this->smarty->is_cached($tpl,substr($tpl,stripos($tpl,'/')+1));
    }

    /**
     * 显示模板
     * @param $file
     */
    public function display($file){
        $this->smarty->display($file,substr($file,stripos($file,'/')+1));
    }
    /**
     * 获取时间
     */
    protected function nowtime(){
        $h=date('G');
        $time='';
        if ($h<11) $time='早上好';
        else if ($h<13) $time= '中午好';
        else if ($h<17) $time= '下午好';
        else $time= '晚上好';
        $this->assign('nowtime',$time);
    }

    /**
     * 提示消息
     * @param $message 消息
     * @param int $type 类型
     * @param null $link 跳转地址
     * @param bool $auto_redirect 是否自动返回
     */
    protected function message($message,$type=0,$link=null,$auto_redirect=true){
        $links[0]['text'] = '返回';
        $links[0]['href'] = 'javascript:history.go(-1)';
        if($link){
            $links=array_merge($links,$link);
        }
        $this->assign('msg_detail',  $message);
        $this->assign('msg_type',    $type);
        $this->assign('links',       $links);
        $this->assign('default_url', $links[0]['href']);
        $this->assign('auto_redirect', $auto_redirect);
        $this->assign('page_title','系统消息');
        $this->assign('keywords','系统消息');
        $this->assign('description','系统消息');
        $this->smarty->caching = false;
        $this->display('sys_message.html');
        exit(0);
    }
    /**
     * ajax返回视图文件
     * @param string $tpl
     */
    protected final function ajax_view($tpl){
        $this->ajax_page();
        $this->smarty->caching = false;
        $this->success($this->smarty->fetch($tpl));
    }

    /**
     * 返回ajax分页数据
     * @param $datas
     * @param $count
     * @param null $url
     * @param int $limit
     * @param null $tpl
     */
    protected function ajax_page()
    {
        $datas=$this->get_list();
        $page=new Page($datas['record_count'],$datas['page_size']);
        $show=$page->ajax_show();
        $this->assign('page', $show);
        $this->assign('lists', $datas['data']);
    }
}
class Frontend extends Base{

    public function index(){
        $this->ajax_page();
        $this->assign('full_page',    1);
    }
    /**
     * ajax返回成功的消息
     * @param $data
     */
    protected final function success($data){
        $res = array('code' => 1, 'msg' => 'success', 'content' => $data);
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

    /**
     * 跳转
     * @param $url
     */
    protected final function redirect($url){
        header("Location:".$url);
        exit(0);
    }
    /**
     * 魔术方法 有不存在的操作的时候执行
     * @access public
     * @param string $method 方法名
     * @param array $args 参数
     * @return mixed
     */
    public function __call($method,$args) {
        if(method_exists($this,'_empty')) {
            // 如果定义了_empty操作 则调用404
            $this->_empty($method, $args);
        }else{
            $this->redirect('/404.html');
        }
    }

    /**
     * 获取单个tdk的信息
     * @param $id
     */
    protected function getTdk($id){
        $tdk=M('tdk')->show($id);
        $this->assign('page_title',$tdk['title']);
        $this->assign('keywords',$tdk['keywords']);
        $this->assign('description',$tdk['description']);
    }

    /**
     * 根据信息的id获取对应的信息
     * @param $id
     * @return mixed
     */
    protected final function findOrFail($id){
        $data=$this->model->show($id);
        if(!$data){
            $this->message('没有找到信息');
        }
        return $data;
    }
    protected function get_list(){
        return null;
    }
    public function query(){
        $this->ajax_view(strtolower(get_class($this)).'/'.strtolower(get_class($this)).'.html');
    }

    public function price_format($price){
        if($price==='')
        {
            $price=0;
        }
        $price = number_format($price, 2, '.', '');
        return sprintf($this->config['currency_format'], $price);
    }

}

/**
 * 用户基础类
 * Class User
 */
class UserBase extends Frontend{

    //用户登录初始化
    public function init()
    {
        parent::init();
        if(!session('user_id')){
            $this->redirect("/user.html?redirect=".$_SERVER['REQUEST_URI']);
        }
        $this->assign('page_title', '用户中心_'.$this->config['shop_name']);
        //分配状态
        $action=$_SERVER['REDIRECT_URL'];
        $this->assign('action',$action);
        $this->assign('current',1);
        //获取购物车数据
        $cart=M('cart')->info($this->user_id);
        $this->assign('cart',$cart);

    }

    public function index()
    {
        parent::index();
        $this->display(strtolower(get_class($this)).'/'.strtolower(get_class($this)).'.html');
    }

    /**
     * 用户中心的数据不进行缓存
     * @param $file
     */
    public function display($file)
    {
        $this->smarty->caching = false;
        parent::display($file);
    }
}