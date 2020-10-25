/**
 *	邮件模板
 *
 *	@auth 牧羊人
 *	@date 2018-07-16
 */
layui.use(['func'],function(){
	var func = layui.func,
		$ = layui.$;
	
	if(A=='index') {
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				  ,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				  ,{ field:'title', width:200, title: '模板标题', align:'center' }
				  ,{ field:'content', width:500, title: '模板内容', align:'center' }
				  ,{ field:'status', width:100, title: '模板状态', align:'center', templet:function(d){
					  var str = "";
					  if(d.status==1){
						  str = '<span class="layui-btn layui-btn-normal layui-btn-xs">在用</span>';
					  }else{
						  str = '<span class="layui-btn layui-btn-danger layui-btn-xs">停用</span>';
					  }
					  return str;
				  } }
				  ,{ field:'format_add_user', width:120, title: '添加人', align:'center' }
				  ,{ field:'format_add_time', width:180, title: '添加时间', align:'center', sort: true }
				  ,{ field:'format_upd_user', width:120, title: '更新人', align:'center' }
				  ,{ field:'format_upd_time', width:180, title: '更新时间', align:'center', sort: true }
				  ,{ field:'sort_order', width:80, title: '排序', align:'center' }
				  ,{ fixed:'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【TABLE渲染】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("邮件模板",800,600);
		
	}
	
});