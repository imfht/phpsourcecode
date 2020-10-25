/**
 *	属性值
 *
 *	@auth 牧羊人
 *	@date 2018-10-16
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
				,{ field:'attribute_value', width:200, title: '商品属性值', align:'center' }
				,{ field:'category_attribute_name', width:150, title: '属性名称', align:'center' }
				,{ field:'category_name', width:120, title: '所属分类', align:'center' }
				,{ field:'status', width:100, title: '状态', align:'center', templet:function(d){
	  	  			  var str = "";
	  	  			  if(d.status==1){
	  	  				  str = '<span class="layui-btn layui-btn-green layui-btn-xs">在用</span>';
	  	  			  }else if(d.status==2){
	  	  				  str = '<span class="layui-btn layui-bg-cyan layui-btn-xs">停用</span>';
	  	  			  }
	  	  			  return str;
	  	  		  }}
				,{ field:'sort_order', width:100, title: '排序', align:'center' }
				,{ field:'format_add_user', width:100, title: '创建人', align:'center' }
				,{ field:'format_add_time', width:180, title: '添加时间', align:'center', sort: true }
				,{ field:'format_upd_time', width:180, title: '更新时间', align:'center', sort: true }
				,{ fixed:'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("属性值",450,370);
		
	}

});