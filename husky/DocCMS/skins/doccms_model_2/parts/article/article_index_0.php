<?php
    // 为方便并保证您以后的快速升级 请使用SHL提供的如下全局数组
	
	// 数组定义/config/doc-global.php
	
	// 如有需要， 请去掉注释，输出数据。
	/*
	echo '<pre>';
		print_r($tag);
	echo '</pre>';
	*/
?>
<style type="text/css">
*{ padding:0; margin:0;}
img{ border:none;}
a{ text-decoration:none;}
#articlebox{ margin:0 15px; line-height:22px;}
#articlebox h1{text-align:center; font-size:20px; font-family:'微软雅黑'; font-weight:normal; padding:10px 0;}
#articlebox hr{height:5px;border:none;border-top:5px ridge green;}
.details h2{ background:#F8F8FF; border: 1px solid #DDD; line-height: 25px; margin-bottom: 15px;  padding: 10px; font-size:14px; text-indent:28px;}
.artcontent{clear: both; font-size: 14px; line-height: 23px; overflow: hidden; padding: 9px 0; width:99%; word-wrap: break-word;}
#articeBottom { font-size: 14px; margin: 6px 0 10px; padding-top: 10px; text-align: right; width: 97%;}
#articeBottom span { float: left;}
#articeBottom span a { font-size: 12px;}
#articeBottom span a:hover {color: #0099FF;}
</style>
<div id="articlebox">
<?php
if(!empty($tag['data.results']))
{
	foreach($tag['data.results'] as $k =>$data)
	{
	?>
		<h1><?php echo $data['title']; ?></h1><hr />
        <?php echo $data['description']; ?>
		<div class="artcontent"><?php echo stripslashes($data['content']); ?></div>
		<div id="articeBottom"><?php if(!empty($tag['pager.cn']))echo $tag['pager.cn']; ?></div>
	<?php
	}
}else{
	echo '暂无数据！';
}
?>
</div>