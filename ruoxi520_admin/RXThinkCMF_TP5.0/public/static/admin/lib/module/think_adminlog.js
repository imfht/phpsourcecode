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
 *	登录日志
 *
 *	@auth 牧羊人
 *	@date 2018-07-16
 */
layui.use(['func'],function(){
	var func = layui.func,
		$ = layui.$;
	
	//【TABLE列数组】
	var cols = [
			{ type:'checkbox', fixed: 'left' }
			  ,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
			  ,{ field:'title', width:120, title: '日志标题', align:'center' }
			  ,{ field:'content', width:500, title: '日志内容', align:'center' }
			  ,{ field:'login_ip', width:150, title: 'IP地址', align:'center' }
			  ,{ field:'city_name', width:200, title: '登录城市', align:'center' }
			  ,{ field:'format_add_user', width:100, title: '创建人', align:'center' }
			  ,{ field:'format_add_time', width:180, title: '添加时间', align:'center', sort: true }
			  ,{ fixed: 'right', width:100, title: '功能操作', align:'center', toolbar: '#toolBar' }
		];
	
	//【TABLE渲染】
	func.tableIns(cols,"tableList");
	
});