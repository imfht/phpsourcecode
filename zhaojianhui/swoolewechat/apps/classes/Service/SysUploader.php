<?php

namespace  App\Service;

class SysUploader
{
    private $jsonConfig;
    private $uploadType; //文件上传方式
    private $qiniuConfig; //qiniu config
    private $uploader;
    private $userId; //用户ID

    /**
     * Uploader constructor.
     * [
     * 上传保存路径
     * 'pathFormat' => '/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}',
     *  上传大小限制，单位B
     * 'maxSize' => 2048000,
     * 上传图片格式限制
     * 'allowFiles' => [".png", ".jpg", ".jpeg", ".gif", ".bmp"],
     * 原始图片名称，可省略
     * 'oriName' => '',
     * ].
     *
     * @param $config
     */
    public function __construct($config = [])
    {
        $this->jsonConfig = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", '', file_get_contents(WEBPATH . '/public/ueditor/php/config.json')), true);

        switch (ENV) {
            case 'devlop':
            case 'test':
            case 'product':
                $this->qiniuConfig = \Swoole::$php->config['qiniu']['master'];
                $this->uploadType  = 'qiniu';
                break;
            default:
                $this->uploadType = 'local';
        }
        //设置上传类
        switch ($this->uploadType) {
            case 'qiniu':
                $qiniuUploader = new \App\Service\Uploader\UploaderQiniu($config);
                $qiniuUploader->setQiniuConfig($this->qiniuConfig);
                $this->uploader = $qiniuUploader;
                //设置抓取远程图片配置本地域名
                $this->jsonConfig['catcherLocalDomain'][] = $this->qiniuConfig['domain'];
                break;
            case 'local':

            default:
            $this->uploader = new \App\Service\Uploader\UploaderLocal($config);
        }
        //用户userId
        $this->userId = \Swoole::$php->user->getUid();
    }

    /**
     * 设置jsonConfig.
     */
    public function setJsonConfig()
    {
        switch ($this->uploadType) {
            case 'qiniu':
                $this->jsonConfig['uploadType']      = $this->uploadType;
                $this->jsonConfig['tokenActionName'] = 'getToken';
                $this->jsonConfig['uploadUrl']       = 'http://upload.qiniu.com/';
                //上传文件回写url
                $this->jsonConfig['callbackAction'] = 'callBack';

                $this->jsonConfig['imageFieldName'] = 'file';
                $this->jsonConfig['videoFieldName'] = 'file';
                $this->jsonConfig['fileFieldName']  = 'file';
                //上传大小限制，单位B，默认100MB
                $this->jsonConfig['videoMaxSize'] = 102400000 * 10;
                break;
            default:
                $this->jsonConfig['uploadType']      = $this->uploadType;
                $this->jsonConfig['tokenActionName'] = 'getToken';
        }
    }

    /**
     * 获取百度编辑器json配置.
     *
     * @return mixed
     */
    public function getJsonConfig()
    {
        return $this->jsonConfig;
    }

    /**
     * getToken.
     *
     * @param $key
     * @param mixed $type
     *
     * @return mixed|string
     */
    public function getToken($key, $type)
    {
        return $this->uploader->getToken($key, $type);
    }

    /**
     * 上传文件.
     *
     * @param $fileField
     *
     * @return bool
     */
    public function upFile($fileField)
    {
        $this->uploader->upFile($fileField);

        return $this->uploader->getFileInfo();
    }

    /**
     * 上传编码64图片.
     *
     * @param $fileField
     *
     * @return array
     */
    public function upBase64($fileField)
    {
        $this->uploader->upBase64($fileField);

        return $this->uploader->getFileInfo();
    }

    /**
     * 更具文件目录上传文件
     * @param $filePath
     * @return array
     */
    public function upByPath($filePath)
    {
        $this->uploader->upByPath($filePath);

        return $this->uploader->getFileInfo();
    }
    /**
     * 上传远程图片.
     *
     * @param $fileField
     *
     * @return array
     */
    public function saveRemote($fileField)
    {
        $this->uploader->saveRemote($fileField);

        return $this->uploader->getFileInfo();
    }

