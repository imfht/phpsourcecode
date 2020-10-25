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
*{ margin:0; padding:0;}
ul,li{ list-style:none;}
a{ text-decoration:none;}
#newlist{ width:99%; float:left;}
#newlist li{ line-height:30px; border-bottom:#999 1px dashed; list-style:none; padding-right:10px;}
#newlist li span{ float:right;}
#newlist li a{ background:url(<?php echo $tag['path.skin']; ?>res/images/list_a.gif) 6px 5px no-repeat; color:#999; padding:0 0 0 25px;}
#newlist li a:hover{ background:url(<?php echo $tag['path.skin']; ?>res/images/list_l.gif) 6px 5px no-repeat; color:#fc0;}
#articeBottom { font-size: 14px; margin: 6px 0 10px; padding-top: 10px; text-align: right; width: 97%;}
</style>
<div id="newlist">
	<ul>
<?php
	if( !empty( $tag['data.results'] ) )
	{
		foreach($tag['data.results'] as $k =>$data)
		{

			?>
			<li><span><?php echo date('Y-m-d',strtotime($data['dtTime'])); ?></span><a href="<?php echo sys_href($data['channelId'],'list',$data['id'])?>" title="<?php echo $data['title']; ?>" <?php echo $data['style']; ?>><?php echo $data['title']; ?></a></li>
			<?php			
		}
	}
	else
	{
		echo '暂无文章。';
	}
?>
	</ul>
	<div class="clear"></div>
	<div id="articeBottom"><?php if(!empty($tag['pager.cn'])) echo $tag['pager.cn']; ?></div>
</div>