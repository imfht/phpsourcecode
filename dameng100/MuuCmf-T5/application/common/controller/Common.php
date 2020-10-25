<?php
namespace app\common\controller;

use think\Controller;
use think\Db;

/**
 * 前台控制器基类
 */
class Common extends Controller
{
	public function _initialize()
    {
 		//获取站点LOGO
 		$this->initLogo();
        //获取前端导航菜单
        $this->initNav();
		//获取用户菜单
		$this->initUserNav();
        //用户登录、注册
        $this->initRegAndLogin();
        //记住登陆
        $this->initRembemberLogin();
        //获取用户基本资料
        $this->initUserBaseInfo();
        //初始化统计代码
        $this->initCountCode();
        //微信授权登陆
        $this->wxoauth();
    }

    /**
     * 初始化站点LOGO
     */
    private function initLogo()
    {
        $logo = get_cover(modC('LOGO',0,'Config'),'path');
        $logo = $logo?$logo: STATIC_URL . '/common/images/logo.png';
        $this->assign('logo',$logo);
    }

    /**
     * 初始化前端导航
     */
    private function initNav()
    {
        $nav = model('common/Channel')->lists();
        $this->assign('nav',$nav);
    }

    /**
     * 初始化用户导航
     */
    private function initUserNav()
    {
        $user_nav=cache('common_user_nav');
        if($user_nav===false){
            $user_nav=Db::name('UserNav')->order('sort asc')->where('status=1')->select();
            cache('common_user_nav',$user_nav);
        }
        $this->assign('user_nav',$user_nav);
    }

    /**
     * 初始化用户基本信息
     */
    private function initUserBaseInfo()
    {
        $common_header_user = query_user(['nickname','avatar32']);
        $this->assign('common_header_user',$common_header_user);
    }

    /**
     * 初始化记住登陆
     */
    private function initRembemberLogin()
    {
        model('common/Member')->rembember_login();
    }

    /**
     * 初始化用户登陆注册
     */
    private function initRegAndLogin()
    {   
        //邀请注册开关
        $register_type = modC('REGISTER_TYPE','normal','Invite');
        $register_type = explode(',',$register_type);
        $invite = in_array('invite',$register_type);
        $this->assign('invite',$invite);

        //用户注册登陆
        $open_quick_login = modC('OPEN_QUICK_LOGIN', 0, 'USERCONFIG');
        $this->assign('open_quick_login',$open_quick_login);
        $only_open_register = 0;
        if(in_array('invite',$register_type)&&!in_array('normal',$register_type)){
            $only_open_register = 1;
        }
        $this->assign('only_open_register',$only_open_register);
        $login_url = url('ucenter/Member/login');
        $this->assign('login_url',$login_url);
    }

    /**
     * 初始化统计代码
     */
    private function initCountCode()
    {
        $count_code = config('COUNT_CODE');
        $this->assign('count_code',$count_code);
    }

    /**
     * 手动设置SEO部分
     *
     * @param      <type>  $title  The title
     */
    public $_seo = [
        'title' => '',
        'setKeywords' => '', 
        'description' => ''
    ];

    public function setTitle($title)
    {
        $this->_seo['title'] = $title;
        $this->assign('set_meta', $this->_seo);
    }

    public function setKeywords($keywords)
    {
        $this->_seo['keywords'] = $keywords;
        $this->assign('set_meta', $this->_seo);
    }

    public function setDescription($description)
    {
        $this->_seo['description'] = $description;
        $this->assign('set_meta', $this->_seo);
    }

    public function checkAuth($rule ='',$except_uid =-1,$msg = ''){
        if (!check_auth($rule,$except_uid)) {
            $this->error(empty($msg)?'您无操作权限。':$msg);
        }
    }

    public function checkActionLimit($action = null, $model = null, $record_id = null, $user_id = null, $ip = false,$url = false){
        
        $return = model('ActionLimit')->checkActionLimit($action, $model, $record_id, $user_id, $ip);
        if ($return && !$return['code']) {
            if($url === true){
                $url = $return['url'];
            }elseif($url === false){
                $url = '';
            }

            $this->error($return['msg'],$url);
        }
    }

    public function wxoauth()
    {
        if(!is_login()){
            if(isWeixinBrowser() && addons_status('wxoauth')){
                $requer_url = request()->url(true);
                hook('wxoauth',['requer_url'=>$requer_url]);
            }
        }
    }
}