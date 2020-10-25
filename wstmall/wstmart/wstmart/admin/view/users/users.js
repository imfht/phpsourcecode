var mmg;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'账号', name:'loginName', width: 110,sortable:true},
            {title:'昵称', name:'userName' ,width:150,sortable:true,renderer:function(val,item,rowIndex){
                var html = '';
                if(item['wxOpenId'] && item['wxOpenId']!='')html = html+'<img class="order-source2" src="'+WST.conf.ROOT+'/wstmart/admin/view/img/order_source_1.png" title="有绑定微信"> ';
                if(item['weOpenId'] && item['weOpenId']!='')html = html+'<img class="order-source2" src="'+WST.conf.ROOT+'/wstmart/admin/view/img/order_source_5.png" title="有绑定小程序"> ';
                html = html +WST.blank(val)
                return html;
            }},
            {title:'手机号码', name:'userPhone' ,width:100,sortable:true},
            {title:'电子邮箱', name:'userEmail' ,width:100,sortable:true},
            {title:'积分', name:'userScore' ,width:50,sortable:true},
            {title:'等级', name:'rank' ,width:60,sortable:true},
            {title:'注册时间', name:'createTime' ,width:100,sortable:true},
            {title:'用户钱包', name:'userMoney' ,width:100,sortable:true, renderer:function(val,item,rowIndex){
                return '<div style="line-height:20px">可用金额：￥'+val+'<br/>冻结金额：￥'+item['lockMoney']+'<br/>充值送：￥'+item['rechargeMoney']+'</div>';
            }},
            {title:'状态', name:'userStatus' ,width:40,sortable:true, renderer:function(val,item,rowIndex){
                return (val==1)?"<span class='statu-yes'><i class='fa fa-check-circle'></i> 启用&nbsp;</span>":"<span class='statu-no'><i class='fa fa-ban'></i> 停用&nbsp;</span>";
            }},
            {title:'操作', name:'' ,width:150, align:'center', renderer: function(val,rowdata,rowIndex){
                var h = "";
                if(WST.GRANT.HYGL_02)h += "<a  class='btn btn-blue' href='"+WST.U('admin/Users/toEdit','id='+rowdata['userId'])+'&p='+WST_CURR_PAGE+"'><i class='fa fa-pencil'></i>修改</a> ";
                if(WST.GRANT.HYGL_03)h += "<a  class='btn btn-red' href='javascript:toDel(" + rowdata['userId'] + ","+rowdata['userType']+")'><i class='fa fa-trash-o'></i>删除</a> ";
                h += "<br/><a href='"+WST.U('admin/userscores/touserscores','id='+rowdata['userId'])+'&p='+WST_CURR_PAGE+"'>积分</a> ";
                h += "<a href='"+WST.U('admin/logmoneys/tologmoneys','id='+rowdata['userId']+'&src=users')+'&p='+WST_CURR_PAGE+"&type=0'>用户资金</a> ";
                h += "<a href='"+WST.U('admin/orders/index','userId='+rowdata['userId'])+'&p='+WST_CURR_PAGE+"&type=0'>消费信息</a> ";
                return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-187,indexCol: true,indexColWidth:50, cols: cols,method:'POST',nowrap:true,
        url: WST.U('admin/Users/pageQuery'), fullWidthRows: true, autoLoad: false,remoteSort: true,sortName:'createTime',sortStatus:'desc',
        plugins: [
            $('#pg').mmPaginator({})
        ]
    }); 
    $('#headTip').WSTTips({width:90,height:35,callback:function(v){
         if(v){
             mmg.resize({height:h-187});
         }else{
             mmg.resize({height:h-142});
         }
    }});
    loadGrid(p);
}
function loadGrid(p){
    p=(p<=1)?1:p;
    mmg.load({page:p});
}
function toEdit(id){
   location.href=WST.U('admin/users/toEdit','id='+id+'&p='+WST_CURR_PAGE);
}
function toDel(id,userType){
  var msg = (userType==1)?"您要删除的用户是商家用户，您确定要删除吗？":"您确定要删除该用户吗?";
	var box = WST.confirm({content:msg,yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/Users/del'),{id:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("操作成功",{icon:1});
	           			    	layer.close(box);
	           		         userQuery(WST_CURR_PAGE);
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}

function userQuery(p){
    p=(p<=1)?1:p;
		var query = WST.getParams('.query');
    query.page = p;
    mmg.load(query);
}



function editInit(p){
  var laydate = layui.laydate;
  laydate.render({elem: '#brithday'});
	/* 表单验证 */
  $('#userForm').validator({
            dataFilter: function(data) {
                if (data.ok === '该登录账号可用' ) return "";
                else return "已被注册";
            },
            rules: {
                loginName: function(element) {
                    return /\w{5,}/.test(element.value) || '账号应为5-16字母、数字或下划线';
                },
                myRemote: function(element){
                    return $.post(WST.U('admin/users/checkLoginKey'),{'loginName':element.value,'userId':$('#userId').val()},function(data,textStatus){});
                }
            },
            fields: {
                loginName: {
                  rule:"required;loginName;myRemote",
                  msg:{required:"请输入会员账号"},
                  tip:"请输入会员账号",
                  ok:"",
                },
                userPhone: {
                  rule:"mobile;myRemote",
                  ok:"",
                },
                userEmail: {
                  rule:"email;myRemote",
                  ok:"",
                },
                userScore: {
                  rule:"integer[+0]",
                  msg:{integer:"当前积分只能是正整数"},
                  tip:"当前积分只能是正整数",
                  ok:"",
                },
                userTotalScore: {
                  rule:"match[gte, userScore];integer[+0];",
                  msg:{integer:"当前积分只能是正整数",match:'会员历史积分必须不小于会员积分'},
                  tip:"当前积分只能是正整数",
                  ok:"",
                },
                userQQ: {
                  rule:"integer[+]",
                  msg:{integer:"QQ只能是数字"},
                  tip:"QQ只能是数字",
                  ok:"",
                },
                
            },

          valid: function(form){
            var params = WST.getParams('.ipt');
            var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
            $.post(WST.U('admin/Users/'+((params.userId==0)?"add":"edit")),params,function(data,textStatus){
              layer.close(loading);
              var json = WST.toAdminJson(data);
              if(json.status=='1'){
                  WST.msg("操作成功",{icon:1});
                  location.href=WST.U('Admin/Users/index',"p="+p);
              }else{
                    WST.msg(json.msg,{icon:2});
              }
            });

      }

    });



//上传头像
  WST.upload({
      pick:'#adFilePicker',
      formData: {dir:'users'},
      accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
      callback:function(f){
        var json = WST.toAdminJson(f);
        if(json.status==1){
        $('#uploadMsg').empty().hide();
        //将上传的图片路径赋给全局变量
        $('#userPhoto').val(json.savePath+json.thumb);
        $('#preview').html('<img src="'+WST.conf.RESOURCE_PATH+'/'+json.savePath+json.thumb+'"  height="30" />');
        }else{
          WST.msg(json.msg,{icon:2});
        }
    },
    progress:function(rate){
        $('#uploadMsg').show().html('已上传'+rate+"%");
    }
    });
}

function toExport(){
  var params = {};
  params = WST.getParams('.query');
  var box = WST.confirm({content:"您确定要导出这些会员信息吗?",yes:function(){
      layer.close(box);
      location.href=WST.U('admin/users/toExport',params);
  }});
}