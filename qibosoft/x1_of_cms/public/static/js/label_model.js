var loadAllLabel = [];
var have_label_model_num = 0;
function load_all_label(o){
	loadAllLabel.push(o);
}

load_all_label(function(){
	if(typeof(showlabel_)=='function'){
		//拖动 
		$(".diy_pages").each(function(){
			var that = $(this);
			that.find(".headle").css("cursor","move");
			that.sortable({
				handle:".headle",
				//revert: true,
				axis: 'y', 
				items: '.c_diypage', 
				stop:function(){
					save_labelmodel(that) 
				}
			});
		});	
	}
});


function label_model_init(file,tags,hyid,ids){
	var that = $(".diyKey-"+file+"-"+tags+"[data-tags='"+tags+"']");

	if(label_model_synchronize){
		init_model(that);
	}else{
		var url = label_model_url + "?id=" + hyid + "&path=" + file + "&tags="+tags + "&ids="+ids;
		$.get(url,function(res){
			if(res.code==0){
				that.append(res.data.content);	//标签内容赋值到页面
				
				have_label_model_num++;
				$(function(){
					if(label_model_num==have_label_model_num){
						for (var i=0;i<loadAllLabel.length;i++){
							loadAllLabel[i]();
						}
					}
				});

				init_model(that);
			}
		});
	}

	function init_model(that){
			if(typeof(admin_url)!=='undefined'){
				if(that.height()==that.find(".headle").height()){
					that.css("min-height","80px");
				}
			}
			
			
			init_margin(that);	//初始化边距

			that.find('.headle .copy').click(function(){
				if(that.find(".taglabel").length>0){
					layer.alert('该标签是qb:tag标签不可复制，只有qb:hy标签才能复制，如有需要，请联系开发者升级为qb:hy标签才能复制');
					return false;
				}
				that.after( that.clone(true) );
				that.next().data("tags", '9'+(new Date().getTime()).toString().substring(9,13) );
				that.next().find(".p8label").prop("onclick",null);
				that.next().find(".p8label").click(function(){
					layer.confirm("复制模块化,需要刷新网页才能重新设置标签",{btn:['刷新','取消']},function(index){
						layer.close(index);
						window.location.reload();
					});
				});
				save_labelmodel( that.parent() );
			});

			that.find('.headle .delete').click(function(){
				layer.confirm("你确认要删除吗?",{btn:['删除','取消']},function(index){
					layer.close(index);
					var parent = that.parent();
					that.remove();	//删除当前节点
					save_labelmodel( parent );
				});
			});

			that.find('.headle .down').click(function(){
				if(that.next().hasClass("c_diypage")){
					that.next().after( that.clone(true) );
					var temp = that.next().next();
					that.remove();	//删除当前节点
					that = temp;	//重新获取节点
					save_labelmodel( that.parent() );
				}else{
					layer.alert("已经到尽头了!");					
				}							
			});

			that.find('.headle .up').click(function(){
				if(that.prev().hasClass("c_diypage")){
					that.prev().before( that.clone(true) );
					var temp = that.prev().prev();
					that.remove();	//删除当前节点
					that = temp;	//重新获取节点
					save_labelmodel( that.parent() );
				}else{
					layer.alert("已经到尽头了!");
				}							
			});

			that.find('.headle .margin-size').click(function(){
				layer.confirm("请选择要设置的边距?",{
					btn:['<i class="glyphicon glyphicon-arrow-up"></i>上','<i class="glyphicon glyphicon-arrow-down"></i>下','<i class="glyphicon glyphicon-arrow-left"></i>左','右<i class="glyphicon glyphicon-arrow-right"></i>'],
					btn1:function(index){
						layer.close(index);
						set_size(that,'top');
					},
					btn2:function(index){
						layer.close(index); 
						set_size(that,'bottom');
					},
					btn3:function(index){
						layer.close(index); 
						set_size(that,'left');
					},
					btn4:function(index){
						layer.close(index);
						set_size(that,'right');
					},
				});						
			});
	}

	function init_margin(that){
		var top = that.data("top");
		var bottom = that.data("bottom");
		var left = that.data("left");
		var right = that.data("right");
		if(top!=0){
			that.css({'margin-top':top+"px"});
		}
		if(bottom!=0){
			that.css({'margin-bottom':bottom+"px"});
		}
		if(left!=0){
			that.css({'margin-left':left+"px"});
		}
		if(right!=0){
			that.css({'margin-right':right+"px"});
		}
	}

	//定义边距
	function set_size(that,type){
		layer.prompt({
			title: '请输入边距像素',
			value:that.data(type),
			formType: 0
		}, function(value){
			if (isNaN(value)) {
				layer.alert("请输入数字！");
				return;
			}
			layer.closeAll();
			that.data(type,value);
			that.css('margin-'+type,value+"px");
			save_labelmodel( that.parent() );
		 });
	}

}


//保存设置
function save_labelmodel(obj){
		var label_model_tagname = obj.data("tagname");
		var label_model_pagename = obj.data("pagename");
		var label_model_id = obj.data("id");
		var arr = [];
		obj.find(".c_diypage").each(function(){
			var tags = $(this).data("tags");	//同名标签编号
			var path = $(this).data("path");	//模块路径
			//边距
			var top = $(this).data("top");
			var bottom = $(this).data("bottom");
			var left = $(this).data("left");
			var right = $(this).data("right");
			arr.push({
				path:path,
				tags:tags,
				top:top,
				bottom:bottom,
				left:left,
				right:right,
			});
		});
		$.post(label_model_saveurl,{model:arr,tagname:label_model_tagname,id:label_model_id,pagename:label_model_pagename},function(res){
			if(res.code==0){
				layer.msg("设置成功",{time:800});
			}else{
				layer.alert("设置失败,"+res.msg);
			}
		});
}

