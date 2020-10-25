<?php
namespace Action;
use HY\Action;
!defined('HY_PATH') && exit('HY_PATH not defined.');
class HYBBS extends Action {
    public $_user=array();  //当前用户数据
    public $_login=false;   //当前是否登录
    public $_theme;
    public $_forum=array();
    public $conf;       //Config/conf.php array
    public $_group = 3; //当前用户组 3 = 游客
    public $_usergroup=array();
    public $_uid = -1;
    public $_username = false;
    public $CacheObj;
    public $_count = array();
    
    //{hook a_hybbs_var}

    public function __construct(){
        
        if(isset($GLOBALS['HYBBS'])) //防止多次执行 构造函数
            return;
        $GLOBALS['HYBBS'] = true;
        //{hook a_hybbs_init}

        //加载Conf.php配置
        $this->init_conf();

        
        if(empty($this->conf['debug_page']))
            C("DEBUG_PAGE",false);
        else
            C("DEBUG_PAGE",true);
        
  
        //跳转安装
        $DOMAIN_NAME = C('DOMAIN_NAME');
        if(empty($DOMAIN_NAME)){
            header("location: ./install");
            exit;
        }

        //{hook a_hybbs_init_1}
        //当前主题模板名称
        if(IS_SHOUJI)
            $this->_theme = $this->view = $this->conf['wapview'];
        else
            $this->_theme = $this->view = $this->conf['theme'];


        define('THEME_NAME',$this->_theme);
        define('WWW',C('DOMAIN_NAME') . '/');
        
        //初始化用户状态
        $this->init_user();
        //当前用户组
        $this->v("group",$this->_group);
        define("NOW_GROUP",$this->_group); //默认游客用户组 ,登录后再次确认
        define("NOW_UID",$this->_uid); //当前用户组

        define("IS_LOGIN",$this->_login);
        define('NOW_USER',$this->_login?$this->_user['user']:false);
        


        $this->v('title','页面缺少标题');
        //{hook a_hybbs_init_2}

        $this->CacheObj = cache(array());

        //板块缓存
        $this->init_forum();
        //{hook a_hybbs_init_3}
        //用户组缓存
        $this->init_group();
        

        //站点统计缓存
        $this->init_count();
        if(IS_LOGIN)
            $this->init_ol();
        $this->update_ol();
        //{hook a_hybbs_init_v}


    }
    //初始化 用户
    protected function init_user(){
        //{hook a_hybbs_init_user_1}
        $cookie = cookie("HYBBS_HEX");
        if(!empty($cookie)){
            //{hook a_hybbs_init_user_2}
            $UserLib = L("User");
            $user = $UserLib->get_cookie($cookie);

            if(!empty($user)){
                //{hook a_hybbs_init_user_3}
                if(isset($user['id']) 
                    && isset($user['user'])
                    && isset($user['pass'] )
                    ){
                    $user_data = M("User")->read($user['id']);
                    //更改密码后 重新登录
                    //用户更改用户组后 重新登录
                    //{hook a_hybbs_init_user_4}
                    if($user_data['pass'] == $user['pass'] && 
                        $user_data['user'] == $user['user'] 
                        ){
                        //{hook a_hybbs_init_user_5}
                        $this->_uid = $user_data['id'];

                        $this->_group = $user_data['group'];
                        //替换cookie美容
                        $user = $user_data;
                        $user['avatar'] = $this->avatar($user['user']);
                        $user['mess'] = M("Chat_count")->get_c($user['id']);
                        $this->_user = $user;

                        $this->_login=true;
                        $this->v('user',$this->_user);
                        //{hook a_hybbs_init_user_6}
                    }

                    

                }
            }
        }
        //{hook a_hybbs_init_user_v}


    }
    //初始化板块
    protected function init_forum(){
        //{hook a_hybbs_init_forum_1}
        $forum = $this->CacheObj->forum;
        if(empty($forum) || DEBUG){ //调试模式 每次都生成缓存
            //{hook a_hybbs_init_forum_2}
            $ForumModel = M("Forum");
            $forum = $ForumModel->read_all();
            $ForumModel->format($forum);
            $this->CacheObj->forum = $forum;
        }
        $this->_forum = $forum;
        //{hook a_hybbs_init_forum_v}
        $this->v("forum",$this->_forum);

    }
    //初始化用户组
    protected function init_group(){
        //{hook a_hybbs_init_group_1}
        $usergroup = $this->CacheObj->usergroup;
        if(empty($usergroup) || DEBUG){ //调试模式 每次都生成缓存
            //{hook a_hybbs_init_group_2}
            $UsergroupObj = M("Usergroup");
            $usergroup = $UsergroupObj->select("*");
            $UsergroupObj->format($usergroup);
            $this->CacheObj->usergroup = $usergroup;
        }
        
        $this->_usergroup = $usergroup;
        //{hook a_hybbs_init_group_v}
        $this->v("usergroup",$this->_usergroup);
    }
    //站点统计缓存
    protected function init_count(){
        //{hook a_hybbs_init_count_1}
        $bbs_count = $this->CacheObj->bbs_count;
        //var_dump($bbs_count
        if(empty($bbs_count) || DEBUG){
            //{hook a_hybbs_init_count_2}
            $Post = S("Post");
            $Thread = S("Thread");
            $User = S("User");
            $bbs_count = array(
                'post'  =>  $Post->count(),
                'thread'=>  $Thread->count(),
                'user'  =>  $User->count(),
                'day_thread'  =>  $Thread->count(array('atime[>]'=>strtotime(date('Y-m-d')))),
                'day_post'    =>  $Post->count(array('atime[>]'=>strtotime(date('Y-m-d')))),
                'day_user'    =>  $User->count(array('atime[>]'=>strtotime(date('Y-m-d')))),
                'clien'         =>  S('ol')->count(),

            );
            $this->CacheObj->bbs_count = $bbs_count;
            $this->_count = $bbs_count;
            $this->update_ol_1();
            //{hook a_hybbs_init_count_3}
        }
        //{hook a_hybbs_init_count_4}
        
        isset($bbs_count['post']) or $bbs_count['post'] = 0;
        isset($bbs_count['thread']) or $bbs_count['thread'] = 0;
        isset($bbs_count['user']) or $bbs_count['user'] = 0;
        isset($bbs_count['day_thread']) or $bbs_count['day_thread'] = 0;
        isset($bbs_count['day_post']) or $bbs_count['day_post'] = 0;
        isset($bbs_count['day_user']) or $bbs_count['day_user'] = 0;
        isset($bbs_count['clien']) or $bbs_count['clien'] = 0;
        $this->_count = $bbs_count;
        //{hook a_hybbs_init_count_v}
        $this->v("hy_count",$bbs_count);
    }

