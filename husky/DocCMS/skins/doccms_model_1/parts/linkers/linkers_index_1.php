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
#lkslist{ width:98%; margin:0 10px;}
#lkslist h1{ font-family:"微软雅黑"; font-size:20px; padding:20px 0; font-weight:normal;}
#lkslist ul{ width:100%; float:left; list-style:none;}
#lkslist ul li{ float:left; padding:10px 15px; word-break:keep-all;white-space: nowrap;}
#lkslist ul li img{ border:1px solid #f2f2f2; padding:2px;}
#lkslist ul li a{ color:#666; font-size:14px;}
#lkslist ul a:hover{ color:#03c}
</style>
<div id="lkslist">
	<ul id="textlinkers">
    	<h1>文字友情链接:</h1>				
		<?php
		if( !empty( $tag['data.results'] ) )
		{
			foreach( $tag['data.results'] as $k=>$data)
			{
				if($data['links']){
			?>
			<li><a href="<?php echo $data['linkAddress']; ?>"target="_blank"><?php echo $data['title'];?></a></li>
			<?php
				}
			}
		}else{
			echo'暂无友情链接';
		}
		?>	
	</ul>
    <ul id="imglinkers">
    	<h1>图片友情链接:</h1>
    	<?php 
		if( !empty( $tag['data.results'] ) )
		{
			foreach( $tag['data.results'] as $k=>$data)
			{
				if(!$data['links']){
			?>
			<li><a href="<?php echo $data['linkAddress']; ?>" target="_blank"><img src="<?php echo $data['smallPic']; ?>" width="100" height="33" border="0" alt="<?php echo $data['title']; ?>"></a></li>
			<?php
				}
			}
		}else{
			echo'暂无友情链接';
		}
		?>
    </ul>
</div>