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
.clear { clear:both; }
*{ padding:0; margin:0;}
img{ border:none;}
a{ text-decoration:none;}
#piclist{ width:98%; margin:0 auto;}
#piclist ul li{ list-style:none; float:left; background:url(<?php echo $tag['path.skin']; ?>res/images/probox_bg3.gif) 10px 6px no-repeat; width:195px; padding:20px 0 0 25px;}
#piclist ul li img{ width:172px; height:129px; float:left;}
#piclist ul li h5{ font-size:12px; font-family:"微软雅黑"; padding:25px 0 10px 10px; float:left;}
#piclist ul li a{ color:#666;}
#piclist ul li a:hover{ color:#03c;}
#articeBottom { font-size: 14px; margin: 6px 0 10px; padding-top: 10px; text-align: right; width: 97%;}
</style>
<link rel="stylesheet" href="<?php echo $tag['path.skin']; ?>res/css/colorbox.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo $tag['path.skin'];?>res/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $tag['path.skin'];?>res/js/jquery.colorbox.js"></script>
<script>
	$(document).ready(function(){
		$(".colorbox").colorbox({rel:'colorbox', transition:"fade"});
	});
</script>
<div class="picmain">
  <div id="piclist">
    <ul>
<?php
if( !empty( $tag['data.results'] ) )
{
	foreach($tag['data.results'] as $k =>$data)
	{
	?>
      <li> 
	  <a class="colorbox" href="<?php echo $data['originalPic']?>" title="<?php echo $data['title'];?>" ><img src="<?php echo $data['smallPic']; ?>" alt="<?php echo $data['title'];?>"/></a>
      <h5><a href="<?php echo sys_href($data['channelId'],'picture',$data['id'])?>"><?php echo $data['title']; ?></a></h5>
      </li>
      <?php
	}
}
else
{
	echo '暂无图片。';
}
?>
    </ul>
    <div class="clear"></div>
    <div id="articeBottom">
      <?php if($tag['pager.cn']) echo $tag['pager.cn']; ?>
    </div>
  </div>
</div>
