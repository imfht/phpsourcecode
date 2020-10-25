<?php

namespace CigoAdminLib\Lib\Uploader;

use CigoAdminLib\Lib\ErrorCode;
use CigoAdminLib\Lib\IResponse;

/**
 * 上传类
 * 负责文件的上传处理
 */
class Uploader implements IResponse
{
    const FILE_TYPE_FILE = 0;
    const FILE_TYPE_IMG = 1;
    const FILE_TYPE_VIDEO = 2;

    private $args = false;
    private $files = false;
    private $configs = false;

    private $fileUploader = null;
    private $response = array();

    public function init($args = false, $files = false, $configs = false)
    {
        if (!$args) {
            $this->args = $_POST;
        }
        if (!$files) {
            $this->files = $_FILES;
        }
        if (!$configs) {
            $this->configs = C('FILE_UPLOAD');
        }

        return $this;
    }

    public function makeFileUploader()
    {
        if (!isset($this->args['fileType']) || empty($this->args['fileType'])) {
            $this->makeResponse(false, null, '参数错误！', ErrorCode::ERROR_CODE_ARGS_WRONG);
            return false;
        }
        $fileUpCls = 'CigoAdminLib\\Lib\\Uploader\\FileUploader\\' . ucfirst($this->args['fileType']);
        $this->fileUploader = new $fileUpCls();
        if (!$this->fileUploader) {
            $fileUpCls = 'CigoAdminLib\\Lib\\Uploader\\FileUploader\\File';
            $this->fileUploader = new $fileUpCls();
        }
        //初始化文件上传类
        $this->fileUploader->init($this->args, $this->configs);
        return true;
    }

    /**
     * @param string $fileName 上传文件名称
     * @return bool
     */
    function doUpload($fileName = 'upload')
    {
        $file = $this->files[$fileName];
        //TODO 上传文件
        return $this->upload($file);
    }

    private function upload($file)
    {
        //1. 检查上传文件
        if (!$this->checkUploadFile($file)) {
            return false;
        }
        //2. 检查上传文件配置限制
        if (!$this->fileUploader->checkConfigs($file, $this->configs)) {
            $this->response = $this->fileUploader->response();
            return false;
        }
        //3. 上传文件
        $status = $this->fileUploader->upload($this->args, $file, $this->configs);
        $this->response = $this->fileUploader->response();
        return $status;
    }

    private function checkUploadFile(&$file)
    {
        // 检查是否存在上传错误
        if ($file['error']) {
            $this->makeResponse(false, null, $this->checkUploadErrorNo($file['error']), ErrorCode::ERROR_CODE_UPLOAD_TMP_FILE_ERROR);
            return false;
        }
        // 无效上传
        if (empty($file['name'])) {
            $this->makeResponse(false, null, '上传文件名为空！', ErrorCode::ERROR_CODE_UPLOAD_TMP_FILE_ERROR);
            return false;
        }
        // 检查上传临时文件是否合法
        if (!is_uploaded_file($file['tmp_name'])) {
            $this->makeResponse(false, null, '非法上传文件！', ErrorCode::ERROR_CODE_UPLOAD_TMP_FILE_ERROR);
            return false;
        }

        // 补齐上传文件信息
        $this->addInfoToUploadFile($file);

        /* 通过检测 */
        return true;
    }

    private function addInfoToUploadFile(&$file)
    {
        $file['ext'] = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file['md5'] = md5_file($file['tmp_name']);
        $file['sha1'] = sha1_file($file['tmp_name']);
    }

    private function checkUploadErrorNo($errorNo)
    {
        $errorMsg = '';
        switch ($errorNo) {
            case 1:
                $errorMsg = '上传的文件超过了php配置限制！';
                break;
            case 2:
                $errorMsg = '上传文件的大小超过了表单限制！';
                break;
            case 3:
                $errorMsg = '文件只有部分被上传！';
                break;
            case 4:
                $errorMsg = '没有文件被上传！';
                break;
            case 6:
                $errorMsg = '找不到临时文件夹！';
                break;
            case 7:
                $errorMsg = '文件写入失败！';
                break;
            default:
                $errorMsg = '未知上传错误！';
        }
        return $errorMsg;
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
