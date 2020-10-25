/**
 *	商家结算申请单
 *
 *	@auth 牧羊人
 *	@date 2018-10-24
 */
layui.use(['func'],function(){
	
	//【声明变量】
	var func = layui.func
		,$ = layui.$;
	
	if(A=='index') {
		
		//订单状态
		var status = parseInt($("#status").val());
		var option = {};
		if(status==1) {
			//待审核
			option = { fixed:'right', width:280, title: '功能操作', align:'center', toolbar: '#toolBar' };
		}else if(status==2) {
			//已审核
			option = { fixed:'right', width:130, title: '功能操作', align:'center', toolbar: '#toolBar' };
		}else if(status==3) {
			//审核未通过
			option = { fixed:'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' };
		}else if(status==4) {
			//已结算
			option = { fixed:'right', width:130, title: '功能操作', align:'center', toolbar: '#toolBar' };
		}else{
			//全部
			option = { fixed:'right', width:130, title: '功能操作', align:'center', toolbar: '#toolBar' };
		}
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				,{ field:'invoice_no', width:200, title: '增值税发票号', align:'center' }
				,{ field:'format_amount', width:150, title: '结算总额/元', align:'center' }
				,{ field:'taxpayer_number', width:200, title: '纳税人识别号', align:'center' }
				,{ field:'mobile', width:130, title: '用户手机号', align:'center' }
				,{ field:'status_name', width:100, title: '订单状态', align:'center' }
				,{ field:'format_add_time', width:180, title: '申请时间', align:'center' }
				,{ field:'invoice_bank', width:100, title: '开户银行', align:'center' }
				,{ field:'invoice_bank_account', width:170, title: '开户账号', align:'center' }
				,option
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList",function(layEvent,data){
			if(layEvent==='confirmOrder') {
				//订单审核
				
				if(data.status!=1) {
					layer.msg("申请单不在待审核状态,无法操作");
					return false;
				}
				
				var url = cUrl + "/confirmOrder?id="+data.id;
				func.showWin("结算申请单审核",url,650,450);
			}
		},cUrl+"/index?status="+status);
		
		//【设置弹框】
		func.setWin("商家结算申请单");
		
	}

});