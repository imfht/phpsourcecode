/**
 *	会员分组
 *
 *	@auth 牧羊人
 *	@date 2018-08-17
 */
layui.use(['func'],function(){
	
	//【声明变量】
	var func = layui.func
		,$ = layui.$;
	
	if(A=='index') {
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				,{ field:'name', width:200, title: '分组名称', align:'center' }
				,{ field:'sort_order', width:120, title: '排序', align:'center' }
				,{ field:'format_add_user', width:100, title: '创建人', align:'center' }
				,{ field:'format_add_time', width:200, title: '添加时间', align:'center', sort: true }
				,{ field:'format_upd_time', width:200, title: '更新时间', align:'center', sort: true }
				,{ fixed: 'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("会员分组",450,250);
	}
	
});
