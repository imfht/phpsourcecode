/**
 *	标签
 *
 *	@auth 牧羊人
 *	@date 2018-10-19
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
				  ,{ field:'name', width:200, title: '标签名称', align:'center' }
				  ,{ field:'type_name', width:100, title: '标签类型', align:'center' }
				  ,{ field:'status', width:100, title: '状态', align:'center', templet:function(d){
	  	  			  var str = "";
	  	  			  if(d.status==1){
	  	  				  str = '<span class="layui-btn layui-btn-green layui-btn-xs">在用</span>';
	  	  			  }else if(d.status==2){
	  	  				  str = '<span class="layui-btn layui-bg-cyan layui-btn-xs">停用</span>';
	  	  			  }
	  	  			  return str;
	  	  		  }}
				  ,{ field:'sort_order', width:80, title: '排序', align:'center' }
				  ,{ field:'format_add_user', width:100, title: '添加人', align:'center' }
				  ,{ field:'format_add_time', width:180, title: '添加时间', align:'center' }
				  ,{ field:'format_upd_time', width:180, title: '更新时间', align:'center' }
				  ,{ fixed:'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【TABLE渲染】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("标签",450,350);
		
	}
	
});
