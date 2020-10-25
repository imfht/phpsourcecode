/**
 *	发票管理
 *
 *	@auth 牧羊人
 *	@date 2018-10-18
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
				,{ field:'mobile', width:130, title: '用户手机号', align:'center' }
				,{ field:'invoice_head', width:230, title: '发票抬头', align:'center' }
				,{ field:'taxpayer_number', width:200, title: '纳税人识别号', align:'center' }
				,{ field:'type', width:80, title: '类型', align:'center', templet:function(d){
	  	  			  var str = "";
	  	  			  if(d.type==1){
	  	  				  str = '<span class="layui-btn layui-btn-green layui-btn-xs">普票</span>';
	  	  			  }else if(d.type==2){
	  	  				  str = '<span class="layui-btn layui-bg-cyan layui-btn-xs">专票</span>';
	  	  			  }
	  	  			  return str;
	  	  		  }}
				,{ field:'register_address', width:250, title: '注册地址', align:'center' }
				,{ field:'register_tel', width:130, title: '注册电话', align:'center' }
				,{ field:'deposit_bank', width:150, title: '开户银行', align:'center' }
				,{ field:'deposit_account', width:200, title: '开户账号', align:'center' }
				,{ field:'status', width:80, title: '状态', align:'center', templet:function(d){
	  	  			  var str = "";
	  	  			  if(d.status==1){
	  	  				  str = '<span class="layui-btn layui-btn-green layui-btn-xs">在用</span>';
	  	  			  }else if(d.status==2){
	  	  				  str = '<span class="layui-btn layui-bg-cyan layui-btn-xs">停用</span>';
	  	  			  }
	  	  			  return str;
	  	  		  }}
				,{ field:'format_add_user', width:100, title: '创建人', align:'center' }
				,{ field:'format_add_time', width:180, title: '添加时间', align:'center', sort: true }
				,{ field:'format_upd_time', width:180, title: '更新时间', align:'center', sort: true }
				,{ fixed:'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("发票");
		
	}

});