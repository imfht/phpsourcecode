<?php
/**@name 营销*/

//分类备注
$categories['03']['COMMENT'] = explode("\r\n", $categories['03']['COMMENT']);

//重点推荐6个
$article_031 = $_article->get_list('031', 0, 6, '', '', 1);

//还有更多营销模块20个
$article_032 = $_article->get_list('032', 0, 20, '', '', 1);

//当前导航
$menu['market'] = 'nav-active';
$page_title = "营销 - {$page_title}";

include_once view('/tpl/_cms/view/pc_market');
?>