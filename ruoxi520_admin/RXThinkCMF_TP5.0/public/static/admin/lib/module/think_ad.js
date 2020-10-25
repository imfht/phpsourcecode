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
 *	广告管理
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
				  ,{ field:'cover_url', width:60, title: '封面', align:'center', templet:function(d){
		              return '<a href="'+d.cover_url+'" target="_blank"><img src="'+d.cover_url+'" height="26" /></a>';
		          } }
				  ,{ field:'title', width:200, title: '广告标题', align:'center' }
				  ,{ field:'t_type_name', width:100, title: '广告类型', align:'center' }
				  ,{ field:'type_name', width:100, title: '推荐类型', align:'center' }
				  ,{ field:'ad_sort_name', width:200, title: '所属广告位', align:'center' }
				  ,{ field:'description', width:300, title: '描述', align:'center' }
				  ,{ field:'format_add_user', width:100, title: '添加人', align:'center' }
				  ,{ field:'format_add_time', width:180, title: '添加时间', align:'center', sort: true }
				  ,{ field:'sort_order', width:80, title: '排序', align:'center' }
				  ,{ fixed:'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【TABLE渲染】
		func.tableIns(cols,"tableList");
		
		//【设置弹框】
		func.setWin("广告");
	}
	
	var t_type = $("#t_type").val();
	if(t_type==4) {
		$(".t_type").removeClass("layui-hide");
	}else{
		$(".t_type").addClass("layui-hide");
	}
	
	//【监听选择类型】
    form.on('select(t_type)', function (data) {
		if (data.value == 4) {
			$(".t_type").removeClass("layui-hide");
		}else {
			$(".t_type").addClass("layui-hide");
		} 
    });
    
    //监听推荐类型
	var type = $("#type").val();
	var typeStr = '';
    form.on('select(type)', function (data) {
		type = data.value;
		typeStr = data.elem[data.elem.selectedIndex].text;
		
		console.log(data.elem); //得到select原始DOM对象
		console.log(data.value); //得到被选中的value
		console.log($(data.elem).find("option:selected").text()); //得到被选中的text
		  
    });
    
    //【选择模块】
	$("#type_value").click(function(){
		//推荐类型
		var	url;
		if(type==1) {
			//CMS文章
			url = mUrl + "/Article/index/?simple=1";
		}else{
			//其他
			
		}
		if(!url) {
			layer.msg("请选择类型");
			return false;
		}
		
		//【弹开窗体】
		func.showWin("选择内容",url,1000,600);
	});
	
});
