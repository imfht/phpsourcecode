// +----------------------------------------------------------------------
// | RXThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2019 http://rxthink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 *	菜单管理
 *
 *	@auth 牧羊人
 *	@date 2018-07-17
 */
layui.use(['func'],function(){
	var func = layui.func,
		$ = layui.$;

	if(A=='index') {
		
		//【TREE列数组】
		var layout =
			[{	name: 'ID',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
		 		style: 'width: 5%;min-width:50px;',
		 		render: function(row) {
		 			return row.id;
		 		}
		 	},
		 	{ 
		 		name: '菜单名称', 
		 		treeNodes: true, 
		 		headerClass: 'value_col2', 
		 		colClass: 'value_col2', 
		 		style: '20%;min-width:150px;',  
		 		render: function(row) {
		 			return row.title;
		 		} 
		 	},
		 	{
		 		name: '图标',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
	        	style: 'width: 5%;min-width:50px;',
	        	render: function(row) {
	        		return '<i class="larry-icon '+row.icon+'" data-icon="'+row.icon+'" data-font="larry-icon"></i>';
	        	}
		 	},
		 	{
		 		name: '类型',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
	        	style: 'width: 5%;min-width:50px;',
	        	render: function(row) {
	        		var cls = "";
	        		if(row.type==1) {
	        			//模块
	            		cls = "layui-btn-normal";
	            	}else if(row.type==2){
	            		//导航
	            		cls = "layui-btn-danger";
	            	}else if(row.type==3){
	            		//菜单
	            		cls = "layui-btn-warm";
	            	}else if(row.type==4){
	            		//节点
	            		cls = "";
	            	}
	        		return '<span class="layui-btn '+cls+' layui-btn-xs">'+row.type_name+'</span>';
	        	}
		 	},
		 	{
		 		name: '菜单URL',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
	        	style: 'width: 10%;min-width:250px;',
	        	render: function(row) {
	        		return row.url ? row.url : '';
	        	}
		 	},
		 	{
		 		name: '权限标识',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
	        	style: 'width: 8%;min-width:150px;',
	        	render: function(row) {
	        		return row.auth ? row.auth : '';
	        	}
		 	},
		 	{
		 		name: '状态',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
		 		style: 'width: 5%;min-width:50px;',
		 		render: function(row) {
		 			return (row.is_show==1 ? '<span class="layui-btn layui-btn-normal layui-btn-xs">显示</span>' : '<span class="layui-btn layui-btn-normal layui-btn-xs layui-btn-danger">不显示</span>');
		 		}
		 	},
		 	{
		 		name: '是否公共',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
		 		style: 'width: 5%;min-width:60px;',
		 		render: function(row) {
		 			return row.is_public==1 ? "是" : "否";
		 		}
		 	},
		 	{
		 		name: '排序',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
		 		style: 'width: 5%;min-width:50px;',
		 		render: function(row) {
		 			return row.sort_order;
		 		}
		 	},
			{
	    	  	name: '操作',	
	    	  	headerClass: 'value_col',
	    	  	colClass: 'value_col2',
	    	  	style: 'width: 15%;min-width:180px;text-align:left;',
	    	  	render: function(row) {
	    	  		
	    	  		var strXml = $("#toolBar").html();
	    	  	    var regExp = /<a.*?>([\s\S]*?)<\/a>/g;
	    	  	    var itemArr = strXml.match(regExp);
	    	  	    if(itemArr) {
	    	  	    	var itemStr = "";
	    	  	    	for(var i=0;i<itemArr.length;i++) {
		    	  			if(i==2 && row.type>3) continue;
		    	  			itemStr += itemArr[i].replace('<a',"<a data-id="+row.id);
		    	  		}
	    	  	    	return itemStr;
	    	  	    }
	    	  		return "";
	    	  		
	    	  	}
			}];
		
		//【TREE渲染】
		func.treeIns(layout,"treeList",false,function(layEvent,id,pid){
			
			if(layEvent==='btnAdd2') {
				//添加
				
				var url = '<div style="padding: 50px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;">菜单管理模块主要是为了实现系统权限架构而设计制作，节点权限的设置分为多种模式可供选择<br><br>我们将为您提供最真挚的服务 ^_^</div>';
				func.showWin("功能节点",url,450,300,null,1,['独立设置', '批量设置','关闭'],function(layero, index){
					//弹框回调
					var btn = layero.find('.layui-layer-btn');
					btn.find('.layui-layer-btn0').click(function(){
						layer.msg("独立设置");
						layui.common.edit("菜单",0,0,0,['pid='+pid]);
					});
					btn.find('.layui-layer-btn1').click(function(){
						layer.msg("批量设置"+pid);
						var url = cUrl + "/batchFunc?menu_id="+pid;
						func.showWin("权限节点",url,500,350);
					});
					
				});
				
			}else if(layEvent==='btnEdit') {
				//编辑
				layui.common.edit("菜单",id,0,0);
			}else if(layEvent==='btnDel') {
				//删除
				layui.common.drop(id,function(data,isSuc){
					console.log("树节点已删除");
				});
			}
			
		});
		
		//【设置弹框】
		func.setWin("菜单");
		
	}else{
		
		/**
		 * 选择图标
		 */
		$(".btnIcon").click(function(){
			var url = cUrl + "/getSysIcon";
			func.showWin("选择系统图标",url);
		});
		
	}
		
});
