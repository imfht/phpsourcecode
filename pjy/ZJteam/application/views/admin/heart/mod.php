<?php include 'application/views/admin/public/head.php'?>
<head>
	<script src="<?php echo base_url()?>Public/js/jquery.min.js"></script>
</head>
<form name="content" action="<?php echo site_url('admin/updHeart');?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php echo $detail->id;?>"/>
<table cellpadding="0" cellspacing="1" class="table_form">
 <caption>修改爱心名人</caption>
 <tr>
  <td align="right"> 姓名</td>
  <td>
   <input name="uname"  type="text" value="<?php echo $detail->uname;?>" size="40" require="true" datatype="require"/>
  </td>
 </tr>
 <tr>
  <td align="right"><span>修改照片</span></td>
  <td><input type="file" name="pic" id="file0" multiple="multiple" /><br><img src="<?php echo base_url().$detail->pic;?>" id="img0" width="200" height="200">
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
  <td align="right">简介</td>
  <td><textarea style="overflow: hidden;" name="info" cols="60" rows="8"><?php echo $detail->info;?></textarea></td>
 </tr>

 <tr>
  <td colspan="2" height="30" align="center"><button type="submit" name="sub" value="submit">确定修改</button></td>
 </tr>
</table>
</form>
