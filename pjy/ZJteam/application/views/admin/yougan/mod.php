<?php include 'application/views/admin/public/head.php'?>
<script src="<?php echo base_url()?>Public/js/jquery.min.js"></script>
<head>
	<script type="text/javascript" charset="utf-8" src="<?php echo base_url();?>Public/ueditor/ueditor.config.js"></script>
	<script type="text/javascript" charset="utf-8" src="<?php echo base_url();?>Public/ueditor/ueditor.all.js"></script>

	<script type="text/javascript" charset="utf-8" src="<?php echo base_url();?>Public/ueditor/lang/zh-cn/zh-cn.js"></script>
	<link rel="stylesheet" href="<?php echo base_url();?>Public/ueditor/themes/default/css/ueditor.css" />
	<script type="text/javascript">
		var ue = UE.getEditor('editor');
	</script>
</head>
<form name="content" action="<?php echo site_url('admin/updyougan');?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php echo $yougan->id;?>"/>
<table cellpadding="0" cellspacing="1" class="table_form">
 <caption>修改文章</caption>
 <tr>
  <td align="right"> 标 题</td>
  <td>
   <input name="title"  type="text" value="<?php echo $yougan->title;?>" size="40" require="true" datatype="require"/>
  </td>
 </tr>
   <tr>
 <tr>
  <td align="right"><span class="fred">*</span> 文章内容：</td>
<td>
  <textarea id="editor" name="editor" style="width:100%;height:100%;"><?php echo $yougan->content;?></textarea>
  </td></tr>

 <tr>
  <td colspan="2" height="30" align="center"><button type="submit" name="sub" value="submit">确定修改</button></td>
 </tr>
</table>
</form>
