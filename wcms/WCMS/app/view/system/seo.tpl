{include file="news/header.tpl"}

<!-- 头部// -->
{include file="news/top.tpl"}


{include file="news/nav.tpl"}
	<!-- start: Content -->
			<div style="margin:10px;">
			
						
			<div class="row-fluid">







<table class="table table-striped table-condensed">

	<form class="form" style="margin:0px;">
<tr><th class="span2">SEO</th><th></th></tr>

     <tr>
		<td  >网站名称</td>
		<td   ><input type="text" name="website_name" value="{$config.website_name}"></td>

	</tr>
	
	
	 <tr>
		<td >关键词</td>
		<td  ><input type="text" name="keywords" value="{$config.keywords}" class="input-large"></td>

	</tr>
	
	<tr>
		<td   >描述</td>
		<td  ><input type="text" name="description" value="{$config.description}" class="input-xxlarge"></td>

	</tr>
	
		<tr>
		<td   >统计代码</td>
		<td  ><input type="text" name="analysis" value="{$config.analysis}" class="input-xxlarge"></td>

	</tr>
	
	<td></td>
	<td><input type="button" value="保存"  class="btn"  onclick="save()"></td>
	</tr>
	

</table>



				<div class="well" >
				

</div></div></div>
</form>






	<script type="text/javascript" src="./static/public/jquery-1.11.0.min.js"></script>
{literal}
<script>

function email(){
var email=$("input[name='email_test']").val();
$.post("./index.php?email/test",{email_test:email},function(data){
alert("邮件已发送，请查收 !");
},"json")
}
function save(){

	var data=$("form").serialize();
$.get("./index.php?system/save/?"+data,function(data){
	 alert(data.message);
	},"json")
}


</script>

{/literal}
{include file="news/footer.tpl"}

