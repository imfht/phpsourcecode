<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php if (Mark_Is_Post() || Mark_Is_Page()) { Mark_The_Title(); ?> | <?php Mark_Site_Name(); } else { Mark_Site_Name();} ?></title>
<meta name="keywords" content="<?php if ($Mark_Url_Action == ""){Mark_Site_Key();}else{Mark_The_Keyword_Des();} ?>" />
<meta name="description" content="<?php if ($Mark_Url_Action == ""){Mark_Site_Desc();}else{Mark_The_Des();} ?>" />
<link rel="shortcut icon" type="image/x-icon" href="<?php __Index__('Public/Images/favicon.ico');?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php __Index__('Default/Css/global.css'); ?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php __Index__('Public/Css/tag_ltd.css'); ?>" />
<script src="<?php __Index__('Public/Js/tag_ltd.js');?>"></script>
<script src="<?php __Index__('Public/Js/jquery-1.10.1.code.js');?>"></script>
<script src="<?php __Index__('Public/Js/code.js');?>"></script>
<link rel="stylesheet" href="<?php __Index__('Public/Css/code.css');?>" type="text/css"/>
<script src="<?php __Index__('Public/Js/jquery.min.js'); ?>" type="text/javascript"></script>
   <script type="text/javascript"> 
      $(document).ready(function() {
         var tags_a = $("#div1 a");
         tags_a.each(function(){
             var x = 9;
             var y = 0;
             var rand = parseInt(Math.random() * (x - y + 1) + y);
             $(this).addClass("tags"+rand);
          });
      })   
$(document).ready(function() {
  hljs.initHighlightingOnLoad();
});
</script>
</head>
