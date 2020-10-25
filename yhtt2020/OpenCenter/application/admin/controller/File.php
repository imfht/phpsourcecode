<?php
/**
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: sun(slf02@ourstu.com)
 * Date: 2018/9/27
 * Time: 16:50
 */

namespace app\admin\controller;


use think\Controller;

class File extends Controller
{
    /**
     * @return \think\response\Json
     * @author sun slf02@ourstu.com
     * @date 2018/9/28 9:09
     * 上传图片
     */
    public function uploadPicture()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->validate(['size' => 2 * 1024 * 1024, 'ext' => 'jpg,png,gif'])->move('../public/uploads');
        if ($info) {
            // 成功上传后 获取上传信息
            $id = model('Picture')->upload($info);
            $result = [
                'code' => 0,
                'msg' => '上传成功',
                'id' => $id,
                "data" => ['src' => pic($id)]

            ];
        } else {
            // 上传失败获取错误信息
            $result = [
                'code' => -1,
                'msg' => $file->getError()
            ];
        }
        return json($result);
    }
}