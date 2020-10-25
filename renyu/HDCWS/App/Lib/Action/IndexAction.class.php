<?php

class IndexAction extends GlobalAction {

    public function index(){
    	
    	//Banner展示
    	$this -> assign('banner', C('HD_banner'));

    	/*产品展示*/
    	$this -> assign('allProCatList', $this -> getProCatList());
    	
    	$product = D('product');
    	
    	$proList = $product -> field('id,name,price,thumburl,cid') -> where('status=1') -> order('time desc') -> limit('0,20') -> select();
    	
    	$this -> assign('proList', $proList);
    	
    	/*团队展示*/
    	$article = D('article');
    	
    	$teamList = $article -> field('id,title,thumburl') -> where('status=1 and tid=2') -> order('time desc') -> limit('0,20') -> select();
    	
    	$this -> assign('teamList', $teamList);
    	
		/*关于公司*/
    	$aboutList = $article -> field('id,title,thumburl,description') -> where('status=1 and tid=3') -> order('time desc') -> limit('0,3') -> select();
    	
    	$this -> assign('aboutList', $aboutList);
    	
     	/*友情链接*/
    	$link = D('link');
    	
    	$linkList = $link -> where('status=1') -> order('sort desc') -> select();
    	
    	$this -> assign('linkList', $linkList);   	
    	
		$this -> show();

    }
    
	protected function getProCatList(){
	
    	$cat = D('product_cat');
    	
    	$catList = $cat -> field('id,name') -> where('cid = 0 and status=1') -> order('sort desc') -> select();
    	
    	return $catList;
		
	}    
	
}