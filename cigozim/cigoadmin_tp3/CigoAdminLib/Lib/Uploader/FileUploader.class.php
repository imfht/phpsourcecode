<?php

namespace CigoAdminLib\Lib\Uploader;

use CigoAdminLib\Lib\ErrorCode;
use CigoAdminLib\Lib\IResponse;

/**
 * 上传文件类
 * 负责铁定文件的上传处理
 */
abstract class FileUploader implements IResponse
{
	protected $response = array();

	private $maxSize = 0;
	private $exts = array();

	function init($args, $configs)
	{
		$this->initFileLimit($args, $configs);
	}

	protected function initFileLimit($args, $configs)
	{
		$fileLimit = $this->getConfigFileLimit($configs);

		//限制文件大小
		$this->maxSize = (isset($fileLimit['maxSize']) && is_numeric($fileLimit['maxSize']))
			? $fileLimit['maxSize']
			: 0;
		//限制文件后缀
		$exts = (isset($fileLimit['exts']) && !empty($fileLimit['exts']))
			? $fileLimit['exts']
			: '';
		$exts = preg_replace('# #', '', trim(strtolower($exts)));
		$this->exts = explode(',', $exts);
	}

	protected abstract function getConfigFileLimit($configs);

	protected abstract function getFileType();

	public function checkConfigs($file, $configs)
	{
		//1. 检查大小限制
		if (
			($this->maxSize == 0) ||
			($file["size"] > $this->maxSize)
		) {
			$this->makeResponse(false, null, '文件大小超出限制(' . ($this->maxSize / 1024 / 1024) . 'M)!', ErrorCode::ERROR_CODE_UPLOAD_FILE_TOOBIG);
			return false;
		}
		//2. 检查文件后缀
		if (!$this->exts || !count($this->exts) || !in_array(strtolower($file['ext']), $this->exts)) {
			$this->makeResponse(false, null, '上传文件后缀不允许！', ErrorCode::ERROR_CODE_UPLOAD_FILE_EXT_ERROR);
			return false;
		}

		/*检查通过*/
		return true;
	}


	public function upload($args, $file, $configs)
	{
		//1. 保存上传文件
		if (!$this->saveUploadFile($args, $file, $configs)) {
			return false;
		}
		//2. 处理上传文件
		if (!$this->ctrlUploadFile($args, $file, $configs)) {
			return false;
		}
		return true;
	}

	protected function saveUploadFile($args, &$file, $configs)
	{
		// 检查文件是否存在且保存于数据库中
		$dbInfo = $this->getUploadFileInDB($file);
		// 确保文件保存到磁盘
		if (!$this->saveUploadFileToDisk($dbInfo, $file, $configs)) {
			return false;
		}
		// 确保文件保存到数据库
		if (!$this->saveUploadFileToDb($dbInfo, $file)) {
			return false;
		}
		return true;
	}

	private function saveUploadFileToDisk($dbInfo, &$file, $configs)
	{
		if ($dbInfo && is_file($dbInfo['path'])) {
            $file['ext'] = pathinfo($dbInfo['path'], PATHINFO_EXTENSION);

            $file['saved_path'] = pathinfo($dbInfo['path'], PATHINFO_DIRNAME);
            $file['saved_name'] = pathinfo($dbInfo['path'], PATHINFO_FILENAME);
            $file['saved_path_name'] = $dbInfo['path'];
			return true;
		} else {
			$file['root_path'] = $configs['rootPath'];
			$file['sub_path'] = $this->getSubPath();

			$file['saved_path'] = $file['root_path'] . '/' . $file['sub_path'];//TODO 注意路径'./',避免linux造成'./'和'/'歧义
			$file['saved_name'] = $this->getSaveFileName();
			$file['saved_path_name'] = $file['saved_path'] . '/' . $file['saved_name'] . '.' . $file['ext'];

			//检查是否允许覆盖
			if (!$configs['replace'] && is_file($file['saved_path_name'])) {
				$this->makeResponse(false, null, '保存文件重名，请重新尝试！', ErrorCode::ERROR_CODE_UPLOAD_FILE_SAVE_ERROR);
				return false;
			}
		}
		//检查保存目录是否存在
		if (!$this->mkPath($file['saved_path'])) {
			return false;
		}
		//检查保存目录是否可写
		if (!$this->checkPathWritable($file['saved_path'])) {
			return false;
		}
		if (!move_uploaded_file($file['tmp_name'], $file['saved_path_name'])) {
			$this->makeResponse(false, null, '上传文件保存错误！', ErrorCode::ERROR_CODE_UPLOAD_FILE_SAVE_ERROR);
			return false;
		}
		return true;
	}

