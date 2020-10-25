<?php

class ArticleAction extends GlobalAction {

	protected $artType = 1;//1为普通，2为公司团队，3为关于公司
	
	protected $artMode = 1;//1为普通，2为单页，3为特殊图片

    public function index(){

		$this -> lists();

    }

    public function lists(){

    	$tid = intval($_GET['tid']);

    	if($tid == 2 || $tid == 3){
    		
    		if($tid == 2) $this -> title = '公司团队';
    		
    		else $this -> title = '关于公司';
    	
	    	$this -> assign('location', $this -> getLocation(0, $this -> title));

	    	$this -> assign('artList', $this -> getArtList($tid, true));
	    	
	    	$this -> assign('tid', $tid);
	
			$this -> display('list');
    	
    	}else{
    		
    		$tid = 1;
 
	    	$cid = intval($_GET['cid']);
	
	    	$cat = D('article_cat');
	    	
	    	if(empty($cid)){
	
	    		$cid = 0;
	    		
	    		$this -> title = '文章中心';
	    		
	    	}
	
	    	$catList = $cat -> field('id,name') -> where(array('cid' => $cid, 'status' => 1)) -> order('sort desc') -> select();
	
	    	if($cid != 0){
	
	    		$arr = $cat -> field('name,keywords,description') -> where('id = ' . $cid) -> select();
	    		
	    		if(sizeof($arr) < 1){
	
	    			$this -> redirect('index');
	
	    		}else{
	    			
	    			$this -> title = $arr[0]['name'];
	    			
	    			$this -> assign('thisCat', $arr[0]);
	    			
	    		}
	    		
	    	}
	    	
	    	$idArr = array($cid);
	
	    	foreach($catList as $c){
	    			
	    		$idArr[] = $c['id'];
	    			
	    	}
	    	
	    	$idStr = implode(",", $idArr);
	    	
	    	$this -> assign('location', $this -> getLocation($cid, $this -> title, false));
	
	    	$this -> assign('artList', $this -> getArtList($idStr));
	    	
	    	$this -> assign('catList', $catList);
	    	
	    	$this -> assign('tid', $tid);
	
			$this -> display('list');
		
    	}
    	
    }
    
    protected function getArtList($cid, $type = false){

    	$pro = D('article');
    	
    	if($type){

    		$condition = 'tid = (' . $cid . ') and status = 1';
 
    	}else $condition = 'cid in (' . $cid . ') and status = 1 and tid = 1';

		import('ORG.Util.Page');

		$count = $pro -> where($condition) -> count();

		$Page = new Page($count, 16);

		$show = $Page -> show();

		$list = $pro -> field('id,title,thumburl,cid')
		
					 -> where($condition) -> order('id desc')
		
					 -> limit($Page -> firstRow . ',' . $Page -> listRows) -> select();
					 
		$this -> assign('page', array('totalRows' => $Page -> totalRows, 'totalPages' => $Page -> totalPages));
		
		$this -> assign('pageLink', $show);

    	return $list;

    }

    public function v(){
    	
    	$tid = intval($_GET['tid']);
    	
    	$cid = intval($_GET['cid']);

    	$id = intval($_GET['id']);
    	
    	$pro = D('article');
    	
    	$prefix = C('DB_PREFIX');
    	
        if($tid == 2 || $tid == 3){
    		
	    	$sql = 'select p.* from ' . $prefix . 'article p where p.id = ' . $id . ' and p.status = 1';
	
	    	$article = $pro -> query($sql);
	
	    	$otherArtList = $pro -> query('select p.* from ' . $prefix . 'article p where p.id <> ' . $id . ' and p.status = 1 and tid = ' . $tid . ' order by time desc limit 0,4');
    	
    		$this -> assign('tid', $tid);
        
        }else{

    		$sql = 'select p.*,c.name cname from ' . $prefix . 'article p left join ' . $prefix . 'article_cat c on p.cid = c.id where p.id = ' . $id . ' and p.status = 1 and c.status = 1';

    		$article = $pro -> query($sql);

    		$otherArtList = $pro -> query('select p.*,c.name cname from ' . $prefix . 'article p left join ' . $prefix . 'article_cat c on p.cid = c.id where p.id <> ' . $id . ' and p.status = 1 and c.status = 1 order by time desc limit 0,4');

    	}
    	
        if(empty($article) || sizeof($article) < 1){
    	
    		$this -> redirect('index');
    		
    		exit;
    		
    	}  	
    	
    	$this -> assign('location', $this -> getLocation($cid, $article[0]['title']));
    	
    	$this -> assign('article', $article[0]);

    	$this -> assign('otherArtList', $otherArtList);
    	
		$this -> display('v');

    }

    protected function getLocation($cid = 0, $name, $flag = true){
    	
    	$str = '您当前的位置：';
    	
    	if($cid == 0){
    	
    		$str .= $name;

    	}else{

			$str .= '<a href="' . U('Article/index'). '">文章中心</a>';

			$arr = $this -> getCatTree($cid);
			
			$len = sizeof($arr);

			if($flag === false) $len -= 1;
			
			for($i = 0; $i < $len; $i++){
				
				$val = $arr[$i];
		    	
				$str .= '&nbsp;>&nbsp;<a href="' . U('Article/lists?cid=' . $val['id']). '">' . $val['name'] . '</a>';
		    	
		 	}
		
			$str .= '&nbsp;>&nbsp;' . $name;
		
    	}
 
    	return $str;

    }
    
    protected function getCatTree($cid){

    	$catList = $this -> getCatList();
    	
    	$arr = array();
    	
    	$flag = true;

    	while($flag){

    		$c = $this -> getCatSelf($catList, $cid);

    		if($c == false){

    			$flag = false;

    		}else{

    			array_unshift($arr, $c[0]);
    			
    			$cid = $c[1];
    			
    			if($cid == 0) $flag = false;

    		}

    	}

    	return $arr;

    }
    
	protected function getCatList(){

		$cat = D('article_cat');

		$catList = $cat -> field('id,cid,name') -> where('status=1') -> select();
		
		$tree = $this -> listToTree($catList, 'id', 'cid', 'list');

		return $tree;
	
	}

	protected function getCatSelf($tree, $cid){

		foreach($tree as $row){

			if($row['id'] == $cid){

				return array(array('id' => $cid, 'name' => $row['name']), $row['cid']);

			}else if(!empty($row['list'])){

				$t1 = $this -> getCatSelf($row['list'], $cid);

				if($t1 !== false) return $t1;

			}

		}

		return false;
	
	}
    
}