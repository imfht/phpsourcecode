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
#stuffbox{ color:#999;}
#stuffbox img{ width:320px; height:240px; padding:2px; border:1px solid #ccc; margin-bottom:10px;}
.webSearch1 a{font-size: 12px; color: #0000FF; text-decoration: underline;}
.webSearch2 a{font-size: 12px; color: #009900; text-decoration: underline;}
.webContent {font-size: 14px; text-decoration: none; line-height:25px; color:#333;}
.webSearch1 h2{ font-size:16px; color:#03c; font-family:"微软雅黑", "黑体"; font-weight:normal;}
</style>
<div id="stuffbox">
<?php
	if(!empty($tag['data.results']))
	{
		$keyword=urldecode($request['keyword']);
		$request['i'] = $request['i'] ? $request['i'] : 1;
		$temResult = array_slice($tag['data.results'],($request['i']-1)*10,10,true);
		foreach($temResult as $data)
		{
			if(!empty($data['title'])){
				$data['title']=get_keyword_str($data['title'],$keyword,30);
			}else{
				$data['title']=get_keyword_str($data['content'],$keyword,30);
			}
			if(!empty($data['content'])){
				$data['content']=get_keyword_str($data['content'],$keyword,100);
			}
			?>
			<table border="0" cellpadding="0" cellspacing="0">
			<tr>
			<?php
			$tempTypeArr=array('jobs','article','tour');
			if(in_array($data['type'],$tempTypeArr))
			{
					?>
					<td><span class="webSearch1"><a target="_blank" href="<?php echo sys_href($data['p'])?>"><h2><?php echo $data['title']; ?></h2></a></span></td>
					<?php
			}
			elseif($data['type'] == 'linkers')
			{
					?>
					<td><span class="webSearch1"><a target="_blank" href="<?php echo './?p='.$data['p'].'&r='.$data['id']; ?>"><h2><?php echo $data['title']; ?></h2></a></span></td>
					<?php 
			}
			elseif($data['type'] == 'product')
			{
					?>
					<td><img src="<?php echo ispic($data['smallPic'])?>" width="320" height="240"><br><span class="webSearch1"><a target="_blank" href="<?php echo sys_href($data['p'],$data['type'],$data['id'])?>"><h2><?php echo $data['title']; ?></h2></a></span></td>
					<?php 
			}
			else
			{
			    	?>
					<td><span class="webSearch1"><a target="_blank" href="<?php echo sys_href($data['p'],$data['type'],$data['id'])?>"><h2><?php echo $data['title']; ?></h2></a></span></td>
					<?php
			}
			?>
			</tr>
			<tr>
			<td class="webContent"><?php echo $data['content']; ?></td>
			</tr>
			<tr>
			<?php 
			if(in_array($data['type'],$tempTypeArr))
			{
				?>
				<td><?php echo $data['dtTime']; ?>&nbsp;&nbsp;<span class="webSearch2"><a target="_blank" href="<?php echo sys_href($data['menuName'])?>">快速链接</a></span></td>
				<?php 
			}elseif($data['type'] == 'linkers'){
				?>
				<td><?php echo $data['dtTime']; ?>&nbsp;&nbsp;<span class="webSearch2"><a target="_blank" href="<?php echo '/?p='.$data['p'].'&r='.$data['id']; ?>">快速链接</a></span></td>
				<?php 
			}else{
				?>
				<td><?php echo $data['dtTime']; ?>&nbsp;&nbsp;<span class="webSearch2"><a target="_blank" href="<?php echo sys_href($data['p'],$data['type'],$data['id'])?>">快速链接</a></span></td>
				<?php 
			}
			?>
			</tr>			
			</table>
			<br />
			<?php
		}
		echo $tag['pager.cn']	;
	}
	else
	{
		echo '对不起！没有找到相关内容！';
	}

?>
</div>