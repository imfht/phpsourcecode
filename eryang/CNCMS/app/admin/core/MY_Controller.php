<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 *  扩展  Controller类
 *
 * @category    core
 * @author      二　阳°(QQ:707069100)
 * @link        http://weibo.com/513778937?topnav=1&wvr=5
 */

class MY_Controller extends CI_Controller {

	/**
	 * 构造函数
	 *
	 * @access  public
	 * @return  void
	 */
	function __construct() {
		//构造函数
		parent::__construct();

		//前台设置
		$config_web = $this -> base_model -> get_one('config_web');
		//后台设置
		$config_admin = $this -> base_model -> get_one('config_admin');
		//===========================================================================
		//前台设置
		//===========================================================================
		//资源存放文件夹
		define('SITE_WEB_RESOURCES', '/' . $config_web['resources']);
		//站点名称
		define('SITE_WEB_NAME', $config_web['name']);
		//站点 LOGO
		define('SITE_WEB_LOGO', SITE_WEB_RESOURCES . '/web/default/logo/' . $config_web['logo']);
		//站点 CSS
		define('SITE_WEB_CSS', SITE_WEB_RESOURCES . '/web/default/' . $config_web['css']);
		//站点 JS
		define('SITE_WEB_JS', SITE_WEB_RESOURCES . '/web/default/' . $config_web['js']);
        //站点 IMG
        define('SITE_WEB_IMG', SITE_WEB_RESOURCES . '/web/default/' . $config_web['img']);
		//站点 EDITOR
		define('SITE_WEB_EDITOR', SITE_WEB_RESOURCES . '/web/default/' . $config_web['editor']);
		//站点 ART
		define('SITE_WEB_ART', SITE_WEB_RESOURCES . '/web/default/' . $config_web['art']);
		//站点 UPLOADS
		define('SITE_WEB_UPLOADS', SITE_WEB_RESOURCES . '/web/default/' . $config_web['uploads']);
		//站点 VALICODE
		define('SITE_WEB_VALICODE', SITE_WEB_RESOURCES . '/web/default/' . $config_web['valicode']);
		//站点备案号
		define('SITE_WEB_ICP', $config_web['icp']);
		//站点上传图片大小
		define('SITE_WEB_UPLOAD_IMAGE_SIZE', $config_web['upload_image_size']);
		//站点上传动画大小
		define('SITE_WEB_UPLOAD_FLASH_SIZE', $config_web['upload_flash_size']);
		//站点上传视频大小
		define('SITE_WEB_UPLOAD_MEDIA_SIZE', $config_web['upload_media_size']);
		//站点上传文件大小
		define('SITE_WEB_UPLOAD_FILE_SIZE', $config_web['upload_file_size']);
		//站点开始密钥
		define('SITE_WEB_ENCRYPTION_KEY_BEGIN', $config_web['encryption_key_begin']);
		//站点结束密钥
		define('SITE_WEB_ENCRYPTION_KEY_END', $config_web['encryption_key_end']);
		//站点统计代码
		define('SITE_WEB_STATISTICAL_CODE', $config_web['statistical_code']);
		//站点分享代码
		define('SITE_WEB_SHARE_CODE', $config_web['share_code']);
		//站点关键字
		define('SITE_WEB_KEYWORDS', $config_web['keywords']);
		//站点描述
		define('SITE_WEB_DESCRIPTION', $config_web['description']);
		//站点状态
		define('SITE_WEB_STATUS', $config_web['status']);
		//站点关闭原因
		define('SITE_WEB_CLOSE_REASON', $config_web['close_reason']);
		//站点注册协议
		define('SITE_WEB_REG_AGREEMENT', $config_web['reg_agreement']);
		//站点主题
		define('SITE_WEB_THEME', $config_web['theme']);
        //邮件状态
        define('SITE_WEB_EMAIL_STATUS', $config_web['email_status']);
        //SMTP服务器
        define('SITE_WEB_EMAIL_SMTP', $config_web['email_smtp']);
        //SMTP端口
        define('SITE_WEB_EMAIL_PORT', $config_web['email_port']);
        //发件人地址
        define('SITE_WEB_EMAIL_USER', $config_web['email_user']);
        //发件人密码
        define('SITE_WEB_EMAIL_PASSWORD', $config_web['email_password']);
        //邮件标题
        define('SITE_WEB_EMAIL_TITLE', $config_web['email_title']);
        //发件人署名
        define('SITE_WEB_EMAIL_USERNAME', $config_web['email_username']);
        //邮件测试内容
        define('SITE_WEB_EMAIL_CONTENT', $config_web['email_content']);
		//===========================================================================
		//后台设置
		//===========================================================================
		//资源存放文件夹
		define('SITE_ADMIN_RESOURCES', '/' . $config_admin['resources']);
		//后台名称
		define('SITE_ADMIN_NAME', $config_admin['name']);
		//后台 LOGO
		define('SITE_ADMIN_LOGO', SITE_ADMIN_RESOURCES . '/admin/default/logo/' . $config_admin['logo']);
		//后台 CSS
		define('SITE_ADMIN_CSS', SITE_ADMIN_RESOURCES . '/admin/default/' . $config_admin['css']);
		//后台 JS
		define('SITE_ADMIN_JS', SITE_ADMIN_RESOURCES . '/admin/default/' . $config_admin['js']);
        //后台 IMG
        define('SITE_ADMIN_IMG', SITE_ADMIN_RESOURCES . '/admin/default/' . $config_admin['img']);
		//后台 EDITOR
		define('SITE_ADMIN_EDITOR', SITE_ADMIN_RESOURCES . '/admin/default/' . $config_admin['editor']);
		//后台 ART
		define('SITE_ADMIN_ART', SITE_ADMIN_RESOURCES . '/admin/default/' . $config_admin['art']);
		//后台 UPLOADS
		define('SITE_ADMIN_UPLOADS', SITE_ADMIN_RESOURCES . '/admin/default/' . $config_admin['uploads']);
		//后台 VALICODE
		define('SITE_ADMIN_VALICODE', SITE_ADMIN_RESOURCES . '/admin/default/' . $config_admin['valicode']);
		//后台上传图片大小
		define('SITE_ADMIN_UPLOAD_IMAGE_SIZE', $config_admin['upload_image_size']);
		//后台上传动画大小
		define('SITE_ADMIN_UPLOAD_FLASH_SIZE', $config_admin['upload_flash_size']);
		//后台上传视频大小
		define('SITE_ADMIN_UPLOAD_MEDIA_SIZE', $config_admin['upload_media_size']);
		//后台上传文件大小
		define('SITE_ADMIN_UPLOAD_FILE_SIZE', $config_admin['upload_file_size']);
		//后台开始密钥
		define('SITE_ADMIN_ENCRYPTION_KEY_BEGIN', $config_admin['encryption_key_begin']);
		//后台结束密钥
		define('SITE_ADMIN_ENCRYPTION_KEY_END', $config_admin['encryption_key_end']);
		//后台视图路径
		define('SITE_ADMIN_THEME', $config_admin['theme']);
        //邮件状态
        define('SITE_ADMIN_EMAIL_STATUS', $config_admin['email_status']);
        //SMTP服务器
        define('SITE_ADMIN_EMAIL_SMTP', $config_admin['email_smtp']);
        //SMTP端口
        define('SITE_ADMIN_EMAIL_PORT', $config_admin['email_port']);
        //发件人地址
        define('SITE_ADMIN_EMAIL_USER', $config_admin['email_user']);
        //发件人密码
        define('SITE_ADMIN_EMAIL_PASSWORD', $config_admin['email_password']);
        //邮件标题
        define('SITE_ADMIN_EMAIL_TITLE', $config_admin['email_title']);
        //发件人署名
        define('SITE_ADMIN_EMAIL_USERNAME', $config_admin['email_username']);
        //邮件测试内容
        define('SITE_ADMIN_EMAIL_CONTENT', $config_admin['email_content']);


        //后台视图路径
		$this -> load -> set_admin_template(SITE_ADMIN_THEME);

		unset($config_common, $config_web, $config_admin);
        //找回密码的方式
        define('BY_EMAIL','by_email');

	}

