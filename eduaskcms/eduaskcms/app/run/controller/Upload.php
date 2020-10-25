<?php
namespace app\run\controller;


use app\common\controller\Run;

class Upload extends Run
{
    
    function uploader()
    {
        if(isset($_GET['CKEditorFuncNum'])) {
            $CKEditorFuncNum = $_GET['CKEditorFuncNum'];
        } else {
            $CKEditorFuncNum = 0;
        }
        
        $upload  = $_FILES['upload'];
        $filename  = uploadFile($upload, $this->args['model']);
        
        if ($filename) {
            $this->assign->functionNumber=$CKEditorFuncNum;
    		$this->assign->fileUrl = $filename;
    		$this->assign->message = '上传成功';
        } else {
            $this->assign->functionNumber = $CKEditorFuncNum;
            $this->assign->message = $GLOBALS['upload_file_error'];
        }
        
        $this->fetch = 'upload_result';
    }
}
