<?php
/*
 * This file is part of the emlog for bootstrap Project. See CREDITS and LICENSE files
 *
 * emlog for bootstrap Project URL:https://git.oschina.net/ysfl/emlog-for-bootstrap
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
/*
Template Name:默认模板 for bootstrap
Description:默认模板，简洁优雅
Version:1.2
Author:emlog
Author Url:http://www.emlog.net
Sidebar Amount:1
Version:1.0.1
---------------
Mod:ysfl
Mod Url:http://www.ysfl.cn
Version:Debug
*/
if(!defined('EMLOG_ROOT')) {exit('error!');}
require_once View::getView('module');
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $site_title; ?></title>
<meta name="keywords" content="<?php echo $site_key; ?>" />
<meta name="description" content="<?php echo $site_description; ?>" />
<meta name="generator" content="emlog" />
<link rel="EditURI" type="application/rsd+xml" title="RSD" href="<?php echo BLOG_URL; ?>xmlrpc.php?rsd" />
<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="<?php echo BLOG_URL; ?>wlwmanifest.xml" />
<link rel="alternate" type="application/rss+xml" title="RSS"  href="<?php echo BLOG_URL; ?>rss.php" />
<link href="<?php echo TEMPLATE_URL; ?>css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="<?php echo TEMPLATE_URL; ?>main.css" rel="stylesheet" type="text/css" />
<link href="<?php echo BLOG_URL; ?>admin/editor/plugins/code/prettify.css" rel="stylesheet" type="text/css" />
<script src="<?php echo TEMPLATE_URL; ?>js/jquery-1.9.1.js" type="text/javascript"></script>
<script src="<?php echo BLOG_URL; ?>admin/editor/plugins/code/prettify.js" type="text/javascript"></script>
<script src="<?php echo BLOG_URL; ?>include/lib/js/common_tpl.js" type="text/javascript"></script>
<script src="<?php echo TEMPLATE_URL; ?>js/bootstrap.js" type="text/javascript"></script>
<!--[if IE 6]>
<script src="<?php echo TEMPLATE_URL; ?>iefix.js" type="text/javascript"></script>
<![endif]-->
<?php doAction('index_head'); ?>
</head>
<body>
<div class="container">
<?php /* 博客logo
  <div id="header">
    <a href="<?php echo BLOG_URL; ?>"><img style="width:80px" class="img-circle" src="<?php echo TEMPLATE_URL # 博客logo?>images/logo.png" id="logo" alt="LOGO"></a>
  </div> */ ?>
  <?php /* <h3 id="slogan"><?php echo $bloginfo ?></h3> 站点副标题*/?>
  <div class="masthead">
  	<h3 class="text-muted"><?php echo $site_title; ?></h3>
  	<div class="navbar navbar-inverse navbar-static-top">
  		<?php blog_navi();?>
  		<?php widget_search($title);?>
  	</div>
  </div>