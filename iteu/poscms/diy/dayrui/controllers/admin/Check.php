<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

	
class Check extends M_Controller {

	private $step;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		// 检测步骤
		$this->step = $this->_get_step();
    }

	/**
     * 系统体检
     */
    public function index() {
		$this->template->assign(array(
			'step' => $this->step,
		));
		$this->template->display('check_index.html');exit;
	}
	
	/**
     * PHP环境
     */
    public function phpinfo() {
		phpinfo();
		$this->output->enable_profiler(TRUE);
	}
	
	/**
     * 执行检测
     */
    public function todo() {
		$step = max(1, (int)$this->input->get('step'));
		if (isset($this->step[$step]) && method_exists($this, $this->step[$step])) {
			echo @call_user_func_array(array($this, $this->step[$step]), array());
		}
		exit;
	}

	/**
     * 版本检测
     */
    private function _version() {

	}

	/**
     * zend检测
     */
    private function _zend() {

	}

    // php 5.6
    private function _always_populate_raw_post_data() {



    }

    // 未使用的附件
    private function _attachment_unused() {

        if ($this->db->count_all_results('attachment_unused') > 15) {
            return $this->halt("<a style='color:blue' href=".dr_url('attachment/unused').">系统存在大量未使用的附件，请及时清理无用附件，累计越多程序执行速度越慢。</a>", 0);
        }

    }
	
	/**
     * 上传参数检测
     */
    private function _upload() {

        $post = intval(@ini_get("post_max_size"));
        $file = intval(@ini_get("upload_max_filesize"));

        $str = '';
        if ($file >= $post) {
            $str.= $this->halt('系统配置不合理，post_max_size值必须大于upload_max_filesize值，否则会出现“进度条100%卡住”或者提示“游客不允许上传”', 0);
        }
        if ($file < 10) {
            $str.= $this->halt('系统环境只允许上传'.$file.'MB文件，可以设置upload_max_filesize值提升上传大小', 1);
        }
        if ($post < 10) {
            $str.= $this->halt('系统环境要求每次发布内容不能超过'.$post.'MB（含文件），可以设置post_max_size值提升发布大小', 1);
        }

        return $str;
	}

	/**
     * ini_get
     */
    private function _ini_get() {
		if (!function_exists('ini_get')) {
			return $this->halt('系统函数ini_get被禁用了，将无法获取到系统环境参数，解决方案：在php.ini中找到disable_functions并去掉ini_get', 0);
		}
	}

	/**
     * 模板名称检测
     */
    private function _template() {
		if (SITE_TEMPLATE == 'default') {
			return $this->halt('网站模板【default】未更换，建议正式站点不要采用系统默认模板【default】，默认模板仅用于学习', 0);
		}
	}


	/**
     * 解压函数检测
     */
    private function _unzip() {
		if (!function_exists('gzopen')) {
			return $this->halt('未开启zlib扩展，您将无法进行在线升级、无法下载模块/应用、无法进行模块/应用升级更新、无法上传头像、无法上传头像，解决方案：Google/百度一下“PHP开启zlib扩展”', 0);
		}
	}

	/**
     * 解压函数检测
     */
    private function _gzinflate() {
		if (!function_exists('gzinflate')) {
			return $this->halt('函数gzinflate被禁用了，您将无法进行在线升级、无法下载模块/应用、无法进行模块/应用升级更新、无法上传头像，解决方案：在php.ini中找到disable_functions并去掉gzinflate', 0);
		}
	}
	
	/**
     * 后台入口名称检测
     */
    private function _admin_file() {
		if (SELF == 'admin.php') {
			return $this->halt('如果管理帐号泄漏，后台容易遭受攻击，为了系统安全，请修改根目录admin.php的文件名', 0);
		}
	}
	
	/**
     * 目录是否可写
     */
    private function _dir_write() {
	
		$dir = array(
            WEBPATH.'cache/' => '无法生成系统缓存文件',
            WEBPATH.'config/' => '无法生成系统配置文件',
            WEBPATH.'config/domain.php' => '无法生成系统配置文件',
            WEBPATH.'config/rewrite.php' => '无法生成伪静态配置文件',
            WEBPATH.'config/site/' => '无法生成网站配置文件',
            WEBPATH.'config/site/1.php' => '无法生成网站配置文件',
            WEBPATH.'cache/data/' => '无法生成系统配置文件，会导致系统配置无效',
            WEBPATH.'cache/templates/' => '无法生成模板解析文件',
            WEBPATH.'api/thumb/' => '无法生成缩略图缓存文件',
            WEBPATH.'cache/session/' => '无法生成session会话文件',
            WEBPATH.'cache/errorlog/' => '无法保存错误日志',
            WEBPATH.'cache/cron/' => '无法执行任务计划',
            WEBPATH.'cache/index/' => '无法生成首页静态页面',
            WEBPATH.'cache/file/' => '无法存储文件缓存内容',
            WEBPATH.'cache/optionlog/' => '无法存储操作日志',
			SYS_UPLOAD_PATH.'/' => '无法上传附件',
		);

        $local = dr_dir_map(FCPATH, 1); // 本地模块
        if ($local) {
            foreach ($local as $mdir) {
                if (is_file(FCPATH.'module/'.$mdir.'/config/module.php')) {
                    $dir[FCPATH.'module/'.$mdir.'/config/'] = '无法生成模块【'.$dir.'】配置文件';
                }
            }
        }
        $local = dr_dir_map(WEBPATH.'api/pay/', 1); // 支付接口
        if ($local) {
            foreach ($local as $mdir) {
                if (is_file(WEBPATH.'api/pay/'.$mdir.'/config.php')) {
                    $dir[WEBPATH.'api/pay/'.$mdir.'/config.php'] = '无法生成支付接口【'.$mdir.'】配置文件';
                }
            }
        }

		$str = '';
		foreach ($dir as $file => $note) {
            if (!$this->_check_write_able($file)) {
                $str.= $this->halt(str_replace(FCPATH, '/', $file).'无写入权限，'.$note, 0);
            }
            if (!file_exists($file)) {
                $str.= $this->halt(str_replace(FCPATH, '/', $file).'不存在，'.$note, 0);
            }
		}
		
		return $str;
	}
	
	/**
     * 栏目数量检查
     */
    private function _category() {
		$module = $this->get_module(SITE_ID);
		if ($module) {
			$string = '';
			foreach ($module as $t) {
				if (count($t['category']) > 50) {
					$string.= $this->halt("当前站点模块【{$t[name]}】的栏目超过了50个，内存消耗会比较多，栏目数量建议控制在50个以内", 0);
				}
			}
		}
		return $string;
	}

    /**
     * 域名绑定检测
     */
    private function _domain() {

        $ip = $this->_get_server_ip();
        $string = '';
        $domain = $name = array();
        $member = $this->get_cache('member');

        // 检测域名重复性和可用性
        foreach ($this->site_info as $sid => $site) {
            // 站点域名
            if ($site['SITE_DOMAIN']) {
                $name[] = '站点【'.$site['SITE_NAME'].'】主域名';
                $domain[] = $site['SITE_DOMAIN'];
            }
            // 站点其他域名
            if ($site['SITE_DOMAINS']) {
                $arr = @explode(',', $site['SITE_DOMAINS']);
                if ($arr) {
                    foreach ($arr as $a) {
                        if ($a) {
                            $name[] = '站点【'.$site['SITE_NAME'].'】其他域名';
                            $domain[] = $a;
                        }
                    }
                }
            }
            // 站点移动端域名
            if ($site['SITE_MOBILE']) {
                $name[] = '站点【'.$site['SITE_NAME'].'】移动端域名';
                $domain[] = $site['SITE_MOBILE'];
            }
            // 当前站点的所属模块
            $module = $this->get_module($sid);
            if ($module) {
                foreach ($module as $m) {
                    foreach ($m['site'] as $t) {
                        if ($t['domain']) {
                            $name[] = '模块【'.$m['name'].'】在站点【'.$site['SITE_NAME'].'】中的域名';
                            $domain[] = $t['domain'];
                        }
                    }
                }
                unset($module);
            }
            // 当前站点的会员中心域名
            if ($member['setting']['domain'][$sid]) {
                $name[] = '会员中心在站点【'.$site['SITE_NAME'].'】中的域名';
                $domain[] = $member['setting']['domain'][$sid];
            }

        }
        // 空间搜索页面的域名
        if ($member['setting']['space']['domain']) {
            $name[] = '空间搜索页面的域名';
            $domain[] = $member['setting']['space']['domain'];
        }
        // 个人空间二级域名
        if ($member['setting']['space']['spacedomain']) {
            $name[] = '个人空间二级域名处设置的域名';
            $domain[] = $member['setting']['space']['spacedomain'];
        }

        // 判断域名是否重复
        $repeat = @array_diff_assoc($domain, @array_unique($domain));
        if ($repeat) {
            foreach ($repeat as $i => $v) {
                foreach ($domain as $id => $d) {
                    if ($v == $d) {
                        $string.= $this->halt("域名【{$v}】与{$name[$id]}相同，请更换...", 0);
                    }
                }
            }
        }

        // 判断ip解析
        foreach ($domain as $ym) {
            if (gethostbyname($ym) != $ip) {
                $string.= $this->halt("请将域名【{$ym}】解析到【{$ip}】", 0);
            }
        }

        return $string;
    }
	
	/**
     * 风格与模板是否重名
     */
    private function _template_theme() {
		if (SITE_TEMPLATE == SITE_THEME) {
			return $this->halt('模板和风格目录同名可能导致模板被下载，建议模板和风格使用不相同的目录名称', 0);
		}
	}
	
	/**
     * Cookie安全码验证
     */
    private function _cookie_code() {
		if (SYS_KEY == 'finecms') {
			return $this->halt("请重新生成安全密钥，否则网站数据有被盗的风险，解决方案：系统-配置-生成密钥", 0);
		}
	}
	
	/**
     * allow_url_fopen
     */
    private function _url_fopen() {
		if (!ini_get('allow_url_fopen')) {
			return $this->halt('远程图片无法保存、网络图片无法上传、一键登录无法登录、无法访问云商店、无法使用微信。解决方案：在php.ini文件中allow_url_fopen设置为On', 0);
		}
	}
	
	/**
     * curl_init
     */
    private function _curl_init() {
		if (!function_exists('curl_init')) {
			return $this->halt('PHP不支持CURL扩展，一键登录可能无法登录、无法访问云商店、无法使用微信。解决方案：将php.ini中的;extension=php_curl.dll中的分号去掉', 0);
		}
	}

	/**
     * openssl_open
     */
    private function _openssl_open() {
		if (!function_exists('openssl_open')) {
			return $this->halt('PHP不支持openssl，一键登录可能无法登录、无法访问云商店、无法使用微信。解决方案：将php.ini中的;extension=php_openssl.dll中的分号去掉', 0);
		}
	}
	
	/**
     * fsockopen
     */
    private function _fsockopen() {
		if (!function_exists('fsockopen')) {
			return $this->halt('PHP不支持fsockopen，可能充值接口无法使用、手机短信无法发送、电子邮件无法发送、一键登录无法登录、无法访问云商店、无法使用微信', 0);
		}
	}
	
	/**
     * php
     */
    private function _php() {
	
		if (version_compare(PHP_VERSION, '5.2.8', '<')) {
			return $this->halt('您的当前PHP版本是'.PHP_VERSION.'，会导致某些功能无法正常使用，建议PHP版本在5.3.0以上，最低支持5.2.8', 0);
		}
		
		if (version_compare(PHP_VERSION, '5.3.0', '<')) {
			return $this->halt('您的当前PHP版本是'.PHP_VERSION.'，建议PHP版本在5.3.0以上，性能会大大提高', 1);
		}
	}
	
	/**
     * mysql
     */
    private function _mysql() {
		if ($this->db->dbdriver == 'mysql') {
			return $this->halt("建议将数据库驱动设置为 mysqli 或 pdo ，设置方式：config/database.php中的dbdriver选项", 1);
		}
	}


	/**
     * email
     */
    private function _email() {
		if (!$this->db->count_all_results($this->db->dbprefix('mail_smtp'))) {
			return $this->halt("邮件服务器尚未设置，可能系统无法发送邮件通知，设置方式：系统->系统功能->邮件系统->添加SMTP服务器", 0);
		}
	}
	
	/**
     * memcache
     */
    private function _memcache() {
	

	}
	
	/**
     * mcryp
     */
    private function _mcryp() {
		if (!function_exists('mcrypt_encrypt')) {
			return $this->halt('PHP未开启Mcrypt扩展，邮件验证无法使用、密码找回不能使用，文件上传安全系数降低', 0);
		}
	}

	/**
     * mcryp
     */
    private function _ctype() {
		if (!function_exists('ctype_alnum')) {
			return $this->halt('PHP未开启Ctype扩展，无法正常发布内容，并且系统安全系数降低', 0);
		}
	}
	
	/**
     * 表结构检测
     */
    private function _tableinfo() {
	
		$sql = "SHOW TABLE STATUS FROM `{$this->db->database}`";
		$table = $this->db->query($sql)->result_array();
		if (!$table) {
            return $this->halt("无法通过( $sql )获取到数据表结构，系统模块无法使用，解决方案：为Mysql账号开启SHOW TABLE STATUS权限", 0);
        }
		
		$sql = 'SHOW FULL COLUMNS FROM `'.$this->db->dbprefix('admin').'`';
		$field = $this->db->query($sql)->result_array();
		if (!$field) {
            return $this->halt("无法通过( $sql )获取到数据表字段结构，系统模块无法使用，解决方案：为Mysql账号开启SHOW FULL COLUMNS权限", 0);
        }
	}
	
	/**
     * 检测结果
     */
    private function _result() {
		return $this->halt('系统检查完成', 1);
	}
	
	/**
     * 消息提示
     */
	private function halt($msg, $status = 1) {
	
		return $status ? "<tr><td align=\"left\"><font color=green><img width=\"16\" src=\"".THEME_PATH."admin/images/ok.png\">&nbsp;&nbsp;".$msg."</font></td></tr>" : "<tr><td align=\"left\"><font color=red><img width=\"16\" src=\"".THEME_PATH."admin/images/b_drop.png\">&nbsp;&nbsp;".$msg."</font></td></tr>";
	}
}