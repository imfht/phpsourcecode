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
<form name="content" action="<?php echo site_url('admin/updZc');?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php echo $zc->id;?>"/>
<table cellpadding="0" cellspacing="1" class="table_form">
 <caption>修改众筹</caption>
 <tr>
  <td align="right"> 标 题</td>
  <td>
   <input name="title"  type="text" value="<?php echo $zc->title;?>" size="40" require="true" datatype="require"/>
  </td>
 </tr>
 <tr>
  <td align="right">众筹网址</td>
  <td>
   <input name="url" type="text" value="<?php echo $zc->url;?>" size="100">
  </td>
 </tr>  
 <tr>
  <td align="right">发布者</td>
  <td>
   <input name="author" type="text" value="<?php echo $zc->author;?>" size="100">
  </td>
 </tr> 
 <tr>
  <td align="right">目标资金</td>
  <td>
   <input name="money" type="text" value="<?php echo $zc->money;?>" size="40">
  </td>
 </tr>
 <tr>
  <td style="width:100px;" align="right">活动时间</td>
  <td>
   <input name="zctime" type="text" value="<?php echo $zc->zctime;?>" size="30" maxlength="100"> 
  </td>
 </tr> 
 <tr>
  <td align="right"><span>修改封面图</span></td>
  <td><input type="file" name="pic" id="file0" multiple="multiple" /><br><img src="<?php echo base_url().$zc->pic;?>" id="img0" width="200" height="120">&nbsp;&nbsp;&nbsp;&nbsp;当前200 * 150， 原图最少600*360， 图片比例5:3
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
			url = window.webkitURL.createObjectURL(file);
		}
		return url ;
	}
	</script>
  </td></tr>
 <tr>
  <td style="width:100px;" align="right">众筹状态</td>
  <td>
   <input name="status" type="radio" value="0" <?php if($zc->status==0){echo "checked";}?> /> 正在众筹
   <input name="status" type="radio" value="1" <?php if($zc->status==1){echo "checked";}?> /> 已结束
  </td>
 </tr> 
 <tr>
  <td align="right"><span class="fred">*</span> 众筹详情：</td>
<td>
  <textarea id="editor" name="editor" style="width:100%;height:100%;"><?php echo $zc->content;?></textarea>
  </td></tr>

 <tr>
  <td colspan="2" height="30" align="center"><button type="submit" name="sub" value="submit">确定修改</button></td>
 </tr>
</table>
</form>
