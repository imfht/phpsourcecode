<?php

/**
 * ==========================================
 * Created by Pocket Knife Technology.
 * Author: ZhiHua_W <zhihua_wei@foxmail.com>
 * Date: 2016/11/21 0023
 * Time: 上午 10:03
 * Project: Pkadmin后台管理系统
 * Version: 1.0.0
 * Power: 后台管理员管理控制器
 * ==========================================
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Administrator extends Pkadmin_Controller {

	public function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
		$this -> load -> model('admingroup_model', 'ag');
	}

	/**
	 * 管理员用户管理首页
	 */
	public function index($offset = '') {
		$data = $this -> data;
		$keyword = $this -> input -> post('keyword');

		//配置分页信息
		$config['base_url'] = site_url('Pkadmin/Administrator/index/');
		$config['total_rows'] = $this -> ag -> get_administrator_count();
		$config['per_page'] = 20;

		//初始化分类页
		$this -> pagination -> initialize($config);
		//生成分页信息
		$data['pageinfo'] = $this -> pagination -> create_links();
		$data['administrator_list'] = $this -> ag -> get_administrator_list($keyword, $config['per_page'], $offset);
		$this -> load -> view('administrator.html', $data);
	}

	/**
	 * 删除管理员
	 */
	public function del($id) {
		$data = $this -> data;
		//超级管理员不允许删除
		if ($id == 1) {
			$error['msg'] = "超级管理员不允许删除！";
			$error['url'] = site_url("Pkadmin/Administrator/index");
			$error['wait'] = 3;
			$data['error'] = $error;
			$this -> load -> view('error.html', $data);
			return;
		}
		if ($this -> ag -> del_administrator($id)) {
			$this -> pk -> add_log('删除管理员用户，ID：' . $id, $this -> ADMINISTRSTORS['admin_id'], $this -> ADMINISTRSTORS['username']);
			$success['msg'] = "管理员用户删除成功！";
			$success['url'] = site_url("Pkadmin/Administrator/index");
			$success['wait'] = 3;
			$data['success'] = $success;
			$this -> load -> view('success.html', $data);
		} else {
			$error['msg'] = "管理员用户删除失败！";
			$error['url'] = site_url("Pkadmin/Administrator/index");
			$error['wait'] = 3;
			$data['error'] = $error;
			$this -> load -> view('error.html', $data);
		}
	}

	/**
	 * 编辑管理员
	 */
	public function edit($id) {
		$data = $this -> data;
		$data['auth_group'] = $this -> ag -> get_auth_group_list();
		$data['access'] = $this -> ag -> get_administrator_authgroup($id);
		$data['admin_info'] = $this -> ag -> get_administrator_info($id);
		$this -> load -> view('admin_edit.html', $data);
	}

	/**
	 * 新增管理员
	 */
	public function add() {
		$data = $this -> data;
		$data['auth_group'] = $this -> ag -> get_auth_group_list();
		$this -> load -> view('admin_add.html', $data);
	}

	/**
	 * 添加编辑保存管理员信息
	 */
	public function addeditadmininfo() {
		$data = $this -> data;
		$admin_id = $this -> input -> post('admin_id');
		if ($admin_id != 1) {
			$params['username'] = $this -> input -> post('username');
			$auth_group['group_id'] = $this -> input -> post('group_id');
		}
		$password = $this -> input -> post('password');
		if (!empty($password)) {
			$params['password'] = password($password);
		}

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
				$error['url'] = site_url("Pkadmin/Administrator/add");
				$error['wait'] = 3;
				$data['error'] = $error;
				$this -> load -> view('error.html', $data);
				return;
			}
		}

		//修改管理员用户信息
		if ($admin_id) {
			if ($this -> ag -> update_administrator($admin_id, $auth_group, $params)) {
				$this -> pk -> add_log('修改管理员信息，管理员ID：' . $admin_id, $this -> ADMINISTRSTORS['admin_id'], $this -> ADMINISTRSTORS['username']);
				$success['msg'] = "管理员信息修改成功！";
				$success['url'] = site_url("Pkadmin/Administrator/index");
				$success['wait'] = 3;
				$data['success'] = $success;
				$this -> load -> view('success.html', $data);
			} else {
				$error['msg'] = "管理员信息修改失败！";
				$error['url'] = site_url("Pkadmin/Administrator/index");
				$error['wait'] = 3;
				$data['error'] = $error;
				$this -> load -> view('error.html', $data);
			}
		} else {
			//添加管理员信息
			if ($this -> ag -> insert_administrator($params, $auth_group)) {
				$this -> pk -> add_log('添加管理员信息，管理员用户名：' . $params['username'], $this -> ADMINISTRSTORS['admin_id'], $this -> ADMINISTRSTORS['username']);
				$success['msg'] = "管理员信息添加成功！";
				$success['url'] = site_url("Pkadmin/Administrator/index");
				$success['wait'] = 3;
				$data['success'] = $success;
				$this -> load -> view('success.html', $data);
			} else {
				$error['msg'] = "管理员信息添加失败！";
				$error['url'] = site_url("Pkadmin/Administrator/index");
				$error['wait'] = 3;
				$data['error'] = $error;
				$this -> load -> view('error.html', $data);
			}
		}
	}

}