	protected function mkPath($path)
	{
		if (is_dir($path)) {
			if (!is_writable($path)) {
				$this->makeResponse(false, null, '上传目录不可写！', ErrorCode::ERROR_CODE_UPLOAD_PATH_NO_WRITABLE);
				return false;
			}
			return true;
		}
		if (!mkdir($path, 0777, true)) {
			$this->makeResponse(false, null, '目录 {' . $path . '} 创建失败！！', ErrorCode::ERROR_CODE_UPLOAD_PATH_MKDIR_ERROR);
			return false;
		}

		/* 检查通过 */
		return true;
	}

	protected function checkPathWritable($path)
	{
		/* 检测目录是否可写 */
		if (!is_writable($path)) {
			$this->makeResponse(false, null, '上传目录{' . $path . '}不可写！', ErrorCode::ERROR_CODE_UPLOAD_PATH_NO_WRITABLE);
			return false;
		}
		return true;
	}

	private function saveUploadFileToDb($dbInfo, &$file)
	{
		if ($dbInfo && is_file($dbInfo['path'])) {
			$file['id'] = $dbInfo['id'];
			return true;
		}
		$model = D('Files');
		$data = array(
			'type' => $this->getFileType(),
			'name' => $file['saved_name'],
			'ext' => $file['ext'],
			'mime' => $file['type'],
			'path' => $file['saved_path_name'],
			'md5' => $file['md5'],
			'sha1' => $file['sha1']
		);
		if ($dbInfo) {
			$file['id'] = $dbInfo['id'];
			$data['id'] = $dbInfo['id'];
			$data = $model->create($data);
			$res = $model->save($data);
		} else {
			$data = $model->create($data);
			$res = $model->add($data);
			$file['id'] = $res;
		}

		if ($res === false) {
			$this->makeResponse(false, null, '保存数据库失败！', ErrorCode::ERROR_CODE_UPLOAD_DB_SAVE_ERROR);
			return false;
		}
		return true;
	}

	private function getUploadFileInDB(&$file)
	{
		$model = D('Files');
		$data = $model->where(array('md5' => $file['md5'], 'sha1' => $file['sha1']))->find();
		return $data;
	}

	protected function getSaveFileName()
	{
		return time() . $this->randomFileNameExt();
	}

	function randomFileNameExt()
	{
		$srcStr = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
		$desStr = "";
		for ($i = 0; $i < 5; $i++) {
			$desStr .= $srcStr[mt_rand(0, strlen($srcStr) - 1)];
		}
		return $desStr;
	}

	protected function getSubPath()
	{
		return date('Y-m-d', time());
	}

	protected function ctrlUploadFile($args, $file, $configs)
	{
		//TODO 返回数据信息
		$this->makeResponse(true, $this->getResponseFileInfo($file), '上传成功！');
		return true;
	}

	protected function getResponseFileInfo($file)
	{
		return array(
			'id' => $file['id'],
			'name' => $file['saved_name'],
			'ext' => $file['ext'],
			'path' => trim($file['saved_path_name'], '.')
		);
	}

	function makeResponse($status = false, $data = array(), $msg = '', $errorCode = '')
	{
		$this->response = array(
			IResponse::FLAG_STATUS => $status,
			IResponse::FLAG_DATA => $data,
			IResponse::FLAG_MSG => $msg,
			IResponse::FLAG_ERRORCODE => $errorCode
		);
	}

	public function response()
	{
		return $this->response;
	}
}
