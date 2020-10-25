<?php
/**@name 首页*/

//分类备注
$categories['01']['COMMENT'] = explode("\r\n", $categories['01']['COMMENT']);

//我们带来的是什么？限3个
$article_011 = $_article->get_list('011', 0, 3, '', '', 1);

//能解决什么问题？限4个
$article_012 = $_article->get_list('012', 0, 4, '', '', 1);

//谁在使用我们的产品？限72个
$article_013 = $_article->get_list('013', 0, 72);
$article_013 = array_chunk($article_013, 18);

//下载产品限2个
$article_014 = $_article->get_list('014', 0, 2, '', '', 1);

//当前导航
$menu['index'] = 'nav-active';

include_once view('/tpl/_cms/view/pc_index');
?>