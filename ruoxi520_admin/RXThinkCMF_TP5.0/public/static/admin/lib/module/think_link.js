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
 *	友链管理
 *
 *	@auth 牧羊人
 *	@date 2018-07-17
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
				  ,{ field:'logo_url', width:80, title: 'LOGO', align:'center', templet:function(d){
						var coverStr = "";
						if(d.logo_url) {
							coverStr = '<a href="'+d.logo_url+'" target="_blank"><img src="'+d.logo_url+'" height="26" /></a>';
						}
						return coverStr;
		          }}
				  ,{ field:'name', width:150, title: '友链名称', align:'center', event: 'setSign', style:'cursor: pointer;' }
				  ,{ field:'url', width:250, title: 'URL地址', align:'center' }
				  ,{ field:'category', width:150, title: '分类', align:'center', templet:function(d){
					  return d.category==1 ? "友情链接" : (d.category==2 ? "合作伙伴" : "");
				  } }
				  ,{ field:'platform_name', width:150, title: '所属平台', align:'center' }
				  ,{ field:'t_type_name', width:100, title: '类型', align:'center' }
				  ,{ field:'is_show', width:100, title: '是否显示', align:'center',templet:function(d){
					  var str = "";
					  if(d.is_show==1){
						  str = '<span class="layui-btn layui-btn-normal layui-btn-xs">显示</span>';
					  }else if(d.is_show==2){
						  str = '<span class="layui-btn layui-btn-normal layui-btn-xs layui-btn-danger">隐藏</span>';
					  }
					  return str;
				  } }
				  ,{ field:'format_add_user', width:120, title: '添加人', align:'center' }
				  ,{ field:'format_add_time', width:200, title: '添加时间', align:'center', sort: true }
				  ,{ field:'sort_order', width:120, title: '排序', align:'center' }
				  ,{ fixed:'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【TABLE渲染】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("友链");
		
	}else{
		
		//【监听友链类型】
	    var t_type = $('#t_type').val();
	    if(t_type==1) {
	    	//文字
			$(".logo").addClass("layui-hide");
		}else if(t_type==2) {
			  	//图片
			$(".logo").removeClass("layui-hide");
		}
	    form.on('select(t_type)', function (data) {
			if (data.value == 1) {
				$(".logo").addClass("layui-hide");
			}else if (data.value == 2) {
				$(".logo").removeClass("layui-hide");
			} 
	    });
		
	}
	
});
