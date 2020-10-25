<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');
	
/**
 * 后台Api调用类
 */
 
class Api extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    public function menu() {

        $url = urldecode(dr_safe_replace($this->input->get('v')));
        $arr = parse_url($url);
        $queryParts = explode('&', $arr['query']);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        // 基础uri
        $uri = ($params['s'] ? $params['s'].'/' : '').'admin/'.($params['c'] ? $params['c'] : 'home').'/'.($params['m'] ? $params['m'] : 'index');
        // 查询名称
        $menu = $this->db->select('name')->like('uri', $uri)->get('admin_menu')->row_array();
        $name = $menu ? $menu['name'] : '未知名称';
        // 替换URL

        $admin = $this->db->where('uid', $this->uid)->get('admin')->row_array();
        if ($admin) {
            $menu = dr_string2array($admin['usermenu']);
            foreach ($menu as $t) {
                $t['url'] == $url && exit('已经存在');
            }
            $menu[] = array(
                'name' => $name,
                'url' => $url,
            );
            $this->db->where('uid', $this->uid)->update(
                'admin', array(
                    'usermenu' => dr_array2string($menu)
                )
            );
            exit();
        }
        exit('稍后再试');
        // 当前用户的自定义菜单
        /*
        if ($mymenu == TRUE && $this->admin['usermenu']) {
            foreach ($this->admin['usermenu'] as $my) {
                $id ++;
                $ico = strpos($my['name'], 'icon-') ? ' ' : '<i class="iconm icon-th-large"></i> ';
                $string.= '<li tid="'.$ii.'" id="_MP_'.$id.'" class="dr_link nav-item"><a href="javascript:_MP(\''.$id.'\', \''.$my['url'].'\');">'.$ico.' <span class="title">'.$my['name'].'</span></a></li>';
            }
            $mymenu = FALSE;
        }*/

    }

    public function color() {

        $color = dr_safe_replace($this->input->get('v'));
        $this->db->where('uid', $this->uid)->update(
            'admin', array(
                'color' => $color
            )
        );
        exit(dr_json(1,'ok'));
    }

    /**
     * ajax文件上传
     *
     * @return void
     */
	public function ajax_upload() {
		
		$ext = $this->input->get('ext');
		$file = $this->input->get('file');
		$path = $this->input->get('path');
		$name = $this->input->get('name');
        
        !is_dir(FCPATH.$path) && exit(dr_json(0, '目录（'.$path.'）不存在'));
        
		$this->load->library('upload', array(
			'max_size' => 1024 * 1024,
			'overwrite' => TRUE,
			'file_name' => $file,
			'upload_path' => FCPATH.$path,
			'allowed_types' => $ext ? $ext : '*',
			'file_ext_tolower' => TRUE,
		));
		if ($this->upload->do_upload($name)) {
			$info = $this->upload->data();
			exit(dr_json(1, $info['orig_name']));
		} else {
			exit(dr_json(0, $this->upload->display_errors('', '')));
		}
	}
	
	
    /**
     * 文件上传
     *
     * @return void
     */
    public function upload() {
        $ext = 'jpg,gif,png,js,css,html,swf,zip';
        $this->template->assign(array(
            'ext' => str_replace(',', '|', $ext),
            'page' => 0,
            'size' => 1024 * 1024,
            'path' => $this->input->get('path'),
            'types' => '*.'.str_replace(',', ';*.', $ext),
            'fcount' => 50,
            'is_admin' => 1,
        ));
        $this->template->display('upload.html');
    }

    /**
     * 文件上传处理
     *
     * @return void
     */
    public function swfupload() {

        if (IS_POST) {
            $ext = 'jpg,gif,png,js,css,html,swf,zip';
            $path = $this->input->post('path');
            !is_dir($path) && exit('0,目录（'.$path.'）不存在');
            $this->load->library('upload', array(
                'max_size' => 1024 * 1024,
                'overwrite' => TRUE,
                'file_name' => '',
                'upload_path' => $path,
                'allowed_types' => str_replace(',', '|', $ext),
                'file_ext_tolower' => TRUE,
            ));
            if ($this->upload->do_upload('Filedata')) {
                $info = $this->upload->data();
                $_ext = str_replace('.', '', $info['file_ext']);
                $file = str_replace(WEBPATH, '', $info['full_path']);
                !is_file(WEBPATH.$file) && $file = THEME_PATH.'admin/images/ext/blank.gif';
                $icon = is_file(THEME_PATH.'admin/images/ext/'.$_ext.'.gif') ? THEME_PATH.'admin/images/ext/'.$_ext.'.gif' : THEME_PATH.'admin/images/ext/blank.gif';
                //唯一ID,文件全路径,图标,文件名称,文件大小,扩展名
                exit('1,'.$file.','.$icon.','.str_replace(array('|', '.'.$_ext), '', $info['client_name']).','.dr_format_file_size($info['file_size'] * 1024).','.$_ext);
            } else {
                exit('0,'.$this->upload->display_errors('', ''));
            }
        }
    }
	
	/**
     * 查看资料
     */
	public function member() {

        $uid = str_replace('author_', '', $this->input->get('uid'));
        ($uid == 'guest' || !$uid) && exit('<div style="padding-top:50px;color:blue;font-size:14px;text-align:center">'.fc_lang('游客').'</div>');
        
        $data = is_numeric($uid) ? $this->db->where('uid', (int)$uid)->limit(1)->get('member')->row_array() : $this->db->where('username', $uid)->limit(1)->get('member')->row_array();

        !$data && exit('(#'.$uid.')'.fc_lang('对不起，该会员不存在！'));

        $this->load->library('dip');
        $data['address'] = $this->dip->address($data['regip']);

		$this->template->assign(array(
			'data' => $data,
		));
		$this->template->display('member.html');
	}
	
	/**
     * 测试ftp链接状态
     */
	public function testftp() {
	
		$rurl = $this->input->get('rurl');
		$host = $this->input->get('host');
		$port = $this->input->get('port');
		$pasv = $this->input->get('pasv');
		$path = $this->input->get('path');
		$mode = $this->input->get('mode');
        $username = $this->input->get('username');
        $password = $this->input->get('password');

        (!$host || !$username || !$password) && exit(fc_lang('参数不完整'));

        !$rurl && exit(fc_lang('没有设置远程访问URL'));

		$this->load->library('ftp');
		if (!$this->ftp->connect(array(
			'hostname' => $host,
			'username' => $username,
			'password' => $password,
			'port' => $port ? $port : 21,
			'passive' => $pasv ? TRUE : FALSE,
			'debug' => FALSE
		))) {
            exit(fc_lang('Ftp服务器连接失败'));
        }

        !$this->ftp->upload(WEBPATH.'index.php', $path.'/test.ftp', $mode, 0775) && exit(fc_lang('Ftp服务器无上传权限'));

        strpos(dr_catcher_data($rurl.'/test.ftp'), 'dayrui.com') === FALSE && exit(fc_lang('远程服务器连接成功，但远程访问URL貌似没有正确'));

        !$this->ftp->delete_file($path.'/test.ftp') && exit(fc_lang('Ftp服务器无删除权限'));

		$this->ftp->close();
		
		exit('ok');
	}

    // 测试阿里云存储状态
    public function aliyuntest() {

        $id = $this->input->get('id');
        $host = $this->input->get('host');
        $rurl = $this->input->get('rurl');
        $secret = $this->input->get('secret');
        $bucket = $this->input->get('bucket');

        (!$id || !$host || !$secret || !$bucket) && exit(fc_lang('参数不完整'));

        !$rurl && exit(fc_lang('没有设置远程访问URL'));

        require_once FCPATH . 'dayrui/libraries/Remote/AliyunOSS/sdk.class.php';
        $oss = new ALIOSS($id, $secret, $host);
        $response = $oss->upload_file_by_file($bucket, 'test.txt', WEBPATH.'index.php');

        if ($response->status == 200) {
            if (strpos(dr_catcher_data($rurl.'/test.txt'), 'dayrui.com') === FALSE) {
                $oss->delete_object($bucket, 'test.txt');
                exit(fc_lang('远程服务器连接成功，但远程访问URL貌似没有正确').''.$rurl.'/test.txt');
            }
            exit('ok');
        } else {
            exit($response->body);
        }

    }

    public function login() {

        $uid = (int)$this->session->userdata('uid');
        $admin = (int)$this->session->userdata('admin');
        if ($this->uid == FALSE
            || $uid != $this->uid
            || $admin != $uid) {
            $this->template->display('login_ajax.html');
        } else {
            $data = $this->member_model->get_admin_member($this->uid, 1);
            if ($data) {
                @ob_clean();
                exit('this_finecms_test');
            } else {
                $this->template->display('login_ajax.html');
            }
        }

    }

    // 测试百度云存储状态
    public function baidutest() {

        $ak = $this->input->get('ak');
        $sk = $this->input->get('sk');
        $host = $this->input->get('host');
        $rurl = $this->input->get('rurl');
        $bucket = $this->input->get('bucket');

        (!$ak || !$host || !$sk || !$bucket) && exit(fc_lang('参数不完整'));

        !$rurl && exit(fc_lang('没有设置远程访问URL'));

        require_once FCPATH . 'dayrui/libraries/Remote/BaiduBCS/bcs.class.php';
        $bcs = new BaiduBCS($ak, $sk, $host);
        $opt = array();
        $opt['acl'] = BaiduBCS::BCS_SDK_ACL_TYPE_PUBLIC_WRITE;
        $opt['curlopts'] = array(CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 1800);
        $response = $bcs->create_object($bucket, '/test.txt', WEBPATH.'index.php', $opt);

        if ($response->status == 200) {
            if (strpos(dr_catcher_data($rurl.'/test.txt'), 'dayrui.com') === FALSE) {
                exit(fc_lang('远程服务器连接成功，但远程访问URL貌似没有正确'));
            }
            $bcs->delete_object($bucket, '/test.txt');
            exit('ok');
        } else {
            exit('error');
        }
    }

    // 测试百度云存储状态
    public function qcloudtest() {

        $id = $this->input->get('id');
        $app = $this->input->get('app');
        $key = $this->input->get('key');
        $rurl = $this->input->get('rurl');
        $bucket = $this->input->get('bucket');

        (!$id || !$key || !$bucket || !$app) && exit(fc_lang('参数不完整'));

        require_once FCPATH . 'dayrui/libraries/Remote/QcloudCOS/Conf.php';
        Conf::init($app, $id, $key);

        $result = Cosapi::upload(WEBPATH.'index.php', $bucket, '/test.txt', "biz_attr");
        if ($result['code'] == 0) {
            if (strpos(dr_catcher_data($rurl.'/test.txt'), 'dayrui.com') === FALSE) {
                exit(fc_lang('远程服务器连接成功，但远程访问URL貌似没有正确'));
            }
            Cosapi::del($bucket, '/test.txt');
            exit('ok');
        } else {
            exit($result['message']);
        }
    }


    // 图片剪切保存
    public function ajax_save_image() {

        $uid = $this->uid;
        // 附件上传时采用后台登陆会员
        $this->session->userdata('member_auth_uid') && $uid = $this->member_model->member_uid(1);

        $code = str_replace(' ', '+', $this->input->get('code'));
        list($size, $path) = explode('|', dr_authcode($code, 'DECODE'));
        !$size && exit('0|此字段没有设置文件大小');

        // 判断会员权限
        !$uid && exit('0|未登录无权限');

        if (isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
            $pic = $GLOBALS["HTTP_RAW_POST_DATA"];
            isset($_GET['width']) && !empty($_GET['width']) && $width = intval($_GET['width']);
            isset($_GET['height']) && !empty($_GET['height']) && $height = intval($_GET['height']);
            if (isset($_GET['image']) && !empty($_GET['image'])) {
                $id = (int)$this->input->get('id');
                if ($id && $info = get_attachment($id)) {
                    // 远程图片下载到本地缓存目录
                    if (isset($info['remote']) && $info['remote']) {
                        // 远程附件信息
                        $remote_cfg = $this->get_cache('attachment');
                        $config = $remote_cfg[SITE_ID]['data'][$info['remote']];
                        if ($config) {
                            // 保存临时文件
                            $file = WEBPATH.'cache/attach/crop_'.$id.'.'.$info['fileext'];
                            file_put_contents($file, $pic);
                            $this->load->model('attachment_model');
                            list($remote, $file) = $this->attachment_model->upload2($config, $file, $info);
                            exit($id.'|'.$file.'?'.SYS_TIME);
                        } else {
                            exit('0|系统异常，此远程附件配置不存在');
                        }
                    } else {
                        $file = SYS_UPLOAD_PATH.'/'.$info['attachment'];
                        // 覆盖原图片图片
                        file_put_contents($file, $pic);
                        // 更新图片信息
                        echo $id.'|'.SYS_ATTACHMENT_URL.$info['attachment'].'?'.SYS_TIME;exit;
                    }
                } else {
                    // 入库附件表
                    $path = $path ? '/'.$path.'/' : '/'.date('Ym', SYS_TIME).'/';
                    !is_dir(SYS_UPLOAD_PATH.$path) && dr_mkdirs(SYS_UPLOAD_PATH.$path);
                    $fileext = strtolower(trim(substr(strrchr($_GET['image'], '.'), 1, 10))); //扩展名
                    $filename = substr(md5(time()), 0, 7).rand(100, 999);
                    $this->load->model('attachment_model');
                    $id = $this->attachment_model->add_catcher($uid, $path.$filename.'.'.$fileext);
                    !$id && exit('0|文件入库失败，请重试');
                    $newfile = SYS_UPLOAD_PATH.$path.$filename.'.'.$fileext;
                    if (@file_put_contents($newfile, $pic)) {
                        $info = array(
                            'file_ext' => '.'.$fileext,
                            'full_path' => $newfile,
                            'file_size' => filesize($newfile)/1024,
                            'client_name' => basename($_GET['image']),
                        );
                        $result = $this->attachment_model->upload($uid, $info, $id);
                        if (is_array($result)) {
                            exit($id.'|'.dr_get_file($id));
                        } else {
                            @unlink($info['full_path']);
                            exit('0|'.$result);
                        }
                    } else {
                        exit('0|文件移动失败，目录无权限（'.$path.'）');
                    }
                }

            } else {
                exit('0|无图片');
            }
            exit;
        }

    }


    // 图片剪切
    public function ajax_edit_image() {

        $file = $this->input->get('file');

        // 是附件id时
        if (is_numeric($file) && $info = get_attachment($file)) {
            $image = isset($info['remote']) && $info['remote'] ? $info['attachment'] : SYS_ATTACHMENT_URL.$info['attachment'];
            unset($info);
        } else {
            $image = $file;
            $file = 0;
        }
        // 图片缓存本地

        $local = 'cache/attach/'.time().'_'.basename($image);
        file_put_contents(WEBPATH.$local, dr_catcher_data($image));
        $image = SITE_URL.$local;

        $upload = dr_url('api/ajax_save_image').'&id='.$file.'&image='.$image.'&code='.$this->input->get('code');

        $this->template->assign(array(
            'name' => $this->input->get('name'),
            'image' => $image,
            'upload' => $upload,
        ));
        $this->template->display('edit_image.html');
    }
}