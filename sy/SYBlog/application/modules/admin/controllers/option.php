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
use \blog\libs\Common;
use \blog\model\Admin;
use \blog\model\Attachment as AttachmentModel;

class Option extends Controller {
	public function __construct() {
		if (!Admin::checkLogin()) {
			Admin::gotoLogin();
		}
		$this->assign('page', 'option');
	}
	/**
	 * 选项
	 */
	public function actionBasic() {
		Sy::setMimeType('html');
		$this->display('option/basic');
	}
	/**
	 * 保存选项
	 */
	public function actionSave() {
		foreach ($_POST as $k => $v) {
			if (substr($k, 0, 3) === 'op_') {
				$k = substr($k, 3);
				if ($k === 'pagesize') {
					$v = intval($v);
				}
				Common::option($k, $v);
			}
		}
		echo json_encode(['success' => 1]);
	}
	/**
	 * 修改密码
	 */
	public function actionPassword() {
		if (isset($_POST['ajax'])) {
			Sy::setMimeType('json');
			$old = $_POST['old'];
			$new = $_POST['new'];
			if (strlen($new) < 5) {
				echo json_encode(['success' => 0, 'message' => i18n::get('password_mustnt_less_than_5_char')]);
				exit;
			}
			if (Security::password($old) !== $this->password) {
				echo json_encode(['success' => 0, 'message' => i18n::get('old_password_wrong')]);
				exit;
			}
			$this->password = Security::password($new);
			Common::option('password', $this->password);
			Cookie::set(['name' => 'auth', 'value' => md5($this->password), 'httponly' => TRUE]);
			echo json_encode(['success' => 1]);
		} else {
			Sy::setMimeType('html');
			$this->display('option/password');
		}
	}
	/**
	 * SEO选项
	 */
	public function actionSeo() {
		Sy::setMimeType('html');
		$this->display('option/seo');
	}
	public function actionSeoSave() {
		Sy::setMimeType('json');
		$enable = ($_POST['enable'] == 1 ? TRUE : FALSE);
		switch ($_POST['_type']) {
			case 'ping':
				Common::option('seoPing', str_replace("\r", '', $_POST['ping']));
				echo json_encode(['success' => 1]);
				break;
			case 'baidusubmit':
				Common::option('seoBaiduSubmit', serialize(['enable' => $enable, 'site' => $_POST['site'], 'token' => $_POST['token']]));
				echo json_encode(['success' => 1]);
				break;
			case 'sitemap':
				Common::option('seoSitemap', serialize(['enable' => $enable, 'type' => $_POST['type'], 'changefreq' => $_POST['changefreq']]));
				echo json_encode(['success' => 1]);
				break;
			default:
				echo json_encode(['success' => 0, 'message' => i18n::get('unknow_operation')]);
				break;
		}
	}
	/**
	 * 附件选项
	 */
	public function actionAttachment() {
		if (isset($_POST['type'])) {
			Sy::setMimeType('json');
			$type = $_POST['type'];
			Common::option('attachmentType', $type);
			Common::option('attachmentFormat', $_POST['format']);
			Common::option('attachmentSize', $_POST['size']);
			if ($type !== 'local') {
				Common::option('attachmentUrl', $_POST['url']);
				$backup = $_POST['backup'];
				Common::option('attachmentBackup', $backup);
				if ($backup == 1) {
					Common::option('attachmentBackupFormat', $_POST['backupFormat']);
				}
				AttachmentModel::remoteDo($type, 'config', [$_POST[$type]]);
			}
			echo json_encode(['success' => 1]);
		} else {
			Sy::setMimeType('html');
			$support = AttachmentModel::getRemoteSupport();
			$config = [];
			foreach ($support as $k => $v) {
				$config[$k] = AttachmentModel::remoteDo($k, 'getForm');
			}
			$this->assign('support', $support);
			$this->assign('config', $config);
			$this->display('option/attachment');
		}
	}
}
