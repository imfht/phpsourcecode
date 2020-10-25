<?php /* Smarty version Smarty-3.1.21-dev, created on 2015-01-03 10:30:24
         compiled from "D:\wwwroot\blog\templates\article.html" */ ?>
<?php /*%%SmartyHeaderCode:2119654a220a8353cd2-25027306%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8b569a45e9248e37f213726ec7ae9e7407069675' => 
    array (
      0 => 'D:\\wwwroot\\blog\\templates\\article.html',
      1 => 1420277393,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2119654a220a8353cd2-25027306',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_54a220a8382ae0_80572550',
  'variables' => 
  array (
    'result' => 0,
    'seo' => 0,
    'edit' => 0,
    'url' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54a220a8382ae0_80572550')) {function content_54a220a8382ae0_80572550($_smarty_tpl) {?><!doctype html>
<html>
<head>
<meta charset = "UTF-8" />
<meta name="keywords" content="<?php echo $_smarty_tpl->tpl_vars['result']->value['keywords'];?>
" />
<meta name="description" content="<?php echo $_smarty_tpl->tpl_vars['result']->value['description'];?>
" />
<link rel="stylesheet" href="../css/style.css?dsk" type="text/css" media="screen" />
<?php echo '<script'; ?>
 src = "http://lib.sinaapp.com/js/jquery/1.7.2/jquery.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src = "../js/myjs.js"><?php echo '</script'; ?>
>
<title><?php echo $_smarty_tpl->tpl_vars['result']->value['title'];?>
 | <?php echo $_smarty_tpl->tpl_vars['seo']->value['title'];?>
</title>
</head>
<body>
<div id = "top">
<div class = "logo">
<h1><a href = "../index.php">邹修平Blog</a></h1>
路漫漫其修远兮，吾将上下而求索。
</div>
<div id = "nav">
	<ul>
		<li><a href = "../index.php">首 页</a></li>
		<li><a href = "../all.php">所有文章</a></li>
		<li><a href = "http://www.xiaoz.me/" target = "_blank" rel = "nofollow">小z博客</a></li>
		<li><a href = "http://weibo.com/337003006" target = "_blank" rel = "nofollow">新浪微博</a></li>
		<li><a href = "./?p=20">关 于</a></li>
	</ul>
</div>
</div>

<div id = "show">
	<div class = "neirong">
		<p><h2><?php echo $_smarty_tpl->tpl_vars['result']->value['title'];?>
</h2></p>
		<p style = "text-indent:0px;">
			作者：邹修平 | 发表于：<?php echo $_smarty_tpl->tpl_vars['result']->value['date'];?>
 
			<?php if ($_smarty_tpl->tpl_vars['edit']->value==1) {?>
				<a href = "../admin/edit.php?id=<?php echo $_smarty_tpl->tpl_vars['result']->value['id'];?>
" rel = "nofollow" target = "_blank">编辑</a>
			<?php }?>
		</p>
		<p><?php echo $_smarty_tpl->tpl_vars['result']->value['content'];?>
</p>
	</div>
	<div class = "share">
		<!-- JiaThis Button BEGIN -->
		<div class="jiathis_style">
			<a class="jiathis_button_qzone"></a>
			<a class="jiathis_button_tsina"></a>
			<a class="jiathis_button_tqq"></a>
			<a class="jiathis_button_weixin"></a>
			<a class="jiathis_button_renren"></a>
			<a href="http://www.jiathis.com/share" class="jiathis jiathis_txt jtico jtico_jiathis" target="_blank"></a>
			<a class="jiathis_counter_style"></a>
		</div>
<!-- JiaThis Button END -->
	</div><div style = "clear:both;"></div>
	
</div>

<!--多说评论-->
<div id = "pinglun">
	<!-- 多说评论框 start -->
	<div class="ds-thread" data-thread-key="<?php echo $_smarty_tpl->tpl_vars['result']->value['id'];?>
" data-title="<?php echo $_smarty_tpl->tpl_vars['result']->value['title'];?>
" data-url="<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
"></div>
<!-- 多说评论框 end -->
<!-- 多说公共JS代码 start (一个网页只需插入一次) -->

<?php echo '<script'; ?>
 type="text/javascript">
var duoshuoQuery = {short_name:"helloz"};
	(function() {
		var ds = document.createElement('script');
		ds.type = 'text/javascript';ds.async = true;
		ds.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') + '//static.duoshuo.com/embed.js';
		ds.charset = 'UTF-8';
		(document.getElementsByTagName('head')[0] 
		 || document.getElementsByTagName('body')[0]).appendChild(ds);
	})();
	<?php echo '</script'; ?>
>

<!-- 多说公共JS代码 end -->
</div>
<!-- 多说评论END -->

<!-- 返回顶部 -->
<p id="back-to-top" style="display: block;"><a href="#top"><span></span>回到顶部</a></p>
<!-- 返回顶部END -->

<!--  底部版权信息  -->
<div id = "wrap">©2015 Powered by M-Blog | <a href = "../all.php" target = "_blank">站点地图</a> | 本站托管于<a href = "http://www.xiaoz.me/jump.php?id=03" target = "_blank" rel = "nofollow">恒创主机</a> </div>
<!--  底部版权信息END -->


	<?php echo '<script'; ?>
 type = "text/javascript"> 
		var jiathis_config = {appkey:{"tsina":"1951156462","tqq":"9d485c447899eec2c7f0f65d04a63c97"}}
	<?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 type="text/javascript" src="http://v3.jiathis.com/code_mini/jia.js" charset="utf-8"><?php echo '</script'; ?>
>

</body>
</html><?php }} ?>
