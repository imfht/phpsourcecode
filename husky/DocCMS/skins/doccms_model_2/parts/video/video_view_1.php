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
<?php
	//2011-09-11
	$data=$tag['data.row'];
?>
<style type="text/css">
*{ padding:0; margin:0;}
ul,ol,li{ list-style:none;}
img{ border:none;}
a{ text-decoration:none;}
#videoview p{ line-height:25px; color:#666; font-family:"微软雅黑";}
.prodetails{ color:#666; font-size:14px;}
.details{ line-height:20px; border:1px solid #ddd; padding:10px; background:#fbfbfb; margin-bottom:15px;}
</style>
<h3><?php echo $data['title'];?></h3>
<div id="videoview">
	<p>关键字：<?php echo $data['keywords'];?></p>
    <p>视频大小：<?php echo $data['fileSize']!=' B'?$data['fileSize']:'未知';?></p>
    <p class="details">视频摘要: <?php echo $data['description']; ?></p> 
	<p>
    <?php $temp = explode('/',$data['filePath']); 
		  if($temp[0]!='http:')
		  {
	?>
<script type="text/javascript" src="<?php echo $tag['path.skin']; ?>res/js/swfobject.js"></script>
<div id="CuPlayer" >
<strong>你的Flash Player版本过低，请<a href="http://get.adobe.com/cn/flashplayer/" >点此进行播放器升级</a>！</strong>
</div>
<script type="text/javascript">
var so = new SWFObject("<?php echo $tag['path.skin']; ?>res/swf/CuPlayerMiniV20_Black_S.swf","CuPlayer","600","450","9","#000000");
so.addParam("allowfullscreen","true");
so.addParam("allowscriptaccess","always");
so.addParam("wmode","opaque");
so.addParam("quality","high");
so.addParam("salign","lt");
so.addVariable("CuPlayerFile","<?php echo $data['filePath']; ?>");
so.addVariable("CuPlayerImage","<?php echo $data['picture']; ?>");
so.addVariable("CuPlayerShowImage","true");
so.addVariable("CuPlayerWidth","600");
so.addVariable("CuPlayerHeight","450");
so.addVariable("CuPlayerAutoPlay","false");
so.addVariable("CuPlayerAutoRepeat","true");
so.addVariable("CuPlayerShowControl","false");
so.addVariable("CuPlayerAutoHideControl","true");
so.addVariable("CuPlayerAutoHideTime","1");
so.addVariable("CuPlayerVolume","80");
so.write("CuPlayer"); 
</script>
<?php }else{?>
<embed src="<?php echo $data['filePath']?>" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="opaque" width="600" height="450"></embed>
<?php }?>
</p>
<p class="prodetails">视频详情：<br /><?php echo $data['content']; ?></p>	
</div>
<?php unset($data); ?>