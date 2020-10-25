function judgeUserName(options){
	var url=options.url+'&a='+options.AjaxAction;
	var parameters=options.parameters.split('-');
	for( var ind in parameters){
		var tmp=$('#'+parameters[ind]).val();
		if(tmp){
			url+='&'+parameters[ind]+'='+tmp;
		}else{
			$("#"+options.messbox.tagsId).empty();
			$("#"+options.messbox.tagsId).append('请输入用户名！');
			return false;
		}
	}
    $.ajax({
		type: "GET",                                    
		url: url,                                      
		success: function(msg){
           messageTips(msg,options.messbox);
		}
	});
}
function messageTips(msg,options){//返回消息处理 
	var msg=msg.split(':');
	if(msg[0]=='ok'){
	     $("#"+options.tagsId).empty();
	     $("#"+options.tagsId).append(msg[1]);
	     return false;
	 }else if(msg[0]=='error'){
	 	$("#"+options.tagsId).empty();
	 	 $("#"+options.tagsId).append(msg[1]);
	     return false;
	 }else if(msg[0]=='illegal'){
	 	$("#"+options.tagsId).empty();
	 	 $("#"+options.tagsId).append(msg[1]);
	    return false;
	}else{
		return false;
	}
}