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
 *	城市管理
 *
 *	@auth 牧羊人
 *	@date 2018-07-17
 */
layui.use(['func','common'],function(){
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
		 		name: '城市名称', 
		 		treeNodes: true, 
		 		headerClass: 'value_col2', 
		 		colClass: 'value_col2', 
		 		style: '20%;min-width:200px;',  
		 		render: function(row) {
		 			return row.name;
		 		} 
		 	},
		 	{
		 		name: '城市编码(区号)',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
	        	style: 'width: 15%;min-width:100px;',
	        	render: function(row) {
	        		return row.citycode;
	        	}
		 	},
		 	{
		 		name: '级别',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
		 		style: 'width: 10%;min-width:60px;',
		 		render: function(row) {
		 			return '<span class="layui-btn layui-btn-xs layui-badge layui-bg-cyan">'+row.level+'</span>';
		 		}
		 	},
		 	{
		 		name: '是否开放',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
		 		style: 'width: 10%;min-width:80px;',
		 		render: function(row) {
		 			return row.is_public==1 ? "是" : "否";
		 		}
		 	},
		 	{
		 		name: '排序',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
		 		style: 'width: 10%;min-width:60px;',
		 		render: function(row) {
		 			return row.sort_order;
		 		}
		 	},
			{
	    	  	name: '操作',	
	    	  	headerClass: 'value_col',
	    	  	colClass: 'value_col2',
	    	  	style: 'width: 15%;min-width:180px;',
	    	  	render: function(row) {
	    	  		
	    	  		var strXml = $("#toolBar").html();
	    	  	    var regExp = /<a.*?>([\s\S]*?)<\/a>/g;
	    	  	    var itemArr = strXml.match(regExp);
	    	  	    if(itemArr) {
	    	  	    	var itemStr = "";
	    	  	    	for(var i=0;i<itemArr.length;i++) {
		    	  			if(i==2 && row.level>2) continue;
		    	  			itemStr += itemArr[i].replace('<a',"<a data-id="+row.id);
		    	  		}
	    	  	    	return itemStr;
	    	  	    }
	    	  		return "";
	    	  		
	    	  	}
			}];
		
		//【TREE渲染】
		func.treeIns(layout,"treeList");
		
		//【设置弹框】
		func.setWin("城市",500,400);
	}

});
