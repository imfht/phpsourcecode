<?php

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class AdController extends AdminbaseController {

	protected $ad_obj;

	function _initialize() {
		parent::_initialize();
		$this->ad_obj = D("Ad");
	}

	function index() {
		$ads = $this->ad_obj->where("status!=0")->select();
		$this->assign("ads", $ads);
		$this->display();
	}

	function add() {
		if (IS_POST) {
			if ($this->ad_obj->create()) {
				if ($this->ad_obj->add()) {
					$this->success("添加成功！", U("ad/index"));
				} else {
					$this->error("添加失败！");
				}
			} else {
				$this->error($this->ad_obj->getError());
			}
		} else {
			$this->display();
		}
	}

	function edit() {
		if (IS_POST) {
			if ($this->ad_obj->create()) {
				if (false !== $this->ad_obj->save()) {
					$this->success("保存成功！", U("ad/index"));
				} else {
					$this->error("保存失败！");
				}
			} else {
				$this->error($this->ad_obj->getError());
			}
		} else {
			$id = I("get.id");
			$ad = $this->ad_obj->where("ad_id=$id")->find();
			$this->assign($ad);
			$this->display();
		}
	}

	/**
	 *  删除
	 */
	function delete() {
		$id = (int) I("get.id");
		$data['status'] = 0;
		$data['ad_id'] = $id;
		if ($this->ad_obj->save($data)) {
			$this->success("删除成功！");
		} else {
			$this->error("删除失败！");
		}
	}

}
