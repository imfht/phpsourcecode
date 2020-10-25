<?php

class ProductAction extends GlobalAction {

    public function index(){

		$this -> lists();

    }

    public function lists(){
    
    	$cid = intval($_GET['cid']);

    	$cat = D('product_cat');
    	
    	if(empty($cid)){

    		$cid = 0;
    		
    		$this -> title = '产品中心';
    		
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

    	$this -> assign('proList', $this -> getProList($idStr));
    	
    	$this -> assign('catList', $catList);

		$this -> display('list');
    	
    }
    
    protected function getProList($cid){

    	$pro = D('product');

    	$condition = 'cid in (' . $cid . ') and status = 1';

		import('ORG.Util.Page');

		$count = $pro -> where($condition) -> count();

		$Page = new Page($count, 16);

		$show = $Page -> show();

		$list = $pro -> field('id,name,price,thumburl,cid')
		
					 -> where($condition) -> order('id desc')
		
					 -> limit($Page -> firstRow . ',' . $Page -> listRows) -> select();
					 
		$this -> assign('page', array('totalRows' => $Page -> totalRows, 'totalPages' => $Page -> totalPages));
		
		$this -> assign('pageLink', $show);

    	return $list;

    }    

    public function v(){
    	
    	$cid = intval($_GET['cid']);

    	$id = intval($_GET['id']);
    	
    	$pro = D('product');
    	
    	$prefix = C('DB_PREFIX');

    	$sql = 'select p.*,c.name cname from ' . $prefix . 'product p left join ' . $prefix . 'product_cat c on p.cid = c.id where p.id = ' . $id . ' and p.status = 1 and c.status = 1';

    	$product = $pro -> query($sql);

    	if(empty($product) || sizeof($product) < 1){
    	
    		$this -> redirect('index');
    		
    		exit;
    		
    	}
    	
    	$product = $product[0];
    	
    	$product['imgurl'] = explode(';', $product['imgurl']);
    	
    	$otherProList = $pro -> query('select p.*,c.name cname from ' . $prefix . 'product p left join ' . $prefix . 'product_cat c on p.cid = c.id where p.id <> ' . $id . ' and p.status = 1 and c.status = 1 order by time desc limit 0,4');

    	$this -> assign('location', $this -> getLocation($cid, $product['name']));
    	
    	$this -> assign('product', $product);

    	$this -> assign('otherProList', $otherProList);
    	
		$this -> display('v');

    }

    protected function getLocation($cid = 0, $name, $flag = true){
    	
    	$str = '您当前的位置：';
    	
    	if($cid == 0){
    	
    		$str .= $name;

    	}else{

			$str .= '<a href="' . U('Product/index'). '">产品中心</a>';

			$arr = $this -> getCatTree($cid);
			
			$len = sizeof($arr);

			if($flag === false) $len -= 1;
			
			for($i = 0; $i < $len; $i++){
				
				$val = $arr[$i];
		    	
				$str .= '&nbsp;>&nbsp;<a href="' . U('Product/lists?cid=' . $val['id']). '">' . $val['name'] . '</a>';
		    	
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

		$cat = D('product_cat');

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