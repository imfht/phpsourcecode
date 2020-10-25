<?php
defined('IN_ECS')   or define('IN_ECS',true);
require_once(dirname(__FILE__) . '/includes/init.php');
require_once(dirname(__FILE__) . '/includes/common.php');
include_once(ROOT_PATH .'includes/lib_clips.php');
include_once(ROOT_PATH . 'includes/common/utils/Page.php');
include_once(ROOT_PATH .'plugins/sendmsg/MsgUtil.class.php' );
define('WWW',"http://www.toodudu.com");
class AdminBase{
    protected $db;
    protected $smarty;
    protected $success=array('code'=>1,'msg'=>'success');
    protected $fail=array('code'=>0,'msg'=>'fail');
    protected $error=array('code'=>2,'msg'=>'出错啦，请检查信息');//发生错误进行跳装
    protected $user_id;
    public function __construct($db,$smarty)
    {
        $this->db=$db;
        $this->smarty=$smarty;

        $sql="select * from ecs_shop_config WHERE id=118";
        $data=$this->db->getRow($sql);
        $this->smarty->assign('logo',$data['value']);
    }
    public function assign($name,$value){
        $this->smarty->assign($name,$value);
    }
    /**
     *获取轮播图信息
     * @param $cat_id
     * @return  array image图片 url 路径
     */
    protected function getImages($cat_id){
        $sql = "SELECT image,url FROM `ecs_tdd_images` where cateid = ".$cat_id." AND is_show = 1 ORDER BY sort DESC " ;
        $data = $this->db->getAll($sql);
        return $data;
    }
    protected function ajax($data){
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
    }
    public function display($file){
        $this->smarty->display($file);
    }

    /**
     * ajax返回错误信息
     * @param $message
     */
    protected  final  function error($message){
        $res = array('error' => 1, 'message' => $message, 'content' => null);
        $this->ajax($res);
    }

    /**
     * ajax返回成功数据
     * @param $data
     */
    protected  final  function success($data){
        $res = array('error' => 0, 'message' => null, 'content' => $data);
        $this->ajax($res);
    }
    /**
     * 数据不存在
     */
    protected  final  function not_found() {
        header("Location:/404.html");
        exit();
    }
    /**
     * 获取输入参数 支持过滤和默认值
     * 使用方法:
     * <code>
     * I('id',0); 获取id参数 自动判断get或者post
     * I('post.name','','htmlspecialchars'); 获取$_POST['name']
     * I('get.'); 获取$_GET
     * </code>
     * @param string $name 变量的名称 支持指定类型
     * @param mixed $default 不存在的时候默认值
     * @param mixed $filter 参数过滤方法
     * @param mixed $datas 要获取的额外数据源
     * @return mixed
     */
    final  function I($name, $default = '', $filter = null, $datas = null) {
        if (strpos($name, '.')) { // 指定参数来源
            list($method, $name) = explode('.', $name, 2);
        } else { // 默认为自动判断
            $method = 'param';
        }
        switch (strtolower($method)) {
            case 'get':
                $input = & $_GET;
                break;
            case 'post':
                $input = & $_POST;
                break;
            case 'put':
                parse_str(file_get_contents('php://input'), $input);
                break;
            case 'param':
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'POST':
                        $input = $_POST;
                        break;
                    case 'PUT':
                        parse_str(file_get_contents('php://input'), $input);
                        break;
                    default:
                        $input = $_GET;
                }
                break;
            case 'path':
                $input = array();
                if (!empty($_SERVER['PATH_INFO'])) {
                    $depr = '/';
                    $input = explode($depr, trim($_SERVER['PATH_INFO'], $depr));
                }
                break;
            case 'request':
                $input = & $_REQUEST;
                break;
            case 'session':
                $input = & $_SESSION;
                break;
            case 'cookie':
                $input = & $_COOKIE;
                break;
            case 'server':
                $input = & $_SERVER;
                break;
            case 'globals':
                $input = & $GLOBALS;
                break;
            case 'data':
                $input = & $datas;
                break;
            default:
                return NULL;
        }
        if ('' == $name) { // 获取全部变量
            $data = $input;
            array_walk_recursive($data, 'filter_exp');
            $filters = isset($filter) ? $filter : 'htmlspecialchars';
            if ($filters) {
                if (is_string($filters)) {
                    $filters = explode(',', $filters);
                }
                foreach ($filters as $filter) {
                    $data = array_map_recursive($filter, $data); // 参数过滤
                }
            }
        } elseif (isset($input[$name])) { // 取值操作
            $data = $input[$name];
            is_array($data) && array_walk_recursive($data, 'filter_exp');
            $filters = isset($filter) ? $filter : 'htmlspecialchars';
            if ($filters) {
                if (is_string($filters)) {
                    $filters = explode(',', $filters);
                } elseif (is_int($filters)) {
                    $filters = array(
                        $filters
                    );
                }

                foreach ($filters as $filter) {
                    if (function_exists($filter)) {
                        $data = is_array($data) ? array_map_recursive($filter, $data) : $filter($data); // 参数过滤
                    } else {
                        $data = filter_var($data, is_int($filter) ? $filter : filter_id($filter));
                        if (false === $data) {
                            return isset($default) ? $default : NULL;
                        }
                    }
                }
            }
        } else { // 变量默认值
            $data = isset($default) ? $default : NULL;
        }
        return $data;
    }
    /**
     * 获取用户输入的数据
     * @param $input
     * @return mixed|string
     */
    protected  final  function input($input){
        $input=$this->I($input,0);
        if(!$input){
            $this->not_found();//没有数据返回４０４
        }
        return $input;
    }
}
abstract class Admin extends AdminBase{
    const LIMIT=30;
    public function __construct($db, $smarty)
    {
        parent::__construct($db, $smarty);
        $this->assign_query_info();
    }
    /**
     * 查询某个数据
     * @param $sql
     * @return mixed
     */
    protected  final  function getOne($sql){
        return $this->db->getOne($sql);
    }
    /**
     * 查询一条数据
     * @param $sql
     * @return mixed
     */
    protected function find($sql){
        return $this->db->getRow($sql);
    }
    /**
     * 查询多条数据
     * @param $sql　sql语句
     * @return mixed　数据结果集
     */
    protected  final  function select($sql){
        return $this->db->getAll($sql);
    }

