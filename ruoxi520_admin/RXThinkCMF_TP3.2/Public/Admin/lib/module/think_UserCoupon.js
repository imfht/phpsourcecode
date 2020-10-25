/**
 *  用户优惠券
 *
 *	@auth 牧羊人
 *	@date 2018-11-28
 */
layui.use(['func','form'],function(){
	
	//【声明变量】
	var func = layui.func
		,$ = layui.$;
	
	if(A=='index') {
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				,{ field:'mobile', width:130, title: '用户手机', align:'center' }
				,{ field:'title', width:200, title: '优惠券标题', align:'center' }
				,{ field:'format_face_value', width:120, title: '优惠券面值/元', align:'center' }
				,{ field:'format_start_time', width:180, title: '有效期开始时间', align:'center' }
				,{ field:'format_end_time', width:180, title: '有效期结束时间', align:'center' }
				,{ field:'format_amount', width:120, title: '满减金额/元', align:'center' }
				,{ field:'status', width:100, title: '使用状态', align:'center', templet:function(d){
					  var str = "";
					  if(d.status==1){
						  str = '<span class="layui-btn layui-btn-normal layui-btn-xs">未使用</span>';
					  }else if(d.status==2){
						  str = '<span class="layui-btn layui-bg-cyan layui-btn-xs">已使用</span>';
					  }else if(d.status==3){
						  str = '<span class="layui-btn layui-btn-xs layui-btn-danger">已过期</span>';
					  }
					  return str;
				  } }
				,{ field:'note', width:250, title: '备注', align:'center' }
				,{ field:'format_add_time', width:180, title: '领券时间', align:'center' }
				,{ field:'format_use_time', width:180, title: '使用时间', align:'center' }
				,{ field:'format_expired_time', width:180, title: '过期时间', align:'center' }
				,{ fixed:'right', width:130, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList");
		
	}

});