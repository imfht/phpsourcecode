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
<script>
jQuery(document).ready(function(){
jQuery(".jobopen").click(function () {	
	     jQuery(this).parent().parent().next().toggle("slow");
    })
});
</script>
<style type="text/css">
*{ padding:0; margin:0;}
ul,ol,li{ list-style:none;}
img{ border:none;}
a{ text-decoration:none;}
.infodd{ width:98%; float:left;}
.ddtitle{ width:98%; height:35px; background:#EBEBEB;}
.ddtr{ width:98%; height:33px; background:url(<?php echo $tag['path.skin']; ?>res/images/location_bg.gif) bottom repeat-x;}
.jobinfo{ padding:12px 0 30px 45px; float:left;}
.jobopen{ background:url(<?php echo $tag['path.skin']; ?>res/images/jobopen.gif) 0 2px no-repeat; padding-left:15px;}
.jobinfo p{ line-height:25px; float:left; width:95%;}
.readol{ color:#116201; cursor:pointer;}
.pubdownload{ color:#7E0404;}
#articeBottom { font-size: 14px; margin: 6px 0 10px; padding-top: 10px; text-align: right; width: 97%;}
</style>
<div class="infodd">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
    	<tr class="ddtitle" align="center">
          <td width="25%">职位</td>
          <td width="5%">人数</td>
          <td width="10%">工作性质</td>
          <td width="10%">工作地点</td>
          <td width="25%">发布日期</td>
          <td width="15%">职位描述</td>
          <td width="10%">&nbsp;</td>
        </tr>
<?php
if(!empty($tag['data.results']))
{
	foreach($tag['data.results'] as $k=>$data)
	{
		?>
        <tr class="ddtr" align="center">
          <td><?php echo $data['title']; ?></td>
          <td><?php echo $data['requireNum']; ?></td>
          <td><?php echo $data['jobKind']; ?></td>
          <td><?php echo $data['address']; ?></td>
          <td><?php echo $data['dtTime']; ?></td>
          <td><a class="readol jobopen">展开阅读</a></td>
          <td><a href="<?php echo sys_href($data['channelId'],"job_send",$data['id'])?>" class="pubdownload">应聘</a></td>
        </tr>
        <tr id="jobbox" style="display:none;">
          <td colspan="7">
              <div class="jobinfo">
                  <b>职位描述及要求：</b>
                  <?php echo $data['content']; ?>
              </div>
          </td>
      	</tr>
        <?php
	}
	?>
	
	<?php
}
else
{
	echo '暂无招聘。';
}
?>
	</table>
    <div id="articeBottom">
		<div id="apartPage"><?php if(!empty($tag['pager.cn']))echo $tag['pager.cn']; ?></div>
	</div>
</div>