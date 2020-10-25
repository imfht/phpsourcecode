<?php

require_once('pub_inc.php');		//公共包含

$forum_id = 102;
if (time()-10*24*3600 < dt_query_one("SELECT l_r_at FROM forum_topic WHERE forum_id = '$forum_id' ORDER BY l_r_at DESC LIMIT 1")['l_r_at']) return true;  //每十天执行一次

$qq_house = 'http://db.house.qq.com/index.php?mod=search&act=newsearch&city=hengyang&st=6&page_no=';
for ($i = 20; $i >= 1; $i--) {
	$html_h = stripslashes(file_get_contents($qq_house.$i));

	if (preg_match_all('/<a bosszone=\'1\' href=\'\/hengyang_([^\']*)\/\'/', $html_h, $m)) {
		foreach ($m[1] as $h_id) {
			do_pick_content($h_id);
		}
	} 
}
return true;


function do_pick_content($h_id) {
	global $forum_id;

	$topic = get_topic_by_order_l_n($h_id, $forum_id);

	//看对方站是否有更新，避免频繁更新
	$html_c = mb_convert_encoding(file_get_contents('http://db.house.qq.com/hengyang_'.$h_id), 'utf-8', 'gb2312');

	if (preg_match('/'.date('Y年m月', time()).'/', $html_c)) {
		if (preg_match('/<span>动态<i><\/i><\/span>[^<]*<a href="[^"]*" title="([^"]*)"[^>]*>[^<]*<\/a>[^<]*<\/h3>[^<]*<span class="fr">[^<]*<\/span>[^<]*<p>([^<]*)<a href=/', $html_c, $m)) {
			$title = $m[1];
			$content = $m[2].'<hr>';
		}
	} else return true;


	//交通图
	$content .= '<script> $(document).ready(function(){ get_content_file("/auto/qq_house.html") }) </script>';
	if (preg_match('/htraffic"> <img src="(http:\/\/p\d.qpic.cn\/estate\/\d\/[^\.]*\.jpg\/)450"/', $html_c, $m)) {
		$icon_url = $m[1].'1024';
	} 
	//标题
	if (preg_match('/<h2 class="yh">([^<]*)<\/h2>/', $html_c, $m)) {
		$hyfc365 = hyfc365($m[1]);
		$title =  '【'.$m[1].'】'.$title;
	} 
	//提取价格
	if (preg_match('/<span class="price">[^<]*<strong>([^<]*)<\/strong>/', $html_c, $m)) {
		$price = $m[1];
	} else {
		$price = 0;
	}
	$price_org = $price;

	//详情
	$html_c = mb_convert_encoding(file_get_contents('http://db.house.qq.com/hengyang_'.$h_id.'/info.html'), 'utf-8', 'gb2312');
	if (preg_match('/<i.*id="housedetailmore">([^<]*)<div id="nextDiv" class="cf">/', $html_c, $m)) {
		$content .= $m[1].'<hr>';
	}
	if (preg_match('/<!--基本信息开始-->([^`]*)<!--内容区域结束-->/', $html_c, $m)) {
		$content .= preg_replace('/ul>/', 'ol>', 
			preg_replace('/h2/', 'h3', 
			preg_replace('/<!--.*开始-->/', '<hr>', 
			preg_replace('/class="[^"]*"|style="[^"]*"|<div.*id ="peitaoxinxiwenziless".*>[^`]*<div.*id ="peitaoxinxiwenzimore".*>|收起|<em>/', '', $m[1])))).'<hr>';
	}
	$content .= $hyfc365;
	//图片
	for ($i = 1; $i <= 11; $i++) {
		$html_c = stripslashes(file_get_contents('http://photo.house.qq.com/index.php?mod=photo&act=getmore&houseid='.$h_id.'&type='.$i.'&page=1'));
		if (preg_match_all('/<img src="http:\/\/p\d.qpic.cn\/estate\/\d\/[^\.]*\.jpg\/180">/', $html_c, $m)) {
			foreach ($m[0] as $img) {
				@$imgs .= preg_replace('/\/180/', '/1024', $img);
			}
		}
	}
	$html_c = mb_convert_encoding(file_get_contents('http://photo.house.qq.com/hengyang_'.$h_id.'/photo/'), 'utf-8', 'gb2312');
	if (preg_match_all('/<img  src="(http:\/\/p\d.qpic.cn\/estate\/\d\/[^\.]*\.jpg\/)180">/', $html_c, $m)) {
		if (empty($icon_url)) $icon_url = $m[1][0].'1024';
		foreach ($m[0] as $img) {
			@$imgs .= preg_replace('/\/180/', '/1024', $img);
		}
	} else return false; 
	$content .= '<i id="qq_house_imgs" style="display:none">'.preg_replace('/ src=/', ' src_d=', $imgs).'</i><a class="btn btn-default qq_house_imgs" onclick="qq_house_imgs()">楼盘图片</a>';

	//电话
	$phone = '137-8642-0875';

	do_topic_add_ext_1_x($forum_id, $title, $content, $icon_url, $price, $price_org, $phone, null, null, $h_id, $topic['id']); 

	return true;
}


