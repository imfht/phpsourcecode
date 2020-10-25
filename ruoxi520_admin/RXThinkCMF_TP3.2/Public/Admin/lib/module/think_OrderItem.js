/**
 *	订单产品
 *
 *	@auth 牧羊人
 *	@date 2018-10-08
 */
layui.use(['form','func'],function(){
	var form = layui.form,
		func = layui.func,
		$ = layui.$;
	
	if(A=='index') {
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				  ,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				  ,{ field:'name', width:200, title: '产品名称', align:'center' }
				  ,{ field:'format_price', width:100, title: '产品单价', align:'center' }
				  ,{ field:'num', width:100, title: '产品份数', align:'center' }
				  ,{ field:'format_total_price', width:100, title: '产品总价', align:'center' }
				  ,{ field:'format_add_time', width:200, title: '添加时间', align:'center', sort: true }
				  ,{ fixed:'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【TABLE渲染】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("订单",700,600);
		
	}
	
});