    /**
     * 对数据进行删除
     * @param $sql　
     * @return mixed
     */
    protected  final  function delete($sql){
        return $this->db->query($sql);
    }

    /**
     * 对数据表进行添加
     * @param $table　数据表 如　ecs_users
     * @param $data　需要更新的数据字段 如 array('id'=>$id)
     * @return mixed　添加的id　失败返回false
     */
    protected  final  function add($table,$data){
        return $this->db->autoExecute($table, $data, 'INSERT');
    }

    /**
     * 对数据表进行更新
     * @param $table 数据表 如　ecs_users
     * @param $data 需要更新的数据字段 如 array('id'=>$id)
     * @param $where 执行条件　如　$where='user_id=1'
     * @return mixed 更新的id　失败返回false
     */
    protected  final  function update($table,$data,$where){
        return $this->db->autoExecute($table, $data, 'UPDATE',$where);
    }
    /**
     * 发送短信
     * @param $phone　手机号
     * @param $type　　类型
     * @return bool　　true表示发送成功,false表示发送失败
     */
    protected  final  function send($phone,$code,$type){
        $start_time=time();
        $end_time=$start_time+60;
        $ip=real_ip();
        $data['phone']=$phone;
        $data['code']=$code;
        $data['start_time']=$start_time;
        $data['end_time']=$end_time;
        $data['type']=$type;
        $data['ip']=$ip;
        $flag=MsgUtil::send($phone,array($code,1));
        if($flag){
            $this->add('ecs_phone_msg',$data);
        }
        return $flag;
    }
    /**
     * 保存过滤条件
     * @param   array   $filter     过滤条件
     * @param   string  $sql        查询语句
     * @param   string  $param_str  参数字符串，由list函数的参数组成
     */
    protected  final  function set_filter($filter, $sql, $param_str = '')
    {
        $filterfile = basename(PHP_SELF, '.php');
        if ($param_str)
        {
            $filterfile .= $param_str;
        }
        setcookie('ECSCP[lastfilterfile]', sprintf('%X', crc32($filterfile)), time() + 600);
        setcookie('ECSCP[lastfilter]',     urlencode(serialize($filter)), time() + 600);
        setcookie('ECSCP[lastfiltersql]',  base64_encode($sql), time() + 600);
    }
    /**
     * 取得上次的过滤条件
     * @param string $param_str　参数字符串，由list函数的参数组成
     * @return array|bool　如果有，返回array('filter' => $filter, 'sql' => $sql)；否则返回false
     */
    protected  final  function get_filter($param_str = ''){
        $filterfile = basename(PHP_SELF, '.php');
        if ($param_str)
        {
            $filterfile .= $param_str;
        }
        if (isset($_GET['uselastfilter']) && isset($_COOKIE['ECSCP']['lastfilterfile'])
            && $_COOKIE['ECSCP']['lastfilterfile'] == sprintf('%X', crc32($filterfile)))
        {
            return array(
                'filter' => unserialize(urldecode($_COOKIE['ECSCP']['lastfilter'])),
                'sql'    => base64_decode($_COOKIE['ECSCP']['lastfiltersql'])
            );
        }
        else
        {
            return false;
        }
    }
    /**
     * 根据过滤条件获得排序的标记
     *
     * @access  public
     * @param   array   $filter
     * @return  array
     */
    protected  final  function sort_flag($filter)
    {
        $flag['tag']    = 'sort_' . preg_replace('/^.*\./', '', $filter['sort_by']);
        $flag['img']    = '<img src="images/' . ($filter['sort_order'] == "DESC" ? 'sort_desc.gif' : 'sort_asc.gif') . '"/>';

        return $flag;
    }
    /**
     * 分页的信息加入条件的数组
     *
     * @access  public
     * @return  array
     */
    final function page_and_size($filter)
    {
        if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
        {
            $filter['page_size'] = intval($_REQUEST['page_size']);
        }
        elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0)
        {
            $filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
        }
        else
        {
            $filter['page_size'] = 15;
        }

