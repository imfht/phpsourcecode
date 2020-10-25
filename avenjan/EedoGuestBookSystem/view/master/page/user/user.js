layui.config({
    base : "js/"
}).use(['form','layer','jquery'],function(){
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : parent.layer,
        $ = layui.jquery;
    //添加验证规则
    form.verify({
        oldPwd : function(value, item){
            if(hex_md5(value) != oldpwd){  //旧密码验证	
                return "密码错误，请重新输入！";	
            }
        },
        newPwd : function(value, item){
            if(value.length < 6){
                return "密码长度不能小于6位";
            }
        },
        confirmPwd : function(value, item){
            if(!new RegExp($("#oldPwd").val()).test(value)){
                return "两次输入密码不一致，请重新输入！";
            } else{
					 //执行修改
			layui.use('layer', function(){
			layer.msg('修改中，请稍候',{icon: 16,time:false,shade:0.8});
			});
		
        var uname = $("#uname").val();
		var pwd = $("#pwd").val(); 
        //调ajax
        $.ajax({            
            url:"changepwd_do.php",
            data:{uname:uname,pwd:pwd},
            type:"POST",
            dataType:"TEXT",
            success: function(data){
                    if(data.trim()=="OK")
                    {
					layui.use('layer', function(){
					layer.msg('恭喜，密码修改成功！',{time:5000,shade:0.8});
					});
                    }
                    else
                    {
					layui.use('layer', function(){
					layer.msg('修改失败,错误信息：'+data.trim(),{time:5000,shade:0.8});
					});
                        //alert(data.trim());
                    }
                } 
            });	
			}
        }
    });
    form.on("submit(edit)",function(data){
        var formData = new FormData(editform) ;//
        var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
        $.ajax({            
            url:"info_do.php",
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
                        top.layer.close(index);
                        
                        
                        //刷新父页面
                        location.reload();
                    },2000);
                    return false;
                    }
                    else
                    {
                        setTimeout(function(){
                        top.layer.close(index);
                        top.layer.msg("提交数据失败，请重新提交，错误信息："+data.trim());
                        
                    },2000);
                    return false;
                        
                    }
            
                }
            });
            //end ajax          
    })
    //end function
 });   
	
