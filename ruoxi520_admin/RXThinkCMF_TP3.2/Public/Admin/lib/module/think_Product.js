/**
 *	商品
 *
 *	@auth 牧羊人
 *	@date 2018-10-16
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
				,{ field:'product_sn', width:100, title: '商品编码', align:'center' }
				,{ field:'name', width:250, title: '商品名称', align:'center', templet:function(d){
					return '<a href="'+d.detail_url+'" title="'+d.name+'" class="layui-table-link" target="_blank">'+d.name+'</a>';
				} }
				,{ field:'cover_url', width:60, title: '封面', align:'center', templet:function(d){
					  var coverUrl = "";
			 			if(d.cover_url) {
			 				coverUrl = '<a href="'+d.cover_url+'" target="_blank"><img src="'+d.cover_url+'" height="26" /></a>';
			 			}
			 			return coverUrl;
		          }}
				,{ field:'brand_name', width:150, title: '品牌名称', align:'center' }
				,{ field:'product_model', width:150, title: '商品型号', align:'center' }
				,{ field:'product_spec', width:150, title: '商品规格', align:'center' }
				,{ field:'is_spec', width:120, title: '参数设置', align:'center', templet:"#specTpl" }
				,{ field:'is_sale_name', width:80, title: '上下架', align:'center' }
				,{ field:'format_price', width:100, title: '商品价格', align:'center' }
				,{ field:'stock_num', width:100, title: '商品库存', align:'center' }
				,{ field:'sales_num', width:100, title: '销售总量', align:'center' }
				,{ field:'view_num', width:100, title: '浏览量', align:'center' }
				,{ field:'give_points', width:100, title: '赠送积分', align:'center' }
				,{ field:'sort_order', width:100, title: '排序', align:'center' }
				,{ field:'format_add_time', width:180, title: '添加时间', align:'center', sort: true }
				,{ field:'format_upd_time', width:180, title: '更新时间', align:'center', sort: true }
				,{ fixed:'right', width:380, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【渲染TABLE】
		func.tableIns(cols,"tableList",function(layEvent,data){
			if(layEvent==='productModel') {
				//规格管理
				if(data.is_spec==1) {
					// SKU设置
					var url = cUrl + "/productModel?product_id="+data.id;
					func.showWin("商品规格管理",url);
				}else if(data.is_spec==2) {
					// 阶梯报价
					var url = cUrl + "/ladderPrice?product_id="+data.id;
					func.showWin("阶梯报价",url);
				}
			}else if(layEvent==='productFile') {
				// 附件管理
				var url = mUrl + "/ProductFile/index?product_id="+data.id;
				func.showWin("商品附件管理",url);
			}
		});
		
		//【设置弹框】
		func.setWin("商城商品");
		
		//【规格状态】
    	form.on('switch(is_spec)', function(obj){
    		//获取值
    		var is_spec = this.checked ? '1' : '2';
    		
    		//向服务端发起POST请求
    		var url = cUrl+"/setIsSpec",
    			data = {"product_id":this.value,"is_spec":is_spec};
    		func.ajaxPost(url,data,function(res,flag){
    			location.reload();
    		});
    		
    	});
    	
    	// 推荐选择
    	if(simple) {
    		var cols2 = [
					{ type:'radio', fixed: 'left' }
					,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
					,{ field:'name', width:300, title: '商品名称', align:'center', event: 'setSign', style:'cursor: pointer;' }
					,{ field:'product_sn', width:100, title: '商品编码', align:'center' }
					,{ field:'brand_name', width:100, title: '品牌名称', align:'center' }
					,{ field:'tags_name', width:100, title: '标签名称', align:'center' }
					,{ field:'product_model', width:100, title: '商品型号', align:'center' }
					,{ field:'product_spec', width:150, title: '商品规格', align:'center' }
					,{ field:'business_sn', width:150, title: '商家商品编码', align:'center' }
					,{ field:'format_price', width:100, title: '商品价格', align:'center' }
					,{ field:'stock_num', width:100, title: '商品库存', align:'center' }
					,{ field:'sales_num', width:100, title: '销售总量', align:'center' }
					,{ field:'view_num', width:100, title: '浏览量', align:'center' }
				];
	    	
	    	// 点击回调
	    	func.tableIns(cols2,"tableList2",function(layEvent,data){
	    		
	    		if(layEvent === 'setSign') {
	    			var index = parent.layer.getFrameIndex(window.name);
	    		    parent.layer.close(index);
	    		    
	    		    // 给父类传值
	    			parent.layui.$("#type_id").val(data.id);
	    			parent.layui.$("#type_value").val(data.name);
	    		}
	    		
	    	});
	    	
	    	//【搜索功能】
	    	func.searchForm("searchForm2","tableList2");
    	}
    	
	}else{
		
		// 选择商品分类
		$("#btnSelectCate").click(function(){
			
			var category_id = $("#category_id").val();
			var url = cUrl + "/cateSelect?category_id="+category_id;
			func.showWin("选择分类",url);
			
			return false;
		});

		// 提交分类
		form.on('submit(confirmCateSelect)', function(data){
			var ids = [], name = [];
			$.each(data.field, function(i, value){
				var item = i.split('_');
				ids.push(item[0]);
				name.push(item[1]);
			});
			
			// ID赋值
			var cateId = ids.join(',');
			parent.layui.$("#category_id").val(cateId);
			
			// 名称渲染
			parent.layui.$(".multiple").html('');
			name.forEach(function(item,index){
				var html = '<a href="javascript:;"><span lay-value="'+ids[index]+'">'+item+'</span><i class="layui-icon" onclick="removeFrom(this);">ဆ</i></a>';
				parent.layui.$(".multiple").append(html);
			});
			
			parent.layer.closeAll("iframe");
			
			return false;
		});
		
		// 选择属性
		$("#btnSelectAttr").click(function(){
			var attribute_id = $("#attribute_id").val();
			var url = cUrl + "/attrSelect?attribute_id="+attribute_id;
			func.showWin("选择属性",url);
			
			return false;
		});
		
		// 提交属性
		form.on('submit(confirmAttrSelect)', function(data){
			var ids = [], name = [];
			$.each(data.field, function(i, value){
				var item = i.split('_');
				ids.push(item[0]);
				name.push(item[1]);
			});
			
			// ID赋值
			var attrId = ids.join(',');
			parent.layui.$("#attribute_id").val(attrId);
			
			// 名称渲染
			parent.layui.$(".multiple2").html('');
			name.forEach(function(item,index){
				var html = '<a href="javascript:;"><span lay-value="'+ids[index]+'">'+item+'</span><i class="layui-icon" onclick="removeFrom2(this);">ဆ</i></a>';
				parent.layui.$(".multiple2").append(html);
			});
			
			parent.layer.closeAll("iframe");
			
			return false;
		});
		
	}

});

/**
 * 移除分类
 * @param obj
 */
function removeFrom(obj) {
	//移除当前对象
	layui.$(obj).parent().remove();
	
	var id = layui.$(obj).parent().children("span").attr("lay-value");
	var text = layui.$(obj).parent().children("span").text();
	var categoryId = layui.$("#category_id").val();
	var cateArr = categoryId.split(',');
	var list = [];
	cateArr.forEach(function(item,i){
		if(item!=id) {
			list.push(item);
		}
	});
	var cateStr = list.join(',');
	layui.$("#category_id").val(cateStr);
	console.log("分类ID："+cateStr);
}

/**
 * 移除属性
 * @param obj
 */
function removeFrom2(obj) {
	//移除当前对象
	layui.$(obj).parent().remove();
	
	var id = layui.$(obj).parent().children("span").attr("lay-value");
	var text = layui.$(obj).parent().children("span").text();
	var attributeId = layui.$("#attribute_id").val();
	var attrArr = attributeId.split(',');
	var list = [];
	attrArr.forEach(function(item,i){
		if(item!=id) {
			list.push(item);
		}
	});
	var attrStr = list.join(',');
	layui.$("#attribute_id").val(attrStr);
	console.log("属性ID："+cateStr);
}