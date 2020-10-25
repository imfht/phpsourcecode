<?php
namespace App\Controller\Admin;
use App\BaseController\AdminBaseController as Base;

/**
 * 文件操作
 * @package App\Controller\Admin
 */
class SysFile extends Base
{
    /**
     * 文件构造函数
     * @param \Swoole $swoole
     */
    public function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
        $this->addBreadcrumb('系统管理', '/Admin/System/index');
        $this->sysAuthRuleModel = model('SysAuthRule');
    }

    /**
     * 百度编辑器上传
     */
    public function uedituploader()
    {
        // Make sure file is not cached (as it happens for example on iOS devices)
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        ini_set('upload_max_filesize', '100m');
        //ini_set('memory_limit', '100m');

        $uploader = new \App\Service\SysUploader([]);
        $uploader->setJsonConfig();

        $jsonConfig = $uploader->getJsonConfig();

        $action = $this->request->getQuery('action');
        switch ($action) {
            case 'config':
                return $jsonConfig;
                break;
            /* 上传图片 */
            case 'uploadimage':
                $uploader = new \App\Service\SysUploader([
                    "pathFormat" => $jsonConfig['imagePathFormat'],
                    "maxSize"    => $jsonConfig['imageMaxSize'],
                    "allowFiles" => $jsonConfig['imageAllowFiles'],
                ]);
                $result = $uploader->upFile($jsonConfig['imageFieldName']);
                $uploader->saveDataToTable('image');
                break;
            /* 上传涂鸦 */
            case 'uploadscrawl':
                $uploader = new \App\Service\SysUploader([
                    "pathFormat" => $jsonConfig['scrawlPathFormat'],
                    "maxSize"    => $jsonConfig['scrawlMaxSize'],
                    "allowFiles" => $jsonConfig['imageAllowFiles'],
                    "oriName"    => "scrawl.png",
                ]);
                $result = $uploader->upBase64($jsonConfig['scrawlFieldName']);
                $uploader->saveDataToTable('image');
                break;
            /* 上传视频 */
            case 'uploadvideo':
                $uploader = new \App\Service\SysUploader([
                    "pathFormat" => $jsonConfig['videoPathFormat'],
                    "maxSize"    => $jsonConfig['videoMaxSize'],
                    "allowFiles" => $jsonConfig['videoAllowFiles'],
                ]);
                $result = $uploader->upFile($jsonConfig['videoFieldName']);
                $uploader->saveDataToTable('video');
                break;
            /* 上传文件 */
            case 'uploadfile':
                $uploader = new \App\Service\SysUploader([
                    "pathFormat" => $jsonConfig['filePathFormat'],
                    "maxSize"    => $jsonConfig['fileMaxSize'],
                    "allowFiles" => $jsonConfig['fileAllowFiles']
                ]);
                $result = $uploader->upFile($jsonConfig['fileFieldName']);
                $uploader->saveDataToTable('file');
                break;
            /* 列出图片 */
            case 'listimage':
                $size = (int) $this->request->get('size', 'int', $jsonConfig['imageManagerListSize']);
                $start = (int) $this->request->get('start', 'int', 0);
                $result = (new \App\Service\SysUploader())->getFileList('image', $start, $size);
                break;
            /* 列出文件 */
            case 'listfile':
                $size = (int) $this->request->get('size', 'int', $jsonConfig['imageManagerListSize']);
                $start = (int) $this->request->get('start', 'int', 0);
                $result = (new \App\Service\SysUploader())->getFileList('file', $start, $size);
                break;
            /* 抓取远程文件 */
            case 'catchimage':
                $uploader = new \App\Service\SysUploader([
                    "pathFormat" => $jsonConfig['catcherPathFormat'],
                    "maxSize"    => $jsonConfig['catcherMaxSize'],
                    "allowFiles" => $jsonConfig['catcherAllowFiles'],
                    "oriName"    => "remote.png",
                ]);
                $result = $uploader->saveRemote($jsonConfig['catcherFieldName']);
                $uploader->saveDataToTable('image');
                break;
            /*获取上传token*/
            case 'getToken':
                $type = $this->request->get('type');
                $fileName = $this->request->get('fileName');
                $uploader = new \App\Service\SysUploader([]);
                //file name
                $key = $uploader->getFullName($type, $fileName);
                if ($key){
                    $result['key'] = $key;
                    $token = $uploader->getToken($key, $type);
                    $result['token'] = $token;
                }else{
                    $result['token'] = '';
                }
                break;
            /*上传成功文件数据回写*/
            case 'callBack':
                $fileModel = new \App\Model\SysFile();
                $rs = $fileModel->put([
                    'user_id'    => $this->user->getUid(),
                    'type'       => $this->request->get('type'),
                    'uploadType' => $this->request->get('uploadType'),
                    'url'        => $this->request->get('url'),
                    'fileName'   => $this->request->get('fileName'),
                    'oriName'    => $this->request->get('oriName'),
                    'fileExt'    => $this->request->get('fileExt'),
                    'size'       => $this->request->get('size', 'int'),
                ]);
                $result = $rs ? ['status'=>'success'] : ['status'=>'error'];
                break;
            default:
                $result = [
                    'state' => '请求地址出错'
                ];
                break;
        }
        return $result;
    }
}