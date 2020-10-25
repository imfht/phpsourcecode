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
 *	短信发送日志
 * 
 *	@auth 牧羊人
 *	@date 2018-07-20
 */
layui.use(['laydate','func'],function(){
	var laydate = layui.laydate,
		func = layui.func,
		$ = layui.$;
	
	if(A=='index') {
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				  ,{ field:'mobile', width:130, title: '手机号码', align:'center' }
				  ,{ field:'type_name', width:150, title: '类型', align:'center' }
				  ,{ field:'content', width:400, title: '短信内容', align:'center' }
				  ,{ field:'sign', width:100, title: '短信签名', align:'center' }
				  ,{ field:'status_name', width:80, title: '状态', align:'center', templet:function(d){
		  			  var cls = "";
		  			  if(d.status==1) {
		  				  //成功
		  				  cls = "layui-btn-normal";
		  			  }else if(d.status==2){
		  				  //失败
		  				  cls = "layui-btn-danger";
		  			  }else if(d.status==3){
		  				  //待处理
		  				  cls = "layui-btn-warm";
		  			  }
		  			  return '<span class="layui-btn '+cls+' layui-btn-xs">'+d.status_name+'</span>';
		  		  } }
				  ,{ field:'msg', width:200, title: '短信返回值', align:'center' }
				  ,{ field:'format_add_time', width:180, title: '发送时间', align:'center', sort: true }
				  ,{ fixed:'right', width:130, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【TABLE渲染】
		func.tableIns(cols,"tableList");
		
		//日期选择
		func.initDate(['send_date|date||-'],function(value,date){
			console.log("当前选择日期:"+value);
			console.log("日期详细信息："+JSON.stringify(date));
		});
		
	}
	
});