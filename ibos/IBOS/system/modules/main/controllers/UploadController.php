<?php

namespace application\modules\main\controllers;

use application\core\controllers\Controller;
use application\core\utils\Env;

class UploadController extends Controller
{

    public function actionUpload()
    {
        $returnData = array();
        if (empty($_FILES)){
            $echo = array('icon' => '', 'aid' => -1, 'name' => '上传失败', 'url' => '');
            $this->ajaxReturn(json_encode($echo), 'eval');
        }

        // 安全验证
        // 附件类型，可指定可不指定，不指定为普通类型
        $attachType = Env::getRequest('type');
        if (empty($attachType)) {
            $attachType = 'common';
        }

        $module = Env::getRequest('module');
        $object = '\application\modules\main\components\CommonAttach';

        if (class_exists($object)) {
            foreach ($_FILES['Filedata']['error'] as $key => $error){
                if ($_FILES['Filedata']['error'][$key] !=0){
                    $returnData[$key] = array('icon' => '', 'aid' => -1, 'name' => '上传失败', 'url' => '');
                }else{
                    $uploadFile = array();
                    $uploadFile['name'] = $_FILES['Filedata']['name'][$key];
                    $uploadFile['tmp_name'] = $_FILES['Filedata']['tmp_name'][$key];
                    $uploadFile['size'] = $_FILES['Filedata']['size'][$key];
                    $uploadFile['type'] = $_FILES['Filedata']['type'][$key];
                    $uploadFile['error'] = $_FILES['Filedata']['error'][$key];
                    $attach = new $object('Filedata', $module, $uploadFile);
                    $return = $attach->upload();
                    $return = json_decode($return, true);
                    $return['size'] = $this->getSize($_FILES['Filedata']['size'][$key]);
                    $returnData[$key] = $return;
                }
            }
        }
        $this->ajaxReturn(array(
            'isSuccess' => true,
            'msg' => '上传成功',
            'data' => $returnData,
        ));
    }

    private function getSize($_size) {
//判断文件大小是否大于1024bit 如果大于，则将大小取值为KB，以此类推
        if ($_size>1024*1024) {
            return round($_size/1024/1024,2).' MB';
        }else if ($_size>1024) {
            $_size=$_size/1024;
            return ceil($_size).'KB';
        }else {
            return $_size.' bit';
        }
    }
}