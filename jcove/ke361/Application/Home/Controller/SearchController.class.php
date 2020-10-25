<?php
namespace Home\Controller;
use Home\Model\GoodsModel;
class SearchController extends HomeController{

    public function index(){
        $keywords = I('keywords') ;
        if(empty($keywords)){
            $this->error ('请输入搜索的关键词');
        }
        $page = I('get.p','','intval');
        $where['name'] = array('like',"%".$keywords."%");
        $GoodsModel = new GoodsModel();
     
        
        $goods = $this->lists($GoodsModel,$where);
        foreach ($goods as $k=>$v){
            $goods[$k]['url'] = U('goods/info',array('id'=>$v['id']));
        }
        $this->assign('page',$page+1);
        $this->assign('list',$goods);
        $this->assign('keyword',$keywords);
        $this->setSiteTitle('商品搜索'.$keywords);
        $this->setKeyWords($keywords);
        $this->setDescription('搜索商品关键词--'.$keywords);
        $this->show();
        
    }
}