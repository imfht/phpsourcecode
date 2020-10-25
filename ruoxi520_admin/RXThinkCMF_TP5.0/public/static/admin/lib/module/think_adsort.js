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
 *	广告位描述
 *
 *	@auth 牧羊人
 *	@date 2018-07-16
 */
layui.use(['func'],function(){
	var func = layui.func,
		$ = layui.$;
	
	if(A=='index') {
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				  ,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				  ,{ field:'name', width:200, title: '广告位名称', align:'center' }
				  ,{ field:'item_name', width:100, title: '所属站点', align:'center' }
				  ,{ field:'cate_name', width:250, title: '所属栏目', align:'center' }
				  ,{ field:'loc_id', width:100, title: '广告位置', align:'center' }
				  ,{ field:'platform_name', width:100, title: '所属平台', align:'center' }
				  ,{ field:'description', width:250, title: '描述', align:'center' }
				  ,{ field:'format_add_user', width:80, title: '添加人', align:'center' }
				  ,{ field:'format_add_time', width:180, title: '添加时间', align:'center', sort: true }
				  ,{ field:'sort_order', width:80, title: '排序', align:'center' }
				  ,{ fixed:'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【TABLE渲染】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("广告位");
	}

});