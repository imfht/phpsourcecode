<?php
 
include_once "smarty/Smarty.class.php";
$tpl =new Smarty;
$tpl->template_dir = WEB_ROOT.TPL_DIR;
$tpl->compile_dir = WEB_ROOT ."templates_c/";
$tpl->left_delimiter = '<{';
$tpl->right_delimiter = '}>';
$TPL_IMGURL = 'templates/images';#模板图片URL

?>