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
 *	布局描述管理
 *
 *	@auth 牧羊人
 *	@date 2018-07-17
 */
layui.use(['func'],function(){
	var func = layui.func,
		$ = layui.$;
	
	if(A=='index') {
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				  ,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				  ,{ field:'page_name', width:150, title: '页面编号', align:'center' }
				  ,{ field:'loc_id', width:150, title: '页面位置编号', align:'center' }
				  ,{ field:'loc_desc', width:250, title: '页面位置描述', align:'center' }
				  ,{ field:'format_add_user', width:100, title: '创建人', align:'center' }
				  ,{ field:'format_add_time', width:180, title: '添加时间', align:'center', sort: true }
				  ,{ field:'format_upd_time', width:180, title: '更新时间', align:'center', sort: true }
				  ,{ field:'sort_order', width:100, title: '排序', align:'center' }
				  ,{ fixed: 'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【TABLE渲染】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("布局描述",470,350);
	}
	
});