<?php

namespace Controller\Editor;

use SCH60\Kernel\BaseController;

use Service\AliyunCsc\YundunSpamValidate;

class Content extends BaseController{
    
    protected $layout = "layout_default";
    
    public function actionCheck(){
        $viewData = array();
        $viewData['title'] = '发表文章';
        $viewData['parentTitle'] = '编辑工作站';
        return $this->render(null, $viewData);
    }
    
    public function actionSubmit(){
        $content = trim($this->request->input($_POST, 'content'));
        if(empty($content)){
            $this->response->error('内容为空');
        }
        
        $spamValidateSrv = new YundunSpamValidate();
        $isOk = true;
        $error = "";
        if(!$spamValidateSrv->run($content)){
            $isOk = false;
            $error = $spamValidateSrv->getLastError();
        }
        
        $viewData = array();
        $viewData['title'] = '发表文章结果';
        $viewData['parentTitle'] = '编辑工作站';
        
        $viewData['content'] = $content;
        $viewData['isOk'] = $isOk;
        $viewData['error'] = $error;
        return $this->render(null, $viewData);
        
    }
    
}