    /**
     * 获取全名
     * @param $type
     * @param $originName
     * @return bool|string
     */
    public function getFullName($type, $originName)
    {
        $format   = $this->getFormat($type);
        $ext      = $this->getFileExt($originName);
        $fileName = $this->parseFormat($format) . $ext;
        if ($this->uploadType == 'qiniu') {
            $fileName = substr($fileName, 1);
        }

        return $fileName;
    }

    /**
     * 保存文件数据到数据表.
     *
     * @param array $fileInfo
     * @param mixed $type
     */
    public function saveDataToTable($type)
    {
        //文件信息
        $fileInfo = $this->uploader->getFileInfo();
        if ($fileInfo['state'] == 'SUCCESS') {
            $fileModel = model('SysFile');
            $fileModel->put([
                'type'         => $type,
                'uploadType'   => $this->uploadType,
                'url'          => $fileInfo['url'],
                'fileName'     => $fileInfo['title'],
                'oriName'      => $fileInfo['original'],
                'fileExt'      => $fileInfo['type'],
                'size'         => (int) $fileInfo['size'],
                'addUserId'    => $this->userId,
                'addTime'      => time(),
            ]);
        }
    }

    /**
     * 获取文件列表.
     *
     * @param $type
     * @param int $start
     * @param int $size
     *
     * @return array
     */
    public function getFileList($type, $start = 0, $size = 10)
    {
        //开始位置
        $start = (int) ($this->request->request['start'] ?? 0);
        //长度
        $length = (int) ($this->request->request['length'] ?? 10);

        $fileModel = model('SysFile');
        $where = [
            'where'  => "addUserId={$this->userId} AND type={$type}",
            'order'  => 'addTime DESC',
            'limit'  => $start . ',' . $length,
        ];
        $fileList  = $fileModel->getList($where);
        if (empty($fileList)) {
            return [
                'state' => 'no match file',
                'list'  => [],
                'start' => $start,
                'total' => 0,
            ];
        }
        $list = [];
        foreach ($fileList as $v) {
            $list[] = [
                    'url'   => $v['url'],
                    'mtime' => (int) $v['addTime'],
                ];
        }

        return [
                'state' => 'SUCCESS',
                'list'  => $list,
                'start' => $start,
                'total' => count($fileList),
            ];
    }

    protected function parseFormat($format)
    {
        //替换日期事件
        $t      = time();
        $d      = explode('-', date('Y-y-m-d-H-i-s'));
        $format = str_replace('{yyyy}', $d[0], $format);
        $format = str_replace('{yy}', $d[1], $format);
        $format = str_replace('{mm}', $d[2], $format);
        $format = str_replace('{dd}', $d[3], $format);
        $format = str_replace('{hh}', $d[4], $format);
        $format = str_replace('{ii}', $d[5], $format);
        $format = str_replace('{ss}', $d[6], $format);
        $format = str_replace('{time}', $t, $format);

        //替换随机字符串
        $randNum = rand(1, 10000000000) . rand(1, 10000000000);
        if (preg_match("/\{rand\:([\d]*)\}/i", $format, $matches)) {
            $format = preg_replace("/\{rand\:[\d]*\}/i", substr($randNum, 0, $matches[1]), $format);
        }

        return $format;
    }

    protected function getFileExt($fileName)
    {
        return strtolower(strrchr($fileName, '.'));
    }

    protected function getFormat($type)
    {
        $format = '';
        switch ($type) {
            case 'image':
                $format = $this->jsonConfig['imagePathFormat'];
                break;
            case 'scrawl':
                $format = $this->jsonConfig['scrawlPathFormat'];
                break;
            case 'snapscreen':
                $format = $this->jsonConfig['snapscreenPathFormat'];
                break;
            case 'catcher':
                $format = $this->jsonConfig['catcherPathFormat'];
                break;
            case 'video':
                $format = $this->jsonConfig['videoPathFormat'];
                break;
            case 'file':
                $format = $this->jsonConfig['filePathFormat'];
                break;
            default:
                $format = $this->jsonConfig['imagePathFormat'];
        }

        return $format;
    }
}
