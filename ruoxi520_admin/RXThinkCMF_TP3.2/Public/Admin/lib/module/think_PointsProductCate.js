/**
 *	品牌
 *
 *	@auth 牧羊人
 *	@date 2018-10-08
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
				  ,{ field:'name', width:200, title: '分类名称', align:'center' }
				  ,{ field:'icon_url', width:120, title: '分类图片', align:'center', templet:function(d){
					  var iconStr = "";
			 			if(d.icon_url) {
			 				iconStr = '<a href="'+d.icon_url+'" target="_blank"><img src="'+d.icon_url+'" height="26" /></a>';
			 			}
			 			return iconStr;
		          }}
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
				  ,{ field:'format_add_time', width:180, title: '添加时间', align:'center' }
				  ,{ field:'format_upd_time', width:180, title: '更新时间', align:'center' }
				  ,{ fixed:'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【TABLE渲染】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("积分商城分类",750,550);
		
	}
	
});
