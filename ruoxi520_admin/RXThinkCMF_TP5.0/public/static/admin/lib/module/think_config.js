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
 *  配置
 *
 *	@auth 牧羊人
 *	@date 2018-09-22
 */
layui.use(['func','form'],function(){
	
	//【声明变量】
	var func = layui.func
		,form = layui.form
		,$ = layui.$;
	
	if(A=='index') {
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				,{ field:'name', width:150, title: '配置项名称', align:'center' }
				,{ field:'tag', width:150, title: '配置项标签', align:'center' }
				,{ field:'type_name', width:100, title: '配置项类型', align:'center' }
				,{ field:'group_name', width:150, title: '分组名称', align:'center' }
				,{ field:'status', width:100, title: '状态', align:'center', templet:function(d){
					  var str = "";
					  if(d.status==1){
						  str = '<span class="layui-btn layui-btn-normal layui-btn-xs">在用</span>';
					  }else{
						  str = '<span class="layui-btn layui-btn-normal layui-btn-xs layui-btn-danger">停用</span>';
					  }
					  return str;
				  } }
				,{ field:'format_add_user', width:100, title: '创建人', align:'center' }
				,{ field:'format_add_time', width:180, title: '添加时间', align:'center', sort: true }
				,{ field:'format_upd_time', width:180, title: '更新时间', align:'center', sort: true }
				,{ fixed:'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("配置项");
		
	}else{
		
		//初始化类型:1单行文本 2多行文本 3富文本 4图片 5图片集
		var ctype = $("#type").val();
		if(ctype==1) {
			//单行文本
			$(".content1").removeClass("layui-hide");
		}else if(ctype==2){
			//多行文本
			$(".content2").removeClass("layui-hide");
		}else if(ctype==3) {
			//富文本
			$(".content3").removeClass("layui-hide");
		}else if(ctype==4) {
			//单张图片
			$(".image").removeClass("layui-hide");
		}else if(ctype==5) {
			//图集
			$(".imgs").removeClass("layui-hide");
		}
		
		//【监听选择类型】
	    form.on('select(type)', function (data) {
	 
	    	//预先全部隐藏
	    	$(".image").addClass("layui-hide");
	    	$(".imgs").addClass("layui-hide");
	    	$(".content1").addClass("layui-hide");
	    	$(".content2").addClass("layui-hide");
	    	$(".content3").addClass("layui-hide");
	    	
	    	var type = data.value;
	    	
	    	if(type==1) {
	    		//单行文本
	    		$(".content1").removeClass("layui-hide");
	    	}else if(type==2) {
	    		//多行文本
	    		$(".content2").removeClass("layui-hide");
	    	}else if(type==3) {
	    		//富文本
	    		$(".content3").removeClass("layui-hide");
	    	}else if(type==4) {
	    		//单张图片
	    		$(".image").removeClass("layui-hide");
	    	}else if(type==5) {
	    		//多张图集
	    		$(".imgs").removeClass("layui-hide");
	    	}
	    	
	    });
		
	}

});