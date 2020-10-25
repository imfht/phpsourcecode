<?php
namespace app\system\admin;

/**
 * 系统上传
 */
class UploadAdmin extends \app\system\admin\SystemAdmin {

    /**
     * AJAX上传文件
     */
    public function index() {
        $return = array('status' => 1, 'info' => '上传成功', 'data' => '');
        $file = target('system/SystemFile');
        $info = $file->uploadData();
        if ($info) {
            $info = reset($info);
            $return['data'] = $info;
        } else {
            $return['status'] = 0;
            $return['info'] = $file->getError();
        }
        $this->json($return);
    }

    /**
     * 编辑器上传
     */
    public function editor() {

        $file = target('system/SystemFile');
        $info = $file->uploadData();
        if ($info) {
            $info = reset($info);
            $return = $info;
            $return['error'] = 0;
        } else {
            $return = [];
            $return['error'] = 1;
            $return['message'] = $file->getError();
        }

        $this->json($return);
    }

}

