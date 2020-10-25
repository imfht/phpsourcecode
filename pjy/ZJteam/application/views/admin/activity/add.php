<?php include 'application/views/admin/public/head.php'?>
<script src="<?php echo base_url()?>Public/js/jquery.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo base_url();?>Public/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo base_url();?>Public/ueditor/ueditor.all.js"></script>

<script type="text/javascript" charset="utf-8" src="<?php echo base_url();?>Public/ueditor/lang/zh-cn/zh-cn.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>Public/ueditor/themes/default/css/ueditor.css" />

<form name="content" action="<?php echo site_url('admin/addact');?>" method="post" enctype="multipart/form-data">
<table cellpadding="0" cellspacing="1" class="table_form">
 <caption>添加活动</caption>
 <tr>
  <td align="right"><span class="fred">*</span> 标 题：</td>
  <td>
   <input name="title"  type="text" value="" size="40" require="true" datatype="require"/>
   <span class="blue"><span class="red">*</span>标题不能为空</span>
  </td>
 </tr>
 <tr>
  <td align="right">发布者：</td>
  <td>
   <input name="author" type="text" size="40">
  </td>
 </tr> 
 <tr>
  <td align="right">支教地点：</td>
  <td>
   <input name="address" type="text" size="40">
  </td>
 </tr>
 <tr>
  <td align="right">活动时间：</td>
  <td>
   <input name="duringtime" id="author" type="text" size="30" maxlength="30"> 
  </td>
 </tr> 
 <tr>
  <td align="right"><span>封面图片：</span></td>
  <td><input type="file" name="pic" id="file0" multiple="multiple" /><br><img src="" id="img0" width="200" height="120">&nbsp;*必须是jpg格式
  </td>
  <script>	
	$("#file0").change(function(){
		var objUrl = getObjectURL(this.files[0]) ;
		console.log("objUrl = "+objUrl) ;
		if (objUrl) {
			$("#img0").attr("src", objUrl) ;
		}
	}) ;
	//建立一個可存取到該file的url
	function getObjectURL(file) {
		var url = null ; 
		if (window.createObjectURL!=undefined) { // basic
			url = window.createObjectURL(file) ;
		} else if (window.URL!=undefined) { // mozilla(firefox)
			url = window.URL.createObjectURL(file) ;
		} else if (window.webkitURL!=undefined) { // webkit or chrome
			url = window.webkitURL.createObjectURL(file) ;
		}
		return url ;
	}
	</script>
 </tr>
 <tr>
  <td align="right"><span class="fred">*</span> 活动具体内容：</td>
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