<?php
/**
 * Created by PhpStorm.
 * User: fengliu
 * Date: 15/8/17
 * Time: 下午11:55
 */
include '../../control.php';
class myFile extends file
{
    /**
     * AJAX: the api to recive the file posted through ajax.
     *
     * @param  string $uid
     * @access public
     * @return array
     */
    public function ajaxUploadImage($uid)
    {
        if(RUN_MODE == 'front' and !commonModel::isAvailable('forum')) exit;
        if(!$this->loadModel('file')->canUpload())  $this->send(array('error' => 1, 'message' => $this->lang->file->uploadForbidden));
        $file = $this->file->getUpload('editormd-image-file');//此处填写上传文件的字段名
        $file = $file[0];
        if($file)
        {
            if(!$this->file->checkSavePath()) $this->send(array('error' => 1, 'message' => $this->lang->file->errorUnwritable));
            if(!in_array(strtolower($file['extension']), $this->config->file->editorExtensions)) $this->send(array('error' => 1, 'message' => $this->lang->fail));
            move_uploaded_file($file['tmpname'], $this->file->savePath . $file['pathname']);

            if(in_array(strtolower($file['extension']), $this->config->file->imageExtensions) !== false)
            {
                $this->file->compressImage($this->file->savePath . $file['pathname']);
                $imageSize = $this->file->getImageSize($this->file->savePath . $file['pathname']);
                $file['width']  = $imageSize['width'];
                $file['height'] = $imageSize['height'];
            }
            $url =  $this->file->webPath . $file['pathname'];

            $file['addedBy']   = $this->app->user->account;
            $file['addedDate'] = helper::now();
            $file['editor']    = 1;
            $file['lang']      = 'all';
            unset($file['tmpname']);
            $this->dao->insert(TABLE_FILE)->data($file)->exec();

            $_SESSION['album'][$uid][] = $this->dao->lastInsertID();
            $this->loadModel('setting')->setItems('system.common.site', array('lastUpload' => time()));
            echo json_encode(array('url' => $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'] .$url,'success'=>1));
            exit;
        }
    }
}