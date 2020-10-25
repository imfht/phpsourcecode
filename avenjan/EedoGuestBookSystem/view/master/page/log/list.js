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
			if(window.sessionStorage.getItem("addsites")){
				var addsites = window.sessionStorage.getItem("addsites");
				linksData = JSON.parse(addsites).concat(linksData);
			}
			//执行加载数据的方法
			linksList();
		}
	})

	//查询
	$(".search_btn").click(function(){
		var newArray = [];
		if($(".search_input").val() != ''){
			var index = layer.msg('查询中，请稍候',{icon: 16,time:false,shade:0.8});
            setTimeout(function(){
            	$.ajax({
					url : "json.php",
					type : "get",
					dataType : "json",
					success : function(data){
						if(window.sessionStorage.getItem("addsites")){
							var addsites = window.sessionStorage.getItem("addsites");
							linksData = JSON.parse(addsites).concat(data);
						}else{
							linksData = data;
						}
						for(var i=0;i<linksData.length;i++){
							var linksStr = linksData[i];
							var selectStr = $(".search_input").val();
		            		function changeStr(data){
		            			var dataStr = '';
		            			var showNum = data.split(eval("/"+selectStr+"/ig")).length - 1;
		            			if(showNum > 1){
									for (var j=0;j<showNum;j++) {
		            					dataStr += data.split(eval("/"+selectStr+"/ig"))[j] + "<i style='color:#03c339;font-weight:bold;'>" + selectStr + "</i>";
		            				}
		            				dataStr += data.split(eval("/"+selectStr+"/ig"))[showNum];
		            				return dataStr;
		            			}else{
		            				dataStr = data.split(eval("/"+selectStr+"/ig"))[0] + "<i style='color:#03c339;font-weight:bold;'>" + selectStr + "</i>" + data.split(eval("/"+selectStr+"/ig"))[1];
		            				return dataStr;
		            			}
		            		}
		            		//网站名称
		            		if(linksStr.info.indexOf(selectStr) > -1){
			            		linksStr["info"] = changeStr(linksStr.info);
		            		}
		            		
		            		/*
		            		if(linksStr.showAddress.indexOf(selectStr) > -1){
			            		linksStr["showAddress"] = changeStr(linksStr.showAddress);
		            		}
							*/
		            		if(linksStr.info.indexOf(selectStr)>-1){
		            			newArray.push(linksStr);
		            		}
		            	}
		            	linksData = newArray;
		            	linksList(linksData);
					}
				})
            	
                layer.close(index);
            },2000);
		}else{
			layer.msg("请输入需要查询的内容");
		}
	})


  	//查看
	//改变窗口大小时，重置弹窗的高度，防止超出可视区域（如F12调出debug的操作）
$("body").on("click",".view",function(){ 
			var sid=$(this).attr("data-id");
			var index = layui.layer.open({
				title : "日志详情",
				type : 2,
				content : "view.php?sid="+sid,
				success : function(layero, index){
					setTimeout(function(){
						layui.layer.tips('点击此处返回任务列表', '.layui-layer-setwin .layui-layer-close', {
							tips: 3
						});
					},500)
				}
			})			
			layui.layer.full(index);

	})



 	//删除
	//改变窗口大小时，重置弹窗的高度，防止超出可视区域（如F12调出debug的操作）
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
						layer.msg('删除错误，错误信息：'+data.trim(),{time:5000,shade:0.8,icon:5});
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
			    	
			    	+'<td>'+currData[i].data+'</td>'
			    	+'<td style="text-align:left">'+currData[i].info+'</td>'
			    	dataHtml +='<td>'
					+  '<a class="layui-btn layui-btn-xs view" data-id="'+currData[i].id+'"><i class="layui-icon">&#xe615;</i> 查看详情</a>'
					+  '<a class="layui-btn layui-btn-danger layui-btn-xs links_del" data-id="'+currData[i].id+'"><i class="layui-icon">&#xe640;</i> 删除</a>'
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
