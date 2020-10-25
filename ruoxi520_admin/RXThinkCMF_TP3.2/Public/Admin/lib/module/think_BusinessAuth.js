/**
 *	商家认证
 *
 *	@auth 牧羊人
 *	@date 2018-10-23
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
				,{ field:'mobile', width:130, title: '用户手机', align:'center' }
				,{ field:'identity_type_name', width:120, title: '认证类型', align:'center' }
				,{ field:'file_uri', width:300, title: '附件路径', align:'center' }
				,{ field:'status_name', width:100, title: '认证状态', align:'center' }
				,{ field:'reason', width:300, title: '审核备注', align:'center' }
				,{ field:'format_add_time', width:180, title: '认证时间', align:'center', sort: true }
				,{ field:'format_upd_time', width:180, title: '审核时间', align:'center', sort: true }
				,{ fixed:'right', width:130, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("商家");
		
	}

});