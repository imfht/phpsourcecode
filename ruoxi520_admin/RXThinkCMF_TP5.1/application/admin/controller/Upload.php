<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 文件上传-控制器
 * @author 牧羊人
 * @date 2019/4/28
 * Class Upload
 * @package app\admin\controller
 */
class Upload extends Backend
{
    /**
     * 初始化方法
     * @author 牧羊人
     * @date 2019/4/28
     */
    public function initialize()
    {
        parent::initialize();
        // TODO...
    }

    /**
     * 上传图片
     * @return array
     * @author 牧羊人
     * @date 2019/4/28
     */
    public function uploadImage()
    {
        // 设置上传约束
        $config = [
            'size' => 10000000,
            'ext' => 'jpg,gif,png,bmp,jpeg,JPG'
        ];
        $file = $this->request->file('file');
        $upload_path = UPLOAD_TEMP_PATH;
        $info = $file->validate($config)->move($upload_path);
        if ($info) {
            //上传成功
            $file_path = IMG_URL . "/temp/" . $info->getSaveName();
            return message("上传成功", true, $file_path);
        } else {
            //上传失败
            $error = $file->getError();
            return message($error, false);
        }
    }

    /**
     * 上传附件
     *
     * @author zongjl
     * @date 2018-12-21
     */
    function uploadFile()
    {
        // 设置上传约束
        $config = [
            'size' => 1024 * 1024 * 10,
            'ext' => 'mp4,avi,mov,rmvb,flv,xls,xlsx,doc,docx'
        ];
        $file = $this->request->file('file');
        $upload_path = UPLOAD_TEMP_PATH;
        $info = $file->validate($config)->move($upload_path);
        if ($info) {
            //上传成功
            $file_path = IMG_URL . "/temp/" . $info->getSaveName();
            $result = [
                'fileName' => $info->getInfo('name'),
                'savePath' => $file_path,
            ];
            return message("上传成功", true, $result);
        } else {
            //上传失败
            $error = $file->getError();
            return message($error, false);
        }
    }
}
