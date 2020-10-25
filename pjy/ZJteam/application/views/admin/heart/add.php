<?php include 'application/views/admin/public/head.php'?>
<script src="<?php echo base_url()?>Public/js/jquery.min.js"></script>

<form name="content" action="<?php echo site_url('admin/addHeart');?>" method="post" enctype="multipart/form-data">
<table cellpadding="0" cellspacing="1" class="table_form">
 <caption>添加人员</caption>
 <tr>
  <td align="right"><span class="fred">*</span> 姓名：</td>
  <td>
   <input name="uname"  type="text" value="" size="40" require="true" datatype="require"/>
  </td>
 </tr>
 <tr>
  <td align="right">发布者：</td>
  <td>
   <input name="edit" type="text" size="40">
  </td>
 </tr>   
 <tr>
  <td align="right">身份简介：</td>
  <td>
   <textarea style="width:400px;height:200px;resize:none;" name="info"></textarea>
  </td>
 </tr>  
 <tr>
  <td align="right"><span>照片：</span></td>
  <td><input type="file" name="pic" id="file0" multiple="multiple" /><br><img src="" id="img0" width="250" height="250">250*250
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
  <td colspan="2" height="30" align="center"><button type="submit" name="sub" value="submit">确定添加</button></td>
 </tr>
</table>
</form>