/**
 *  优惠券
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
				,{ field:'title', width:200, title: '优惠券标题', align:'center' }
				,{ field:'format_face_value', width:150, title: '优惠券面值/元', align:'center' }
				,{ field:'duration', width:100, title: '有效期', align:'center' }
				,{ field:'format_amount', width:120, title: '满减金额/元', align:'center' }
				,{ field:'exchange_num', width:120, title: '已兑换人数', align:'center' }
				,{ field:'note', width:250, title: '备注', align:'center' }
				,{ field:'status', width:100, title: '状态', align:'center', templet:function(d){
					  var str = "";
					  if(d.status==1){
						  str = '<span class="layui-btn layui-btn-normal layui-btn-xs">在用</span>';
					  }else{
						  str = '<span class="layui-btn layui-btn-normal layui-btn-xs layui-btn-danger">停用</span>';
					  }
					  return str;
				  } }
				,{ field:'format_add_user', width:100, title: '创建人', align:'center' }
				,{ field:'format_add_time', width:180, title: '添加时间', align:'center', sort: true }
				,{ field:'format_upd_time', width:180, title: '更新时间', align:'center', sort: true }
				,{ fixed:'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("优惠券",750,450);
		
	}

});