<?php

// +----------------------------------------------------------------------
// | LvyeCMS 云平台下载解压服务
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.lvyecms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 旅烨集团 <web@alvye.cn>
// +----------------------------------------------------------------------

namespace Libs\System;

class CloudDownload {

    //错误信息
    private $error = '出现未知错误 CloudDownload ！';

    /**
     * 获取错误信息
     * @return type
     */
    public function getError() {
        return $this->error;
    }

    /**
     * 存储文件
     * @param string $packageUrl 文件请求地址
     * @param string $md5 文件哈希
     * @return boolean
     */
    public function storageFile($packageUrl, $md5 = '') {
        $tmpdir = $this->getTempFile($packageUrl);
        if (!file_exists($tmpdir)) {
            //发生错误
            if (mkdir($tmpdir, 0777, true) === false) {
                //错误信息
                $this->error = "创建临时缓存目录{$tmpdir}失败！";
                return false;
            }
        }
        //本地文件名
        $locale = $this->getPackFile($packageUrl);
        //文件哈希验证
        if ($this->validFile($locale, $md5)) {
            //直接使用已有安装包
            $package = $locale;
        } else {
            //下载文件包
            $package = $this->download($packageUrl, $locale);
            if ($package === false) {
                $this->error = $this->error? : '下载远程文件失败！';
                return false;
            }
        }
        //实例zip类
        $zip = new \PclZip($package);
        //解压到临时目录
        $stat = $zip->extract(PCLZIP_OPT_PATH, $tmpdir);
        //返回文件数量 不能正常解压附件
        if ($stat) {
            unlink($package);
            return true;
        } else {
            //错误信息
            $this->error = "无法正常解压文件！";
            return false;
        }
    }

    /**
     * 移动文件到指定目录
     * @param type $tmpdir 需要移动的文件路径
     * @param type $newdir 目标路径
     * @param type $pack 需要删除的安装包
     * @return boolean
     */
    public function movedFile($tmpdir, $newdir, $pack) {
        //删除文件包
        unlink($this->getPackFile($pack));
        $list = $this->rglob($tmpdir . '*', GLOB_BRACE);
        if (empty($list)) {
            $this->error = '移动文件到指定目录错误，原因：文件列表为空！';
            return false;
        }
        //权限检查
        if ($this->competence($tmpdir, $newdir) !== true) {
            return false;
        }
        //批量迁移文件
        foreach ($list as $file) {
            $newd = str_replace($tmpdir, $newdir, $file);
            //目录
            $dirname = dirname($newd);
            if (file_exists($dirname) == false && mkdir($dirname, 0777, TRUE) == false) {
                $this->error = "创建文件夹{$dirname}失败！";
                return false;
            }
            //检查缓存包中的文件如果文件或者文件夹存在，但是不可写提示错误
            if (file_exists($file) && is_writable($file) == false) {
                $this->error = "文件或者目录{$file}，不可写！";
                return false;
            }
            //检查目标文件是否存在，如果文件或者文件夹存在，但是不可写提示错误
            if (file_exists($newd) && is_writable($newd) == false) {
                $this->error = "文件或者目录{$newd}，不可写！";
                return false;
            }
            //检查缓存包对应的文件是否文件夹，如果是，则创建文件夹
            if (is_dir($file)) {
                //文件夹不存在则创建
                if (file_exists($newd) == false && mkdir($newd, 0777, TRUE) == false) {
                    $this->error = "创建文件夹{$newd}失败！";
                    return false;
                }
            } else {
                //========文件处理！=============
                if (file_exists($newd)) {
                    //删除旧文件（winodws 环境需要）
                    if (!unlink($newd)) {
                        $this->error = "无法删除{$newd}文件！";
                        return false;
                    }
                }
                //生成新文件，也就是把下载的，生成到新的路径中去
                if (!rename($file, $newd)) {
                    $this->error = "无法生成{$newd}文件！";
                    return false;
                }
            }
        }
        //删除临时目录
        LvyeCMS()->Dir->delDir($tmpdir);
        return true;
    }

