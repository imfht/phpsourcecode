layui.config({
	base : "js/"
}).use(['form','layer','jquery'],function(){
	var form = layui.form,
	layer = parent.layer === undefined ? layui.layer : parent.layer,
	$ = layui.jquery;
		function getQueryVariable(variable)//获取sid
		{
			   var query = window.location.search.substring(1);
			   var vars = query.split("&");
			   for (var i=0;i<vars.length;i++) {
					   var pair = vars[i].split("=");
					   if(pair[0] == variable){return pair[1];}
			   }
			   return(false);
		}
		var sid=getQueryVariable("sid");
	form.on("submit(edittype)",function(data){
		var formData = new FormData(edittype) ;//
		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
		$.ajax({            
		url:"edit_do.php?sid="+sid,
		type : 'POST', 
		data : formData, 
		// 告诉jQuery不要去处理发送的数据
		processData : false, 
		// 告诉jQuery不要去设置Content-Type请求头
		contentType : false,
		success: function(data){
				if(data.trim()=="OK")
				{
				   setTimeout(function(){
					top.layer.msg("修改成功！");
					top.layer.close(index);
					layer.closeAll("iframe");
					//刷新父页面
					parent.location.reload();
				},2000);
				return false;
				}
				else
				{
					setTimeout(function(){
					top.layer.close(index);
					top.layer.msg("操作失败，请重新提交，错误信息："+data.trim());
					//刷新父页面
				},2000);
				return false;
				}
			}
		});
						//end ajax
 	})
	//end function
})