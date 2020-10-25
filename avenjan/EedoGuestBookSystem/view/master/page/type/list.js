layui.config({
	base : "js/"
}).use(['form','layer','jquery','laypage'],function(){
	var form = layui.form,
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		laypage = layui.laypage,
		$ = layui.jquery;
	//加载页面数据
	var linksData = '';
	$.ajax({
		url : "json.php",
		type : "get",
		dataType : "json",
		success : function(data){
			linksData = data;
			//执行加载数据的方法
			linksList();
		}
	})
	//添加分类
	$(".linksAdd_btn").click(function(){
		var index = layui.layer.open({
			title : "添加分类",
			type : 2,
			content : "add.php",
			success : function(layero, index){
				setTimeout(function(){
					layui.layer.tips('点击此处返回列表', '.layui-layer-setwin .layui-layer-close', {
						tips: 3
					});
				},500)
			}
		})
		$(window).resize(function(){
			layui.layer.full(index);
		})
		layui.layer.full(index);
	})
  	//修改
	$("body").on("click",".links_edit",function(){ 
			var sid=$(this).attr("data-id");
			var index = layui.layer.open({
				title : "修改分类",
				type : 2,
				content : "edit.php?sid="+sid,
				success : function(layero, index){
					setTimeout(function(){
						layui.layer.tips('点击此处返回列表', '.layui-layer-setwin .layui-layer-close', {
							tips: 3
						});
					},500)
				}
			})			
			layui.layer.full(index);
	})
 	//删除
	$("body").on("click",".links_del",function(){ 
			var sid=$(this).attr("data-id");
			layer.open({
			title: '警告！',
			closeBtn : false,
			content: '<div style="text-align:center;color:#F7B824"><i class="layui-icon" style="font-size: 30px; color: #FF5722;">&#x1007;</i> <br/>确定删除此条信息？删除后不可恢复，谨慎操作！！！<br/>'
			,btn: ['确定删除','取消']
			,btn1: function(){
			$.ajax({            
				url:"del.php",
				data:{sid:sid},
				type:"POST",
				dataType:"TEXT",
				success: function(data){
						if(data.trim()=="OK")//要加上去空格，防止内容里面有空格引起错误。
						{
						  layui.use('layer', function(){
						layer.msg('该分类已被删除！',{shade:0.8,icon:6});
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
	function linksList(that){
		//渲染数据
		function renderDate(data,curr,limit){
			var dataHtml = '';
			if(!that){
				currData = linksData.concat().splice(curr*limit-limit, limit);
			}else{
				currData = that.concat().splice(curr*limit-limit, limit);
			}
			if(currData.length != 0){
				for(var i=0;i<currData.length;i++){
					dataHtml += '<tr>'
			    	+'<td>'+currData[i].name+'</td>'
			    	+'<td>'+currData[i].count+'</td>';
			    	dataHtml +='<td>'
					+  '<a class="layui-btn layui-btn-mini links_edit" data-id="'+currData[i].id+'"><i class="layui-icon">&#xe642;</i> 编辑</a>'
					+  '<a class="layui-btn layui-btn-danger layui-btn-mini links_del" data-id="'+currData[i].id+'"><i class="layui-icon">&#xe640;</i> 删除</a>'
			        +'</td>'
			    	+'</tr>';
				}
			}else{
				dataHtml = '<tr><td colspan="7">暂无数据</td></tr>';
			}
		    return dataHtml;
		}
		//分页
		if(that){
			linksData = that;
		}
		laypage.render({
			elem : "page",
			count : Math.ceil(linksData.length),
			limits:[1,5,10, 20,50, 100],
			layout: ['count', 'prev', 'page', 'next', 'limit', 'refresh', 'skip'],
			jump : function(obj){
				$(".links_content").html(renderDate(linksData,obj.curr,obj.limit));
				$('.links_list thead input[type="checkbox"]').prop("checked",false);
		    	form.render();
			}
		})
	}
})
