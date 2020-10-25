/**
 *	版本更新管理
 *
 *	@auth 牧羊人
 *	@date 2018-07-16
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
				,{ field:'version_num', width:100, title: '版本号', align:'center' }
				,{ field:'update_version', width:150, title: '待更新版本号', align:'center' }
				,{ field:'version_type_name', width:150, title: '版本类型', align:'center' }
				,{ field:'type', width:120, title: '设备类型', align:'center', templet:function(d){
					var str = "";
					if(d.type==1){
						str = '<span class="layui-btn layui-btn-normal layui-btn-xs">苹果</span>';
					}else if(d.type==2){
						str = '<span class="layui-btn layui-btn-normal layui-btn-xs layui-btn-danger">安卓</span>';
					}
					return str;
				  } }
				,{ field:'is_update', width:100, title: '是否更新', align:'center', templet:function(d){
					var str = "";
					if(d.is_update==1){
						str = '<span class="layui-btn layui-btn-normal layui-btn-xs">是</span>';
					}else if(d.is_update==2){
						str = '<span class="layui-btn layui-btn-normal layui-btn-xs layui-btn-danger">否</span>';
					}
					return str;
				} }
				,{ field:'is_force', width:100, title: '强制更新', align:'center', templet:function(d){
					var str = "";
					if(d.is_force==1){
						str = '<span class="layui-btn layui-btn-normal layui-btn-xs">是</span>';
					}else if(d.is_force==2){
						str = '<span class="layui-btn layui-btn-normal layui-btn-xs layui-btn-danger">否</span>';
					}
					return str;
				} }
				,{ field:'format_add_user', width:100, title: '创建人', align:'center' }
				,{ field:'format_add_time', width:200, title: '添加时间', align:'center', sort: true }
				,{ field:'format_upd_time', width:200, title: '更新时间', align:'center', sort: true }
				,{ fixed: 'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("版本",750,550);
	}
	
});