	// ------------------------------------------------------------------------


    /**
     * 获得邮箱的模糊显示
     *
     * @return string
     */
    protected  function _get_email_dim($email) {
        $info = explode('@', $email);
        $info[0] = $info[0][0] . str_repeat('*', strlen($info[0]) - 1);
        return implode('@', $info);
    }

    // ------------------------------------------------------------------------


    /**
     * 是否可以使用邮箱找回密码
     *
     * @return boolean
     */
    protected  function _allow_find_by_email() {
        return SITE_ADMIN_EMAIL_STATUS==1?TRUE:FALSE;
    }

    // ------------------------------------------------------------------------

    /**
     * 发送邮件
     * @param $email 电子邮件
     * @param $title 标题
     * @param $message 内容
     * @param $type 类型1是忘记密码邮件2是注册邮件
     * @return bool
     *
     *     * $this->load->library ( 'mymd5' );
    echo $this->mymd5->md5_encrypt('1fsdfasdf23fsdffffffffffff456fsdfsssss','123456');

    echo '<br/>';
    //字符串转换成专用url
    echo rawurlencode($this->mymd5->md5_encrypt('1fsdfasdf23fsdffffffffffff456fsdfsssss','123456'));
    echo '<br/>';
    //url转换成字符串
    echo rawurldecode($this->mymd5->md5_encrypt('1fsdfasdf23fsdffffffffffff456fsdfsssss','123456'));
    echo '<br/>';
    echo $this->mymd5->md5_decrypt($this->mymd5->md5_encrypt('12345689489849','123456'),'123456');
    exit;
     *
     * $this->db->set('status','status+1',FALSE);
    $this->db->where('id','1');
    $this->db->update('manager');
     */
    protected  function _send_email($email,$type=1){

        $this->load->library ( 'email' );
        $this->load->config ( 'email' );
        $this->lang->load ( 'email' );

        $manager_result=$this->base_model->get_one('manager',array('email'=>$email));

        if($manager_result['email']){
            $this->email->from ( $this->config->item('smtp_user'),$this->config->item('username') );
            $this->email->to ( $email );

            if($type==1){
                $title=$manager_result['username'].lang('email_password_title_one').lang('email_password_title_two');
                $this->load->library ( 'mymd5' );
                $this->load->helper ( 'my_md5' );
                //产生激活码
                $code = substr(str_md5(now()), mt_rand(1, 8), 8);
                //加密
                $status=$this->_create_find_password_identify($manager_result['username'],BY_EMAIL,$manager_result['email']);
                $url='<a href='.site_url($this -> config -> item('admin_folder').'login/reset_pwd/'.$code.rawurlencode(rawurlencode(rawurlencode($status)))).' target="_blank">'.site_url($this -> config -> item('admin_folder').'login/reset_pwd/'.$code.rawurlencode(rawurlencode(rawurlencode($status)))).'</a>';
                $message=lang('email_password_content_one').$manager_result['username'].lang('email_password_content_two').SITE_WEB_NAME.lang('email_password_content_three').'<br/>'.$url.'<br/>'.lang('email_password_content_four').'<br/>'.SITE_WEB_NAME.'<br/>'.date('Y-m-d H:i:s');

            }elseif($type==2){
                $title=$manager_result['username'].lang('email_password_title_one').lang('email_activate_title_two');
                $this->load->library ( 'mymd5' );
                $this->load->helper ( 'my_md5' );
                //产生激活码
                $code = substr(str_md5(now()), mt_rand(1, 8), 8);
                //加密
                $status=$this->_create_find_password_identify($manager_result['username'],BY_EMAIL,$manager_result['email']);
                $url='<a href='.site_url($this -> config -> item('admin_folder').'login/reset_pwd/activate_email/'.$code.rawurlencode(rawurlencode(rawurlencode($status)))).' target="_blank">'.site_url($this -> config -> item('admin_folder').'login/reset_pwd/activate_email/'.$code.rawurlencode(rawurlencode(rawurlencode($status)))).'</a>';
                $message=lang('email_password_content_one').$manager_result['username'].lang('email_password_content_two').SITE_WEB_NAME.lang('email_activate_content_three').'<br/>'.$url.'<br/>'.lang('email_password_content_four').'<br/>'.SITE_WEB_NAME.'<br/>'.date('Y-m-d H:i:s');
            }else{
                return FASLE;
            }

            $this->email->subject ( $title);
            $this->email->message (html_entity_decode($message));

            //echo $this->email->print_debugger ();
            $data_email ['type'] = $type;
            $data_email ['title'] = $title;
            $data_email ['content'] = $message;
            $data_email ['email'] = $email;
            $data_email ['addtime'] = now();
            $data_email ['sendtime'] = now();
            $data_email ['addip'] = $this->input->ip_address ();
            $data_email['code']=$code;
            $data_email['url']=$code.rawurlencode(rawurlencode(rawurlencode($status)));

            if($this->email->send ()){
                $data_email ['status'] = 1;
                $this->base_model->insert('email_log',$data_email);
                return TRUE;
            }else{
                $data_email ['status'] = 2;
                $this->base_model->insert('email_log',$data_email);
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * 创建找回密码的唯一标识
     *
     * @param string $username 需要找回密码的用户名
     * @param string $way 找回方式标识
     * @param string $value 找回方式对应的值
     * @return string
     */
    protected  function _create_find_password_identify($username, $way, $value) {
        $this->load->library ( 'mymd5' );
        $code=$this->mymd5->md5_encrypt($username.'||'.$way.'||'.$value,SITE_ADMIN_ENCRYPTION_KEY_BEGIN.SITE_ADMIN_ENCRYPTION_KEY_END);
        return rawurlencode($code);
    }

    // ------------------------------------------------------------------------

    /**
     * 解析找回密码的标识
     *
     * @param string $identify
     * @return array array($username, $way, $value)
     */
    protected  function _parser_find_pwd_identify($identify) {
        $this->load->library ( 'mymd5' );
        return explode("||", $this->mymd5->md5_decrypt(rawurldecode($identify),SITE_ADMIN_ENCRYPTION_KEY_BEGIN.SITE_ADMIN_ENCRYPTION_KEY_END));
    }

    // ------------------------------------------------------------------------

    /**
     * 获得email链接地址
     *
     * @return string
     */
    protected  function _get_email_url($email) {
        $info = explode('@', $email);
        return 'http://mail.' . $info[1];
    }

    // ------------------------------------------------------------------------

    /**
     * 检查是否符合要求
     * @param string $type 类型
     */
    protected function _check_status($_status) {
        $data_code=$this->_parser_find_pwd_identify($_status);
        if($data_code){
            $this->load->model ('manager_model');
            $manager_result = $this->manager_model->get_manager_by_username($data_code['0']);
            if($manager_result){
                if($data_code['1']=BY_EMAIL && $data_code['2']==$manager_result->email){
                    return $manager_result;
                }else{
                    return FALSE;
                }
            } else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * 检查验证码
     *
     * @param string $code
     * @return boolean
     */
    protected function _check_code($code,$email) {
        if($code && $email){
            $code_reslut=$this->base_model->get_one('email_log',array('status'=>1,'email'=>$email,'active_time'=>0,'code'=>$code),'code');
            if($code_reslut){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    // ------------------------------------------------------------------------
}


/* End of file MY_Controller.php */
/* Location: ./app/admin/core/MY_Controller.php */


/**
 *  后台控制器基类
 *
 * @category    core
 * @author      二　阳°(QQ:707069100)
 * @link        http://weibo.com/513778937?topnav=1&wvr=5
 */

class Admin_Controller extends MY_Controller {

	/**
	 * _manager
	 * 保存当前登录管理员的信息
	 *
	 * @var object
	 * @access  public
	 **/
	public $_manager = NULL;
	// ------------------------------------------------------------------------

	/**
	 * power_menu
	 * 角色所拥有的目录
	 *
	 * @var object
	 * @access  public
	 **/
	public $power_menu = NULL;

	// ------------------------------------------------------------------------

	/**
	 * 构造函数
	 *
	 * @access  public
	 * @return  void
	 * 设置默认编码为 utf-8
	 * 设置默认时区为东八区
	 */
	function __construct() {
		//构造函数
		parent::__construct();
		$this -> load -> library('auth');
		$this -> load -> model('manager_model');
		$this -> load -> helper('form');
		$this -> lang -> load('admin_common');
		//检查登录
		$this -> auth -> is_logged_in(uri_string());
        $manager = $this -> auth -> CI -> manager_session -> userdata('manager');
		$this -> _manager = $this -> manager_model -> get_manager_by_username($manager['id'], 'id');
		if ($this -> _manager -> status != 1) {//管理员被禁用
			/*销毁manager_session*/
			$this -> auth -> logout();
			$this -> session -> set_flashdata('error', lang('manager_status'));
			redirect($this -> config -> item('admin_folder') . 'login');
		}
		//获取目录菜单
		$this -> power_menu = $this -> auth -> get_menu($this -> _manager -> role_id);

		header('Content-type:text/html; charset=utf-8');
		date_default_timezone_set('Asia/Shanghai');

	}

	// ------------------------------------------------------------------------

	/**
	 * 检查是否具有访问权限
	 *
	 * @access   public
	 * @param    string    权限名称
	 * @param    boolean   是否返回值
	 * @return
	 */
	function check_power($page_name, $return = FALSE) {
		$check_power = $this -> auth -> check_power($page_name, $this -> _manager -> role_id);
		if (!$check_power) {
			if ($return) {
				return FALSE;
			}
			/*销毁manager_session*/
			$this -> auth -> logout();
			$this -> session -> set_flashdata('error', lang('error_power'));
			redirect($this -> config -> item('admin_folder') . 'login');
		}
		return $page_name;
	}

	// ------------------------------------------------------------------------




}

/* End of file MY_Controller.php */
/* Location: ./app/admin/core/Admin_Controller.php */
