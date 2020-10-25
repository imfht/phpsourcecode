// +----------------------------------------------------------------------
// | RXThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2019 http://rxthink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 *	职位管理
 *
 *	@auth 牧羊人
 *	@date 2018-05-30
 */
layui.use(['func'],function(){
	
	//声明变量
	var func = layui.func
		,$ = layui.$;
	
	if(A=='index') {
		
		//【TABLE列数组】
		var cols = [
		       { type:'checkbox', fixed: 'left' }
			  ,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
			  ,{ field:'name', width:300, title: '职位名称', align:'center' }
			  ,{ field:'status', width:100, title: '状态', align:'center', templet: '#statusTpl', sort: true, templet:function(d){
			  var str = "";
			  if(d.status==1){
				  str = '<span class="layui-btn layui-btn-normal layui-btn-xs">在用</span>';
			  }else{
				  str = '<span class="layui-btn layui-btn-normal layui-btn-xs layui-btn-danger">停用</span>';
			  }
				  return str;
			  } }
			  ,{ field:'format_add_user', width:100, title: '添加人', align:'center' }
			  ,{ field:'format_add_time', width:180, title: '添加时间', align:'center', sort: true }
			  ,{ field:'format_upd_time', width:180, title: '更新时间', align:'center', sort: true }
			  ,{ field:'sort_order', width:80, title: '排序', align:'center' }
			  ,{ fixed:'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("职位",500,300);
		
	}

});
