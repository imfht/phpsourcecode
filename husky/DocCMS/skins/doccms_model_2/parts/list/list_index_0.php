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
.mionmenu1 li{ font-size:12px; height:24px; float:left; cursor:pointer; padding-left:12px; margin-right:8px;}
.mionmenu1 li span{ display:block; padding-right:12px; line-height:24px;}
.mionmenu1 .hover{ color:#fff;background:url(/images/index3/zxlileft.png) no-repeat left top;}
.mionmenu1 .hover span{background:url(/images/index3/zxliright.png) no-repeat right top;}
.mionlist1{ display:none;}
#mionblock{ display:block;}
.mionlist1 li{ width:328px; height:124px; float:left; background:#eee; margin:0 14px 14px 0;}
.mionlist1 li img{ float:left; width:124px; height:124px;}
.mionlist1 li h3{ float:left; width:180px; height:40px; line-height:40px; text-indent:12px; overflow:hidden; font-size:16px; font-family:"微软雅黑";}
.mionlist1 li h3 a{ color:#333;}
.mionlist1 li p{ float:left; width:167px; padding:2px 0 0 13px; overflow:hidden; height:63px; line-height:21px; color:#888;}
#articeBottom { font-size: 14px; margin: 6px 0 10px; padding-top: 10px; text-align: right; width: 97%;}
</style>
<div class="mionlist1" id="mionblock">
	<ul>
<?php
	if( !empty( $tag['data.results'] ) )
	{
		foreach($tag['data.results'] as $k =>$data)
		{

			?>
            <li><a href="<?php echo sys_href($data['channelId'],'list',$data['id'])?>"><img src="<?php echo ispic($data['originalPic']); ?>" /></a><h3><a href="<?php echo sys_href($data['channelId'],'list',$data['id'])?>" title="<?php echo $data['title']; ?>" <?php echo $data['style']; ?>><?php echo $data['title']; ?></a></h3><p><?php echo $data['description']; ?></p></li>
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