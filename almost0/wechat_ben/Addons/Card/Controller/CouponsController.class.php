<?php

namespace Addons\Card\Controller;

use Addons\Card\Controller\BaseController;

class CouponsController extends BaseController {
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'card_coupons' );
		parent::_initialize ();
	}
	function show() {
		$map ['token'] = get_token ();
		// $map ['addon_condition'] = 3;
		$list = M ( 'coupon' )->where ( $map )->order ( 'id desc' )->select ();
		
		$map ['uid'] = $this->mid;
		$map ['addon'] = 'Coupon';
		$code = M ( 'sn_code' )->where ( $map )->select ();
		foreach ( $code as $c ) {
			$arr [$c ['target_id']] = 1;
		}
		foreach ( $list as &$v ) {
			$v ['is_use'] = intval ( $arr [$v ['id']] );
		}
		$this->assign ( 'list', $list );
		
		$this->display ();
	}
	// 通用插件的列表模型
	public function lists() {
		$map ['token'] = get_token ();
		session ( 'common_condition', $map );
		
		parent::common_lists ( $this->model );
	}
	
	// 通用插件的编辑模型
	public function edit() {
		parent::common_edit ( $this->model );
	}
	
	// 通用插件的增加模型
	public function add() {
		parent::common_add ( $this->model );
	}
	
	// 通用插件的删除模型
	public function del() {
		parent::common_del ( $this->model );
	}
}
