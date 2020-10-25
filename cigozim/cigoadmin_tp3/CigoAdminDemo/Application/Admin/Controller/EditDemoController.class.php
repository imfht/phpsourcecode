<?php

namespace Admin\Controller;

use CigoAdminLib\Lib\Admin;
use CigoAdminLib\Lib\AdminDataMg;
use Think\Model;

class EditDemoController extends AdminDataMg {
	public function index() {
		$this->assign('args', array(
			'keyword' => isset($_GET['keyword']) ? $_GET['keyword'] : '',
			'status' => !isset($_GET['status']) ? '' : (in_array($_GET['status'], array('0', '1')) ? $_GET['status'] : '0'),
			'startDate' => isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d', 0),
			'endDate' => isset($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-d', time() + 24 * 3600),
			'orderBy' => isset($_GET['orderBy']) ? $_GET['orderBy'] : 'create_time',
			'order' => isset($_GET['order']) ? $_GET['order'] : '1'
		));
		$this->assign('label_title', '功能演示');
		$this->display();
	}

	public function getDataList() {
		if (!IS_POST) {
			$this->error('请求类型错误！');
		}
		$map = array();
		//判断关键词
		if (isset($_POST['keyword']) && !empty($_POST['keyword'])) {
			$map['title'] = array('like', '%' . $_POST['keyword'] . '%');
		}
		//判断状态
		if (isset($_POST['status'])) {
			if ($_POST['status'] === '0') {
				$map['status'] = array('eq', 0);
			} else if ($_POST['status'] === '1') {
				$map['status'] = array('eq', 1);
			}
		}
		//判断时间段
		if (
			isset($_POST['startDate']) && !empty($_POST['startDate']) &&
			isset($_POST['endDate']) && !empty($_POST['endDate'])
		) {
			$map['create_time'] = array('between', array(
				strtotime($_POST['startDate']), strtotime($_POST['endDate'])
			));
		}
		//判断排序
		$orderBy = '';
		if (
			isset($_POST['orderBy']) &&
			in_array(
				$_POST['orderBy'],
				array('id', 'create_time')
			) && isset($_POST['order'])
		) {
			$orderBy = $_POST['orderBy'] . ' ' . (($_POST['order'] === '0') ? 'asc' : 'desc');
		}

		$model = D('EditDemo');
		$dataList = $model->getList($map, $orderBy);
		if ($dataList) {
			foreach ($dataList['dataList'] as $index => $item) {
				$this->prepareDateToString($dataList['dataList'][$index], 'Y-m-d', 'build-date');
				$this->prepareDateToString($dataList['dataList'][$index], 'Y-m-d H:i', 'create_time');
			}
			$this->success($dataList, '', true);
		} else {
			$this->success(array(), '', true);
		}
	}

	public function setStatus() {
		$this->doSetStatus(M('EditDemo'), Admin::DATA_TYPE_EDIT_DEMO);
	}

	public function add() {
		$this->doAdd(D('EditDemo'));
	}

	protected function beforeAddDisplay($model) {
		$this->getRadioCheckboxOptionsConfig();
		$this->assign('label_class_list', json_encode($this->getLabelClassList()));
	}

	protected function beforeAdd($model, &$data, &$dataExtra) {
		$this->prepareMultiDataToJson($data, 'img-multi');
		!isset($data['checkbox_landscape']) ? $data['checkbox_landscape'] = '' : $this->prepareMultiDataToJson($data, 'checkbox_landscape');
		!isset($data['checkbox_portrait']) ? $data['checkbox_portrait'] = '' : $this->prepareMultiDataToJson($data, 'checkbox_portrait');
		!isset($data['img-show']) ? $data['img-show'] = '' : $this->prepareMultiDataToJson($data, 'img-show');

		$this->prepareDateToTimeStamp($data, 'build-date', true);
		$this->prepareDateToTimeStamp($data, 'create_time', true);
	}

	public function edit() {
		$this->doEdit(D('EditDemo'), Admin::DATA_TYPE_EDIT_DEMO);
	}

	protected function beforeEditDisplay($model, &$data) {
		$this->getRadioCheckboxOptionsConfig();
		$this->assign('label_class_list', json_encode($this->getLabelClassList()));
		$this->assign('img_list_multi_config', json_encode(array(
			'pc-list' => array('label' => 'PC列表图', 'width' => 200, 'height' => 250),
			'app-list' => array('label' => 'App列表图', 'width' => 200, 'height' => 200),
			'phone-list' => array('label' => 'Phone列表图', 'width' => 200, 'height' => 250)
		)));

		//多图
		$this->prepareMultiDataToArray($data, 'img-multi');
		foreach ($data['img-multi'] as $key => $item) {
			$data['img-multi'][$key] = array(
				'img-id' => $item,
				'img-src' => getUploadFilePath($item, 'path')
			);
		}
		$this->prepareMultiDataToJson($data, 'img-multi');
		//图片橱窗
		$this->prepareMultiDataToArray($data, 'img-show');
		foreach ($data['img-show'] as $key => $item) {
			$data['img-show'][$key] = array(
				'img-id' => $item,
				'img-src' => getUploadFilePath($item, 'path')
			);
		}
		$this->prepareMultiDataToJson($data, 'img-show');
		$this->prepareDateToString($data, 'Y-m-d', 'build-date');
		$this->prepareDateToString($data, 'Y-m-d H:i', 'create_time');
	}

	protected function beforeEdit($model, &$data, &$dataExtra) {
		$this->prepareMultiDataToJson($data, 'img-multi');
		!isset($data['checkbox_landscape']) ? $data['checkbox_landscape'] = '' : $this->prepareMultiDataToJson($data, 'checkbox_landscape');
		!isset($data['checkbox_portrait']) ? $data['checkbox_portrait'] = '' : $this->prepareMultiDataToJson($data, 'checkbox_portrait');
		!isset($data['img-show']) ? $data['img-show'] = '' : $this->prepareMultiDataToJson($data, 'img-show');

		$this->prepareDateToTimeStamp($data, 'build-date', true);
		$this->prepareDateToTimeStamp($data, 'create_time', true);
	}

	public function editValItem() {
		$model = M('EditDemo');
		$model->validate(array(
			array('sort', 'number', '排序必须为数字！', Model::VALUE_VALIDATE, '', Model::MODEL_BOTH)
		));
		$this->doEditValItem($model);
	}

	private function getRadioCheckboxOptionsConfig() {
		$modeList = array(
			array('id' => '0', 'text' => '多选、单选测试1'),
			array('id' => '1', 'text' => '多选、单选测试2'),
			array('id' => '2', 'text' => '多选、单选测试Disabled', 'disabled' => true)
		);
		$this->assign('radio_checkbox_options_list', json_encode($modeList));
	}

	private function getLabelClassList() {
		return array(
			array('id' => 'label-default', 'text' => 'Default-样式'),
			array('id' => 'label-primary', 'text' => 'Primary-样式'),
			array('id' => 'label-success', 'text' => 'Success-样式'),
			array('id' => 'label-info', 'text' => 'Info-样式'),
			array('id' => 'label-warning', 'text' => 'Warning-样式'),
			array('id' => 'label-danger', 'text' => 'Danger-样式')
		);
	}
}
