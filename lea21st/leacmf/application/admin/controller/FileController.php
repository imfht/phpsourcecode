<?php

namespace app\admin\controller;

use app\common\util\Qiniu;
use Hashids\Hashids;
use lea\Y;
use think\Controller;
use think\Db;
use think\Image;
use think\Request;

class FileController extends BaseController
{

    public function upload(Request $request)
    {
        $type = $request->get('type', 'image');
        $file = $request->file('file');
        if (empty($file)) {
            return json(['status' => 5, 'msg' => '文件不存在']);
        }
        //获取上传配置
        $config = config('upload.');
        $path   = $config['upload_path'] . '/' . $type;
        if (!isset($config['upload_size_limit'][$type])) {
            return json(['code' => 2, 'msg' => '上传文件格式不允许']);
        }
        $info = $file->validate(['size' => $config['upload_size_limit'][$type], 'ext' => $config['upload_type_limit'][$type]])->move($path);
        if ($info) {
            $src = '/uploads/' . $type . '/' . $info->getSaveName();
            return json(['code' => 0, 'msg' => '上传成功', 'data' => ['src' => $src]]);
        } else {
            return json(['code' => -10, 'msg' => $file->getError()]);
        }
    }

    public function um(Request $request)
    {
        $file = $request->file('file');
        if (empty($file)) {
            return response(json_encode(['code' => 1, 'state' => '文件不存在']));
        }
        $type = 'um-editor';
        //获取上传配置
        $config = config('upload.');
        $path   = $config['upload_path'] . '/' . $type;
        if (!isset($config['upload_size_limit'][$type])) {
            return response(json_encode(['code' => 2, 'state' => '上传文件格式不允许']));
        }
        $info = $file->validate(['size' => $config['upload_size_limit'][$type], 'ext' => $config['upload_type_limit'][$type]])->move($path);
        if ($info) {
            return response(json_encode(['code' => 0, 'state' => 'SUCCESS', 'url' => '/uploads/' . $type . '/' . $info->getSaveName()]));
        } else {
            return response(json_encode(['code' => -10, 'state' => $file->getError()]));
        }
    }
}