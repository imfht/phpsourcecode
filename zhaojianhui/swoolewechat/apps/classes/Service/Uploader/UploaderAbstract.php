<?php
namespace App\Service\Uploader;

/**
 * 上传抽象类
 * @package App\Service\Uploader
 */
abstract class UploaderAbstract
{
    protected $fileField; //文件域名
    protected $file; //文件上传对象
    protected $base64; //文件上传对象
    protected $config; //配置信息
    protected $oriName; //原始文件名
    protected $fileName; //新文件名
    protected $fullName; //完整文件名,即从当前配置目录开始的URL
    protected $filePath; //完整文件名,即从当前配置目录开始的URL
    protected $fileSize; //文件大小
    protected $fileType; //文件类型
    protected $fileKey;//文件KEY，用于删除
    protected $stateInfo; //上传状态信息,
    protected $stateMap = [ //上传状态映射表，国际化用户需考虑此处数据的国际化
        "SUCCESS", //上传成功标记，在UEditor中内不可改变，否则flash判断会出错
        "文件大小超出 upload_max_filesize 限制",
        "文件大小超出 MAX_FILE_SIZE 限制",
        "文件未被完整上传",
        "没有文件被上传",
        "上传文件为空",
        "ERROR_TMP_FILE"           => "临时文件错误",
        "ERROR_TMP_FILE_NOT_FOUND" => "找不到临时文件",
        "ERROR_SIZE_EXCEED"        => "文件大小超出网站限制",
        "ERROR_TYPE_NOT_ALLOWED"   => "文件类型不允许",
        "ERROR_CREATE_DIR"         => "目录创建失败",
        "ERROR_DIR_NOT_WRITEABLE"  => "目录没有写权限",
        "ERROR_FILE_MOVE"          => "文件保存时出错",
        "ERROR_FILE_NOT_FOUND"     => "找不到上传文件",
        "ERROR_WRITE_CONTENT"      => "写入文件内容错误",
        "ERROR_UNKNOWN"            => "未知错误",
        "ERROR_DEAD_LINK"          => "链接不可用",
        "ERROR_HTTP_LINK"          => "链接不是http链接",
        "ERROR_HTTP_CONTENTTYPE"   => "链接contentType不正确",
        "INVALID_URL"              => "非法 URL",
        "INVALID_IP"               => "非法 IP",
    ];

    /**
     * 构造函数
     * config
     * [
     * 上传保存路径
     * 'pathFormat' => '/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}',
     *  上传大小限制，单位B
     * 'maxSize' => 2048000,
     * 上传图片格式限制
     * 'allowFiles' => [".png", ".jpg", ".jpeg", ".gif", ".bmp"],
     * 原始图片名称，可省略
     * 'oriName' => '',
     * ]
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;

        //$this->stateMap['ERROR_TYPE_NOT_ALLOWED'] = iconv('unicode', 'utf-8', $this->stateMap['ERROR_TYPE_NOT_ALLOWED']);
    }

    /**
     * 上传错误检查
     * @param $errCode
     * @return string
     */
    protected function getStateInfo($errCode)
    {
        return !$this->stateMap[$errCode] ? $this->stateMap["ERROR_UNKNOWN"] : $this->stateMap[$errCode];
    }

    /**
     * 获取文件扩展名
     * @return string
     */
    protected function getFileExt()
    {
        return strtolower(strrchr($this->oriName, '.'));
    }

    /**
     * 重命名文件
     * @return string
     */
    protected function getFullName()
    {
        //替换日期事件
        $t = time();
        $d = explode('-', date("Y-y-m-d-H-i-s"));
        $format = $this->config["pathFormat"];
        $format = str_replace("{yyyy}", $d[0], $format);
        $format = str_replace("{yy}", $d[1], $format);
        $format = str_replace("{mm}", $d[2], $format);
        $format = str_replace("{dd}", $d[3], $format);
        $format = str_replace("{hh}", $d[4], $format);
        $format = str_replace("{ii}", $d[5], $format);
        $format = str_replace("{ss}", $d[6], $format);
        $format = str_replace("{time}", $t, $format);

        //过滤文件名的非法自负,并替换文件名
        $oriName = substr($this->oriName, 0, strrpos($this->oriName, '.'));
        $oriName = preg_replace("/[\|\?\"\<\>\/\*\\\\]+/", '', $oriName);
        $format = str_replace("{filename}", $oriName, $format);

        //替换随机字符串
        $randNum = rand(1, 10000000000) . rand(1, 10000000000);
        if (preg_match("/\{rand\:([\d]*)\}/i", $format, $matches)) {
            $format = preg_replace("/\{rand\:[\d]*\}/i", substr($randNum, 0, $matches[1]), $format);
        }

        $ext = $this->getFileExt();

        return $format . $ext;
    }

    /**
     * 获取文件名
     * @return string
     */
    protected function getFileName()
    {
        return substr($this->filePath, strrpos($this->filePath, '/') + 1);
    }

    /**
     * 获取文件完整路径
     * @return string
     */
    protected function getFilePath()
    {
        $fullname = $this->fullName;
        $rootPath = $_SERVER['DOCUMENT_ROOT'];

        if (substr($fullname, 0, 1) != '/') {
            $fullname = '/' . $fullname;
        }

        return $rootPath . $fullname;
    }

    /**
     * 文件类型检测
     * @return bool
     */
    protected function checkType()
    {
        return in_array($this->getFileExt(), $this->config["allowFiles"]);
    }

    /**
     * 文件大小检测
     * @return bool
     */
    protected function checkSize()
    {
        return $this->fileSize <= ($this->config["maxSize"]);
    }

    /**
     * 获取当前上传成功文件的各项信息
     * @return array
     */
    public function getFileInfo()
    {
        return [
            "state"    => $this->stateInfo,
            "url"      => $this->fullName,
            "title"    => $this->fileName,
            "original" => $this->oriName,
            "type"     => $this->fileType,
            "size"     => $this->fileSize
        ];
    }
}