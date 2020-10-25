/**
 *	快递公司
 *
 *	@auth 牧羊人
 *	@date 2018-10-18
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
				  ,{ field:'e_name', width:150, title: '快递公司名称', align:'center' }
				  ,{ field:'e_state', width:100, title: '状态', align:'center', templet:function(d){
	  	  			  var str = "";
	  	  			  if(d.e_state==1){
	  	  				  str = '<span class="layui-btn layui-btn-green layui-btn-xs">在用</span>';
	  	  			  }else if(d.e_state==0){
	  	  				  str = '<span class="layui-btn layui-bg-cyan layui-btn-xs">停用</span>';
	  	  			  }
	  	  			  return str;
	  	  		  }}
				  ,{ field:'e_code', width:150, title: '编码', align:'center' }
				  ,{ field:'e_letter', width:80, title: '首字母', align:'center' }
				  ,{ field:'e_order', width:100, title: '使用状态', align:'center', templet:function(d){
	  	  			  var str = "";
	  	  			  if(d.e_order==1){
	  	  				  str = '<span class="layui-btn layui-btn-green layui-btn-xs">常用</span>';
	  	  			  }else if(d.e_order==2){
	  	  				  str = '<span class="layui-btn layui-bg-cyan layui-btn-xs">不常用</span>';
	  	  			  }
	  	  			  return str;
	  	  		  }}
				  ,{ field:'e_url', width:300, title: '公司地址', align:'center' }
				  ,{ field:'format_add_time', width:180, title: '添加时间', align:'center' }
				  ,{ field:'format_upd_time', width:180, title: '更新时间', align:'center' }
				  ,{ fixed:'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【TABLE渲染】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("快递公司",700,350);
		
	}
	
});
