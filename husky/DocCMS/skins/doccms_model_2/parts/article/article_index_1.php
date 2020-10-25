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
ul, li { margin:0; padding:0; list-style:none; }
h1 { font-size:16px; font-weight:600; }
h2 { font-size:16px; margin-top:10px; }
h2, h3 { padding-left:10px; }
#stuffbox { margin:0 10px; line-height: 22px; }
#articeBottom { width:100%; text-align:right; margin:6px 0 10px 0; padding-top:10px; }
#stuffbox-newspage { width:100%;  margin:6px 0 10px 0; padding-top:1px; }
#articleHeader { color:#6A591F; padding:10px 0 5px 0; }
#articleHeader span { float:right; }
#articleHeader strong { color:#557A34; font-weight:normal; }
#correlationStuff { width:100%; color:#aaa; background:#F8F7EF ; margin-top:0; padding-bottom:1px; }
#correlationStuff li { line-height:20px; margin:0; }
#myStuff { float:left; width:100%; }
#myStuff span { color:#557A34; }
.topPadding { padding-top:10px; }
.details h2{ background:#F8F8FF; border: 1px solid #DDD; line-height: 25px; margin-bottom: 15px;  padding: 10px; font-size:14px; text-indent:28px;}
</style>
<div id="stuffbox">
<?php
if(!empty($tag['data.results']))
{
	foreach($tag['data.results'] as $k =>$data)
	{
	?>
		<h1 style="text-align:center;"><?php echo $data['title']; ?></h1>
		<?php echo stripslashes($data['content']); ?>
		<div id="articeBottom"><?php if(!empty($tag['pager.cn']))echo $tag['pager.cn']; ?></div>
	<?php
	}
}else{
	echo '暂无数据！';
}
unset($tag['data.results']);
?>
</div>