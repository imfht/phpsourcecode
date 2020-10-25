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
 *	权限设置
 *
 *	@auth 牧羊人
 *	@date 2018-07-17
 */
layui.use(['form','func'],function(){
	var form = layui.form,
		func = layui.func,
		$ = layui.$;
	
	//【TREE列数组】
	var layout =
		[{	name: 'ID',
	 		headerClass: 'value_col',
	 		colClass: 'value_col',
	 		style: 'width: 5%',
	 		render: function(row) {
	 			return row.id;
	 		}
	 	},
	 	{
	 		name: '菜单名称', 
	 		treeNodes: true, 
	 		headerClass: 'value_col', 
	 		colClass: 'value_col', 
	 		style: '15%',  
	 		render: function(row) {
	 			return row.title;
	 		} 
	 	},
	 	{
	 		name: '权限节点',
	 		headerClass: 'value_col',
	 		colClass: 'value_col2',
	 		style: 'width: 70%;',
	 		render: function(row) {
	 			var str = '';
	 			if(row.funcList!=null) {
	 				for(var i=0;i<row.funcList.length;i++){
		 				var item = row.funcList[i];
		 				str += '<input name="auth['+row.id+','+item.id+']" lay-skin="primary" title="'+item.title+'" '+(item.selected==1 ? 'checked=""' : "")+' type="checkbox">';
		 			}
	 			}
	 			return str;
	 		}
	 	},
	 	{
	 		name: '图标',
	 		headerClass: 'value_col',
	 		colClass: 'value_col',
        	style: 'width: 5%',
        	render: function(row) {
        		return '<i class="larry-icon '+row.icon+'" data-icon="'+row.icon+'" data-font="larry-icon"></i>';
        	}
	 	},
	 	{
	 		name: '排序',
	 		headerClass: 'value_col',
	 		colClass: 'value_col',
	 		style: 'width: 5%',
	 		render: function(row) {
	 			return row.sort_order;
	 		}
	 	}];
	
	//【系统参数】
	var type = $("#type").val();
	var typeId = $("#type_id").val();
	
	//【TREE渲染】
	var url = aUrl + "?type="+type+"&type_id="+typeId;
	func.treeIns(layout,"treeList",true,null,url);
	
	//【提交表单】
	form.on('submit(submitForm2)', function(data){
		var index = layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.2});
		
		var url = cUrl + "/setAuth"
		$.post(url, data.field, function(data){
			if (data.success) {
				layer.close(index);
				layer.msg("保存成功！");
				layer.closeAll("iframe");
				
				//刷新父页面
				history.back();
				
				return false ;
			}else{
				layer.close(index);
				layer.msg(data.msg);
			}
		}, 'json');
		
		return false;
	});
	
});
