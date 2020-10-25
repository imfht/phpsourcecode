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
 *	管理人员
 *
 *	@auth 牧羊人
 *	@date 2018-07-18
 */
layui.use(['func','laydate'],function(){
	var laydate = layui.laydate,
		func = layui.func,
		$ = layui.$;
	
	if(A=='index') {
		
		//【TABLE列数组】
		var cols = [
				{ type:'checkbox', fixed: 'left' }
				  ,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
				  ,{ field:'avatar_url', width:60, title: '头像', align:'center', templet:function(d){
					  var avatarStr = "";
			 			if(d.avatar_url) {
			 				avatarStr = '<a href="'+d.avatar_url+'" target="_blank"><img src="'+d.avatar_url+'" height="26" /></a>';
			 			}
			 			return avatarStr;
		          }}
				  ,{ field:'num', width:100, title: '工号', align:'center' }
				  ,{ field:'realname', width:100, title: '真实姓名', align:'center', }
				  ,{ field:'gender_name', width:60, title: '性别', align:'center' }
				  ,{ field:'position_name', width:120, title: '职位', align:'center' }
				  ,{ field:'mobile', width:130, title: '手机号码', align:'center' }
				  ,{ field:'email', width:180, title: '邮箱', align:'center', }
				  ,{ field:'dept_name', width:300, title: '所属组织部门', align:'center', }
				  ,{ field:'is_admin', width:80, title: '管理员', align:'center', templet: function(d){
					  var str = "";
					  if(d.is_admin==1){
						  str = '<span class="layui-btn layui-btn-normal layui-btn-xs">是</span>';
					  }else{
						  str = '<span class="layui-btn layui-btn-normal layui-btn-xs layui-btn-danger">否</span>';
					  }
					  return str;
				  } }
				  ,{ field:'status', width:80, title: '状态', align:'center', templet: '#statusTpl', templet:function(d){
					  var str = "";
					  if(d.status==1){
						  str = '<span class="layui-btn layui-btn-normal layui-btn-xs">正常</span>';
					  }else{
						  str = '<span class="layui-btn layui-btn-normal layui-btn-xs layui-btn-danger">禁用</span>';
					  }
					  return str;
				  } }
				  ,{ field:'login_num', width:100, title:'登录次数', align:'center' }
				  ,{ fixed: 'right', width:480, title: '功能操作', align:'center', toolbar: '#toolBar' }
			];
		
		//【TABLE渲染】
		func.tableIns(cols,"tableList",function(layEvent,data){
			if(layEvent === 'auth'){
				layer.msg('权限设置');
				
				//独立权限设置
				location.href = "/adminAuth/index/?type=2&type_id="+data.id;
				
			}else if(layEvent === 'role'){
				layer.msg("角色设置");
				
				//设置角色
				var url = cUrl + "/setRole/?admin_id="+data.id;
				func.showWin("角色配置",url,600,350);
				
			}else if(layEvent === 'reset'){
				layer.msg('初始化密码操作');
				
				//初始化密码
				var url = cUrl + "/resetPwd/?id="+data.id;
				func.showWin("重置密码",url,450,250);
				
			}
		});
		
		//【设置弹框】
		func.setWin("人员");
		
	}else{
		
		//入职日期
		func.initDate(['entry_date|datetime'],function(value,date){
			console.log("当前选择日期:"+value);
			console.log("日期详细信息："+JSON.stringify(date));
		});
		
	}
	
});
