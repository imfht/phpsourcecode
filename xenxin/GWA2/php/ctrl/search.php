<?php

# search in all main tables across the site 
# Sun Jun  7 09:50:06 CST 2015
# refer https://wadelau.wordpress.com/2012/08/31/%E5%BC%80%E5%8F%91%E6%89%8B%E8%AE%B0%EF%BC%9A%E6%95%B0%E6%8D%AE%E8%A1%A8%E5%A6%82%E4%BD%95%E8%81%94%E6%9F%A5%E4%B8%A4%E4%B8%AA%E4%B8%8D%E7%9B%B8%E5%85%B3%E8%81%94%E8%A1%A8%E7%9A%84%E6%95%B0%E6%8D%AE/  

# modules
require_once($appdir."/mod/follow.class.php");
require_once($appdir."/mod/search.class.php");
require_once($appdir."/mod/product.class.php");
require_once($appdir."/mod/ads.class.php");

$search = new Search();
$objfollow = new Follow();

$searchList = array();
$pagesize = 12;

# actions
if($act == 'do'){
	$kw = $_REQUEST['kw'];
	$scope = $_REQUEST['scope'];

	$searchList = $search->getList($kw, $scope);
	if($searchList[0]){
		$data['searchlist'] = $searchList[1];		
	}
	else{
		$data['searchlist'] = array();		
	}

	if($scope == 'pair'){
		if($kw == ''){ $smttpl = 'fs-search.html'; }
		else{ $smttpl = 'fs-new-search.html'; }
	}
	else if($scope == 'user'){
		# something to do
		/*
		$userIds = "999999,";
		foreach($data['searchlist'] as $k=>$v){
			$userIds .= $v['id'].",";
		}
		$userIds = substr($userIds, 0, strlen($userIds)-1);
		*/
		$user2 = new Customer(); 
		$user2->set('orderby','id asc'); 
		$user2->set('pagesize', $pagesize);
		$hm = $user2->getBy('*', "usertype=1 and id in (".getIdList($data['searchlist'], 'id').")");
		$hm = $hm[0]?$hm[1]:array();
		
		foreach($hm as $key => $value){
			$hm[$key]['fanscount'] = $user2 ->getFansCount($value['id']);
			$hm[$key]['relation'] = $objfollow ->getRelationWithCurrent($value['id']);
		}
		$data['userlist'] = $hm;
		//var_dump($data['userlist']);
		/*
		$userIds = "999999,";
		foreach($hm as $k=>$v){
			$userIds .= $v['id'].",";
		}
		$userIds = substr($userIds, 0, strlen($userIds)-1);
		*/
		$userIds = getIdList($hm, 'id');
		$product = new Product();
		$hm = $product->getBy('*', "userid in (".$userIds.")");
		$hm = $hm[0]?$hm[1]:array();
		$hmTmp = array();
		foreach($hm as $k=>$v){
			$hmTmp[$v['userid']][] = $v;	
		}
		$data['productlist'] = $hmTmp;

		if($kw == ''){
			$user2->set('orderby','id desc'); $user2->set('pagesize', $pagesize);
			$hm = $user2->getBy('*', "usertype=1 and id in ($userIds)");
			$hm = $hm[0]?$hm[1]:array();
			foreach($hm as $key => $value){
				$hm[$key]['fanscount'] = $user2 ->getFansCount($value['id']);
				$hm[$key]['relation'] = $objfollow ->getRelationWithCurrent($value['id']);
			}
			$data['userlist_new'] = $hm;
			//var_dump($data['userlist_new']);
			/*
			$userIds = "999999,";
			foreach($hm as $k=>$v){
				$userIds .= $v['id'].",";
			}
			$userIds = substr($userIds, 0, strlen($userIds)-1);
			*/
			$hm = $product->getBy('*', "userid in (".getIdList($hm, 'id').")");
			$hm = $hm[0]?$hm[1]:array();
			$hmTmp = array();
			foreach($hm as $k=>$v){
				$hmTmp[$v['userid']][] = $v;	
			}
			$data['productlist_new'] = $hmTmp;
		}		

		# ads list
		$adplace = 'live_search_upper';
		include_once("include/ads.php");

		# tpl 
		if($kw == ''){ 
			include("include/area.php");
			$smttpl = 'zb-faxianmaishou.html'; 
		}		
		else{ 
		    $smttpl = 'zb-search.html'; 
		}
	}
	else if($scope == 'product'){
			
		/*
		$productIds = "999999,";
		foreach($hm as $k=>$v){
			$productIds .= $v['id'].",";
		}
		$productIds = substr($userIds, 0, strlen($userIds)-1);
		*/
		$product = new Product();
		$product->set('pagesize', $pagesize);
		$hm = $product->getBy('*', "id in (".getIdList($data['searchlist'],'id').")");
		$hm = $hm[0]?$hm[1]:array();
		$data['productlist'] = $hm;

		# tpl
		$smttpl = "liulan-search.html";
	}

	#print_r($data);

}
else{
	$out .= "Unkown act:[$act]";			
}

# tpl & output 
$out .= ""; # $search->toString($searchList);



?>
