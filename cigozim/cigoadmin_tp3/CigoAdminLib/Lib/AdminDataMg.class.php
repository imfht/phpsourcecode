<?php

namespace CigoAdminLib\Lib;

use Common\Lib\SessionCheck;

class AdminDataMg extends SessionCheck {

	private function moveToTrash($type = 0, $dataId = 0, $title = '') {
		if (empty($type) || empty($dataId) || empty($title)) {
			return;
		}

		$model = D('Trash');
		$data = $model->create(array(
			'data_id' => $dataId,
			'type' => $type,
			'title' => $title
		));
		if (!$data) {
			return;
		}

		$model->add($data);
	}

	private function setStatusTip() {
		$id = I('get.id');
		$status = I('get.status');
		if (!$id) {
			$this->error('参数错误!');
		}
		switch ($status) {
			case -1 :
				$tips = array(
					'success' => (I('get.ctrlTip') ? I('get.ctrlTip') : '删除') . '成功!',
					'error' => (I('get.ctrlTip') ? I('get.ctrlTip') : '删除') . '失败!'
				);
				break;
			case 0  :
				$tips = array(
					'success' => (I('get.ctrlTip') ? I('get.ctrlTip') : '禁用') . '成功!',
					'error' => (I('get.ctrlTip') ? I('get.ctrlTip') : '禁用') . '失败!'
				);
				break;
			case 1  :
				$tips = array(
					'success' => (I('get.ctrlTip') ? I('get.ctrlTip') : '启用') . '成功!',
					'error' => (I('get.ctrlTip') ? I('get.ctrlTip') : '启用') . '失败!'
				);
				break;
			default :
				$tips = array(
					'success' => (I('get.ctrlTip') ? I('get.ctrlTip') : '操作') . '成功!',
					'error' => (I('get.ctrlTip') ? I('get.ctrlTip') : '操作') . '失败!'
				);
				break;
		}

		return array(
			'id' => $id,
			'status' => $status,
			'tips' => $tips
		);
	}

	protected function doSetStatus($model, $dataType = 0) {
		if (!$model->create($_GET)) {
			$this->error($model->getError());
		}
		$tipData = $this->setStatusTip();
		$dataInfo = $model->where(array('id' => $tipData['id']))->find();
		if (!$dataInfo) {
			$this->error('数据不存在！');
		}
		//修改状态
		$key = I('get.key') ? I('get.key') : 'status';
		$res = $model->where(array('id' => $tipData['id']))->save(array($key => $tipData['status']));
		if (0 === $res) {
			$this->success('操作成功！');
		} else if (!$res) {
			$this->error($tipData['tips']['error']);
		}
		//修改成功
		if ($key == 'status' && -1 == $tipData['status'] && !empty($dataType)) {
			$this->moveToTrash($dataType, $tipData['id'], $dataInfo['title']);
		}
		//修改完毕
		$this->afterSetStatus($key, $tipData['status'], $dataInfo);
		$this->success($tipData['tips']['success']);
	}

	protected function afterSetStatus($key, $status, $dataInfo) {
	}

	protected function doAdd($model, $jumpTo = '', $tpl = '') {
		if (IS_POST) {
			$dataExtra = array();
			$this->beforeAddCreateData($_POST, $dataExtra);

			$data = $model->create($_POST);
			if (!$data) {
				$this->error($model->getError());
			}

			$this->beforeAdd($model, $data, $dataExtra);
			$insertId = $model->add($data);
			if (!$insertId) {
				$this->error($model->getError());
			}
			$this->afterAdd($model, $data, $dataExtra, $insertId);

			$this->success('添加成功!', (!empty($jumpTo) ? $jumpTo : U('index')));
		} else {
			$this->beforeAddDisplay($model);
			$this->display($tpl);
		}
	}

	protected function doEdit($model, $dataType = 0, $jumpTo = '', $tpl = '') {
		if (IS_POST) {
			$dataExtra = array();
			$this->beforeEditCreateData($_POST, $dataExtra);

			$data = $model->create($_POST);
			if (!$data) {
				$this->error($model->getError());
			}

			$this->beforeEdit($model, $data, $dataExtra);
			$res = $model->save($data);
			if (0 === $res) {
				$this->error('数据未做修改！');
			} else if (!$res) {
				$this->error($model->getError());
			}
			$this->afterEdit($model, $data, $dataExtra, $_POST['id']);

			//修改成功
			if (!empty($dataType))
				$this->deleteFromTrash($dataType, $data['id']);
			$this->success('修改成功!', (!empty($jumpTo) ? $jumpTo : U('index')));
		} else {
			$data = false;
			$this->getEditData($model, $data);
			if (!$data) {
				$this->error('数据不存在!');
			}
			$this->beforeEditDisplay($model, $data);

			$this->assign('data', $data);
			$this->display($tpl);
		}
	}

	protected function beforeAddDisplay($model) {
	}

	protected function beforeAddCreateData(&$data, &$dataExtra) {
	}

	protected function beforeAdd($model, &$data, &$dataExtra) {
	}

	protected function afterAdd($model, &$data, &$dataExtra, $id) {
	}

	protected function getEditData($model, &$data) {
		$id = I('get.id');
		if (!$id) {
			$this->error('参数错误!');
		}

		$map = array(
			'id' => array('eq', $id)
		);
		$data = $model->where($map)->find();
	}

	protected function beforeEditDisplay($model, &$data) {
	}

	protected function beforeEditCreateData(&$data, &$dataExtra) {

	}

	protected function beforeEdit($model, &$data, &$dataExtra) {
	}

	protected function afterEdit($model, &$data, &$dataExtra, $id) {
	}

	public function doEditValItem($model) {
		if (!IS_POST) {
			$this->error('请求失败！');
		}

		$data = $model->create($_POST);
		if (!$data) {
			$this->error($model->getError());
		}

		$this->doEditValItemBefore($model, $data);
		$res = $model->save($data);
		if (0 === $res) {
			$this->error('数据未做修改！');
		} else if (!$res) {
			$this->error($model->getError());
		}
		//修改成功
		$this->success('更新成功!');
	}

	protected function doEditValItemBefore($model, $data) {
	}
}
