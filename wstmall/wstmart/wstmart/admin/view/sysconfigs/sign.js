var layer = layui.layer;
var laytpl, form,laypage;
$(function(){
	form = layui.form;
  	form.render();
	form.on('switch(signScoreSwitch)', function(data){
	  	if(this.checked){
	  		WST.showHide(1,'.signScoreBox')
	  	}else{
	  		WST.showHide(0,'.signScoreBox')
	  	}
	});
});

function edit(){
	if(!WST.GRANT.QDSZ_02)return;
	var params = WST.getParams('.ipt');
	var signScore = '';
	for(var i=0;i<30;i++){
		if(i>0 && params.signScore0!=0){
			if(!params['signScore'+i] || params['signScore'+i]==0){
				params['signScore'+i] = params['signScore'+(i-1)];
			}
		}
		if(!params.signScore0 || params.signScore0==0){
			signScore += '0,';
		}else{
			if(!params['signScore'+i])params['signScore'+i] = 0;
			signScore +=  params['signScore'+i] + ',';
		}
	}
	params.signScore = signScore;
	var loading = WST.msg('正在保存数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/sysconfigs/editSign'),params,function(data,textStatus){
          layer.close(loading);
          var json = WST.toAdminJson(data);
          if(json.status==1){
        	  WST.msg(json.msg,{icon:1});
          }
   });
}