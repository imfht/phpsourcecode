var layer = layui.layer;
$(function(){
	layui.form.on('checkbox',function (data) {
		var obj = $(data['elem']);
		$(obj).attr('checked',!$(obj).attr('checked'));
	});
})
function edit(){
	if(!WST.GRANT.TZSZ_02)return;
	var params = WST.getParams('.ipt');
	var strTitle = ['申请退款','订单投诉','用户提现'];
	var strTip = ['RefundOrderTip','ComplaintOrderTip','CashDrawsTip'];
	var strUser = ['refundOrderTipUsers','complaintOrderTipUsers','cashDrawsTipUsers'];
	var ids = [],wxId = '',smsId;
	for(var i=0;i<strUser.length;i++){
        ids = [];
		$('.'+strUser[i]).each(function(){
           if($(this)[0].checked)ids.push($(this).val());
		});
		wxId = 'wx'+strTip[i];
		smsId = 'sms'+strTip[i];
		params[wxId] = $('#'+wxId)[0].checked?1:0;
		params[smsId] = $('#'+smsId)[0].checked?1:0;
		params[strUser[i]] = ids.join(',');
		if(params[wxId]==0 && params[smsId]==0 && ids.length>0){
			WST.msg('请选择'+strTitle[i]+'提醒方式',{icon:1});
			return;
		}
		if((params[wxId]==1 || params[smsId]==1) && ids.length==0){
			WST.msg('请选择'+strTitle[i]+'提醒人',{icon:1});
			return;
		}
	}
	var loading = WST.msg('正在保存数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/sysconfigs/editNotifyConfig'),params,function(data,textStatus){
          layer.close(loading);
          var json = WST.toAdminJson(data);
          if(json.status==1){
        	  WST.msg(json.msg,{icon:1});
          }
   });
}