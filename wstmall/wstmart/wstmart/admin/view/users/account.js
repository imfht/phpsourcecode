var mmg;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'账号', name:'loginName', width: 30,sortable:true},
            {title:'昵称', name:'userName' ,width:100,sortable:true},
            {title:'手机号码', name:'userPhone' ,width:100,sortable:true},
            {title:'电子邮箱', name:'userEmail' ,width:60,sortable:true},
            {title:'最后登录时间', name:'lastTime' ,width:60,sortable:true},
            {title:'状态', name:'userStatus' ,width:20, renderer:function(val,item,rowIndex){
            	return '<input type="checkbox" '+((val==1)?"checked":"")+' lay-skin="switch" lay-filter="userStatus" data="'+item['userId']+'" lay-text="启用|停用">';
            }},
            {title:'操作', name:'' ,width:170, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
                if(WST.GRANT.ZHGL_02)h += "<a  class='btn btn-blue' onclick='javascript:getForEdit("+item['userId']+")'><i class='fa fa-pencil'></i>修改登录密码</a> ";
                if(WST.GRANT.ZHGL_02)h += "<a  class='btn btn-blue' onclick='javascript:resetPayPwd(" + item['userId'] + ")'><i class='fa fa-key'></i>重置支付密码</a> ";
                return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-167,indexCol: true,indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/Users/pageQuery'), fullWidthRows: true, autoLoad: false,remoteSort: true,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });   
    mmg.on('loadSuccess',function(){
    	layui.form.render('','gridForm');
        layui.form.on('switch(userStatus)', function(data){
            var id = $(this).attr("data");
            if(this.checked){
                changeUserStatus(id, 1);
            }else{
                changeUserStatus(id, 0);
            }
        });
    })
    $('#headTip').WSTTips({width:90,height:35,callback:function(v){
         if(v){
             mmg.resize({height:h-167});
         }else{
             mmg.resize({height:h-137});
         }
    }});
    accountQuery(p);
}  


function resetPayPwd(id){
	var box = WST.confirm({content:"您确定重置支付密码为666666吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/users/resetPayPwd'),{userId:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("重置成功",{icon:1});
	           			    	layer.close(box);
	           		            accountQuery(WST_CURR_PAGE);
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}

function getForEdit(id){
	 var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
     $.post(WST.U('admin/users/get'),{id:id},function(data,textStatus){
           layer.close(loading);
           var json = WST.toAdminJson(data);
           //清空密码
           json.loginPwd = '';
           if(json.userId){
           		WST.setValues(json);
           		layui.form.render();
           		$('#loginName').html(json.loginName);
           		$('#userId').val(json.userId);
           		toEdit(json.userId);
           }else{
           		WST.msg(json.msg,{icon:2});
           }
    });
}

function toEdit(id){
	var box = WST.open({title:'编辑',type:1,content:$('#accountBox'),area: ['600px', '280px'],btn:['确定','取消'],yes:function(){
					$('#accountForm').isValid(function(v){
						if(v){
							var params = WST.getParams('.ipt');
			                if(id>0)
			                	params.userId = id;
			                var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
			           		$.post(WST.U('admin/users/editAccount'),params,function(data,textStatus){
			           			  layer.close(loading);
			           			  var json = WST.toAdminJson(data);
			           			  if(json.status=='1'){
			           			    	WST.msg("操作成功",{icon:1});
			           			    	$('#accountForm')[0].reset();
			           			    	layer.close(box);
			           		            accountQuery(WST_CURR_PAGE);
			           			  }else{
			           			        WST.msg(json.msg,{icon:2});
			           			  }
			           		});
						}else{
							return false;
						}
					});
		        	
		

	},cancel:function(){$('#accountForm')[0].reset();},end:function(){$('#accountBox').hide();$('#accountForm')[0].reset();}});

}

function changeUserStatus(id, status){
	if(!WST.GRANT.ZHGL_02)return;
	$.post(WST.U('admin/Users/changeUserStatus'), {'id':id, 'status':status}, function(data, textStatus){
		var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("操作成功",{icon:1});
	           		            accountQuery(WST_CURR_PAGE);
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	})
}


function accountQuery(p){
	p=(p<=1)?1:p;
    var query = WST.getParams('.query');
    query.userType=query.userType1;
    query.page = p;
	mmg.load(query);
}

		