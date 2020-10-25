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
ul,ol,li{ list-style:none;}
img{ border:none;}
a{ text-decoration:none;}
.videolist{ width:100%; float:left;}
.videolist ul li{ width:320px; height:280px; float:left; margin:0 12px; display:inline;}
.videolist ul li a{ display:block; width:320px; height:260px; z-index:10;}
.videolist ul li img{ width:300px; height:225px; float:left; padding:4px; border:1px solid #ccc;}
.videolist ul li span{ width:300px; text-align:center; float:left; padding-top:10px; font-size:14px;}
</style>
<div class="videolist">
	<ul>
<?php
if(!empty($tag['data.results']))
{
	foreach($tag['data.results'] as $k =>$data)
	{
		?>		
		<li><a href="<?php echo sys_href($params['id'],'view',$data['id'])?>"><img src="<?php echo $data['picture']; ?>" /><span><?php echo $data['title']; ?></span></a></li>
		<?php
	}
}
else
{
	echo '暂无视频。';
}
?>
	</ul>
	<div class="clear"></div>
	<div  id="articeBottom"><?php if(!empty($tag['pager.cn'])) echo $tag['pager.cn']; ?></div>
</div>