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
a:hover{ color:#005bab; text-decoration:underline;}
.infodd{ width:100%; float:left; font-size:12px;}
.ddtitle{ width:100%; height:35px; background:#EBEBEB;}
.ddtitle td{ border-right:1px solid #fff;}
.ddtr{ width:100%; height:33px; background:url(<?php echo $tag['path.skin']; ?>res/images/location_bg.gif) bottom repeat-x;}
.ddtr td{ line-height:22px;}
.readol{ color:#116201;}
.pubdownload{ color:#7E0404;}
#articeBottom { font-size: 14px; margin: 6px 0 10px; padding-top: 10px; text-align: right; width: 97%;}
</style>
<div id="infodd">
	<?php
	if(!empty($tag['data.results']))
	{
	?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr class="ddtitle" align="center">
          <td width="40%">资料名称</td>
          <td width="10%">文件大小</td>
          <td width="20%">更新时间</td>
          <td width="15%">权限设置</td>
          <td width="15%">下载</td>
        </tr>
	<?php foreach($tag['data.results'] as $k =>$data){	?>
        <tr class="ddtr" align="center">
          <td><?php echo $data['title']; ?></td>
          <td><?php echo $data['fileSize']; ?></td>
          <td><?php echo $data['dtTime']; ?></td>
          <td class="readol">开放权限</td>
          <td><a href="<?php echo sys_href($data['channelId'],'download',$data['id']);?>"  target="_blank">下载</a></td>
        </tr>
	<?php } ?>
	</table>
	<div class="clear"></div>
	<div id="articeBottom"><?php if(!empty($tag['pager.cn']))echo $tag['pager.cn']; ?> </div>
	<?php
	}else{
		echo '暂无软件下载。';
	}
	?>
</div>