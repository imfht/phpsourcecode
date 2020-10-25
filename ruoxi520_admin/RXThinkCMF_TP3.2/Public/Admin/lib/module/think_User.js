/**
 *	会员管理
 *
 *	@auth 牧羊人
 *	@date 2018-10-25
 */
layui.use(['func','form'],function(){
	var form = layui.form,
		func = layui.func,
		$ = layui.$;
	
	if(A=='index') {
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				  ,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				  ,{ field:'avatar_url', width:60, title: '头像', align:'center', templet:function(d){
		              return '<a href="'+d.avatar_url+'" target="_blank"><img src="'+d.avatar_url+'" height="26" /></a>';
		          } }
				  ,{ field:'mobile', width:130, title: '手机号码', align:'center' }
				  ,{ field:'level_name', width:100, title: '会员等级', align:'center' }
				  ,{ field:'nickname', width:150, title: '会员昵称', align:'center' }
				  ,{ field:'card_num', width:200, title: '身份证号', align:'center' }
				  ,{ field:'points', width:100, title: '会员积分', align:'center' }
				  ,{ field:'type', width:100, title: '会员类型', align:'center', templet:function(d){
    	  			  var str = "";
    	  			  if(d.type==1){
    	  				  str = '<span class="layui-btn layui-btn-normal layui-btn-xs">个人会员</span>';
    	  			  }else if(d.type==2){
    	  				  str = '<span class="layui-btn layui-btn-danger layui-btn-xs">公司会员</span>';
    	  			  }
    	  			  return str;
    	  		  } }
				  ,{ field:'is_business', width:100, title: '是否上架', align:'center', templet:function(d){
	  	  			  var str = "";
	  	  			  if(d.is_business==1){
	  	  				  str = '<span class="layui-btn layui-btn-green layui-btn-xs">是</span>';
	  	  			  }else if(d.is_business==2){
	  	  				  str = '<span class="layui-btn layui-bg-cyan layui-btn-xs">否</span>';
	  	  			  }
	  	  			  return str;
	  	  		  }}
				  ,{ field:'status', width:100, title: '会员状态', align:'center', templet:"#statusTpl" }
				  ,{ field:'format_add_time', width:180, title:'注册时间', align:'center' }
				  ,{ fixed: 'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【TABLE渲染】
		func.tableIns(cols,"tableList");
		
		//【设置弹窗】
		func.setWin("系统会员",800,450);

		//【设置会员状态】
    	form.on('switch(status)', function(obj){
    		var status = this.checked ? '1' : '2';
    		
    		//发起POST请求
    		var url = cUrl + "/setStatus";
    		func.ajaxPost(url,{"id":this.value,"status":status},function(data,res){
    			console.log("请求回调");
    		});
    		
    	});
		
	}
	
});