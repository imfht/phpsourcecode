<?php
namespace app\app\controller;

class ArticleController extends BaseController
{
    public function index()
    {
        $id = input('param.id');
        
        model('Article')->where('id', $id)->setInc('visiter', 1);
        
		$article = model('Article')->where('status',1)->find($id);
		$this->assign('article', $article);
		
		$wx_config = model('WxConfig')->find();
		$this->assign('wx_config', $wx_config);

        return view();
    }
}
