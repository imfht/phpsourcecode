var layer = layui.layer;
$(function(){
	layui.form.on('checkbox',function (data) {
		var obj = $(data['elem']);
		$(obj).attr('checked',!$(obj).attr('checked'));
	});
})
function edit(){
	var params = WST.getParams('.ipt');
	if(params['privilegeMsgTypes'].length==0 && params["privilegeMsgs"].length>0){
		WST.msg('请选择提醒方式',{icon:1});
		return;
	}
	if(params['privilegeMsgTypes'].length>0 && params["privilegeMsgs"].length==0){
		WST.msg('请选择提醒项目',{icon:1});
		return;
	}
	var loading = WST.msg('正在保存数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('store/storeusers/editNotifyConfig'),params,function(data,textStatus){
          layer.close(loading);
          var json = WST.toJson(data);
          if(json.status==1){
        	  WST.msg(json.msg,{icon:1});
          }
   });
}