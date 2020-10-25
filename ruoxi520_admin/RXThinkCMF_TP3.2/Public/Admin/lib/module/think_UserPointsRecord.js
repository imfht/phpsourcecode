/**
 *	用户积分明细
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
				,{ field:'mobile', width:130, title: '充值用户', align:'center' }
				,{ field:'before_ponits', width:130, title: '变动前积分', align:'center' }
				,{ field:'change_points', width:130, title: '本次变动积分', align:'center' }
				,{ field:'after_points', width:130, title: '变动后积分', align:'center' }
				,{ field:'type', width:100, title: '类型', align:'center', templet:function(d){
	  	  			  var str = "";
	  	  			  if(d.type==1){
	  	  				  str = '<span class="layui-btn layui-btn-green layui-btn-xs">获取积分</span>';
	  	  			  }else if(d.type==2){
	  	  				  str = '<span class="layui-btn layui-bg-cyan layui-btn-xs">消费积分</span>';
	  	  			  }
	  	  			  return str;
	  	  		  }}
				
				,{ field:'points_type_name', width:150, title: '积分类型', align:'center' }
				,{ field:'format_add_time', width:180, title: '变动时间', align:'center', sort: true }
				,{ fixed:'right', width:130, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList");
		
	}

});