    //初始化配置
    protected function init_conf(){
        //{hook a_hybbs_init_conf_1}
        $tmp_conf = array(
            
            'title'         =>  'HYBBS',
            'logo'          =>  'HYBBS',
            'title2'        =>  ' - HYBBS',
            'keywords'      =>  'HYBBS,HYPHP',
            'description'   =>  'HYBBS国内开源PHP论坛程序',

            'theme'         =>  'hy_boss',
            'userview'      =>  'hy_user',
            'messview'      =>  'hy_message',
            'userview2'     =>  'hy_user',
            'pc_search'     =>  'search',

            'wapview'           =>  'hy_moblie',
            'wapuserview'       =>  'hy_moblie',
            'wapuserview2'      =>  'hy_moblie',
            'wapmessview'       =>  'hy_moblie',
            'wap_search'        =>  'hy_moblie',

            'gold_thread'   =>  2,
            'gold_post'     =>  2,
            'credits_thread'   =>  2,
            'credits_post'     =>  2,
            
            'homelist'      =>  10,
            'forumlist'     =>  10,
            'postlist'      =>  10,
            'searchlist'    =>  10,
            'search_key_size'    =>  2,
            'titlesize'     =>  128,
            'titlemin'      =>  5,
            'summary_size'      =>  200,
            'emailhost'     =>  '',
            'emailuser'     =>  '',
            'emailpass'     =>  '',
            'emailport'     =>  '',
            'emailtitle'        =>  'HYBBS找回密码验证码邮件',
            'emailcontent'      =>  '暂无尾部内容.',
            'uploadfileext'     =>  'zip,rar',
            'uploadimageext'    =>  'jpg,gif,png,jpeg,bmp',
            'post_image_size'    =>  5,
            'adminforum'        =>  10,
            'adminthread'        =>  10,
            'adminuser'        =>  10,
            
            'cache_type'        =>  'File',
            'cache_table'       =>  'cache',
            'cache_key'         =>  null,
            'cache_time'        =>  60,
            'cache_pr'          =>  null,
            'cache_ys'          =>  false,
            'cache_outtime'     =>  null,
            'cache_redis_ip'    =>  null,
            'cache_redis_port'  =>  null,
            'cache_mem_ip'      =>  null,
            'cache_mem_port'    =>  null,
            'cache_memd_ip'     =>  null,
            //调试相关
            'debug_page'        => 1,
            'debug'             => 1,
            'uploadimagemax'    =>  3,
            'uploadfilemax'     => 3,
            'key'     => '',

        );
        //{hook a_hybbs_init_conf_2}
        if(is_file(CONF_PATH . 'conf.php')){
            $conf = file(CONF_PATH . 'conf.php');
            $this->conf = json_decode($conf[1],true);
            
            $this->conf = array_merge($tmp_conf,$this->conf);
            $GLOBALS['conf'] =$this->conf;
        }else{
            $this->conf = $tmp_conf;
            $GLOBALS['conf'] = $this->conf;
        }
        
        //{hook a_hybbs_init_conf_3}
        
            C("DATA_CACHE_TYPE",$this->conf['cache_type']);
        
            C("DATA_CACHE_TABLE",$this->conf['cache_table']);

        
            C("DATA_CACHE_KEY",$this->conf['cache_key']);
        
            C("DATA_CACHE_TIME",$this->conf['cache_time']);
        

        
            C("DATA_CACHE_PREFIX",$this->conf['cache_pr']);
        if($this->conf['cache_ys'] == 'on')
            C("DATA_CACHE_COMPRESS",true);
        if($this->conf['cache_outtime'])
            C("DATA_CACHE_TIMEOUT",$this->conf['cache_outtime']);
        if($this->conf['cache_redis_ip'])
            C("REDIS_HOST",$this->conf['cache_redis_ip']);
        if($this->conf['cache_redis_port'])
            C("REDIS_PORT",$this->conf['cache_redis_port']);

        if($this->conf['cache_mem_ip'])
            C("MEMCACHE_HOST",$this->conf['cache_mem_ip']);
        if($this->conf['cache_mem_port'])
            C("MEMCACHE_PORT",$this->conf['cache_mem_port']);
        if($this->conf['cache_memd_ip']){
            $arr = explode("\r\n",$this->conf['cache_memd_ip']);
            $options=array();
            foreach ($arr as $v) {
                array_push($options,explode(":",$v));
            }
            C("MEMCACHED_SERVER",$options);
        }
        $this->conf['title2'].=' - Powered by HYBBS';
        //{hook a_hybbs_init_conf_v}

    
        
        $this->v("conf",$this->conf);
    }
    protected function init_ol(){
        $up_time = cookie('up_time');
        if(empty($up_time)){
            cookie('up_time',NOW_TIME - 300); //提前更新
        }else{
            if($up_time + 280 < NOW_TIME){
                cookie('up_time',NOW_TIME);
                $ol = S('ol');
                if($ol->has(array('uid'=>NOW_UID)))
                    $ol->update(array('atime'=>NOW_TIME),array('uid'=>NOW_UID));
                else{
                    $ol->insert(array('uid'=>NOW_UID,'username'=>NOW_USER,'ip'=>ip2long($_SERVER['ip']),'group'=>NOW_GROUP,'atime'=>NOW_TIME));
                    $this->_count['clien'] = $ol->count();
                    $this->CacheObj->bbs_count = $this->_count;
                }
            }
        }
    }
    //更新在线列表
    protected function update_ol(){
        $ol_time = $this->CacheObj->get("ol_time");
        if(empty($ol_time)){
            $this->CacheObj->set('ol_time',NOW_TIME,0);
            $this->update_ol_1();
        }else{
            if($ol_time + 300 < NOW_TIME){ //5分钟更新一次在线表
                $this->CacheObj->set('ol_time',NOW_TIME,0);
                $this->update_ol_1();
            }
        }
    }
    protected function update_ol_1(){
        $ol = S("ol");
        $ol->delete(array('atime[<]'=>NOW_TIME - 200));
        $this->_count['clien'] = $ol->count();
        $this->CacheObj->bbs_count = $this->_count;
    }
    protected function message($msg,$type=false,$url=''){
        //{hook a_hybbs_message}
        if(IS_AJAX && IS_POST){
            return $this->json(array(
                'error'=>$type,
                'info'=>$msg,
                'url'=>$url
            ));
        }
        $this->v('title',$msg.' - 提示');
        $this->v("msg",$msg);
        $this->v("bool",$type);
        $this->view = IS_MOBILE ? $this->conf['wapmessview'] : $this->conf['messview'];
        $this->display('message');
    }
    //获取用户头像
    protected function avatar($user){
        //{hook a_hybbs_avatar}
        $path = INDEX_PATH . 'upload/avatar/' . md5($user);
        $path1 = 'upload/avatar/' . md5($user);
        //echo $path.'-a.jpg';
        
        if(!is_file($path.'-a.jpg'))
            return array(
                'a'=>'public/images/user.gif',
                'b'=>'public/images/user.gif',
                'c'=>'public/images/user.gif',
            );
        return array(
            "a"=>$path1."-a.jpg",
            "b"=>$path1."-b.jpg",
            "c"=>$path1."-c.jpg"
        );
    }
    //{hook a_hybbs_fun}
}
