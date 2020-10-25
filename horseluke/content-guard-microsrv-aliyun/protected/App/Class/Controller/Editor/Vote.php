<?php

namespace Controller\Editor;

use SCH60\Kernel\BaseController;
use Service\FakeData\Votelist;

class Vote extends BaseController{
    
    protected $layout = "layout_default";
    
    public function actionList(){
        $viewData = array();
        $viewData['title'] = '投票记录列表';
        $viewData['parentTitle'] = '编辑工作站';
        
        $votelistService = new Votelist();
        $viewData['datalist'] = $votelistService->run();
        return $this->render(null, $viewData);
    }
    
}
