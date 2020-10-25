<style>
.articlenav{ border:1px solid #ccc; margin-bottom:10px;}
.articlenav h3{ height:36px; line-height:36px; background:url(/admini/images/articleh3.png) repeat-x; text-indent:12px; font-family:"微软雅黑"; font-size:14px; font-weight:normal;}
.articlenav li{ line-height:30px; text-indent:12px;}
.articlenav li a{ display:block;}
.articlenav li a:hover{ text-decoration:underline;}
.articlenav .artno{ float:left;}
.articlenav .delete{ float:right; padding-right:10px;}
.articlenav .arttitle{ width:100px; overflow:hidden; height:30px; line-height:30px; padding-left:10px;}
</style>
<div class="articlenav">
	<h3>操作菜单</h3>
	<ul>
		<li><a href="?a=addarticle&p=<?php echo $request['p'] ?>">添加新页面</a></li>
	</ul>
</div>
<div class="articlenav">
	<h3>页面导航</h3>
	<ul>
		<?php get_article_page($request['p']) ?>
	</ul>
</div>