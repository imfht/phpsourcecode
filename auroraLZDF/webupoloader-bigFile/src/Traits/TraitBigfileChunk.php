<?php

namespace AuroraLZDF\Bigfile\Traits;

use AuroraLZDF\Bigfile\Bigfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;

Trait TraitBigfileChunk
{
    /**
     * 文件经过 【 切片——整合 】 已经上传到服务器指定位置
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function uploadChunk(Request $request)
    {
        // 关闭缓存
        /*header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");*/

        $data = $request->all();

        $uploader = new Bigfile();

        // 用于断点续传，验证指定分块是否已经存在，避免重复上传
        $status = $request->input('status');
        if (isset($status)) {
            if ($status == 'chunkCheck') {
                $target = $uploader->path . $data['name'] . '/' . $data['chunkIndex'];
                if (file_exists($target) && filesize($target) == $_POST['size']) {
                    return ['ifExist' => 1];
                }
                return ['ifExist' => 0];

            } elseif ($status == 'md5Check') {
                // 模拟持久层查询
                $dataArr = array(
                    'b0201e4d41b2eeefc7d3d355a44c6f5a' => 'auroras.jpg'
                );

                if (isset($dataArr[$data['md5']])) {
                    return ['ifExist' => 1, 'path' => $dataArr[$data['md5']]];
                }
                return ['ifExist' => 0];
            } elseif ($status == 'chunksMerge') {

                if ($path = $uploader->chunksMerge($data['name'], $data['chunks'], $data['ext'])) {
                    //todo 把md5签名存入持久层，供未来的秒传验证
                    return ['status' => 1, 'path' => $path, 'type' => 1];
                }
                return ['status' => 0, 'chunksMerge' => 0, 'msg' => $uploader->getErrorMsg()];
            }
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file'); //获取上传文件

            if (($path = $uploader->upload($file, $data)) !== false) {
                return ['status' => 1, 'path' => $path, 'type' => 2];
            }

            return ['status' => 0, 'upload' => 0, 'msg' => $uploader->getErrorMsg()];
        }
    }

    /**
     * 将切片上传到服务器的文件移动到指定位置
     *
     * @param array $file
     * @return array
     */
    public function uploadToServer(array $file)
    {
        $save_path = config('bigfile.save_path');
        $max_file_size_in_bytes = config('bigfile.max_size');

        $result = ['mes' => '', 'code' => 2];

        $origin_name = $file['name'];
        $tmp_file = $file['tmp_name'];
        $file_size = $file['size'];

        if (!$origin_name) {   // 原始文件名称
            $result['mes'] = '文件不存在';
            return $result;
        }

        if (!is_file($tmp_file)) {  // 缓存文件名称
            $result['mes'] = '临时文件不存在';
            return $result;
        }

        if ($file_size > $max_file_size_in_bytes) {
            $result['mes'] = '文件尺寸太大';
            return $result;
        }
        if ($file_size <= 0) {
            $result['mes'] = '文件大小不能为0';
            return $result;
        }

        $save_path = $save_path . $origin_name;

        // 重复文件名会被替换
        if (!Storage::disk('public')->put($save_path, $tmp_file)) {
            $result['mes'] = '上传文件失败： ' . json_encode($file);
            return $result;
        }

        // 是否需要即时清理临时文件
        if (config('bigfile.remove_tmp_file')) {
            unlink($file["tmp_name"]);
        }

        $result = [
            'mes' => '文件上传成功',
            'code' => 1,
            'url' => $save_path,
            'size' => $file_size,
            'show_name' => $origin_name
        ];
        return $result;
    }
}
