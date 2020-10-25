<?php

defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Admin_nav extends ADMIN_Controller {

	function __construct() {
		parent::__construct ();
		$this->load->model ( 'nav_model' );
	}

	function index($message = '') {
		if (empty ( $message ))
			unset ( $message );
		$navlist = $this->nav_model->get_list ( 0, 100 );
		include template ( 'navlist', 'admin' );
	}

	function add() {
		if (null!== $this->input->post ('submit') ) {
			$name = $this->input->post ('name');
			$title = $this->input->post ('title');
			$url = $this->input->post ('url');
			$target = $this->input->post ('target');
			$navtype = $this->input->post ('type');
			if (! $name || ! $url) {
				$type = 'errormsg';
				$message = '导航名称或导航地址不能为空!';
				include template ( 'addnav', 'admin' );
				exit ();
			}
			$this->nav_model->add ( $name, $url, $title, $target, 1, $navtype );
			$this->cache->remove ( 'nav' );
			$this->index ( '导航添加成功！' );
		} else {
			include template ( 'addnav', 'admin' );
		}
	}

	function edit() {
		if (null!==$this->input->post ('submit') ) {
			$name = $this->input->post ('name');
			$title = $this->input->post ('title');
			$url = $this->input->post ('url');
			$target = $this->input->post ('target');
			$navtype = $this->input->post ('type');
			$nid = intval ( $this->input->post ('nid') );
			if (! $name || ! $url) {
				$type = 'errormsg';
				$message = '导航名称或导航地址不能为空!';
				$curnav = $this->nav_model->get ( $nid );
				include template ( 'addnav', 'admin' );
				exit ();
			}
			$this->nav_model->update ( $name, $url, $title, $target, $navtype, intval ( $nid ) );
			$this->cache->remove ( 'nav' );
			$this->index ( '导航修改成功！' );
		} else {
			$curnav = $this->nav_model->get ( intval (  $this->uri->segment ( 3) ) );
			include template ( 'addnav', 'admin' );
		}
	}

	function remove() {
		$this->nav_model->remove_by_id ( intval (  $this->uri->segment ( 3) ) );
		$this->cache->remove ( 'nav' );
		$this->index ( '导航刪除成功！' );
	}

	function reorder() {
		$orders = explode ( ",", $this->input->post ('order') );
		$hid = intval ( $this->input->post ('hiddencid') );
		foreach ( $orders as $order => $lid ) {
			$this->nav_model->order_nav ( intval ( $lid ), $order );
		}
	}

	function available() {
		$available = intval (  $this->uri->segment ( 4 ) ) ? 0 : 1;
		$this->nav_model->update_available ( intval (  $this->uri->segment ( 3) ), $available );
		$this->cache->remove ( 'nav' );
		$message = $available ? '导航栏启用成功!' : '导航栏禁用成功!';
		$this->index ( $message );
	}

}

?>