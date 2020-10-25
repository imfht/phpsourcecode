<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Main\Base\BaseController;
use App\Common\Code;
use App\Common\Msg;

class ToolsController extends BaseController
{
    /**
     * 设置不需要权限
     */
    protected $isNoAuth = true;

    /**
     * 设置不需要登录的的Action,不加Action前缀
     * @author Chengcheng
     * @date   2016年10月23日 20:39:25
     * @return array
     */
    protected function noLogin()
    {
        // 以下Action不需要登录权限
        return [];
    }

    /**
     * upImage
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return string
     */
    function upFile()
    {
        //获取上传文件信息
        $file = request()->file('file');
        if (!$file || !$file->isValid()) {
            return $this->showMessage([], Code::SYSTEM_PARAMETER_NULL, null, '上传文件file');
        }

        //检查文件扩展名
        $ext = $file->getClientOriginalExtension();
        if (!in_array(strtolower($ext), ['jpg', 'jpeg', 'bmp', 'png', 'gif', 'pdf', 'xls', 'doc', 'docx'])) {
            return $this->showMessage([], Code::SYSTEM_PARAMETER_FORMAT_ERROR, null, '上传文件类型');
        }
        $originalName = $file->getClientOriginalName();

        //建立reader对象
        $fileRealPath = $file->getRealPath();
        $filePath     = '/uploads/' . date('YmdHis') . '-' . uniqid() . '_' . $originalName;

        $fileStorePath = public_path() . $filePath;

        move_uploaded_file($fileRealPath, $fileStorePath);

        $scheme = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
        $url    = $scheme . $_SERVER['HTTP_HOST'];

        //返回数据
        $result              = [];
        $result['path']      = $filePath;
        $result['full_path'] = $url . $filePath;
        return $this->showMessage($result);
    }

}
