<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');
	
 /* v3.1.0  */
 
class Api extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 二维码
     */
    public function qrcode() {

        // 输出图片
        ob_clean();
        header("Content-type: image/png");
        ImagePng($this->get_qrcode($this->input->get('text'), $this->input->get('uid'), $this->input->get('level'), $this->input->get('size')));
        exit;
    }

    /**
     * 会员登录信息JS调用
     */
    public function member() {
        ob_start();
        $this->template->display('member.html');
        $html = ob_get_contents();
        ob_clean();
		$format = $this->input->get('format');
		// 页面输出
		if ($format == 'jsonp') {
			$data = $this->callback_json(array('html' => $html));
			echo $this->input->get('callback', TRUE).'('.$data.')';
		} elseif ($format == 'json') {
			echo $this->callback_json(array('html' => $html));
		} else {
			echo 'document.write("'.addslashes(str_replace(array("\r", "\n", "\t", chr(13)), array('', '', '', ''), $html)).'");';
		}
        exit;
    }

    /**
     * 保存浏览器定位坐标
     */
    public function position() {

        $value = dr_safe_replace($this->input->get('value', true));
        $cookie = get_cookie('my_position');
        if ($cookie != $value) {
            set_cookie('my_position', $value, 999999);
            exit('ok');
        }
        exit('none');
    }

    /**
     * 保存浏览器定位城市
     */
    public function city() {

        $value = dr_safe_replace(str_replace(array('自治区', '自治县', '自治州', '市','县', '州'), '', $this->input->get('value', true)));
        $cookie = get_cookie('my_city');
        if ($cookie != $value) {
            set_cookie('my_city', $value, 999999);
            exit('ok');
        }
        exit('none');
    }


    /**
     * 广告访问
     */
    public function poster_show() {

        if (!dr_is_app('adm')) {
            $this->msg('广告插件未安装');
        }

        $id = (int)$this->input->get('id');
        $data = $this->db->where('id', $id)->get(SITE_ID.'_poster')->row_array();
        if ($data) {
            $value = dr_string2array($data['value']);
            if ($value['url']) {
                $this->db->where('id', $id)->update(SITE_ID.'_poster', array(
                    'clicks' => $data['clicks'] + 1
                ));
                redirect($value['url'], 'refresh');
            } else {
                $this->msg('此广告没有链接地址');
            }
        } else {
            $this->msg('广告信息不存在或者已过期');
        }

    }
    /**
     * 广告调用
     */
    public function poster() {

        $id = (int)$this->input->get('id');
        $html = dr_poster($id);
        $html = addslashes(str_replace(array("\r", "\n", "\t", chr(13)), array('', '', '', ''), $html));
        echo 'document.write("'.$html.'");';

    }


    /**
     * ck播放器接口
     */
    public function ckplayer() {

        $at = $this->input->get('at');
        //$form = $this->input->get('form');
        $data = array(
            'text' => '',
            'api_url' => SITE_URL.'index.php?c=api&m=ckplayer',
            'server_url' => SITE_URL.'api/ckplayer/',
        );

        // 功能部分
        switch($at) {
            case 'js' : // 动态加载js
                //
                $text = $this->get_cache('poster-text-'.SITE_ID);
                if ($text && dr_is_app('adm')) {
                    // 文字滚动广告
                    $this->load->add_package_path(FCPATH.'app/adm/');
                    $this->load->model('poster_model');
                    $poster = $this->poster_model->poster($text);
                    if ($poster) {
                        $url = $this->poster_model->get_url($poster['id']);
                        $value = dr_string2array($poster['value']);
                        if ($value) {
                            $data['text'] = '{a href="'.$url.'" target="_blank"}{font color="'.$value['color'].'" size="12"}'.($value['text'] ? dr_clearhtml($value['text']) : '没有输入广告内容').'{/font}{/a}';
                        }
                    }
                }
                $code = file_get_contents(WEBPATH.'api/ckplayer/config/config.js');
                break;
            case 'share' : // 分享
                header('Content-Type: text/xml');
                $code = file_get_contents(WEBPATH.'api/ckplayer/config/share.xml');
                break;
        }

        // 兼容php5.5
        if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
            $rep = new php5replace($data);
            $code = preg_replace_callback('#{([a-z_0-9]+)}#U', array($rep, 'php55_replace_data'), $code);
            unset($rep);
        } else {
            extract($data);
            $code = preg_replace('#{([a-z_0-9]+)}#Ue', "\$\\1", $code);
        }

        exit($code);

    }

    /**
     * 会员登录信息JS调用
     */
    public function userinfo() {
        ob_start();
        $this->template->display('api.html');
        $html = ob_get_contents();
        ob_clean();
        $html = addslashes(str_replace(array("\r", "\n", "\t", chr(13)), array('', '', '', ''), $html));
        echo 'document.write("'.$html.'");';
    }

    /**
     * 自定义信息JS调用
     */
    public function template() {
        $this->api_template();
    }

    /**
     * ajax 动态调用
     */
    public function html() {

        ob_start();
        $this->template->cron = 0;
        $_GET['page'] = max(1, (int)$this->input->get('page'));
        $params = dr_string2array(urldecode($this->input->get('params')));
        $params['get'] = @json_decode(urldecode($this->input->get('get')), TRUE);
        $this->template->assign($params);
        $name = str_replace(array('\\', '/', '..', '<', '>'), '', dr_safe_replace($this->input->get('name', TRUE)));
        $this->template->display(strpos($name, '.html') ? $name : $name.'.html');
        $html = ob_get_contents();
        ob_clean();

        // 页面输出
        $format = $this->input->get('format');
        if ($format == 'html') {
            exit($html);
        } elseif ($format == 'json') {
            echo $this->callback_json(array('html' => $html));
        } elseif ($format == 'js') {
            echo 'document.write("'.addslashes(str_replace(array("\r", "\n", "\t", chr(13)), array('', '', '', ''), $html)).'");';
        } else {
            $data = $this->callback_json(array('html' => $html));
            echo $this->input->get('callback', TRUE).'('.$data.')';
        }
    }

    /**
	 * 更新浏览数
	 */
	public function hits() {
	
	    $id = (int)$this->input->get('id');
	    $dir = dr_safe_replace($this->input->get('module', TRUE));
        $mod = $this->get_cache('module-'.SITE_ID.'-'.$dir);
        if (!$mod) {
            $data = $this->callback_json(array('html' => 0));
            echo $this->input->get('callback', TRUE).'('.$data.')';exit;
        }

        // 兼容性验证时，尝试创建新的统计表
        if (SYS_UPDATE) {
            $this->load->model('module_model');
            $this->db->query(trim(str_replace('{tablename}', $this->db->dbprefix(SITE_ID.'_'.$dir.'_hits'), $this->module_model->system_table['hits'])));
        }

        // 获取主表时间段
        $data = $this->db
                     ->where('id', $id)
                     ->select('hits,updatetime')
                     ->get($this->db->dbprefix(SITE_ID.'_'.$dir))
                     ->row_array();
        $hits = (int)$data['hits'] + 1;

        // 更新主表
		$this->db->where('id', $id)->update(SITE_ID.'_'.$dir, array('hits' => $hits));
		if ($mod['share']) {
            $this->db->where('id', $id)->update(SITE_ID.'_share', array('hits' => $hits));
        }

        // 获取统计数据
        $total = $this->db->where('id', $id)->get($this->db->dbprefix(SITE_ID.'_'.$dir.'_hits'))->row_array();
        if (!$total) {
            $total['day_hits'] = $total['week_hits'] = $total['month_hits'] = $total['year_hits'] = 1;
        }

        // 更新到统计表
        $this->db->replace($this->db->dbprefix(SITE_ID.'_'.$dir.'_hits'), array(
            'id' => $id,
            'hits' => $hits,
            'day_hits' => (date('Ymd', $data['updatetime']) == date('Ymd', SYS_TIME)) ? $hits : 1,
            'week_hits' => (date('YW', $data['updatetime']) == date('YW', SYS_TIME)) ? ($total['week_hits'] + 1) : 1,
            'month_hits' => (date('Ym', $data['updatetime']) == date('Ym', SYS_TIME)) ? ($total['month_hits'] + 1) : 1,
            'year_hits' => (date('Ymd', $data['updatetime']) == date('Ymd', strtotime('-1 day'))) ? $hits : $total['year_hits'],
        ));

        // 点击时的钩子
        $this->hooks->call_hook('module_hits', array(
            'id' => $id,
            'dir' => $dir,
        ));
        // 输出数据
        echo $this->input->get('callback', TRUE).'('.$this->callback_json(array('html' => $hits)).')';exit;
	}

    /**
	 * 更新扩展的浏览数
	 */
	public function ehits() {

	    $id = (int)$this->input->get('id');
	    $dir = $this->input->get('module', TRUE);
        $mod = $this->get_cache('module-'.SITE_ID.'-'.$dir);
        if (!$mod) {
            $data = $this->callback_json(array('html' => 0));
            echo $this->input->get('callback', TRUE).'('.$data.')';exit;
        }

        $name = 'ehits'.$dir.SITE_ID.$id;
        $hits = (int)$this->get_cache_data($name);
		if (!$hits) {
			$data = $this->db->where('id', $id)->select('hits')->get(SITE_ID.'_'.$dir.'_extend')->row_array();
			$hits = (int)$data['hits'];
		}

		$hits++;
		$this->set_cache_data($name, $hits, (int)SYS_CACHE_MSHOW);

		$this->db->where('id', $id)->update(SITE_ID.'_'.$dir.'_extend', array('hits' => $hits));
        if ($mod['share']) {
            $this->db->where('id', $id)->update(SITE_ID.'_'.$dir.'_extend', array('hits' => $hits));
        }
        // 点击时的钩子
        $this->hooks->call_hook('extend_hits', array(
            'id' => $id,
            'dir' => $dir,
        ));
        $data = $this->callback_json(array('html' => $hits));
        echo $this->input->get('callback', TRUE).'('.$data.')';exit;
	}
	
	/**
	 * 发送桌面快捷方式
	 */
	public function desktop() {
		
		$site = (int)$this->input->get('site');
		$module = $this->input->get('module');
		
		if ($site && !$module) {
			$url = $this->site_info[$site]['SITE_URL'];
			$name = $this->site_info[$site]['SITE_NAME'].'.url';
		} elseif ($site && $module) {
			$mod = $this->get_cache('module-'.$site.'-'.$module);
			$url = $mod['url'];
			$name = $mod['name'].'.url';
		}  else {
			$url = $this->site_info[SITE_ID]['SITE_URL'];
			$name = $this->site_info[SITE_ID]['SITE_NAME'].'.url';
		}
		
		$data = "
		[InternetShortcut]
		URL={$url}
		IconFile={$url}favicon.ico
		Prop3=19,2
		IconIndex=1
		";
		$mime = 'application/octet-stream';
		
		header('Content-Type: "' . $mime . '"');
		header('Content-Disposition: attachment; filename="' . $name . '"');
		header("Content-Transfer-Encoding: binary");
		header('Expires: 0');
		header('Pragma: no-cache');
		header("Content-Length: " . strlen($data));
		echo $data;
	}
	
	/**
	 * 伪静态测试
	 */
	public function test() {
		header('Content-Type: text/html; charset=utf-8');
		echo '服务器支持伪静态';
	}
	
	/**
	 * 自定义数据调用（老版本）
	 */
	public function data() {
		exit('此方法已失效，请使用m=data2');
	}
	
	/**
	 * 自定义数据调用（新版本）
	 */
	public function data2() {

        $data = array();

        // 来路认证
        if (defined('SYS_REFERER') && strlen(SYS_REFERER)) {
            $http = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $_GET['http_referer'];
            if (empty($http)) {
                $data = array('msg' => '来路认证失败（NULL）', 'code' => 0);
            } elseif (strpos($http, SYS_REFERER) === FALSE) {
                $data = array('msg' => '来路认证失败（非法请求）', 'code' => 0);
            }
        }

        if (!$data) {
            // 安全码认证
            $auth = $this->input->get('auth');
            if ($auth != md5(SYS_KEY)) {
                // 授权认证码不正确
                $data = array('msg' => '授权认证码不正确', 'code' => 0);
            } else {
                // 解析数据
                $param = $this->input->get('param');
                if ($param == 'login') {
                    // 登录认证
                    $code = $this->member_model->login(
                        $this->input->get('username'),
                        $this->input->get('password'),
                        0, 1);
                    if (is_array($code)) {
                        $data = array(
                            'msg' => 'ok',
                            'code' => 1,
                            'return' => $this->member_model->get_member($code['uid'])
                        );
                    } elseif ($code == -1) {
                        $data = array('msg' => fc_lang('会员不存在'), 'code' => 0);
                    } elseif ($code == -2) {
                        $data = array('msg' => fc_lang('密码不正确'), 'code' => 0);
                    } elseif ($code == -3) {
                        $data = array('msg' => fc_lang('Ucenter注册失败'), 'code' => 0);
                    } elseif ($code == -4) {
                        $data = array('msg' => fc_lang('Ucenter：会员名称不合法'), 'code' => 0);
                    }
                } elseif ($param == 'update_avatar') {
                    // 更新头像
                    $uid = (int)$_REQUEST['uid'];
                    $file = $_REQUEST['file'];
                    //
                    // 创建图片存储文件夹
                    $dir = SYS_UPLOAD_PATH.'/member/'.$uid.'/';
                    @dr_dir_delete($dir);
                    if (!is_dir($dir)) {
                        dr_mkdirs($dir);
                    }
                    $file = str_replace(' ', '+', $file);
                    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $file, $result)){
                        $new_file = $dir.'0x0.'.$result[2];
                        if (!@file_put_contents($new_file, base64_decode(str_replace($result[1], '', $file)))) {
                            $data = array(
                                'msg' => '目录权限不足或磁盘已满',
                                'code' => 0
                            );
                        } else {
                            $this->load->library('image_lib');
                            $config['create_thumb'] = TRUE;
                            $config['thumb_marker'] = '';
                            $config['maintain_ratio'] = FALSE;
                            $config['source_image'] = $new_file;
                            foreach (array(30, 45, 90, 180) as $a) {
                                $config['width'] = $config['height'] = $a;
                                $config['new_image'] = $dir.$a.'x'.$a.'.'.$result[2];
                                $this->image_lib->initialize($config);
                                if (!$this->image_lib->resize()) {
                                    $data = array(
                                        'msg' => $this->image_lib->display_errors(),
                                        'code' => 0
                                    );
                                    break;
                                }
                            }
                            list($width, $height, $type, $attr) = getimagesize($dir.'45x45.'.$result[2]);
                            if (!$type) {
                                $data = array(
                                    'msg' => '错误的文件格式，请传输图片的字符',
                                    'code' => 0
                                );
                            }
                        }
                    } else {
                        $data = array(
                            'msg' => '图片字符串不规范，请使用base64格式',
                            'code' => 0
                        );
                    }

                    // 更新头像
                    if (!isset($data['code'])){
                        $data = array(
                            'code' => 1,
                            'msg' => '更新成功'
                        );
                        $this->db->where('uid', $uid)->update('member', array('avatar' => $uid));
                    }
                } elseif ($param == 'upload') {
                    // 文件上传接口
                    if (!isset($_FILES['file']['name'])) {
                        if (!$_FILES) {
                            $data = array('msg' => '不是正确的文件上传请求，请检查请求参数是否正确', 'code' => 0);
                        } else {
                            $data = array('msg' => 'file值不存在，请检查请求参数是否正确', 'code' => 0);
                        }
                    } else {
                        $uid = (int)$this->input->get('uid');
                        $member = $this->member_model->get_base_member($uid);
                        if ($member) {
                            $ext = $this->input->get('ext');
                            if (!$ext) {
                                $data = array('msg' => '文件扩展名不存在', 'code' => 0);
                            } else {
                                // 开始上传处理
                                $dir = SYS_UPLOAD_PATH.'/';
                                $path = 'app/'.date('Ym', SYS_TIME);
                                $name = substr(md5('app'.$uid.SYS_TIME.rand(0, 9999)), rand(0, 10), 12);
                                if (!is_dir($dir.$path.'/')) {
                                    mkdir($dir.$path.'/');
                                }
                                if (!is_dir($dir.$path.'/')) {
                                    $data = array('msg' => '服务器无法创建上传目录：'.$dir.$path.'/', 'code' => 0);
                                } else {
                                    $path = $path.'/'.$name.'.'.$ext;
                                    if (!move_uploaded_file($_FILES['file']['tmp_name'], $dir.$path)) {
                                        $data = array('msg' => '文件'.$dir.$path.'创建失败', 'code' => 0);
                                    } else {
                                        // 判断是否是正确的图片
                                        if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif'))) {
                                            $img = getimagesize($dir.$path);
                                            if (!$img) {
                                                $data = array('msg' => '上传文件不是一个正确的图片（'.$ext.'）', 'code' => 0);
                                                @unlink($dir.$path);
                                            }
                                        }
                                        if (!$data) {
                                            // 入库附件
                                            $file = file_get_contents($dir.$path);
                                            $this->db->replace('attachment', array(
                                                'uid' => $uid,
                                                'siteid' => 1,
                                                'author' => $member['username'],
                                                'tableid' => 0,
                                                'related' => '',
                                                'fileext' => $ext,
                                                'filemd5' => $file ? md5($file) : 0,
                                                'download' => 0,
                                                'filesize' => strlen($file),
                                            ));
                                            $id = $this->db->insert_id();
                                            // 增加至未使用附件表
                                            $this->db->replace('attachment_unused', array(
                                                'id' => $id,
                                                'uid' => $uid,
                                                'siteid' => 1,
                                                'author' => $member['username'],
                                                'remote' => 0,
                                                'fileext' => $ext,
                                                'filename' => $name,
                                                'filesize' => strlen($file),
                                                'inputtime' => SYS_TIME,
                                                'attachment' => $path,
                                                'attachinfo' => '',
                                            ));
                                            $data = array(
                                                'id' => $id,
                                                'url' => SYS_ATTACHMENT_URL.$path,
                                                'code' => 1,
                                                'msg' => '上传成功',
                                            );
                                        } else {
                                            $data = array('msg' => '入库失败', 'code' => 0);
                                            @unlink($dir.$path);
                                        }
                                    }
                                }
                            }
                        } else {
                            $data = array('msg' => '会员不存在', 'code' => 0);
                        }
                    }
                } elseif ($param == 'function') {
                    // 执行函数
                    $name = $this->input->get('name', true);
                    if (function_exists($name)) {
                        $_param = array();
                        $_getall = $this->input->get(null, true);
                        if ($_getall) {
                            for ($i=1; $i<=10; $i++) {
                                if (isset($_getall['p'.$i])) {
                                    $_param[] = $_getall['p'.$i];
                                } else {
                                    break;
                                }
                            }
                        }
                        $data = array('msg' => '', 'code' => 1, 'result' => call_user_func_array($name, $_param));
                    } else {
                        $data = array('msg' => '函数 （'.$name.'）不存在', 'code' => 0);
                    }
                } elseif ($param == 'get_file') {
                    // 获取文件地址
                    $info = get_attachment((int)$this->input->get('id'));
                    if (!$info) {
                        $data = array('msg' => fc_lang('附件不存在或者已经被删除'), 'code' => 0, 'url' => '');
                    } else {
                        $data = array('msg' => '', 'code' => 1, 'url' => dr_get_file($info['attachment']));
                    }
                } else {
                    // list数据查询
                    $data = $this->template->list_tag($param);
                    $data['code'] = $data['error'] ? 0 : 1;
                    unset($data['sql'], $data['pages']);
                }
            }
        }

		// 接收参数
		$format = $this->input->get('format');
		$function = $this->input->get('function');
        if ($function) {
            if (!function_exists($function)) {
                $data = array('msg' => fc_lang('自定义函数'.$function.'不存在'), 'code' => 0);
            } else {
                $data = $function($data);
            }
        }
		// 页面输出
		if ($format == 'php') {
			print_r($data);
		} elseif ($format == 'jsonp') {
			// 自定义返回名称
			echo $this->input->get('callback', TRUE).'('.$this->callback_json($data).')';
		} else {
			// 自定义返回名称
			echo $this->callback_json($data);
		}
		exit;
	}

    /**
     * 站点间的同步登录
     */
    public function synlogin() {
        $this->api_synlogin();
    }

    /**
     * 站点间的同步退出
     */
    public function synlogout() {
        $this->api_synlogout();
    }
}
