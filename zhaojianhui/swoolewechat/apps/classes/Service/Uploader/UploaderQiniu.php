<?php
namespace App\Service\Uploader;

/**
 * 七牛上传类
 * @package App\Service\Uploader
 */
class UploaderQiniu extends UploaderAbstract implements UploaderInterface
{
    private $qiniuConfig;

    /**
     * 设置七牛配置
     * @param $qiniuConfig
     */
    public function setQiniuConfig($qiniuConfig)
    {
        $this->qiniuConfig = $qiniuConfig;
    }

    public function getToken($key, $type)
    {
        $qiniuConfig = $this->qiniuConfig;

        $auth = new \Qiniu\Auth($qiniuConfig->accessKey, $qiniuConfig->secretKey);
        $returnBody = [
            'state' => 'SUCCESS',
            'type' => $type,
            'uploadType' => 'qiniu',
            'url'   => $this->qiniuConfig->domain . '$(key)',
            'fileName' => '$(key)',
            'oriName'  => '$(fname)',
            'fileExt' => '$(ext)',
            'size'  => '$(fsize)',
            'w'     => '$(imageInfo.width)',
            'h'     => '$(imageInfo.height)',
        ];
        $returnBody = json_encode($returnBody);
        // 生成上传Token
        $token = $auth->uploadToken($qiniuConfig->bucket, $key, 3600, ['returnBody' => $returnBody]);

        return $token;
    }

    /**
     * 上传文件的主处理方法
     * @return mixed
     */
    public function upFile($fileField)
    {
        $this->fileField = $fileField;
        $file = $this->file = $_FILES[$this->fileField];
        if (!$file) {
            $this->stateInfo = $this->getStateInfo("ERROR_FILE_NOT_FOUND");

            return;
        }
        if ($this->file['error']) {
            $this->stateInfo = $this->getStateInfo($file['error']);

            return;
        } else if (!file_exists($file['tmp_name'])) {
            $this->stateInfo = $this->getStateInfo("ERROR_TMP_FILE_NOT_FOUND");

            return;
        } else if (!is_uploaded_file($file['tmp_name'])) {
            $this->stateInfo = $this->getStateInfo("ERROR_TMPFILE");

            return;
        }

        $this->oriName = $file['name'];
        $this->fileSize = $file['size'];
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->filePath = $this->getFilePath();
        $this->fileName = $this->getFileName();
        $dirname = dirname($this->filePath);

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");

            return;
        }

        //检查是否不允许的文件格式
        if (!$this->checkType()) {
            $this->stateInfo = $this->getStateInfo("ERROR_TYPE_NOT_ALLOWED");

            return;
        }

        $qiniuConfig = $this->qiniuConfig;

        $auth = new \Qiniu\Auth($qiniuConfig->accessKey, $qiniuConfig->secretKey);
        // 生成上传Token
        $token = $auth->uploadToken($qiniuConfig->bucket);
        // 初始化 UploadManager 对象并进行文件的上传
        $uploadZone = new \Qiniu\Zone($qiniuConfig->upHost, $qiniuConfig->upHostBackup);
        $uploadConfig = new \Qiniu\Config($uploadZone);
        $uploadMgr = new \Qiniu\Storage\UploadManager($uploadConfig);
        // 上传到七牛后保存的文件名
        $key = substr($this->fullName, 1);
        // 调用 UploadManager 的 putFile 方法进行文件的上传
        list($ret, $err) = $uploadMgr->put($token, $key, file_get_contents($file["tmp_name"]));

