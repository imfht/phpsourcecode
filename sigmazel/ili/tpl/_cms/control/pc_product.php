<?php
/**@name 产品*/

//分类备注
$categories['02']['COMMENT'] = explode("\r\n", $categories['02']['COMMENT']);

//概览
$article_021 = $_article->get_first('021', '', '', 1);

//主要特点限6个
$article_022 = $_article->get_list('022', 0, 6, '', '', 1);

//六大功能限6个
$article_023 = $_article->get_list('023', 0, 6, '', '', 1);

//适用业务领域限3个
$article_024 = $_article->get_list('024', 0, 3, '', '', 1);

//我们的合作伙伴限30个
$article_025 = $_article->get_list('025', 0, 30, '', '', 1);

//购买
$article_026 = $_article->get_first('026', '', '', 1);

//服务限4个
$article_027 = $_article->get_list('027', 0, 4, '', '', 1);

//当前导航
$menu['product'] = 'nav-active';
$page_title = "产品 - {$page_title}";

include_once view('/tpl/_cms/view/pc_product');
?>