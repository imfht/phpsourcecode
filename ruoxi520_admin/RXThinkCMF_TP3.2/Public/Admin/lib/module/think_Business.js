/**
 *	商家
 *
 *	@auth 牧羊人
 *	@date 2018-10-16
 */
layui.use(['func'],function(){
	
	//【声明变量】
	var func = layui.func
		,$ = layui.$;
	
	if(A=='index') {
		
		var check_status = $("#check_status").val();
		var option = {};
		if(check_status==1) {
			//待认证
			option = { fixed:'right', width:280, title: '功能操作', align:'center', toolbar: '#toolBar' };
		}else if(check_status==2) {
			//已认证
			option = { fixed:'right', width:130, title: '功能操作', align:'center', toolbar: '#toolBar' };
		}else if(check_status==3) {
			//未通过
			option = { fixed:'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' };
		}else{
			//全部
			option = { fixed:'right', width:130, title: '功能操作', align:'center', toolbar: '#toolBar' };
		}
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				,{ field:'realname', width:100, title: '商家名称', align:'center' }
				,{ field:'mobile', width:130, title: '商家手机号', align:'center' }
				,{ field:'company_name', width:200, title: '公司名称', align:'center' }
				,{ field:'logo_url', width:80, title: 'LOGO', align:'center', templet:function(d){
					  var logoUrl = "";
			 			if(d.logo_url) {
			 				logoUrl = '<a href="'+d.logo_url+'" target="_blank"><img src="'+d.logo_url+'" height="26" /></a>';
			 			}
			 			return logoUrl;
		          }}
				,{ field:'taxpayer_number', width:200, title: '纳税人识别号', align:'center' }
				,{ field:'city_name', width:200, title: '商家所在城市', align:'center' }
				,{ field:'check_status_name', width:100, title: '审核状态', align:'center', templet:function(d){
					  if(d.check_status==2) {
						  return '<span style="color: #009688;">'+d.check_status_name+'</span>';
					  }if(d.check_status==3) {
						  return '<span style="color: #F581B1;">'+d.check_status_name+'</span>';
					  }else{
						  return d.check_status_name;
					  }
				  } }
				,{ field:'format_balance', width:100, title: '待结算余额', align:'center' }
				,{ field:'format_add_time', width:180, title: '申请时间', align:'center', sort: true }
				,{ field:'format_upd_time', width:180, title: '更新时间', align:'center', sort: true }
				,option
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList",function(layEvent, data){
			if(layEvent==='checkStatus') {
				//商家审核
				if(data.check_status==2) {
					layer.msg("申请资料已审核通过,无需重复审理");
					return false;
				}
				var url = cUrl+"/checkStatus";
				func.showWin("商家审核",url,650,350,['id='+data.id]);
			}
		},cUrl+"/index?check_status="+check_status);
		
		//【设置弹框】
		func.setWin("商家");
		
	}

});