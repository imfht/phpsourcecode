layui.config({
	base : "js/"
}).use(['form','layer','jquery','layedit'],function(){
	var form = layui.form,
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		layedit = layui.layedit,
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
	form.on("submit(editchick)",function(data){
		var formData = new FormData(editchickform) ;//
	var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
			 $.ajax({            
						url:"system_do.php",
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
									top.layer.msg("提交成功，信息已修改！");
									
									//刷新页面
									location.reload();
								},2000);
								return false;
								}
								else
								{
									setTimeout(function(){
									top.layer.close(index);
									top.layer.msg("提交数据失败，请重新提交，错误信息："+data.trim());
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