layui.config({
	base : "js/"
}).use(['form','layer','jquery','layedit','laydate'],function(){
	var form = layui.form,
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		$ = layui.jquery;
	form.on("submit(addtype)",function(data){
	var formData = new FormData(addtype) ;//
	var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
			 $.ajax({            
						url:"add_do.php",
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
									top.layer.msg("新增成功！");
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
									top.layer.msg("新增失败，请重新提交，错误信息："+data.trim());
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