var mmg;
function initGrid(p){
  var h = WST.pageHeight();
  var cols = [
            {title:'图标', name:'accredImg' ,width:50, align:'center', renderer: function(val,item,rowIndex){
               return '<img src="'+WST.conf.RESOURCE_PATH+'/'+item['accredImg']+'" height="28px" />';
            }},
            {title:'认证名称', name:'accredName' ,width:125},
            {title:'创建时间', name:'createTime' ,width:75,},
            {title:'操作', name:'' ,width:60, align:'center', renderer: function(val,item,rowIndex){
                var h="";
	            if(WST.GRANT.RZGL_02)h += "<a  class='btn btn-blue' href='javascript:getForEdit(" + item['accredId'] + ")'><i class='fa fa-pencil'></i>修改</a> ";
	            if(WST.GRANT.RZGL_03)h += "<a  class='btn btn-red' href='javascript:toDel(" + item['accredId'] + ")'><i class='fa fa-trash-o'></i>删除</a> ";
	            return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-89,indexCol: true, cols: cols,method:'POST',
        url: WST.U('admin/Accreds/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadGrid(p);
}
function loadGrid(p){
    p=(p<=1)?1:p;
    mmg.load({page:p});
}

function getForEdit(id){
	 var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
     $.post(WST.U('admin/accreds/get'),{id:id},function(data,textStatus){
           layer.close(loading);
           var json = WST.toAdminJson(data);
           if(json.accredId){
           		WST.setValues(json);
           		//显示原来的图片
           		$('#preview').html('<img src="'+WST.conf.RESOURCE_PATH+'/'+json.accredImg+'" height="30px" />');
           		$('#isImg').val('ok');
           		toEdit(json.accredId);
           }else{
           		WST.msg(json.msg,{icon:2});
           }
    });
}

function toEdit(id){
	if(!isInitUpload){
		initUpload();
	}
	var title =(id==0)?"新增":"编辑";
	var box = WST.open({title:title,type:1,content:$('#accredBox'),area: ['680px', '300px'],btn: ['确定','取消'],yes:function(){
			$('#accredForm').submit();
	},cancel:function(){
		//重置表单
		$('#accredForm')[0].reset();
		//清空预览图
		$('#preview').html('');
		$('#accredImg').val('');

	},end:function(){
		$('#accredBox').hide();
		//重置表单
		$('#accredForm')[0].reset();
		//清空预览图
		$('#preview').html('');
		$('#accredImg').val('');

	}});
	$('#accredForm').validator({
        fields: {
            accredName: {
            	rule:"required;",
            	msg:{required:"请输入认证名称"},
            	tip:"请输入认证名称",
            	ok:"",
            },
            accredImg:  {
            	rule:"required;",
            	msg:{required:"请上传图标"},
            	tip:"请上传图标",
            	ok:"",
            },
            
        },
       valid: function(form){
		        var params = WST.getParams('.ipt');
		        	params.accredId = id;
		        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		   		$.post(WST.U('admin/accreds/'+((id==0)?"add":"edit")),params,function(data,textStatus){
		   			  layer.close(loading);
		   			  var json = WST.toAdminJson(data);
		   			  if(json.status=='1'){
		   			    	WST.msg("操作成功",{icon:1});
		   			    	$('#accredForm')[0].reset();
		   			    	//清空预览图
		   			    	$('#preview').html('');
		   			    	//清空图片隐藏域
		   			    	$('#accredImg').val('');
		   			    	layer.close(box);
                          loadGrid(WST_CURR_PAGE)
		   			  }else{
		   			        WST.msg(json.msg,{icon:2});
		   			  }
		   		});

    	}

  });
}
var isInitUpload = false;
function initUpload(){
	isInitUpload = true;
	//文件上传
	WST.upload({
	    pick:'#adFilePicker',
	    formData: {dir:'accreds'},
	    accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
	    callback:function(f){
	      var json = WST.toAdminJson(f);
	      if(json.status==1){
	        $('#uploadMsg').empty().hide();
	        //将上传的图片路径赋给全局变量
		    $('#accredImg').val(json.savePath+json.thumb);
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
	           	$.post(WST.U('admin/Accreds/del'),{id:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("操作成功",{icon:1});
	           			    	layer.close(box);
                              loadGrid(WST_CURR_PAGE)
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}






		