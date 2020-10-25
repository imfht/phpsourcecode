<?php

defined('IN_CART') or die;

/**
 *
 * 文件上传处理类
 * 
 */
class Upload
{

    public $max_filesize;  // 文件上传大小限制，字符串，单位B
    public $exts;    //文件扩展名
    private $error;    //错误信息
    public $uploadInfo;   //上传信息
    public $savedir;   //保存地址
    protected $lastdir;   //文件保存路径	
    protected $thumbs;   //缩略图
    protected $customname;      //是否自定义文件名

    /**
     *
     * 构造函数，$max_filesize 单位KB
     * 
     */

    public function __construct($exts = array(), $thumbs = array(), $max_filesize = 2048, $savedir = "", $customname = true)
    {
        $this->setExts($exts);
        $this->setThumbs($thumbs);
        $this->setMaxFilesize($max_filesize);
        $this->setSaveDir($savedir);
        $this->setCustomname($customname);
    }

    public function setExts($exts)
    {
        $this->exts = $exts ? array_map("strtolower", $exts) : array();
    }

    public function setThumbs($thumbs)
    {
        $this->thumbs = $thumbs;
    }

    public function setMaxFilesize($max_filesize = '')
    {
        !$max_filesize && $max_filesize = 2048;
        $this->max_filesize = $max_filesize * 1024;
    }

    public function setSaveDir($savedir = '')
    {
        if (!$savedir) {
            list($year, $month, $day) = explode("-", date("Y-m-d"));
            $this->savedir = SITEPATH . "/uploads";
            $this->lastdir = $this->savedir . "/" . $year . "/" . $month . "/" . $day . "/";
        } else {
            $this->savedir = $savedir;
            $this->lastdir = $savedir . "/";
        }
    }

    public function setCustomname($customname)
    {
        $this->customname = $customname;
    }

    /**
     *
     * 错误提示信息
     * 
     */
    public function error($errno)
    {
        switch ($errno) {
            case 1:
                $this->error = __("upload_err_ini_size");
                break;
            case 2:
                $this->error = __("upload_err_form_size");
                break;
            case 3:
                $this->error = __("upload_err_partial");
                break;
            case 4:
                $this->error = __("upload_err_no_file");
                break;
            case 6:
                $this->error = __("upload_err_no_tmp_dir");
                break;
            case 7:
                $this->error = __("upload_err_cant_write");
                break;
            default:
                $this->error = __("unknown_err");
        }
    }

    /**
     *
     * 上传
     * 
     */
    public function uploadfile()
    {
        if ($this->error) {
            return false;
        }
        $files = $this->dealFiles($_FILES);
        $filedir = $this->getFileDir();
        $isupload = false;
        foreach ($files as $key => $file) {
            if (!empty($file["name"])) {
                $file["key"] = $key;
                $file["ext"] = $this->getExt($file["name"]);
                $file["name"] = basename($file["name"], "." . $file["ext"]);
                $file["savepath"] = $filedir;
                $file["savename"] = $filedir . $this->getSaveName($file["name"], $file['ext']);
                $file['url'] = str_replace(SITEPATH . '/', '', $file['savename']);
                if (!$this->check($file) || !$this->save($file))
                    return false;
                unset($file['tmp_name'], $file['error']);

                $isupload = true;
            }
        }
        if (!$isupload) {
            $this->error = __("unselect_file");
            return false;
        }
        return true;
    }

    /**
     *
     * 保存上传文件
     * 
     */
    public function save($file)
    {
        if (!move_uploaded_file($file['tmp_name'], $file["savename"])) {
            $this->error = __('file_save_err');
            return false;
        }


        if (is_array($this->thumbs) && $this->thumbs) { //截图
            require_once COMMONPATH . "/image.class.php";

            foreach ($this->thumbs as $k => $size) {

                $file["{$k}pic"] = 1;
                Image::thumb($file["savename"], "{$file['savename']}_{$size}x{$size}.jpg", $size, $size);
            }
        }
        $this->uploadInfo[] = $file;
        return true;
    }

    /**
     * 获取保存的文件名称
     *
     */
    public function getSaveName($filename, $ext)
    {
        if ($this->customname === true) { //如果是系统自定义
            return date("YmdHis") . getRandString(4) . getMd5String($filename) . "." . $ext;
        } else if ($this->customname) {
            return $this->customname;
        }
        return $filename;
    }

    /**
     *
     * 获取扩展名
     *
     */
    public function getExt($file)
    {
        $pathinfo = pathinfo($file);
        return $pathinfo["extension"];
    }

    /**
     *
     * 处理文件
     *
     */
    private function dealFiles($files)
    {
        $fileArray = array();
        $n = 0;
        foreach ($files as $file) {
            if (is_array($file['name'])) {
                $keys = array_keys($file);
                $count = count($file['name']);
                for ($i = 0; $i < $count; $i++) {
                    foreach ($keys as $key)
                        $fileArray[$n][$key] = $file[$key][$i];
                    $n++;
                }
            } else {
                $fileArray[$n] = $file;
                $n++;
            }
        }
        return $fileArray;
    }

    /**
     *
     * 获取文件路径
     *
     */
    public function getFileDir()
    {
        if (!is_dir($this->lastdir) && !remkdir($this->lastdir)) {
            $this->error = __("mk_upload_dir_err", $this->lastdir);
            return false;
        }
        return $this->lastdir;
    }

    /**
     *
     * 检查文件
     *
     */
    public function check($file)
    {
        if ($file['error'] !== 0) {
            $this->error($file['error']);
            return false;
        }

        if (!$this->checkSize($file['size'])) {
            $this->error = __("err_upload_file_size");
            return false;
        }

        if (!$this->checkExt($file['ext'])) {
            $this->error = __("err_upload_file_type");
            return false;
        }

        if (!$this->checkUpload($file['tmp_name'])) {
            $this->error = __("err_upload_file");
            return false;
        }
        return true;
    }

    /**
     *
     * 检查扩展名
     *
     */
    public function checkExt($ext)
    {
        return in_array(strtolower($ext), $this->exts);
    }

    /**
     *
     * 检查大小
     *
     */
    public function checkSize($size)
    {
        return $this->max_filesize && $size <= $this->max_filesize;
    }

    /**
     *
     * 检查是否上传文件
     *
     */
    public function checkUpload($file)
    {
        return is_uploaded_file($file);
    }

    /**
     *
     * 返回上传文件信息
     *
     */
    public function getUploadInfo()
    {
        return $this->uploadInfo;
    }

    /**
     *
     * 返回错误
     *
     */
    public function getError()
    {
        return $this->error;
    }

}
