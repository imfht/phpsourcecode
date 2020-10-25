<?php
defined('IN_ECS')   or define('IN_ECS',true);
require_once(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/cls_json.php');
include_once(ROOT_PATH .'includes/lib_clips.php');
include_once(ROOT_PATH . 'includes/common/utils/Page.php');
class Base{
    protected $db;
    protected $json;
    protected $ecs;
    protected $success=array('code'=>1,'msg'=>'success');
    protected $fail=array('code'=>0,'msg'=>'fail');
    protected $error=array('code'=>2,'msg'=>'出错啦，请检查信息');//发生错误进行跳装
    protected $user_id;

    public function __construct($db,$json,$ecs)
    {
        $this->db=$db;
        $this->json=$json;
        $this->ecs=$ecs;
        $this->ecs->assign('tnew',TNEW);
        $this->ecs->assign('weboot',WEBROOT);
        //登陆信息
        $this->user_id=$_SESSION['user_id'];
        $sql="select *from ecs_users WHERE user_id={$this->user_id}";
        $user_info=$this->db->getRow($sql);
        $this->ecs->assign('user_info',$user_info);
        $sql="select * from ecs_shop_config WHERE id=118";
        $data=$this->db->getRow($sql);
        $this->ecs->assign('logo',$data['value']);
    }
    //获取tdk信息
    public function getTdk($id){
        $sql = "SELECT * FROM ecs_tdd_tdk WHERE id=".$id;
        $data = $this->db->getRow($sql);
        $this->assign('page_title',$data['title']);
        $this->assign('keywords',$data['keywords']);
        $this->assign('description',$data['description']);
    }

    //左侧导航
    public function getNav(){
        $sql1 ="SELECT * FROM ecs_webnav_class where parent_id=1196";
        $res = $this->db->getAll($sql1);
        return $res;
    }
    public function assign($name,$value){
        $this->ecs->assign($name,$value);
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
    /**
     * 用户ajax登陆
     */
    public function ajax_login(){
        if($_POST){
            $name=$_POST['name'];
            $password=$_POST['password'];
            $sql="select * from ecs_users WHERE user_name='{$name}' or email='{$name}'";
            $data=$this->db->getRow($sql);
            if($_SESSION['login_times']>2){
                $_SESSION['login_times']=1;
                $this->ajax($this->error);
            }
            if(isset($_SESSION['login_times'])){
                $_SESSION['login_times']++;
            }else{
                $_SESSION['login_times']=1;
            }
            if($data){
                if($data['ec_salt']){
                    $sendpwd=md5(md5($password).$data['ec_salt']);
                }else{
                    $sendpwd=md5($password);
                }
                if($sendpwd==$data['password']){
                    $_SESSION['login_times']=1;
                    $_SESSION['user_id']=$data['user_id'];
                    $this->ajax($this->success);
                }else{
                    $this->ajax($this->fail);
                }

            }else{
                $this->ajax($this->fail);//用户名不存在
            }
        }
    }
}
abstract class Controller extends Base{
    protected $smarty;
    const LIMIT=30;
    protected $config;
    public function __construct($db,$json,$ecs)
    {
        $this->smarty=$ecs;
        $this->smarty->template_dir   = ROOT_PATH . 'themes/new/html';
        parent::__construct($db,$json,$ecs);
        $this->config=load_config();
        $this->assign('config',$this->config);
    }

    /**
     * 判断登陆
     */
    public function is_login(){
        if(!$_SESSION['user_id']){
           header("Location:/user.php");
        }
    }

    /**
     * 查询一条数据
     * @param $sql
     * @return mixed
     */
    protected function getOne($sql){
        return $this->db->getRow($sql);
    }

    /**
     * 查询多条数据
     * @param $sql　sql语句
     * @return mixed　数据结果集
     */
    protected function select($sql){
        return $this->db->getAll($sql);
    }

    /**
     * 对数据进行删除
     * @param $sql　
     * @return mixed
     */
    protected function delete($sql){
        return $this->db->query($sql);
    }

    /**
     * 对数据表进行添加
     * @param $table　数据表
     * @param $data　需要更新的数据字段
     * @return mixed　更新的id　失败返回false
     */
    protected function add($table,$data){
        return $this->db->autoExecute($table, $data, 'INSERT');
    }

    /**
     * 对数据表进行更新
     * @param $table　数据表
     * @param $data　需要更新的数据字段
     * @return mixed　更新的id　失败返回false
     */
    protected function update($table,$data){
        return $this->db->autoExecute($table, $data, 'UPDATE');
    }

    /**
     * 获取当前的url
     * @param int $cat 栏目id
     * @param string $str 标题名称
     */
    protected function url_here($cat = 0, $str = ''){
        $cur_url = basename(PHP_SELF);
        if (intval($GLOBALS['_CFG']['rewrite']))
        {
            $filename = strpos($cur_url,'-') ? substr($cur_url, 0, strpos($cur_url,'-')) : substr($cur_url, 0, -4);
        }
        else
        {
            $filename = substr($cur_url, 0, -4);
        }
        /* 初始化“页面标题”和“当前位置” */
        $page_title = $GLOBALS['_CFG']['shop_title'];
        $ur_here    = '<a href=".">首页</a>';
        if($filename!='index'){
            /* 处理有分类的 */
            if (in_array($filename, array('category', 'goods', 'article_cat', 'article', 'brand')))
            {
                /* 商品分类或商品 */
                if ('category' == $filename || 'goods' == $filename || 'brand' == $filename)
                {
                    if ($cat > 0)
                    {
                        $cat_arr = get_parent_cats($cat);

                        $key     = 'cid';
                        $type    = 'category';
                    }
                    else
                    {
                        $cat_arr = array();
                    }
                }
                /* 文章分类或文章 */
                elseif ('article_cat' == $filename || 'article' == $filename)
                {
                    if ($cat > 0)
                    {
                        $cat_arr = get_article_parent_cats($cat);

                        $key  = 'acid';
                        $type = 'article_cat';
                    }
                    else
                    {
                        $cat_arr = array();
                    }
                }

                /* 循环分类 */
                if (!empty($cat_arr))
                {
                    krsort($cat_arr);
                    foreach ($cat_arr AS $val)
                    {
                        //$page_title = htmlspecialchars($val['cat_title']) . '_' . $page_title;
                        $page_title = htmlspecialchars($val['cat_title']);
                        $args       = array($key => $val['cat_id']);
                        $ur_here   .= ' <code>&gt;</code> <a href="' . build_uri($type, $args, $val['cat_title']) . '">' .
                            htmlspecialchars($val['cat_name']) . '</a>';
                    }
                }
            }
            /* 处理无分类的 */
            else
            {
                /* 团购 */
                if ('group_buy' == $filename)
                {
                    $page_title = $GLOBALS['_LANG']['group_buy_goods'] . '_' . $page_title;
                    $args       = array('gbid' => '0');
                    $ur_here   .= ' <code>&gt;</code> <a href="group_buy.php">' .
                        $GLOBALS['_LANG']['group_buy_goods'] . '</a>';
                }
                /* 拍卖 */
                elseif ('auction' == $filename)
                {
                    $page_title = $GLOBALS['_LANG']['auction'] . '_' . $page_title;
                    $args       = array('auid' => '0');
                    $ur_here   .= ' <code>&gt;</code> <a href="auction.php">' .
                        $GLOBALS['_LANG']['auction'] . '</a>';
                }
                /* 夺宝 */
                elseif ('snatch' == $filename)
                {
                    $page_title = $GLOBALS['_LANG']['snatch'] . '_' . $page_title;
                    $args       = array('id' => '0');
                    $ur_here   .= ' <code> &gt; </code><a href="snatch.php">' .                                 $GLOBALS['_LANG']['snatch_list'] . '</a>';
                }
                /* 批发 */
                elseif ('wholesale' == $filename)
                {
                    $page_title = $GLOBALS['_LANG']['wholesale'] . '_' . $page_title;
                    $args       = array('wsid' => '0');
                    $ur_here   .= ' <code>&gt;</code> <a href="wholesale.php">' .
                        $GLOBALS['_LANG']['wholesale'] . '</a>';
                }
                /* 积分兑换 */
                elseif ('exchange' == $filename)
                {
                    $page_title = $GLOBALS['_LANG']['exchange'] . '_' . $page_title;
                    $args       = array('wsid' => '0');
                    $ur_here   .= ' <code>&gt;</code> <a href="exchange.php">' .
                        $GLOBALS['_LANG']['exchange'] . '</a>';
                }
                /* 晒单 */
                elseif ('single_sun' == $filename)
                {
                    $page_title = $GLOBALS['_LANG']['single_user'] . '_' . $page_title;
                    $args       = array('siid' => '0');
                    $ur_here .= " <code>&gt;</code> ";
                    $ur_here   .= '<a href="single_sun.php">' .
                        $GLOBALS['_LANG']['single_user'] . '</a>';
                }
                /* 其他的在这里补充 */
            }
        }
        /* 处理最后一部分 */
        if (!empty($str))
        {
            $page_title  = $str . '_' . $page_title;
            $ur_here    .= ' <code>&gt;</code> ' . $str;
        }
        /* 返回值 */
        $this->smarty->assign('page_title', $page_title); // 页面标题
        $this->smarty->assign('ur_here',    $ur_here);
    }
    protected function page($count,$url=null,$limit=Controller::LIMIT){
       $page=new Page($count,$limit);
       //$this->smarty->assign('page', $page);
        $to_url=isset($url)?$url:$_SERVER['SCRIPT_NAME'];
        $show=$page->show($to_url);
        $this->smarty->assign('page', $show);
        //echo $sql;
        //var_dump($show);
    }
    protected function is_pwd($password){
        $sql="select * from ecs_users WHERE user_id={$this->user_id}";
        $data=$this->db->getRow($sql);
        if($data){
            if($data['ec_salt']){
                $sendpwd=md5(md5($password).$data['ec_salt']);
            }else{
                $sendpwd=md5($password);
            }
            if($sendpwd==$data['password']){
                $this->ajax($this->success);
            }else{
                $this->ajax($this->error);
            }
        }else{
            $this->ajax($this->error);
        }
    }
    public function display($file){
        $this->smarty->cache_dir = "/temp/newcaches/";  //缓存目录
        $this->smarty->caching = true;  //开启缓存,为flase的时侯缓存无效
        $this->smarty->cache_lifetime = 3600;  //缓存时间
        $this->smarty->display($file);
    }
    /**
     * 数据不存在
     */
    protected function not_found() {
        header("Location:/404.html");
        exit();
    }

    /**
     * 发送短信
     * @param $phone　手机号
     * @param $type　　类型
     * @return bool　　true表示发送成功,false表示发送失败
     */
    protected function send($phone,$code,$type){
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
}

?>