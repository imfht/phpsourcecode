<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\controller\admin;

use app\model\Wechat as WechatM;
use app\model\WechatPay;

/**
 * @title 微信模块
 * @description 微信公众号、小程序管理
 */
class Wechat extends Base {

	/**
	 * @title 微信列表
	 * @author molong <molong@tensent.cn>
	 */
	public function index() {
		$map = [];

		$order = "id desc";
		//获取列表数据
		$list = WechatM::where($map)->order($order)->paginate($this->request->pageConfig)->append(['type_text']);

		$this->data = array(
			'list' => $list,
			'page' => '',
		);
		return $this->fetch();
	}

	/**
	 * @title 添加微信
	 * @author molong <molong@tensent.cn>
	 */
	public function add() {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$result = WechatM::create($data);
			if (false != $result) {
				return $this->success('添加成功！', url('/admin/wechat/index'));
			} else {
				return $this->error('添加失败！');
			}
		} else {
			$this->data = array(
				'keyList' => WechatM::$fieldlist
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 修改微信
	 * @author molong <molong@tensent.cn>
	 */
	public function edit($id = null) {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$result = WechatM::update($data, ['id' => $data['id']]);
			if ($result !== false) {
				return $this->success('编辑成功！', url('/admin/wechat/index'));
			} else {
				return $this->error('修改失败！');
			}
		} else {
			$info = WechatM::find($id);
			if (!$info) {
				return $this->error("非法操作！");
			}
			$this->data = array(
				'info' => $info,
				'keyList' => WechatM::$fieldlist
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 删除微信
	 * @author molong <molong@tensent.cn>
	 */
	public function delete() {
		$id = $this->request->param('id', '');

		$map = [];
		if (!$id) {
			return $this->error('请选择要操作的数据!');
		}
		if (is_array($id)) {
			$map[] = ['id', 'IN', $id];
		}else{
			$map[] = ['id', '=', $id];
		}

		$result = WechatM::where($map)->delete();
		if (false !== $result) {
			return $this->success('删除成功');
		} else {
			return $this->error('删除失败！');
		}
	}

	/**
	 * @title 微信支付
	 * @author molong <molong@tensent.cn>
	 */
	public function pay() {
		$map = [];

		$order = "id desc";
		//获取列表数据
		$list = WechatPay::where($map)->order($order)->paginate($this->request->pageConfig);

		$this->data = array(
			'list' => $list,
			'page' => '',
		);
		return $this->fetch();
	}

	/**
	 * @title 添加微信支付
	 * @author molong <molong@tensent.cn>
	 */
	public function addpay() {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$result = WechatPay::create($data);
			if (false != $result) {
				return $this->success('添加成功！', url('/admin/wechat/pay'));
			} else {
				return $this->error('添加失败！');
			}
		} else {
			$this->data = array(
				'keyList' => WechatPay::$fieldlist
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 修改微信支付
	 * @author molong <molong@tensent.cn>
	 */
	public function editpay($id = null) {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$result = WechatPay::update($data, ['id' => $data['id']]);
			if ($result !== false) {
				return $this->success('编辑成功！', url('/admin/wechat/pay'));
			} else {
				return $this->error('修改失败！');
			}
		} else {
			$info = WechatPay::find($id);
			if (!$info) {
				return $this->error("非法操作！");
			}
			$this->data = array(
				'info' => $info,
				'keyList' => WechatPay::$fieldlist
			);
			return $this->fetch('admin/public/edit');
		}
	}
}