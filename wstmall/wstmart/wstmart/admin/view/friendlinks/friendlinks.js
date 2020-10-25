var mmg,isInitUpload = false;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'网站名称', name:'friendlinkName', width: 80},
            {title:'网址', name:'friendlinkUrl' ,width:100},
            {title:'图标', name:'friendlinkIco' ,width:30,renderer:function(val,item,rowIndex){
              if(item['friendlinkIco']){
                return '<img src="'+WST.conf.RESOURCE_PATH+'/'+item['friendlinkIco']+'" height="28px" />';
              }else{
                return "";
              }
            }},
            {title:'操作', name:'' ,width:80, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
                if(WST.GRANT.YQGL_02)h += "<a  class='btn btn-blue' onclick='javascript:getForEdit("+item['friendlinkId']+")'><i class='fa fa-pencil'></i>修改</a> ";
                if(WST.GRANT.YQGL_03)h += "<a  class='btn btn-red' onclick='javascript:toDel(" + item['friendlinkId'] + ")'><i class='fa fa-trash-o'></i>删除</a> ";
                return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-89,indexCol: true, cols: cols,method:'POST',
        url: WST.U('admin/Friendlinks/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadQuery(p);
}
function loadQuery(p){
    p=(p<=1)?1:p;
    mmg.load({page:p});
}
function initUpload(){
  //文件上传
  WST.upload({
      pick:'#adFilePicker',
      formData: {dir:'friendlinks'},
      accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
      callback:function(f){
        var json = WST.toAdminJson(f);
        if(json.status==1){
          $('#uploadMsg').empty().hide();
          $('#friendlinkIco').val(json.savePath+json.thumb);
          $('#preview').html('<img src="'+WST.conf.RESOURCE_PATH+'/'+json.savePath+json.thumb+'" height="30" />');
        }else{
            WST.msg(json.msg,{icon:2});
        }
    },
    progress:function(rate){
        $('#uploadMsg').show().html('已上传'+rate+"%");
    }
  });
}
function toDel(id){
	var box = WST.confirm({content:"您确定要删除该记录吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/friendlinks/del'),{id:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("操作成功",{icon:1});
	           			    	layer.close(box);
                              loadQuery(WST_CURR_PAGE);
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}

function getForEdit(id){
	 var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
     $.post(WST.U('admin/friendlinks/get'),{id:id},function(data,textStatus){
           layer.close(loading);
           var json = WST.toAdminJson(data);
           if(json.friendlinkId){
           		WST.setValues(json);
           		//显示原来的图片
           		if(json.friendlinkIco!=''){
           			$('#preview').html('<img src="'+WST.conf.RESOURCE_PATH+'/'+json.friendlinkIco+'" height="30px" />');
           		}else{
           			$('#preview').empty();
           		}
           		toEdit(json.friendlinkId);
           }else{
           		WST.msg(json.msg,{icon:2});
           }
    });
}

function toEdit(id){
  if(!isInitUpload){
     isInitUpload = true;
     initUpload();
  }
	var title =(id==0)?"新增":"编辑";
	var box = WST.open({title:title,type:1,content:$('#friendlinkBox'),area: ['660px', '360px'],btn: ['确定','取消'],
		yes:function(){
			$('#friendlinkForm').submit();
	},cancel:function(){
    $('#friendlinkBox').hide();
		//重置表单
		$('#friendlinkForm')[0].reset();
		//清空预览图
		$('#preview').html('');
		//清空图片隐藏域
		$('#friendlinkIco').val('');
	},end:function(){
    $('#friendlinkBox').hide();
		//重置表单
		$('#friendlinkForm')[0].reset();
		//清空预览图
		$('#preview').html('');
		//清空图片隐藏域
		$('#friendlinkIco').val('');

	}});
	$('#friendlinkForm').validator({
        fields: {
            friendlinkName: {
            	rule:"required;",
            	msg:{required:"网站名称不能为空"},
            	tip:"请输入网站名称",
            	ok:"",
            },
            friendlinkUrl: {
	            rule: "required;",
	            msg: {required: "网址不能为空"},
	            tip: "请输入网址",
	            ok: "",
        	}
        },
       valid: function(form){
		        var params = WST.getParams('.ipt');
		        params.friendlinkId = id;
		        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		   		$.post(WST.U('admin/friendlinks/'+((id==0)?"add":"edit")),params,function(data,textStatus){
		   			  layer.close(loading);
		   			  var json = WST.toAdminJson(data);
		   			  if(json.status=='1'){
		   			    	WST.msg("操作成功",{icon:1});
                  $('#friendlinkBox').hide();
		   			    	$('#friendlinkForm')[0].reset();
		   			    	//清空预览图
		   			    	$('#preview').html('');
		   			    	//清空图片隐藏域
		   			    	$('#friendlinkIco').val('');
		   			    	layer.close(box);
                          loadQuery(WST_CURR_PAGE);
		   			  }else{
		   			        WST.msg(json.msg,{icon:2});
		   			  }
		   		});

    	}

  });
}
