<?php

/**
 * ==========================================
 * Created by Pocket Knife Technology.
 * Author: ZhiHua_W <zhihua_wei@foxmail.com>
 * Date: 2016/11/09 0017
 * Time: 上午 8:33
 * Project: Pkadmin后台管理系统
 * Version: 1.0.0
 * Power: 个人中心控制器
 * ==========================================
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Personal extends Pkadmin_Controller {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 个人资料
	 */
	public function index() {
		$data = $this -> data;
		$this -> load -> view('personal.html', $data);
	}

	/**
	 * 更新修改个人资料
	 */
	public function updateprofile() {
		$data = $this -> data;
		$params['sex'] = $this -> input -> post('sex');
		$params['birthday'] = $this -> input -> post('birthday');
		$params['phone'] = $this -> input -> post('mobile');
		$params['qq'] = $this -> input -> post('qq');
		$params['email'] = $this -> input -> post('email');

		//头像上传
		if (!empty($_FILES['head_pic']['tmp_name'])) {
			//配置上传参数
			$config['upload_path'] = './Data/upload/head_pic/' . date("Ym");
			//原图路径
			if (!file_exists($config['upload_path'])) {
				mkdir($config['upload_path'], 0777, true);
			}
			//大缩略图路径 140*140
			if (!file_exists($config['upload_path'] . '/head_pic_140_thumb')) {
				mkdir($config['upload_path'] . '/head_pic_140_thumb', 0777, true);
			}
			//小缩略图路径
			if (!file_exists($config['upload_path'] . '/head_pic_30_thumb')) {
				mkdir($config['upload_path'] . '/head_pic_30_thumb', 0777, true);
			}
			$config['allowed_types'] = 'gif|jpg|jpeg|png';
			$config['file_name'] = 'pkadmin_' . date("YmdHis") . random();
			$config['max_size'] = 2048;
			$this -> load -> library('upload', $config);
			if ($this -> upload -> do_upload('head_pic')) {
				$head_pic_info = $this -> upload -> data();
				//图像处理配置
				//载入图像处理类库
				$this -> load -> library("image_lib");
				//140*140图片
				//gd2图库
				$config_big_thumb['image_library'] = 'gd2';
				//原图
				$config_big_thumb['source_image'] = $head_pic_info['full_path'];
				//大缩略图
				$config_big_thumb['new_image'] = $config['upload_path'] . '/head_pic_140_thumb';
				//是否创建缩略图
				$config_big_thumb['create_thumb'] = true;
				$config_big_thumb['maintain_ratio'] = true;
				//缩略图宽度
				$config_big_thumb['width'] = 140;
				//缩略图的高度
				$config_big_thumb['height'] = 140;
				//缩略图名字后加上 "_140px",可以代表是一个140*140的缩略图
				$config_big_thumb['thumb_marker'] = "_140px";

				$config_small_thumb['image_library'] = 'gd2';
				$config_small_thumb['source_image'] = $head_pic_info['full_path'];
				$config_small_thumb['new_image'] = $config['upload_path'] . '/head_pic_30_thumb';
				$config_small_thumb['create_thumb'] = true;
				$config_small_thumb['maintain_ratio'] = true;
				$config_small_thumb['width'] = 30;
				$config_small_thumb['height'] = 30;
				$config_small_thumb['thumb_marker'] = "_30px";
				//生成big140缩略图
				$this -> image_lib -> initialize($config_big_thumb);
				$this -> image_lib -> resize();
				//生成small30缩略图
				$this -> image_lib -> initialize($config_small_thumb);
				$this -> image_lib -> resize();
				$pic_info = explode('.', $head_pic_info['file_name']);
				$ext = end($pic_info);
				$path_info = "Data/upload/head_pic/" . date("Ym") . "/";
				$head_pic_path['head_pic'] = $path_info . $head_pic_info['file_name'];
				$head_pic_path['head_pic_140_thump'] = $path_info . 'head_pic_140_thumb/' . $config['file_name'] . $config_big_thumb['thumb_marker'] . '.' . $ext;
				$head_pic_path['head_pic_30_thump'] = $path_info . 'head_pic_30_thumb/' . $config['file_name'] . $config_small_thumb['thumb_marker'] . '.' . $ext;
				$params['head_pic'] = serialize($head_pic_path);
			} else {
				$error['msg'] = $this -> upload -> display_errors();
				$error['url'] = site_url("Pkadmin/Personal/index");
				$error['wait'] = 3;
				$data['error'] = $error;
				$this -> load -> view('error.html', $data);
				return;
			}
		}

		if ($this -> pk -> set_admin_profile($this -> ADMINISTRSTORS['admin_id'], $params)) {
			$success['msg'] = "个人资料修改成功！";
			$success['url'] = site_url("Pkadmin/Personal/index");
			$success['wait'] = 3;
			$data['success'] = $success;
			$this -> load -> view('success.html', $data);
		} else {
			$error['msg'] = "系统繁忙，请稍后再试！";
			$error['url'] = site_url("Pkadmin/Personal/index");
			$error['wait'] = 3;
			$data['error'] = $error;
			$this -> load -> view('error.html', $data);
		}

	}

	/**
	 * 修改密码
	 */
	public function changepwd() {
		$data = $this -> data;
		if ($_POST) {
			$oldpwd = password($this -> input -> post('oldpwd'));
			$newpwd = password($this -> input -> post('password'));
			$confirm_password = password($this -> input -> post('confirm_password'));
			if ($newpwd === $confirm_password) {
				if ($this -> pk -> seach_admin_by($this -> ADMINISTRSTORS['admin_id'], $oldpwd)) {
					if ($this -> pk -> set_admin_password($this -> ADMINISTRSTORS['admin_id'], $newpwd)) {
						delete_cookie('auth');
						$success['msg'] = "密码修改成功，请重新登录！";
						$success['url'] = site_url("Pkadmin/Login/index");
						$success['wait'] = 3;
						$data['success'] = $success;
						$this -> load -> view('success.html', $data);
					} else {
						$error['msg'] = "系统繁忙，请稍后再试！";
						$error['url'] = site_url("Pkadmin/Personal/changepwd");
						$error['wait'] = 3;
						$data['error'] = $error;
						$this -> load -> view('error.html', $data);
					}
				} else {
					$error['msg'] = "您的旧密码输入错误，请重新输入！";
					$error['url'] = site_url("Pkadmin/Personal/changepwd");
					$error['wait'] = 3;
					$data['error'] = $error;
					$this -> load -> view('error.html', $data);
				}
			} else {
				$error['msg'] = "您的确认密码和新密码不一致，请重新输入！";
				$error['url'] = site_url("Pkadmin/Personal/changepwd");
				$error['wait'] = 3;
				$data['error'] = $error;
				$this -> load -> view('error.html', $data);
			}
		} else {
			$this -> load -> view('changepwd.html', $data);
		}
	}

}
