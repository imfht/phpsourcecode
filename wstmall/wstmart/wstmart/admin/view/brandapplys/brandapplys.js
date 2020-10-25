function toEdits(id,p){
    var params = WST.getParams('.ipt');
    params.id = id;
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	$.post(WST.U('admin/brandapplys/handleApply'),params,function(data,textStatus){
		  layer.close(loading);
		  var json = WST.toAdminJson(data);
		  if(json.status=='1'){
		    	WST.msg(json.msg,{icon:1});
			    location.href=WST.U('admin/brands/index',"p="+p+'&type=apply');
		  }else{
		        WST.msg(json.msg,{icon:2});
		  }
	});
}