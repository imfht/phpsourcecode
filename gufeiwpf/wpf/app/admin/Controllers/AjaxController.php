<?php
namespace Wpf\App\Admin\Controllers;
class AjaxController extends \Wpf\App\Admin\Common\Controllers\CommonController{
    public $_model;
    
    public function initialize(){
        parent::initialize();
    }
    
    public function onConstruct(){
        parent::onConstruct();
        $this->_model = new \Wpf\Common\Models\Config();
        
    }
    
    
    /**
     * 上传图片
     * 
     * AjaxController::fileupload()
     * 
     * @return void
     */
    public function fileuploadAction(){        
        if ($this->request->hasFiles() == true) {
            $filearray = $this->request->getUploadedFiles();
            $file = $filearray[0];            
        }else{
            $this->error("非法请求！");
        }
        
        $class = new \Wpf\Common\Models\Photo();
        
        if($photoid = $class->uploadfile($file)){
            $photoid = $class->id;            
        }else{
            $this->error($class->error);
        }
        
        $data = array(
            "photoid" => $photoid,
            "thumbnailUrl" => $class->geturl($photoid,1)."/80x80",
            "url" => $class->geturl($photoid,1)
        );
        
        $this->success($data);
        
    }
}