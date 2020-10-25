/**
 *	钱包流水
 *
 *	@auth 牧羊人
 *	@date 2019-01-08
 */
layui.use(['func','form'],function(){
	var form = layui.form,
		func = layui.func,
		$ = layui.$;
	
	if(A=='index') {
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				,{ field:'mobile', width:130, title: '手机号码', align:'center' }
	            ,{ field:'format_before_balance', width:150, title: '账户变动前金额/元', align:'center' }
	            ,{ field:'format_postal_amount', width:150, title: '变动金额/元', align:'center' }
	            ,{ field:'format_after_balance', width:150, title: '账户变动后金额/元', align:'center' }
	            ,{ field:'format_freeze_amount', width:150, title: '结算冻结金额/元', align:'center' }
	            ,{ field:'status_name', width:100, title: '处理状态', align:'center' }
	            ,{ field:'remark', width:300, title: '备注', align:'center' }
				,{ field:'format_add_time', width:180, title:'创建时间', align:'center' }
				,{ field:'format_upd_time', width:180, title:'更新时间', align:'center' }
				,{ fixed: 'right', width:130, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【TABLE渲染】
		func.tableIns(cols,"tableList");
		
	}
	
});