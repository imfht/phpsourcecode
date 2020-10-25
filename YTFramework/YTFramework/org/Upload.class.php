<?php

/**
 * =============================================================================
 *  [YTF] (C)2015-2099 Yuantuan Inc.
 *  This content is released under the Apache License, Version 2.0 (the "License");
 *  Licensed    http://www.apache.org/licenses/LICENSE-2.0
 *  Link        http://www.ytframework.cn
 * =============================================================================
 *  @author     Tangqian<tanufo@126.com> 
 *  @version    $Id: Upload.class.php 108 2016-04-25 09:34:47Z lixiaohui $
 *  @created    2015-10-10
 *  文件上传
 * =============================================================================                   
 */

namespace org;

class Upload
{

    //文件保存路径
    public $save_path = '';
    public $save_path_y = '';
    //上传错误信息
    private $error = '';
    //图片文件上传
    public $img;
    private $uploadInfo = '';
    //图片文件上传类型
    public $file_ext = array('gif', 'jpg', 'jpeg', 'bmp', 'png', 'rar');
    public $img_ext = array('gif', 'jpg', 'jpeg', 'bmp', 'png');
    //上传文件大小
    public $attachment_size = '-1';

    /**
     * 
     * @param type $savePath  上传文件主目录
     * @param type $savePathY  上传文件子目录名
     */
    function __construct($config = array())
    {
        $keys = array('save_path', 'save_path_y', 'file_ext', 'attachment_size'); //上传配置选项
        foreach ($config as $key => $v) {
            if (in_array(strtolower($key), $keys)) {
                $this->$key = $v;
            }
        }
    }

    //保存文件
    private function save($file)
    {
        //上传目录判断
        $this->save_path = str_replace('\\', '/', $this->save_path);
        $this->save_path = rtrim($this->save_path, '/') . '/';
        $dest = $this->save_path . $this->save_path_y . '/';
        if (!is_dir($dest)) {
            @mkdir($dest, 0777, true);
        }
        if (!move_uploaded_file($file['tmp_name'], $dest . $file['savename'])) {
            $this->error = '文件移动错误！';
            return false;
        }
        return true;
    }

    //上传文件 支持多文件上传

    function _upload($file)
    {

        $filearray = $oldfilearray = array();
        $uploadone = '';
        if (!empty($file['name'])) {
            //生成多文件数组
            if (is_array($file['name'])) {
                $keys = array_keys($file);
                $count = count($file['name']);
                for ($i = 0; $i < $count; $i++) {
                    foreach ($keys as $k) {
                        if (!empty($file[$k])) {
                            $oldfilearray[$i][$k] = $file[$k][$i];
                        }
                    }
                }
                //过滤无效的上传
                foreach ($oldfilearray as $ks => $vs) {
                    if ($vs['name'] || $vs['error'] != 0) {
                        $filearray[$ks] = $vs;
                    }
                }
            } else {
                $uploadone = true;
                $filearray = $file;
            }
            //单文件上传

            if ($uploadone) {
                $filearray['extension'] = $this->getExt($file['name']);
                //用于前台判断图片
                $filearray['img'] = (in_array($filearray['extension'], $this->img_ext)) ? true : false;
                //生成保存文件名
                $filearray['savename'] = $this->getFileName($filearray);
                $filearray['savepath'] = $this->save_path_y;
                if (!$this->check($filearray)) {
                    return false;
                }
                if (!$this->save($filearray)) {
                    return false;
                } else {
                    unset($filearray['tmp_name'], $filearray['error']);
                    //文件上传后的信息
                    $this->uploadInfo[] = $filearray;
                }
                return true;
            } else {
                //多文件上传
                $fail = [];
                $uploadInfo = [];
                foreach ($filearray as $k => $v) {
                    if (!empty($v['name'])) {
                        $v['extension'] = $this->getExt($v['name']);
                        //用于前台判断图片
                        $v['img'] = (in_array($v['extension'], $this->img_ext)) ? 1 : 0;
                        //生成保存文件名
                        $v['savename'] = $this->getFileName($v);
                        $v['savepath'] = $this->save_path_y;
                        if (!$this->check($v)) {
                            $fail[] = $this->error;
                            continue;
                        }
                        if (!$this->save($v)) {
                            $fail[$v['name']] = $this->error;
                            continue;
                        } else {
                            unset($v['tmp_name'], $v['error']);
                            //保存文件信息 供外部使用
                            $uploadInfo[] = $v;
                        }
                    } else {
                        $fail[] = '请选择文件';
                    }
                }
                if (empty($uploadInfo)) {
                    $this->error = $fail;
                    return false;
                }
                $this->uploadInfo = ['success' => $uploadInfo, 'fail' => $fail];
                return true;
            }
        } else {
            $this->error = '请选择上传文件！';
            return false;
        }
    }

    //获取文件扩展名
    private function getExt($name)
    {
        $info = pathinfo($name);
        if (empty($info['extension'])) {
            $info['extension'] = 'jpg';
        }
        return strtolower($info['extension']);
    }

    //文件格式判断
    private function getExtCheck($file)
    {
        if (empty($file['extension'])) {
            $file['extension'] = 'jpg';
        }
        if (!in_array($file['extension'], $this->file_ext)) {
            return false;
        }

        return true;
    }

    //返回错误信息
    public function getError()
    {

        return $this->error;
    }

    public function getUploadInfo()
    {
        return $this->uploadInfo;
    }

    //生成新文件名 
    private function getFileName($file)
    {
        return md5(time() . rand()) . '.' . $file['extension'];
    }

    //上传文件检查
    private function check($file)
    {

        //非法上传
        if (!is_uploaded_file($file['tmp_name'])) {
            $this->error = '非法上传文件！';
            return false;
        }
        if (!$file['size']) {
            $this->error = '上传文件大小错误！';
            return false;
        }

        if (($file['size'] > $this->attachment_size * 1024 ) && $this->attachment_size != '-1') {
            $this->error = '超出上传文件大小限制！限制:' . $this->attachment_size . 'Kb';
            return false;
        }

        //类型检查
        if (!$this->getExtCheck($file)) {
            $this->error = '上传文件格式错误！只允许上传' . implode(' , ', $this->file_ext) . '等格式文件！';
            return false;
        }

        if ($file['error'] !== 0) {
            //文件上传失败
            $this->error($file['error']);
            return false;
        }
        return true;
    }

}
