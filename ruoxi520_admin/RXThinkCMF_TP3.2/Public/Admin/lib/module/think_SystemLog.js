/**
 *	系统日志
 *
 *	@auth 牧羊人
 *	@date 2018-07-18
 */
layui.use(['func'],function(){
	var func = layui.func,
		$ = layui.$;
	
	//【TABLE列数组】
	var cols = [
			   { type:'checkbox', fixed: 'left' }
			  ,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
			  ,{ field:'title', width:180, title: '日志标题', align:'center' }
			  ,{ field:'type_name', width:120, title: '日志类型', align:'center' }
			  ,{ field:'method', width:120, title: '请求方式', align:'center' }
			  ,{ field:'format_start_time', width:180, title: '请求时间', align:'center', sort: true }
			  ,{ field:'spend_time', width:120, title: '请求耗时', align:'center', sort: true }
			  ,{ field:'url', width:250, title: '请求地址', align:'center', sort: true }
			  ,{ field:'ip', width:150, title: 'IP地址', align:'center', sort: true }
			  ,{ field:'format_add_user', width:100, title: '操作人', align:'center' }
			  ,{ field:'format_add_time', width:180, title: '记录时间', align:'center', sort: true }
			  ,{ fixed:'right', width:230, title: '功能操作', align:'center', toolbar: '#toolBar' }
		];
	
	//【TABLE渲染】
	func.tableIns(cols,"tableList",function(layEvent,data){
		
		if(layEvent==='detail') {
			layer.msg("查看日志详情");
			detail(data.id);
		}
		
	});
	
	//【系统日志详情】
	function detail(id){
		var url = cUrl + "/detail?id="+id;
		func.showWin("系统日志详情",url);
    };
	
});