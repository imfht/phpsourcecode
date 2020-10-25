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
 *	部门管理
 *
 *	@auth 牧羊人
 *	@date 2018-07-18
 */
layui.use(['func'],function(){
	var func = layui.func,
		common = layui.common,
		$ = layui.$;
	
	if(A=='index') {
		
		//【TREE列数组】
		var layout =
			[{	name: 'ID',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
		 		style: 'width: 5%',
		 		render: function(row) {
		 			return row.id;
		 		}
		 	},
		 	{ 
		 		name: '部门名称', 
		 		treeNodes: true, 
		 		headerClass: 'value_col2', 
		 		colClass: 'value_col2', 
		 		style: '15%;min-width:200px;',  
		 		render: function(row) {
		 			return row.name;
		 		} 
		 	},
		 	{
		 		name: '添加人',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
		 		style: 'width: 10%;min-width:80px;',
		 		render: function(row) {
		 			return row.format_add_user;
		 		}
		 	},
		 	{
		 		name: '添加时间',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
		 		style: 'width: 10%;min-width:150px;',
		 		render: function(row) {
		 			return row.format_add_time;
		 		}
		 	},
		 	{
		 		name: '更新时间',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
		 		style: 'width: 10%;min-width:150px;',
		 		render: function(row) {
		 			return row.format_upd_time ? row.format_upd_time : '';
		 		}
		 	},
		 	{
		 		name: '排序',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
		 		style: 'width: 10%;min-width:50px;',
		 		render: function(row) {
		 			return row.sort_order;
		 		}
		 	},
			{
	    	  	name: '操作',	
	    	  	headerClass: 'value_col',
	    	  	colClass: 'value_col2',
	    	  	style: 'width: 20%;min-width:180px;text-align:left;',
	    	  	render: function(row) {
	    	  		var strXml = $("#toolBar").html();
	    	  	    var regExp = /<a.*?>([\s\S]*?)<\/a>/g;
	    	  	    //exec返回一个数组对象
	    	  	    var arr = strXml.match(regExp);
//	    	  	    console.log(RegExp.$1);
//	    	  		console.log(arr);
//	    	  		console.log(arr.length);
	    	  	    
	    	  	    var itemStr = '';
	    	  	    if(arr!=null) {
	    	  	    	for(var i=0;i<arr.length;i++) {
		    	  			if(i==3 && row.parent_id!=0) continue;
		    	  			itemStr += arr[i].replace('<a',"<a data-id="+row.id);
		    	  		}
	    	  	    }
	    	  		return itemStr;
	    	  	}
			}];
		
		//【TREE渲染】
		func.treeIns(layout,"treeList",true,function(layEvent,id,pid){
			if(layEvent==='btnAdd2') {
				console.log("添加");
				common.edit("部门",id,500,300);
			}else if(layEvent==='btnEdit') {
				console.log("编辑");
				common.edit("部门",id,500,300);
			}else if(layEvent==='btnDel') {
				console.log("删除");
				common.drop(id,function(id,isSuc){
					console.log("树节点已删除");
				});
			}else if(layEvent==='btnSetAuth') {
				console.log("部门权限设置");
				location.href = mUrl + "/adminAuth/index?type=4&type_id="+id;
			}
		});
		
		//【设置弹框】
		func.setWin("部门",500,300);
		
	}
	
});
