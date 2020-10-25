<?php

namespace CigoAdminLib\Lib;

use CigoAdminLib\Lib\JPush\jpush\JPUSH;
use CigoAdminLib\Lib\JPush\jsms\JSMS;
use Think\Controller;

class CigoCommon extends Controller {

	//浏览器类型
	const CLIEANT_TYPE_PC = 0;
	const CLIEANT_TYPE_IPHONE = 1;
	const CLIEANT_TYPE_ANDROID = 2;

	//数据标识
	const DATA_TAG_STATUS = "status";
	const DATA_TAG_INFO = "info";

	private $jpush = null;
	private $jsms = null;

	protected function _initialize() {
		$this->checkSystemConfigFromDb();
		$this->trimRequestArgs();
	}

	protected function trimRequestArgs() {
		if (IS_POST) {
			$this->trimArgs($_POST);
		} else if (IS_GET) {
			$this->trimArgs($_GET);
		}
	}

	/**
	 * 对参数去空值
	 * @param $args
	 */
	protected function trimArgs(&$args) {
		if (is_array($args)) {
			foreach ($args as $key => $item) {
				if (is_string($item)) {
					$args[$key] = trim($item);
				}
				if (is_array($item)) {
					$this->trimArgs($args[$key]);
				}
			}
		}
	}

	private function checkSystemConfigFromDb() {
		/* 读取数据库中的配置 */
		$config = S(CigoGlobal::S_FLAG_SYSTEM_CONFIG_DATA);
		if (!$config) {
			$config = api('CigoAdminLib/Config/getCacheList');
			S(CigoGlobal::S_FLAG_SYSTEM_CONFIG_DATA, $config);
		}
		C($config); //添加配置
	}

	protected function initTmplParseString($enableThemeFlag = true) {
		C('TMPL_PARSE_STRING', array(
			'__CIGO_ADMIN__' => '/Public/CigoAdminPublic',
			'__STATIC__' => '/Public/static',
			'__COMMON__' => '/Public/Common',
			'__IMG__' => '/Public/' . MODULE_NAME . ($enableThemeFlag ? '/' . C('DEFAULT_THEME') : '') . '/img',
			'__CSS__' => '/Public/' . MODULE_NAME . ($enableThemeFlag ? '/' . C('DEFAULT_THEME') : '') . '/css',
			'__JS__' => '/Public/' . MODULE_NAME . ($enableThemeFlag ? '/' . C('DEFAULT_THEME') : '') . '/js'
		));
	}

	protected function getJpushClient() {
		return $this->jpush
			? $this->jpush
			: $this->jpush = new JPUSH(C('JPush')['app_key'], C('JPush')['master_secret']);
	}

	protected function getJsmsClient() {
		return $this->jsms
			? $this->jsms
			: $this->jsms = new JSMS(C('JPush')['app_key'], C('JPush')['master_secret'], ['disable_ssl' => true]);
	}

	public function checkSmsCode($msg_id = '', $code = '') {
		$response = $this->getJsmsClient()->checkCode($msg_id, $code);
		$res = array(
			CigoCommon::DATA_TAG_STATUS => true,
			CigoCommon::DATA_TAG_INFO => '验证成功！'
		);
		if (!$response['body']['is_valid']) {
			if ($response['body']['error']['code'] == 50011) {
				$res = array(
					CigoCommon::DATA_TAG_STATUS => false,
					CigoCommon::DATA_TAG_INFO => '验证码超时！'
				);
			} else {
				$res = array(
					CigoCommon::DATA_TAG_STATUS => false,
					CigoCommon::DATA_TAG_INFO => '验证码错误！'
				);
			}
		}

		return $res;
	}

	public function sendSmsCode($phone = '') {
		if (empty($phone)) {
			return array(
				CigoCommon::DATA_TAG_STATUS => false,
				CigoCommon::DATA_TAG_INFO => '手机号不能为空！'
			);
		}
		$response = $this->getJsmsClient()->sendCode($phone, C('JPUSH')['verifyCodeTplId']);
		if ($response) {
			return array(
				CigoCommon::DATA_TAG_STATUS => true,
				CigoCommon::DATA_TAG_INFO => array(
					'msg' => '发送成功！',
					'msgId' => $response['body']['msg_id']
				)
			);
		}

		return array(
			CigoCommon::DATA_TAG_STATUS => false,
			CigoCommon::DATA_TAG_INFO => '发送失败，请重新尝试！'
		);
	}

	protected function prepareDateToTimeStamp(&$data, $editKey = 'date', $autoDefault = false) {
		if (isset($data[$editKey]) && $data[$editKey] != '') {
			$data[$editKey] = strtotime($data[$editKey]);
		} else {
			if ($autoDefault) {
				$data[$editKey] = time();
			}
		}
	}

	protected function prepareDateToString(&$data, $dateTimeFormat = 'Y-m-d H:i', $editKey = 'date', $autoDefault = false) {
		if (isset($data[$editKey]) && $data[$editKey] != '') {
			$data[$editKey] = date($dateTimeFormat, $data[$editKey]);
		} else {
			if ($autoDefault) {
				$data[$editKey] = date($dateTimeFormat, time());
			}
		}
	}

	protected function prepareMultiDataToJson(&$data, $editKey = 'img', $removeEmptyFlag = true) {
		if (isset($data[$editKey]) && $data[$editKey] != '') {
			$tempKeyData = array();
			foreach ($data[$editKey] as $key => $item) {
				if ($item != '' || !$removeEmptyFlag) {
					$tempKeyData[$key] = $item;
				}
			}
			$data[$editKey] = json_encode($tempKeyData);
		}
	}

	protected function prepareMultiDataToArray(&$data, $editKey = 'img') {
		if (isset($data[$editKey]) && $data[$editKey] != '') {
			$data[$editKey] = json_decode($data[$editKey], true);
		}
	}


	/**
	 * 获取下级地址数组
	 * @param int $pid
	 */
	public function getRegionListByPid($pid = 0) {
		$regionList = M('region')->where(array('parent_id' => intval($pid)))
			->order('sort desc, id asc')
			->select();
		$this->success(
			$regionList ? $regionList : array(),
			"",
			true
		);
	}
}


