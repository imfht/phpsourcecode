/**
 *	字典管理
 *
 *	@auth 牧羊人
 *	@date 2018-07-20
 */
layui.use(['func'],function(){
	var func = layui.func,
		$ = layui.$;
	
	if(A=='index') {
		
		//【TABLE列数组】
		var cols = [
		           { field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				  ,{ field:'title', width:200, title: '字典标题', align:'center',unresize: true }
				  ,{ field:'tag', width:250, title: '内部标签', align:'center'}
				  ,{ field:'type_name', width:120, title: '字典类型', align:'center' }
				  ,{ field:'content', width:500, title: '字典内容', align:'left' }
				  ,{ field:'status', width:120, title: '字典状态', align:'center',  sort: true, templet: function(d){
					  var str = "";
					  if(d.status==1){
						  str = '<span class="layui-btn layui-btn-normal layui-btn-xs">在用</span>';
					  }else{
						  str = '<span class="layui-btn layui-btn-normal layui-btn-xs layui-btn-danger">停用</span>';
					  }
					  return str;
				  } }
				  ,{ field:'sort_order', width:80, title: '排序', align:'center' }
				  ,{ fixed: 'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【TABLE渲染】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("字典",700,500);
		
	}

});