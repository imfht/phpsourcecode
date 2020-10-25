<?php

class IndexAction extends GlobalAction {
	
	private $navType = 1;//1为顶部，2底部
	
	function _initialize(){
	
		$headNav = $this -> setUrl($this -> getHeadNavList());
	
		$this -> assign('headNavList', $headNav);
	
	}

    public function index(){

    	//Banner展示
    	$this -> assign('banner', C('HD_banner'));

    	/*产品展示*/
    	$this -> assign('allProCatList', $this -> getProCatList());
    	
    	$product = D('product');
    	
    	$proList = $product -> field('id,name,price,thumburl,cid') -> where('status=1') -> order('time desc') -> limit('0,6') -> select();
    	
    	$this -> assign('proList', $proList);
    	
    	/*文章中心*/
    	$article = D('article');
    	 
    	$artList = $article -> field('id,title,thumburl') -> where('status=1 and tid=1') -> order('time desc') -> limit('0,6') -> select();
    	 
    	$this -> assign('artList', $artList);
    	
    	/*团队展示*/
    	$teamList = $article -> field('id,title,thumburl') -> where('status=1 and tid=2') -> order('time desc') -> limit('0,2') -> select();
    	
    	$this -> assign('teamList', $teamList);
    	
		/*关于公司*/
    	$aboutList = $article -> field('id,title,thumburl,description') -> where('status=1 and tid=3') -> order('time desc') -> limit('0,2') -> select();
    	
    	$this -> assign('aboutList', $aboutList);
    	
		$this -> show();

    }
    
	protected function getProCatList(){
	
    	$cat = D('product_cat');
    	
    	$catList = $cat -> field('id,name') -> where('cid = 0 and status=1') -> order('sort desc') -> select();
    	
    	return $catList;
		
	}
	
	private function setUrl($list){
	
		foreach($list as $key => $val){
	
			if(!preg_match('/http\:\/\//', $val['url'])){
	
				$list[$key]['url'] = U($val['url']);
	
				$list[$key]['url'] = str_replace('amp;', '', $list[$key]['url']);
	
			}
				
			if(!empty($val['list'])) $list[$key]['list'] = $this -> setUrl($list[$key]['list']);
	
		}
	
		return $list;
	
	}
	
	public function nav(){

		$nav = D('nav');
		
		$rootId = 0;
		
		$list = $nav -> field('id,nid,name,url') -> order('tid asc,sort desc') -> select();

		$list = $this -> setUrl($list);
		
		$listTree = $this -> listToTree($list, 'id', 'nid', 'children', $rootId);

		$this -> assign('list', $listTree);	
		
		$this -> display();
		
	}
	
	protected function getHeadNavList(){
	
		$nav = D('nav');
	
		$navList = $nav -> field('id,nid,name,url') -> order('sort desc') -> where('tid = 1') -> select();
	
		$tree = $this -> listToTree($navList, 'id', 'nid', 'list');
	
		return $tree;
	
	}
	
	protected function getFootNavList(){
	
		$nav = D('nav');
	
		$navList = $nav -> field('id,nid,name,url') -> order('sort desc') -> where('tid = 2') -> select();
	
		$tree = $this -> listToTree($navList, 'id', 'nid', 'list');
	
		return $tree;
	
	}
	
}