    /**
     * 文件权限检查
     * @param type $tmpdir 需要移动的文件路径
     * @param type $newdir 目标路径
     * @return boolean
     */
    public function competence($tmpdir, $newdir) {
        $list = $this->rglob($tmpdir . '*', GLOB_BRACE);
        if (empty($list)) {
            return true;
        }
        //权限检查
        foreach ($list as $file) {
            $newd = str_replace($tmpdir, $newdir, $file);
            //目录
            $dirname = dirname($newd);
            if (file_exists($dirname) == false && mkdir($dirname, 0777, TRUE) == false) {
                $this->error = "创建文件夹{$dirname}失败！";
                return false;
            }
            //检查缓存包中的文件如果文件或者文件夹存在，但是不可写提示错误
            if (file_exists($file) && is_writable($file) == false) {
                $this->error = "文件或者目录{$file}，不可写！";
                return false;
            }
            //检查目标文件是否存在，如果文件或者文件夹存在，但是不可写提示错误
            if (file_exists($newd) && is_writable($newd) == false) {
                $this->error = "文件或者目录{$newd}，不可写！";
                return false;
            }
            //检查缓存包对应的文件是否文件夹，如果是，则创建文件夹
            if (is_dir($file)) {
                //文件夹不存在则创建
                if (file_exists($newd) == false && mkdir($newd, 0777, TRUE) == false) {
                    $this->error = "创建文件夹{$newd}失败！";
                    return false;
                }
            } else {
                //========文件处理！=============
                if (file_exists($newd)) {
                    if (!is_writable($newd)) {
                        $this->error = "文件 {$newd} 不可写！";
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * 遍历文件目录，返回目录下所有文件列表
     * @param type $pattern 路径及表达式
     * @param type $flags 附加选项
     * @param type $ignore 需要忽略的文件
     * @return type
     */
    public function rglob($pattern, $flags = 0, $ignore = array()) {
        //获取子文件
        $files = glob($pattern, $flags);
        //修正部分环境返回 FALSE 的问题
        if (is_array($files) === FALSE)
            $files = array();
        //获取子目录
        $subdir = glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT);
        if (is_array($subdir)) {
            foreach ($subdir as $dir) {
                if ($ignore && in_array($dir, $ignore))
                    continue;
                $files = array_merge($files, $this->rglob($dir . '/' . basename($pattern), $flags, $ignore));
            }
        }
        return $files;
    }

    /**
     * 验证文件哈希
     * @param type $file 文件路径
     * @param type $hash MD5 值
     * @return type
     */
    public function validFile($file, $hash) {
        return file_exists($file) && md5_file($file) == $hash;
    }

    /**
     * 下载文件包临时存放路径
     * @param type $file 远程地址
     * @return type
     */
    public function getPackFile($file) {
        return RUNTIME_PATH . 'Cloud/' . md5(basename($file)) . '.zip';
    }

    /**
     * 获取临时目录路径
     * @param string $file 远程地址
     * @return string
     */
    public function getTempFile($file) {
        $basename = pathinfo($file);
        return RUNTIME_PATH . 'Cloud/' . md5($basename['filename']) . '/';
    }

    /**
     * 远程保存
     * @param type $url 远程地址
     * @param type $file 保存路径
     * @param type $timeout 超时时间
     * @return boolean
     */
    public function download($url, $file = '', $timeout = 60) {
        if (empty($url)) {
            $this->error = '下载地址为空！';
            return false;
        }
        //提取文件名
        $filename = pathinfo($url, PATHINFO_BASENAME);
        if ($file && is_dir($file)) {
            //构造存储名称
            $file = $file . $filename;
        } else {
            //提取文件名
            $file = empty($file) ? $filename : $file;
            //提取目录名
            $dir = pathinfo($file, PATHINFO_DIRNAME);
            //目录不存在时创建
            !is_dir($dir) && mkdir($dir, 0755, true);
            $url = str_replace(" ", "%20", $url);
        }
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            $temp = curl_exec($ch);
            if (!curl_error($ch)) {
                if (empty($temp)) {
                    $this->error = '下载失败，下载的文件为空！';
                    return false;
                }
                if (file_put_contents($file, $temp)) {
                    return $file;
                } else {
                    $this->error = "保存文件失败！文件:{$file}";
                    return false;
                }
            } else {
                $error = curl_error($ch);
                $this->error = "Curl 下载出现错误！";
                if ($error) {
                    $this->error .= "错误信息：{$error}";
                }
                return false;
            }
        } else {
            //PHP 5.3 兼容
            if (PHP_VERSION >= '5.3') {
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                $opts = array(
                    "http" => array(
                        "method" => "GET",
                        "header" => $userAgent,
                        "timeout" => $timeout)
                );
                $context = stream_context_create($opts);
                $res = copy($url, $file, $context);
            } else {
                $res = copy($url, $file);
            }
            if ($res) {
                return $file;
            }
            $this->error = '使用 copy 下载文件失败，请检查防火墙，或者网络不稳定请稍后！';
            return false;
        }
    }

}
