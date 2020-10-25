<?php include 'application/views/admin/public/head.php'?>
<script src="<?php echo base_url()?>Public/js/jquery.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo base_url();?>Public/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo base_url();?>Public/ueditor/ueditor.all.js"></script>

<script type="text/javascript" charset="utf-8" src="<?php echo base_url();?>Public/ueditor/lang/zh-cn/zh-cn.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>Public/ueditor/themes/default/css/ueditor.css" />

<form name="content" action="<?php echo site_url('admin/addYougan');?>" method="post" enctype="multipart/form-data">
<table cellpadding="0" cellspacing="1" class="table_form">
 <caption>添加文章</caption>
 <tr>
  <td align="right"><span class="fred">*</span> 标 题：</td>
  <td>
   <input name="title"  type="text" value="" size="40" require="true" datatype="require"/>
   <span class="blue"><span class="red">*</span>标题不能为空</span>
  </td>
 </tr>
 <tr>
  <td align="right"><span class="fred">*</span> 具体内容：</td>
<td style="width:980px;height:500px;">
  <textarea id="editor" name="editor" style="width:100%;height:100%;"></textarea>
  </td></tr>
 <tr>
  <td colspan="2" height="30" align="center"><button type="submit" name="sub" value="submit">确定添加</button></td>
 </tr>
</table>
</form>
<script type="text/javascript">
	var ue = UE.getEditor('editor');
</script>