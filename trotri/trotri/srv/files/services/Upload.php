<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace files\services;

use tfc\ap\Ap;
use tfc\saf\Cfg;
use tfc\saf\UpProxy;
use files\library\Constant;
use files\library\Lang;

/**
 * Upload class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FileManager.php 1 2014-09-16 19:26:44Z Code Generator $
 * @package files.services
 * @since 1.0
 */
class Upload
{
	/**
	 * 系统管理：批量上传配置
	 * @param array $files
	 * @return array
	 */
	public static function sysbatch(array $files)
	{
		$clusterName = Constant::SYSBATCH_CLUSTER;

		$ret = self::save($clusterName, $files);
		if ($ret['err_no'] !== UpProxy::SUCCESS_NUM) {
			return $ret;
		}

		return $ret;
	}

	/**
	 * 上传图片：文档管理
	 * @param array $files
	 * @param boolean $thumbnail
	 * @return array
	 */
	public static function posts(array $files, $thumbnail = false)
	{
		$clusterName = Constant::POSTS_CLUSTER;

		$ret = self::save($clusterName, $files);
		if ($ret['err_no'] !== UpProxy::SUCCESS_NUM) {
			return $ret;
		}

		return $ret;
	}

	/**
	 * 上传图片|Flash：广告管理
	 * @param array $files
	 * @return array
	 */
	public static function adverts(array $files)
	{
		$clusterName = Constant::ADVERTS_CLUSTER;

		$ret = self::save($clusterName, $files);
		if ($ret['err_no'] !== UpProxy::SUCCESS_NUM) {
			return $ret;
		}

		return $ret;
	}

	/**
	 * 上传图片|Flash：会员图像
	 * @param array $files
	 * @return array
	 */
	public static function headPortrait(array $files)
	{
		$clusterName = Constant::HEAD_PORTRAIT_CLUSTER;

		$ret = self::save($clusterName, $files);
		if ($ret['err_no'] !== UpProxy::SUCCESS_NUM) {
			return $ret;
		}

		return $ret;
	}

	/**
	 * 检查并上传文件
	 * @param string $clusterName
	 * @param array $files
	 * @return array
	 */
	public static function save($clusterName, array $files)
	{
		$upProxy = new UpProxy($clusterName);
		$errNo = $upProxy->save($files);
		$filePath = $upProxy->getSavePath();
		$fileName = str_replace(DIR_ROOT, '', $filePath);
		if ($errNo === UpProxy::SUCCESS_NUM) {
			$baseName = pathinfo($filePath, PATHINFO_BASENAME);
			$url = self::getUrl($filePath);
			$ret = array(
				'err_no' => $errNo,
				'err_msg' => '',
				'file_path' => $filePath, // 文件绝对地址
				'file_name' => $fileName, // 文件地址，目录从'/data/'后面开始
				'base_name' => $baseName, // 文件名
				'url' => $url
			);

			return $ret;
		}

		switch ($errNo) {
			case UpProxy::ERROR_REQUEST:
				$errMsg = Lang::_('SRV_FILTER_FILES_UPLOAD_ERROR_REQUEST');
				break;
			case UpProxy::ERR_ABOVE_MAX_SIZE:
				$errMsg = sprintf(Lang::_('SRV_FILTER_FILES_UPLOAD_SIZE_MAX'), $files['size'], $upProxy->getMaxSize());
				break;
			case UpProxy::ERR_DISALLOW_TYPE:
				$errMsg = sprintf(Lang::_('SRV_FILTER_FILES_UPLOAD_TYPE_DISALLOW'), $files['type'], implode('|', $upProxy->getAllowTypes()));
				break;
			case UpProxy::ERR_DISALLOW_EXT:
				$errMsg = sprintf(Lang::_('SRV_FILTER_FILES_UPLOAD_EXT_DISALLOW'), $upProxy->getFileExt($files['name']), implode('|', $upProxy->getAllowExts()));
				break;
			case UpProxy::ERR_FILE_ALREADY_EXISTS:
				$errMsg = sprintf(Lang::_('SRV_FILTER_FILES_UPLOAD_NAME_UNIQUE'), $fileName);
				break;
			case UpProxy::ERR_DISALLOW_UPLOAD:
				$errMsg = sprintf(Lang::_('SRV_FILTER_FILES_UPLOAD_POSSIBLE_ATTACK'), $fileName);
				break;
			case UpProxy::ERR_MOVE_UPLOADED_FILE:
				$errMsg = sprintf(Lang::_('SRV_FILTER_FILES_UPLOAD_MOVE_FAILED'), $fileName);
				break;
			default:
				$errMsg = Lang::_('SRV_FILTER_FILES_UPLOAD_SAVE_FAILED');
				break;
		}

		$ret = array(
			'err_no' => $errNo,
			'err_msg' => $errMsg,
			'file_path' => '',
			'file_name' => '',
			'base_name' => '',
			'url' => ''
		);

		return $ret;
	}

	/**
	 * 通过文件名，获取访问该文件的URL
	 * @param string $fileName
	 * @return string
	 */
	public static function getUrl($fileName)
	{
		$req = Ap::getRequest();
		$picServer = Cfg::getApp('picture_server');

		$url = $picServer . str_replace('/webroot', '', $req->baseUrl) . str_replace(array(DIR_ROOT, '\\'), array('', '/'), $fileName);
		return $url;
	}

}
