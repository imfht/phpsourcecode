<?php

/**
 * 管理后台
 * 
 * @author ShuangYa
 * @package Blog
 * @category Controller
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

namespace blog\controller;
use \Sy;
use \sy\base\Controller;
use \sy\base\Router;
use \sy\lib\db\Mysql;
use \blog\libs\Common;
use \blog\model\Admin;
use \blog\model\Attachment as AttachmentModel;

class Attachment extends Controller {
	public function __construct() {
		if (!Admin::checkLogin()) {
			Admin::gotoLogin();
		}
		$this->assign('page', 'content');
	}
	/**
	 * 附件上传
	 */
	public function actionUpload() {
		Sy::setMimeType('json');
		//允许跨域调用
		header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET,POST,OPTIONS');
        header('Access-Control-Allow-Headers: Cache-Control,X-Requested-With,Content-Type');
		if (empty($_FILES)) {
			echo json_encode(['message' => i18n::get('$_FILES_is_empty')]);
			exit;
		}
		reset($_FILES);
		$f = current($_FILES);
		if (!AttachmentModel::check($f)) {
			echo json_encode(['message' => i18n::get('illegal_request')]);
			exit;
		}
		$attachmentType = Common::option('attachmentType');
		//生成目标名称
		$f_info = pathinfo($f['name']);
		$save = AttachmentModel::getName(Common::option('attachmentFormat'), $f_info['extension']);
		$remote_info = pathinfo($save);
		$r = AttachmentModel::add($f, $save);
		//本地备份
		if ($attachmentType !== 'local' && Common::option('attachmentBackup') == 1) {
			$local_name = AttachmentModel::getName(Common::option('attachmentBackupFormt'), $f_info['extension'], FALSE);
			$local_name = str_replace('{{file}}', $remote_info['basename'], $local_name);
			AttachmentModel::local($f['tmp_name'], $local_name);
		}
		if ($r !== FALSE) {
			list($id, $type, $url) = $r;
			echo json_encode(['id' => $id, 'type' => $type, 'url' => $url]);
		} else {
			echo json_encode(['message' => '上传文件失败']);
		}
	}
	/**
	 * base64方式上传图片
	 */
	public function actionUploadBase64() {
		$base64Data = $_POST['base64Data'];
		if (substr($base64Data, 0, 22) !== 'data:image/png;base64,') {
			echo json_encode(['message' => i18n::get('illegal_request')]);
		}
		$tmp_name = Common::getTempName();
		file_put_contents($tmp_name, base64_decode(substr($base64Data, 22)));
		$_FILES = [
			'img' => [
				'name' => 'a.png',
				'type' => Sy::getMimeType('png'),
				'size' => filesize($tmp_name),
				'tmp_name' => $tmp_name,
				'error' => UPLOAD_ERR_OK
			]
		];
		$this->actionAttachmentUpload();
		@unlink($tmp_name);
	}
	/**
	 * 修改附件信息
	 */
	public function actionEdit() {
		Sy::setMimeType('json');
		if (empty($_POST['name'])) {
			echo json_encode(['message' => '名称不能为空']);
			exit;
		}
		$type = AttachmentModel::setName($_POST['id'], $_POST['name']);
		echo json_encode(['success' => 1,'type' => $type]);
	}
	/**
	 * 附件管理
	 */
	public function actionManage() {
		Sy::setMimeType('html');
		$this->display('attachment/list');
	}
	/**
	 * 获取附件列表
	 */
	public function actionList() {
		Sy::setMimeType('json');
		$lastid = intval($_POST['id']);
		$sql = 'SELECT * FROM `#@__attachment` WHERE id < ? ORDER BY id DESC LIMIT 0,20';
		$list = Mysql::i()->query($sql, [$lastid]);
		end($list);
		$last = current($list);
		echo json_encode(['success' => 1, 'lastId' => $last['id'], 'list' => $list]);
	}
	/**
	 * 删除附件
	 */
	public function actionDel() {
		Sy::setMimeType('json');
		$r = AttachmentModel::del($_POST['id']);
		echo json_encode(['success' => ($r === TRUE ? 1 : 0)]);
	}
}
