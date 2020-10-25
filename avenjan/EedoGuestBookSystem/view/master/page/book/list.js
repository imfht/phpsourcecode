layui.config({
	base : "js/"
}).use(['form','layer','jquery','element','laypage'],function(){
	var form = layui.form,
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		laypage = layui.laypage,
		element = layui.element,
		$ = layui.jquery;
  function getQueryVariable(variable)//获取URL参数
	{
		var query = window.location.search.substring(1);
		var vars = query.split("&");
		for (var i=0;i<vars.length;i++) {
			var pair = vars[i].split("=");
			if(pair[0] == variable){return pair[1];}
		}
		return(false);
	}
//GET var sid=getQueryVariable("sid");

var type=getQueryVariable('type');
if(type==false){
var type='';
};
var url='json.php?type='+type; //定义json地址
	//加载页面数据
	var linksData = '';
	$.ajax({
		url :  url,
		type : "get",
		dataType : "json",
		success : function(data){
			linksData = data;
			//执行加载数据的方法
			linksList();
		}
	})
	//查询
	//回车键查询
	$(".search_input").bind("keydown",function(e){
	　　// 兼容FF和IE和Opera
	　　var theEvent = e || window.event;
	　　var code = theEvent.keyCode || theEvent.which || theEvent.charCode;
	　　 if (code == 13) {
	　　//回车执行查询
	　　$(".search_btn").click();
	　　}
	});
	$(".search_btn").click(function(){
		var newArray = [];
		if($(".search_input").val() != ''){
			var index = layer.msg('查询中，请稍候',{icon: 16,time:false,shade:0.8});
            setTimeout(function(){
            	$.ajax({
					url :  url,
					type : "get",
					dataType : "json",
					success : function(data){
						if(window.sessionStorage.getItem("addLinks")){
							var addLinks = window.sessionStorage.getItem("addLinks");
							linksData = JSON.parse(addLinks).concat(data);
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
		            		if(linksStr.title.indexOf(selectStr) > -1){
			            		linksStr["title"] = changeStr(linksStr.title);
		            		}
		            		if(linksStr.type.indexOf(selectStr) > -1){
			            		linksStr["type"] = changeStr(linksStr.type);
		            		}
		            		if(linksStr.title.indexOf(selectStr)>-1 || linksStr.type.indexOf(selectStr)>-1){
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
  	//修改
	$("body").on("click",".chick_edit",function(){ 
			var sid=$(this).attr("data-id");
			var index = layui.layer.open({
				title : "编辑留言",
				type : 2,
				content : "edit.php?sid="+sid,
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
	$("body").on("click",".chick_del",function(){ 
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
			    	//+'<td><input type="checkbox" name="checked" lay-skin="primary" lay-filter="choose" value="'+currData[i].id+'"></td>'
			    	+'<td>'+currData[i].date+'</td>'
			    	+'<td>'+currData[i].title+'</td>'
			    	+'<td>'+currData[i].count+'</td>'
			    	+'<td>'+currData[i].type+'</td>';
			    	if(currData[i].view == "1"){
			    		dataHtml +='<td>已审核</td>';
			    	}else{
			    	dataHtml +='<td>未审核</td>';
			    	}
			    	dataHtml +='<td>'
					+  '<a class="layui-btn layui-btn-xs chick_edit "  data-id="'+currData[i].id+'"><i class="layui-icon">&#xe642;</i></a>'
					+  '<a class="layui-btn layui-btn-danger layui-btn-xs chick_del" data-id="'+currData[i].id+'"><i class="layui-icon">&#xe640;</i> </a>'
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
				$(".chick_content").html(renderDate(linksData,obj.curr,obj.limit));
				$('.links_list thead input[type="checkbox"]').prop("checked",false);
		    	form.render();
			}
		})
	}
})
