var mmg;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'快递名称', name:'expressName', width: 160},
            {title:'快递代码', name:'expressCode' ,width:60},
            {title:'操作', name:'' ,width:150, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
			    if(WST.GRANT.KDGL_02)h += "<a  class='btn btn-blue' onclick='javascript:getForEdit(" + item['expressId'] + ")'><i class='fa fa-pencil'></i>修改</a> ";
			    if(WST.GRANT.KDGL_03)h += "<a  class='btn btn-red' onclick='javascript:toDel(" + item['expressId'] + ")'><i class='fa fa-trash-o'></i>删除</a> ";
			    return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: (h-165),indexCol: true, cols: cols,method:'POST',
        url: WST.U('admin/express/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });  
    $('#headTip').WSTTips({width:90,height:35,callback:function(v){
         if(v){
             mmg.resize({height:h-165});
         }else{
             mmg.resize({height:h-135});
         }
    }});
    loadQuery(p);
}
function loadQuery(p){
    p=(p<=1)?1:p;
    mmg.load({page:p});
}
function toDel(id){
	var box = WST.confirm({content:"您确定要删除该记录吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/express/del'),{id:id},function(data,textStatus){
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
     $.post(WST.U('admin/express/get'),{id:id},function(data,textStatus){
           layer.close(loading);
           var json = WST.toAdminJson(data);
           if(json.expressId){
           		WST.setValues(json);
           		toEdit(json.expressId);
           }else{
           		WST.msg(json.msg,{icon:2});
           }
    });
}

function toEdit(id){
	var title = "新增";
	if(id>0){
		title = "编辑";
	}else{
		$('#expressForm')[0].reset();
	}
	var box = WST.open({title:title,type:1,content:$('#expressBox'),area: ['640px', '300px'],btn:['确定','取消'],
        end:function(){$('#expressBox').hide();},
		yes:function(){
		$('#expressForm').submit();
	}});
	$('#expressForm').validator({
        fields: {
            expressName: {
            	rule:"required;",
            	msg:{required:"快递名称不能为空"},
            	tip:"请输入快递名称",
            	ok:"",
            }
        },
       valid: function(form){
		        var params = WST.getParams('.ipt');
	                params.expressId = id;
	                var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           		$.post(WST.U('admin/express/'+((id==0)?"add":"edit")),params,function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("操作成功",{icon:1});
	           			    	$('#expressForm')[0].reset();
	           			    	layer.close(box);
                              loadQuery(WST_CURR_PAGE);
	           			  }else{
	           			        WST.msg(json.msg,{icon:2});
	           			  }
	           		});

    	}

  });

}