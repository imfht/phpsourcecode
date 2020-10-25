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
 *	栏目管理
 *
 *	@auth 牧羊人
 *	@date 2018-07-16
 */
layui.use(['form','func','common'],function(){
	var form = layui.form,
		func = layui.func,
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
		 		name: '栏目名称', 
		 		treeNodes: true, 
		 		headerClass: 'value_col2', 
		 		colClass: 'value_col2', 
		 		style: '15%;min-width:200px;',  
		 		render: function(row) {
		 			return row.name;
		 		} 
		 	},
		 	{
		 		name: '图片',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
		 		style: 'width:30px;min-width:50px;',
		 		render: function(row) {
		 			var coverStr = "";
		 			if(row.cover_url) {
		 				coverStr = '<a href="'+row.cover_url+'" target="_blank"><img src="'+row.cover_url+'" height="26" /></a>';
		 			}
		 			return coverStr;
		 		}
		 	},
		 	{
		 		name: '站点名称',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
		 		style: 'width: 10%;min-width:100px;',
		 		render: function(row) {
		 			return row.item_name;
		 		}
		 	},
		 	{
		 		name: '拼音',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
		 		style: 'width: 10%;min-width:100px;',
		 		render: function(row) {
		 			return row.pinyin;
		 		}
		 	},
		 	{
		 		name: '简拼',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
		 		style: 'width: 10%;min-width:60px;',
		 		render: function(row) {
		 			return row.code;
		 		}
		 	},
		 	{
		 		name: '备注',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
		 		style: 'width: 10%;min-width:200px;',
		 		render: function(row) {
		 			return row.note;
		 		}
		 	},
		 	{
		 		name: '添加人',
		 		headerClass: 'value_col',
		 		colClass: 'value_col',
		 		style: 'width: 5%;min-width:80px;',
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
		    	  			if(i==2 && row.parent_id!=0) continue;
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
		func.setWin("栏目");
		
	}else{
		
		//【监听有无封面】
		form.on('switch(is_cover)', function(obj){
			var isSel = obj.elem.checked;
			if(isSel) {
				$(".cover").removeClass("layui-hide");
			}else{
				$(".cover").addClass("layui-hide");
			}
		});
		
	}

});