        if ($err !== null) {
            $this->stateInfo = $err;
        } else {
            //重设访问url
            $this->fullName = $this->qiniuConfig->domain . $ret['key'];
            $this->fileKey = $ret['key'];
            $this->stateInfo = $this->stateMap[0];
        }
    }

    /**
     * 处理base64编码的图片上传
     * @return mixed
     */
    public function upBase64($fileField)
    {
        $this->fileField = $fileField;
        $base64Data = $_POST[$this->fileField];
        $img = base64_decode($base64Data);

        $this->oriName = $this->config['oriName'];
        $this->fileSize = strlen($img);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->filePath = $this->getFilePath();
        $this->fileName = $this->getFileName();
        $dirname = dirname($this->filePath);

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");

            return;
        }
        $qiniuConfig = $this->qiniuConfig;

        $auth = new \Qiniu\Auth($qiniuConfig['accessKey'], $qiniuConfig['secretKey']);
        // 生成上传Token
        $token = $auth->uploadToken($qiniuConfig['bucket']);
        // 初始化 UploadManager 对象并进行文件的上传
        $uploadZone = new \Qiniu\Zone($qiniuConfig['upHost'], $qiniuConfig['upHostBackup']);
        $uploadConfig = new \Qiniu\Config($uploadZone);
        $uploadMgr = new \Qiniu\Storage\UploadManager($uploadConfig);
        // 上传到七牛后保存的文件名
        $key = substr($this->fullName, 1);
        // 调用 UploadManager 的 putFile 方法进行文件的上传
        list($ret, $err) = $uploadMgr->put($token, $key, $img);

        if ($err !== null) {
            $this->stateInfo = $err;
        } else {
            //重设访问url
            $this->fullName = $qiniuConfig['domain'] . $ret['key'];
            $this->fileKey = $ret['key'];
            $this->stateInfo = $this->stateMap[0];
        }

    }

    /**
     * 拉取远程图片
     * @return mixed
     */
    public function saveRemote($fileField)
    {
        $this->fileField = $fileField;
        $imgUrl = htmlspecialchars($this->fileField);
        $imgUrl = str_replace("&amp;", "&", $imgUrl);

        //http开头验证
        if (strpos($imgUrl, "http") !== 0) {
            $this->stateInfo = $this->getStateInfo("ERROR_HTTP_LINK");

            return;
        }

        preg_match('/(^https*:\/\/[^:\/]+)/', $imgUrl, $matches);
        $host_with_protocol = count($matches) > 1 ? $matches[1] : '';

        // 判断是否是合法 url
        if (!filter_var($host_with_protocol, FILTER_VALIDATE_URL)) {
            $this->stateInfo = $this->getStateInfo("INVALID_URL");

            return;
        }

        preg_match('/^https*:\/\/(.+)/', $host_with_protocol, $matches);
        $host_without_protocol = count($matches) > 1 ? $matches[1] : '';

        // 此时提取出来的可能是 ip 也有可能是域名，先获取 ip
        $ip = gethostbyname($host_without_protocol);
        // 判断是否是私有 ip
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
            $this->stateInfo = $this->getStateInfo("INVALID_IP");

            return;
        }

        //获取请求头并检测死链
        $heads = get_headers($imgUrl, 1);
        if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
            $this->stateInfo = $this->getStateInfo("ERROR_DEAD_LINK");

            return;
        }
        //格式验证(扩展名验证和Content-Type验证)
        $fileType = strtolower(strrchr($imgUrl, '.'));
        if (!in_array($fileType, $this->config['allowFiles']) || !isset($heads['Content-Type']) || !stristr($heads['Content-Type'], "image")) {
            $this->stateInfo = $this->getStateInfo("ERROR_HTTP_CONTENTTYPE");

            return;
        }

        //打开输出缓冲区并获取远程图片
        ob_start();
        $context = stream_context_create(
            ['http' => [
                'follow_location' => false // don't follow redirects
            ]]
        );
        readfile($imgUrl, false, $context);
        $img = ob_get_contents();
        ob_end_clean();
        preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/", $imgUrl, $m);

        $this->oriName = $m ? $m[1] : "";
        $this->fileSize = strlen($img);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->filePath = $this->getFilePath();
        $this->fileName = $this->getFileName();
        $dirname = dirname($this->filePath);

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");

            return;
        }

        $qiniuConfig = $this->qiniuConfig;

        $auth = new \Qiniu\Auth($qiniuConfig['accessKey'], $qiniuConfig['secretKey']);
        // 生成上传Token
        $token = $auth->uploadToken($qiniuConfig['bucket']);
        // 初始化 UploadManager 对象并进行文件的上传
        $uploadZone = new \Qiniu\Zone($qiniuConfig['upHost'], $qiniuConfig['upHostBackup']);
        $uploadConfig = new \Qiniu\Config($uploadZone);
        $uploadMgr = new \Qiniu\Storage\UploadManager($uploadConfig);
        // 上传到七牛后保存的文件名
        $key = substr($this->fullName, 1);
        // 调用 UploadManager 的 putFile 方法进行文件的上传
        list($ret, $err) = $uploadMgr->put($token, $key, $img);

        if ($err !== null) {
            $this->stateInfo = $err;
        } else {
            //重设访问url
            $this->fullName = $qiniuConfig['domain'] . $ret['key'];
            $this->fileKey = $ret['key'];
            $this->stateInfo = $this->stateMap[0];
        }
    }

    /**
     * 文件列表
     * @param $type
     */
    public function listFile($type)
    {

    }

    /**
     * 上传本地文件
     * @param $filePath
     * @return array
     */
    public function upByPath($filePath)
    {
        $qiniuConfig = $this->qiniuConfig;
        $auth = new \Qiniu\Auth($qiniuConfig['accessKey'], $qiniuConfig['secretKey']);
        // 生成上传Token
        $token = $auth->uploadToken($qiniuConfig['bucket']);
        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new \Qiniu\Storage\UploadManager();
        // 上传到七牛后保存的文件名
        $key = str_replace('/tmp/uploads/', '', $filePath);
        // 调用 UploadManager 的 putFile 方法进行文件的上传
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null) {
            $this->stateInfo = '上传到七牛发生错误';
        } else {
            //重设访问url
            $this->fullName = $qiniuConfig['domain'] . $ret['key'];
            $this->fileKey = $ret['key'];
            $this->stateInfo = $this->stateMap[0];
        }
    }
}