//楼盘表
function hyfc365($title) {
	if (!preg_match('/class="listCon1" href="[^\?]*\?ID=([^"]*)">/', search_hyfc365(preg_replace('/^.*·|二期|三期/', '', $title)), $m)) {
		if (!preg_match('/class="listCon1" href="[^\?]*\?ID=([^"]*)">/', search_hyfc365(preg_replace('/·.*$|衡阳|\w*|\s/', '', $title)), $m)) {
			return null;
		}
	}
	$html_c_1 = mb_convert_encoding(file_get_contents('http://www.hyfc365.com/RealEstate/Project/BuildingList.aspx?ID='.$m[1]), 'utf-8', 'gb2312');
	$hyfc365 = get_hyfc365($m[1]);

	//提取其他栋
	if (preg_match_all('/<li class=\'ListProjectOff\'><a href=\'[^\?]*\?ID=([^\']*)\'>/', $html_c_1, $m_1)) {
		foreach ($m_1[1] as $key => $b_id) {
			$hyfc365 .= get_hyfc365($b_id);
			if (20 == $key) break;
		}
	}
	return $hyfc365;
}

function get_hyfc365($b_id) {
	$html_c = mb_convert_encoding(file_get_contents('http://www.hyfc365.com/RealEstate/Project/BuildingList.aspx?ID='.$b_id), 'utf-8', 'gb2312');
	if (preg_match('/<li class=\'ListProjectOn\'><a href=\'#\'><span>([^<]*)<\/span>/', $html_c, $m)) {
		$hyfc365_t = $m[1];
	} else return null;

	if (preg_match('/BUILDING_ID=([^\']*)\'/', $html_c, $m)) {
		$get_data = mb_convert_encoding(file_get_contents('http://www.hyfc365.com/WebRecordManager/HouseTableControl/GetData.aspx?BUILDING_ID='.$m[1]), 'utf-8', 'gb2312');
		$g_d = simplexml_load_string($get_data)->T_LOGICBUILDING;

		$hyfc365 = $hyfc365_t.' <a class="btn btn-default hyfc365" id="'.$g_d->LOGICBUILDING_ID.'">销控表</a><hr><label class="table-responsive hyfc365" id="'.$g_d->LOGICBUILDING_ID.'"></label><hr>';
		return $hyfc365;
	}

	return null;
}













function search_hyfc365($title) {
	return mb_convert_encoding(file_get_contents('http://www.hyfc365.com/RealEstate/RealtyProject/Search.aspx', false, stream_context_create(array('http'=>array(
		'method' => 'POST', 
		'header' => "Content-type:application/x-www-form-urlencoded\r\n", 
		'content'=>"CustomPaging1_CurrentPageIndex=-1&__VIEWSTATE=%2FwEPDwUKLTM2MzMxMTM1Nw8WBB4PSGlkZUNvbnRleHRNZW51CymEAXprU3VwZXJNYXAuV2ViLlVJLnprU3VwZXJNYXBQYWdlU3R5bGUsIHprU3VwZXJNYXAuQ29tbW9uTGlicmFyeSwgVmVyc2lvbj0xLjEuNTAwLjAsIEN1bHR1cmU9bmV1dHJhbCwgUHVibGljS2V5VG9rZW49NzJkNzZkMzJkOGNiYTIyZgIeD0hpZGVTZWxlY3RTdGFydAsrBAIWAgIBD2QWCgIDD2QWAmYPDxYEHghDc3NDbGFzcwUQY3NzQm94VGl0bGVUaHJlZR4EXyFTQgICZBYCAgEPDxYGHgRUZXh0BRLlvIDlj5HkvIHkuJrmn6Xor6IeC05hdmlnYXRlVXJsBSQvUmVhbEVzdGF0ZS9SZWFsdHlEZWFsZXIvU2VhcmNoLmFzcHgeBlRhcmdldGUWAh4MVGV4dENoYW5naW5nBQRUcnVlZAIFD2QWAmYPDxYEHwIFFGNzc0JveFRpdGxlVGhyZWVPdmVyHwMCAmQWAgIBDw8WBh8EBRTmpbznm5go6aG555uuKeafpeivoh8FZR8GZRYCHwcFBFRydWVkAgcPZBYCZg8PFgQfAgUQY3NzQm94VGl0bGVUaHJlZR8DAgJkFgICAQ8PFgYfBAUUKOe9keS4iinmiL%2FmupDmn6Xor6IfBQUqL1JlYWxFc3RhdGUvUmVhbHR5U2VhcmNoL1NlYXJjaF9Ib3VzZS5hc3B4HwZlFgIfBwUEVHJ1ZWQCCQ9kFgJmDw8WBB8CBRBjc3NCb3hUaXRsZVRocmVlHwMCAmQWAgIBDw8WBh8EBRLlkIjlkIzlpIfmoYjmn6Xor6IfBQUsL1JlYWxFc3RhdGUvUmVhbHR5U2VhcmNoL1NlYXJjaF9SZWNvcmRzLmFzcHgfBmUWAh8HBQRUcnVlZAITDzwrAAsAZBgBBR5fX0NvbnRyb2xzUmVxdWlyZVBvc3RCYWNrS2V5X18WAQUNQ3VzdG9tUGFnaW5nMbpNuvQVuP%2BDYqCe1%2BwbVab%2B715lNR%2BeC%2BhDFTSfvE0y&__EVENTVALIDATION=%2FwEWAwKHpppsAqi0zakHArrY8x1xs%2BnwBroCH5%2BKiDI9tW1jyttusdquHQRtH5UPs6GOzg%3D%3D&ValidSearchText=".mb_convert_encoding($title, 'gb2312', 'utf-8')."&ButtonSearch=%B2%E9+%D1%AF")))), 'utf-8', 'gb2312');
}