        /* 每页显示 */
        $filter['page'] = (empty($_REQUEST['page']) || intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

        /* page 总数 */
        $filter['page_count'] = (!empty($filter['record_count']) && $filter['record_count'] > 0) ? ceil($filter['record_count'] / $filter['page_size']) : 1;

        /* 边界处理 */
        if ($filter['page'] > $filter['page_count'])
        {
            $filter['page'] = $filter['page_count'];
        }

        $filter['start'] = ($filter['page'] - 1) * $filter['page_size'];

        return $filter;
    }
    /**
     * 获得查询时间和次数，并赋值给smarty
     *
     * @access  public
     * @return  void
     */
    public  final function  assign_query_info()
    {
        if ($GLOBALS['db']->queryTime == '')
        {
            $query_time = 0;
        }
        else
        {
            if (PHP_VERSION >= '5.0.0')
            {
                $query_time = number_format(microtime(true) - $GLOBALS['db']->queryTime, 6);
            }
            else
            {
                list($now_usec, $now_sec)     = explode(' ', microtime());
                list($start_usec, $start_sec) = explode(' ', $GLOBALS['db']->queryTime);
                $query_time = number_format(($now_sec - $start_sec) + ($now_usec - $start_usec), 6);
            }
        }
        $GLOBALS['smarty']->assign('query_info', sprintf($GLOBALS['_LANG']['query_info'], $GLOBALS['db']->queryCount, $query_time));

        /* 内存占用情况 */
        if ($GLOBALS['_LANG']['memory_info'] && function_exists('memory_get_usage'))
        {
            $GLOBALS['smarty']->assign('memory_info', sprintf($GLOBALS['_LANG']['memory_info'], memory_get_usage() / 1048576));
        }

        /* 是否启用了 gzip */
        $gzip_enabled = gzip_enabled() ? $GLOBALS['_LANG']['gzip_enabled'] : $GLOBALS['_LANG']['gzip_disabled'];
        $GLOBALS['smarty']->assign('gzip_enabled', $gzip_enabled);
    }
    protected final function check_auth($authz){
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
    protected function check_action($authz)
    {
        return (preg_match('/,*'.$authz.',*/', $_SESSION['action_list']) || $_SESSION['action_list'] == 'all');
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
         $this->display('message.htm');
     }
    /**
     * 跳转
     * @param $url
     */
    protected final function redirect($msg){
        $msg=$msg.'<button onclick="javascript:history.go(-1)">返回</button>';
        $this->success($msg);
    }
}

?>