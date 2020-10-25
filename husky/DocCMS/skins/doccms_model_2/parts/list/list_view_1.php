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
<style>
/*======中间部分内======*/
a{ text-decoration:none;}
/*分页*/
.endPageNum{ clear:both; font-size:12px; text-align:center;}
.endPageNum table{ margin:auto;}
.endPageNum .s1{width:52px;}
.endPageNum .s2{background:#1f3a87; border:1px solid #ccc; color:#fff; font-weight:bold;}
.endPageNum a.s2:visited {color:#fff;}
.endPageNum a{padding:2px 5px;margin:5px 4px 0 0; color:#1F3A87;background:#fff; display:inline-table; border:1px solid #ccc; float:left;}
.endPageNum a:visited{color:#1f3a87;} 
.endPageNum a:hover{color:#fff; background:#1f3a87; border:1px solid #1f3a87;float:left; text-decoration:underline;}
.endPageNum .s3{cursor:default;padding:2px 5px;margin:5px 4px 0 0; color:#ccc;background:#fff; display:inline-table; border:1px solid #ccc; float:left;}
.clear{ clear:both;}
.editor{ float:left; font-size:12px; margin:11px 0; width:545px; text-align:right;}
#newscontent{ width:95%; margin:0 auto; padding:10px;}
#newsconttitle a:hover{ color:#04d;}
#newsconttitle span{ float:left;}
#newsconttitle h1{ font-size:14px; font-weight:bold; color:#666; padding:0; margin:0;}
#newsconttitle p{ width:100%; height:20px; line-height:20px; float:left; padding:15px 0; margin:0; color:#666; border-bottom:1px #ddd solid; text-align:left;}
#newsconttitle p a{ width:410px;height:20px; overflow:hidden; display:block; color:#1e6bc5;word-wrap: break-word;word-break: normal; float:left;}	
#newsummary{margin: 10px 0px 0px;padding: 12px 5px 6px;width:98%; background:#FFF;border: 1px solid #DCDDDD; }
#newsummary h2{text-indent: 2em;font-size: 14px;line-height: 20px;color:#666; font-weight:500;}
#newcontent{ line-height:22px; color:#666; margin:10px 5px 0 5px; float:left; display:inline;}
#newcontent p{ text-indent:24px; padding:0; margin:0;}
#newcontent p a:hover{ color:#04d;}
#newsleft .ad{ margin:10px 0; float:left;}
#newsright{ float:left; width:350px; margin-left:10px; display:inline;}
#articeBottom {font-size: 14px; margin: 6px 0 10px; padding-top: 10px; text-align: right; width: 97%;}
#articeBottom span{ float:left;}
#articeBottom span a{ font-size:12px;}
#articeBottom span a:hover{ color:#0099FF;}
#articleHeader { margin:5px 0; padding:10px;background:#F5F5F5; height:60px;}
#articleHeader h4{font-size:12px; color:#333; height:20px;}
#articleHeader h4 a{ font-size:12px; color:#333}
.details h2{ background:#F8F8FF; border: 1px solid #DDD; line-height: 25px; margin-bottom: 15px;  padding: 10px; font-size:14px; text-indent:28px;}
</style>
<?php 
	//2011-09-10
	$data=$tag['data.row']; 
?>
<div id="newscontent">
	<div id="newsconttitle">
		<h1><?php echo $data['title']; ?></h1>
		<p><span style="float:right;">点击数：<?php echo $data['counts']; ?></span><span><?php echo $data['dtTime']; ?>　来源: </span><a  href="<?php echo $data['sourceUrl']; ?>"><?php echo $data['source']; ?></a></p>
	</div>
    <div class="clear"></div>
    <?php echo $data['description']; ?>
	<div id="newcontent">
		<?php echo $data['content']; ?>
	</div>
	<div class="endPageNum">
		<table align="center">
			<tr>
				<td>
				<?php echo $data['navbar']; ?>
				<div class="clear"></div>
				</td>
			</tr>
		</table>
	</div>
	<div id="articeBottom"><span>【责任编辑：<a href="#"><?php echo $data['author']; ?></a>】</span><a href="#">(Top) 返回页面顶端</a></div>
</div>
<div style="border:1px dashed #999999"></div>
<div id="articleHeader">
<?php 
	$is_up=$tag['pager.data.up'];
	$is_down=$tag['pager.data.down'];
	if(is_array($is_down))
	{
	?>
	<h4>下一篇：<a href="<?php echo sys_href($params['id'],'list',$is_down['id'])?>"><?php echo $is_down['title']; ?></a></h4>
	<?php 
	}	
	if(is_array($is_up))
	{
	?>
	<h4 >上一篇：<a href="<?php echo sys_href($params['id'],'list',$is_up['id'])?>"><?php echo $is_up['title']; ?></a> </h4>
	<?php 	
	}	
	unset($is_up);
	unset($is_down);
?>
</div>