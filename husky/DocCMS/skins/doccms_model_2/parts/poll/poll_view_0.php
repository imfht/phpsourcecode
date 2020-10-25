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
<!--
.poll{ width:99%; float:left;}
.poll p{ line-height:35px; font-size:14px;}
.jbjionbt a{ padding:0.2em 1em; margin-right:15px;-moz-border-bottom-colors: none; -moz-border-image: none; -moz-border-left-colors: none; -moz-border-right-colors: none; -moz-border-top-colors: none; background-color: #7FBF4D; background-image: -moz-linear-gradient(center top , #7FBF4D, #63A62F); border-color: #63A62F #63A62F #5B992B; border-radius: 3px 3px 3px 3px; border-style: solid; border-width: 1px; box-shadow: 0 1px 0 0 #96CA6D inset; font-family:"微软雅黑"; color: #FFFFFF; font: 12px; text-align: center; text-shadow: 0 -1px 0 #4C9021; width:80px; cursor:pointer;}
.jbjionbt a:hover { background-color: #76B347; background-image: -moz-linear-gradient(center top , #76B347, #5E9E2E); box-shadow: 0 1px 0 0 #8DBF67 inset; cursor: pointer;}
.creatbt { background-color: #ECECEC; background-image: -moz-linear-gradient(#F4F4F4, #ECECEC); border: 1px solid #D4D4D4; border-radius: 0.2em 0.2em 0.2em 0.2em; color: #333333; cursor: pointer; display: inline-block; font: 12px; outline: medium none; overflow: visible; padding: 0.5em 1em; position: relative; text-decoration: none; text-shadow: 1px 1px 0 #FFFFFF; white-space: nowrap; margin-right:10px;}
.creatbt:hover, .creatbt:focus, .creatbt:active { background-color: #3072B3; background-image: -moz-linear-gradient(#599BDC, #3072B3); border-color: #3072B3 #3072B3 #2A65A0; color: #FFFFFF; text-decoration: none; text-shadow: -1px -1px 0 rgba(0, 0, 0, 0.3); }
-->
</style>
<table cellpadding="5" cellspacing="4" border="0" style="padding-top:20px; padding-left:40px;">
<tr>
<td colspan="4"><span style="font-size:28px; color:#925402; font-weight:bold;"><?php $data=$tag['data.row']; echo $data['title']; ?></span></td>
</tr>
<?php
$nums=0;
if(!empty($tag['data.results']))
{
	$nums=$sb->sum->nums;
	foreach($tag['data.results'] as $k=>$data)
	{
		$nums+=$data['num'];
	}
	foreach($tag['data.results'] as $k=>$data)
	{
	?>
	<tr>
	<td height="50" width="100"><?php echo $data['choice']; ?></td>
	<td width="300"><img src="<?php echo $tag['path.skin']; ?>res/images/dot1.gif" width="<?php echo number_format((($data['num']/$nums)*100),2);?>%" height="15"></td>
	<td width="80"><strong><?php echo number_format((($data['num']/$nums)*100),2);?>%</strong></td>
	<td width="80"><?php echo $data['num'];?>人</td>
	</tr>
	<?php 
	}
}
?>
<tr>
<td colspan="4">投票总数（Total）：<strong><?php if($nums)echo $nums;else echo '暂无投票'; ?></strong></td>
</tr>
</table>