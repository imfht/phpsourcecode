<?php
/**
 *  翻页类
 *
 *  $num            总条数
 *  $pagesize       每页显示的条数
 *  $nowpage        当前页数
 *  $url            翻页参数名
 *  $pagenum        显示出多少页
 *
 *  author          于文龙
 **/
 if(!defined('DIR')){
	exit('Please correct access URL.');
}
 
class Page{
    static public function show($num, $pagesize, $nowpage, $urlcode = 'page', $pagenum = 5){
        $zpage = ceil($num / $pagesize);
        $lang['lastpage'] = '上一页';
        $lang['nextpage'] = '下一页';
        $dot = '...';
        if(intval($nowpage) < 1){
            $nowpage = 1;
        }
        $lastpage = ($nowpage - 1) > 0 ? ($nowpage - 1) : 1;
        $nextpage = $nowpage + 1;
         
        $lastpage = intval($nowpage) > 1 ? '<a href="'.self::pageurl($lastpage, $urlcode).'">'.$lang['lastpage'].'</a>' : '';
        $nextpage = intval($nowpage) < $zpage ? '<a href="'.self::pageurl($nextpage, $urlcode).'">'.$lang['nextpage'].'</a>' : '';
         
        $offset = floor($pagenum / 2);
         
        if(($nowpage - $offset) > 1 && $zpage > $pagenum){
            $lastpage .= '<a href="'.self::pageurl(1, $urlcode).'">1'.$dot.'</a>';
        }
        if(($nowpage + $offset) < $zpage && $zpage > $pagenum){
            $nextpage = '<a href="'.self::pageurl($zpage, $urlcode).'">'.$dot.$zpage.'</a>'.$nextpage;
        }
        if(($nowpage + $offset) <= $zpage){
            $form = ($nowpage - $offset) > 1 ? $nowpage - $offset : 1;
        }else {
            $form = $zpage - $pagenum + 1;
        }
        if(($nowpage - $offset) > 1){
            if($pagenum % 2 == 0){
                $to = ($nowpage + $offset) < $zpage && ($nowpage + $offset) > $pagenum ? $nowpage + $offset - 1 : $zpage;
            }else if($pagenum % 2 == 1){
                $to = ($nowpage + $offset) < $zpage && ($nowpage + $offset) > $pagenum ? $nowpage + $offset : $zpage;
            }
        }else {
			if($zpage >= $pagenum){
				$to = $pagenum;
			}else {
				$to = $zpage;
			}
        }
		
		if($form < 1){
			$form = 1;
		}
		
        $pageval = '';
        for($i = $form; $i <= $to; $i++){
            $pageval .= $nowpage == $i ? '<span>'.$i.'</span>' : '<a href="'.self::pageurl($i, $urlcode).'">'.$i.'</a>'; 
        }
        if($zpage > 1){
        	return $lastpage.$pageval.$nextpage;
		}else {
			return '';
		}
    }
     
    static private function pageurl($num, $urlcode){
        $url = $_SERVER['REQUEST_URI'];
		
        $pattern = '/(\?|&)'.$urlcode.'=[0-9]+/';
        if(preg_match($pattern, $url, $page_v)){
            $url = strtr($url, array($page_v['0'] => ''));
        }
        $url_c = strpos($url, '?');
        return empty($url_c) ? $url.'?'.$urlcode.'='.$num : $url.'&'.$urlcode.'='.$num;
    }
}
?>
