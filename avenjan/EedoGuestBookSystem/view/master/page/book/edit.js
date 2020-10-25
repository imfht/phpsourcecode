layui.config({
	base : "js/"
}).use(['form','layer','jquery','layedit','element','flow','laypage'],function(){
	var form = layui.form,
	layer = parent.layer === undefined ? layui.layer : parent.layer,
	layedit = layui.layedit,
	element = layui.element,
	flow = layui.flow,
	laypage = layui.laypage,
	$ = layui.jquery;
	//验证输入
	form.verify({
	  verifytext: function(value, item){ //value：表单的值、item：表单的DOM对象
	    if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
	      return '不能有特殊字符';
	    }
	    if(/(^\_)|(\__)|(\_+$)/.test(value)){
	      return '首尾不能出现下划线\'_\'';
	    }
	    if(/^\d+\d+\d$/.test(value)){
	      return '不能全为数字';
	    }
	  }
	  ,cd2t30: [
	    /^[\S]{2,30}$/
	    ,'长度2-30字符'
	  ]
	  ,cd2t10: [
	    /^[\S]{2,10}$/
	    ,'长度2-10字符'
	  ]

	});     
	var content = layedit.build('content'); //建立编辑器 
	var layeditIndex = layedit.build('replay'); 
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
	var isindex=getQueryVariable("index");
	$('#submit').click(function(){
		layedit.sync(content);//同步
		form.on("submit(editchick)",function(data){
			var formData = new FormData(editchickform) ;//
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
						top.layer.msg("提交成功，信息已修改！");
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
	$('#send').click(function(){
	//这里嵌套使得富文本和textarea数据同步
		layedit.sync(layeditIndex);//同步
	form.on("submit(message)",function(data){//提交数据
		var formData = new FormData(messageform) ;//
		var index = top.layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.8});
		$.ajax({            
		url:"addmessage.php?sid="+sid,
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
					top.layer.msg("提交数据失败，请重新提交，错误信息："+data.trim());
					//刷新父页面
				},2000);
				return false;
				}
			}
		});
		//end ajax	
		});	//end function	 message
 	})
	//end function send
	//加载页面数据
	var linksData = '';
	if(isindex=="yes"){
		jsurl="js.php?index=yes&sid="+sid;
	}else{
		jsurl="js.php?sid="+sid;
	}
	$.ajax({
		url : jsurl,
		type : "get",
		dataType : "json",
		success : function(data){
			linksData = data;
			//执行加载数据的方法
			List();
		}
	})
	function List(that){
		flow.load({
            elem: '#flow-manual' //流加载容器
            , isAuto: false
            , isLazyimg: true
            ,end :"<hr/>我是有底线的"
            , done: function (page, next) { //加载下一页
                var lis = [];
                for (var i = 0; i < linksData.length; i++) {
                	rehtml ='<div class="media-body"><div class="message-title"><h2>'+linksData[i].name+'</h2>'+linksData[i].time+' '+linksData[i].ip+'</div><div class="message-text"><p><i class="fa fa-1x fa-quote-left"></i>'+linksData[i].content+'<i class="fa fa-1x fa-quote-right"></i></p></div>';
                	if(linksData[i].del !==''){
                		rehtml +='<div id="del_rep"><a class="layui-btn layui-btn-danger layui-btn-xs rep_del" data-id="'+linksData[i].del+'"><i class="layui-icon">&#xe640;</i>删除此条回复 </a></div></div>';
                	}else{
						rehtml +='</div>';
                	}
                    lis.push(rehtml);
                }
                //console.log(page)
                next(lis.join(''), page < linksData.length); //假设总页数为 6
        	}
        });
	}
	


	 	//删除
	$("body").on("click",".rep_del",function(){ 
		var sid=$(this).attr("data-id");
		layer.open({
		title: '警告！',
		closeBtn : false,
		content: '<div style="text-align:center;color:#F7B824"><i class="layui-icon" style="font-size: 30px; color: #FF5722;">&#x1007;</i> <br/>确定删除此条信息？删除后不可恢复，谨慎操作！！！<br/>'
		,btn: ['确定删除','取消']
		,btn1: function(){
		$.ajax({            
			url:"del_rep.php",
			data:{sid:sid},
			type:"POST",
			dataType:"TEXT",
			success: function(data){
					if(data.trim()=="OK")
					{
					  layui.use('layer', function(){
					layer.msg('信息已被删除！',{shade:0.8,icon:6});
					});
					//刷新当前页
					setTimeout(function(){  //使用  setTimeout（）方法设定定时2000毫秒
					window.location.reload();//页面刷新
					},2000);
					}
					else
					{
						layui.use('layer', function(){
					layer.msg('操作失败，错误信息：'+data.trim(),{time:5000,shade:0.8,icon:5});
					});
					}
				}
			});
			},btn2: function(index, layero){  
				layer.close(index)
			  return false; 
			}    
		});
	})
  	//删除END
})