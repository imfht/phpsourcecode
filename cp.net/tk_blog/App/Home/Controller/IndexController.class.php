<?php
namespace Home\Controller;
use Common\Controller\HomebaseController;
class IndexController extends  HomebaseController {
    public function index(){
        $articleModel = new \Common\Model\ArticleModel();
        $where = array('is_recycle'=>0,'status'=>1);
        $order = " add_time DESC , aid DESC";
        $setConfig = array(
            'next' => 'Next →',
            'prev' => '← Previous',
        );
        $data = $articleModel->getListData(5,$where,$order,$setConfig);
        $this->assign('data',$data);
        $this->assign('cocolait','Cocolait博客-首页');
        $this->display();
    }
}