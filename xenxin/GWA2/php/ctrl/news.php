<?php
# news
#include_once($appdir."/mod/ads.class.php");
include($appdir."/mod/pagenavi.class.php");
include_once($appdir."/mod/news.class.php");

# some other stuff

//- ads info
#$adplace = 'news';
#include('include/ads.php');

# pre-set parameters should be placed before $navi is created.
#if(!isset($_REQUEST['pnskstate'])){
#   $_REQUEST['pnskstate'] = 1;
#}
#$_REQUEST['pnps'] = 2;


//- page navi
$navi = new PageNavi();

if(!isset($news)){
    $news = new News();
}

if($act == 'get'){
    //get the page numbers,because several pages will use the page number, so put it outside act="list"
    $orderfield = $navi->getOrder(); 
    if($orderfield == ''){
        $orderfield = "id";
        $navi->set('isasc', $orderfield=='id'?1:0);
    }

    $news->set("pagesize", 3);
    $news->set("pagenum", $navi->get('pnpn'));
    $news->set("orderby",$orderfield." ".($navi->getAsc()==0?"asc":"desc")); 

    if($_REQUEST['pntc'] == '' || $_REQUEST['pntc'] == '0'){ 

		$ckstr = $mod."-".$act."-".$_REQUEST['type']."-".$_REQUEST['pnps']."-".$_REQUEST['pnpn'].$_REQUEST['nocache'];
		
		$ckstrTC = $ckstr.'-totalcount';
		$hm = $news->getBy('cache:', array('key'=>$ckstrTC));
		if($hm[0]){
			$content = $hm[1];
			$navi->set('totalcount',$content);
			#print "first-get::succ:";
		}
		else{
			#print "first-get:fail:";
			$pagenum_orig = $news->get('pagenum');
			$news->set('pagenum', 1);
			$hm = $news->getBy("count(*) as totalcount", $navi->getCondition($news, $user));
			if($hm[0]){
				$hm = $hm[1][0];
				$navi->set('totalcount',$hm['totalcount']);
				$news->setBy('cache:', array('key'=>$ckstrTC, 'value'=>$navi->get('totalcount')));
			}
			$news->set('pagenum', $pagenum_orig);
		}
			
		#exit(__FILE__.": Test point reached.");
		
    }
    $data['pagenums'] = $navi->getNaviNum();

    if(!isset($hm_newslist)){
		
		$ckstrList = $ckstr.'-list';
		$hm = $news->getBy('cache:', array('key'=>$ckstrList));
		#print_r($hm);
		if($hm[0]){
			$data['newslist'] = $hm[1];
			#print "first-get::succ:";
		}
		else{
			$hm_newslist = $news->getBy('*', $navi->getCondition($news, $user));
			if($hm_newslist[0]){
				$data['newslist'] = $hm_newslist[1];
			}
			else{
				$data['newslist'] = array();
			}
			$news->setBy('cache:', array('key'=>$ckstrList, 'value'=>$data['newslist']));	
		}
	
    }
}
else{
	$out .= "Unknown act:[$act]. 1606131024.";
}

if($out == '' && $smttpl == ''){ # if other module do not define a smttpl and $conf['display_style_smttpl']? 
    if($fmt == ''){
		$smttpl = 'news.html';
	}
}

?>
