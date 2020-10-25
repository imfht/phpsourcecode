var layer = layui.layer;
var laytpl, form,laypage;
$(function(){
	form = layui.form;
  	form.render();
  	form.on('switch(mailOpen)', function(data){
	  	if(this.checked){
	  		WST.showHide(1,'.mailOpenTr')
	  	}else{
	  		WST.showHide(0,'.mailOpenTr')
	  	}
	});
	form.on('switch(seoMallSwitch)', function(data){
	  	if(this.checked){
	  		WST.showHide(0,'#close');
	  	}else{
	  		WST.showHide(1,'#close');
	  	}
	});
	form.on('switch(isCryptPwd)', function(data){
	  	if(this.checked){
	  		WST.showHide(1,'.pwdCryptKeyTr')
	  	}else{
	  		WST.showHide(0,'.pwdCryptKeyTr')
	  	}
	});
    var element = layui.element;
	element.on('tab(msgTab)', function(data){
	   if(data.index==3)initUploads();
	   if($(this).attr('isApp')==1 && !this.appLogo){
		   	this.appLogo=true;
			_initUpload('appLogo');
	   }
	});
});
var isInitUpload = false;
function initUploads(){
	if(isInitUpload)return;
	var uploads = ['watermarkFile','mallLogo','shopLogo','shopAdtop','userLogo','goodsLogo','goodsPosterBg'],key;
	for(var i=0;i<uploads.length;i++){
		key = uploads[i];
		_initUpload(key);
	}
	isInitUpload = true;
}
function _initUpload(key){
	WST.upload({
		k:key,
		pick:'#'+key+"Picker",
		formData: {dir:'sysconfigs'},
		accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
		callback:function(f){
			var json = WST.toAdminJson(f);
			if(json.status==1){
				$('#'+this.k+'Msg').empty().hide();
				$('#'+this.k+'Prevw').attr('src',WST.conf.RESOURCE_PATH+'/'+json.savePath+json.name);
				$('#'+this.k).val(json.savePath+json.name);
			}
		},
		progress:function(rate){
			$('#'+this.k+'Msg').show().html('已上传'+rate+"%");
		}
	});
}

function edit(){
	if(!WST.GRANT.SCPZ_02)return;
	var params = WST.getParams('.ipt');
	if(params.loginType==''){
		WST.msg('电脑端至少选择一种登录方式',{icon:1});
		return;
	}
	if(params.mobileLoginType==''){
		WST.msg('移动端至少选择一种登录方式',{icon:1});
		return;
	}
	if(params.registerType==''){
		WST.msg('电脑端至少选择一种注册方式',{icon:1});
		return;
	}
	if(params.mobileRegisterType==''){
		WST.msg('移动端至少选择一种注册方式',{icon:1});
		return;
	}
	if(params.mailOpen==1){
		var fieldObj = ['mailSmtp','mailPort','mailAddress','mailUserName','mailPassword','mailSendTitle'];
		var fieldTip = ['请填写SMTP服务器','SMTP端口','SMTP发件人邮箱','SMTP登录账号','SMTP登录密码','发件人名称'];
		for(var i=0;i<fieldObj.length;i++){
			if(params[fieldObj[i]]==''){
				WST.msg(fieldTip[i],{icon:1});
				return;
			}
		}
	}
	var loading = WST.msg('正在保存数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/sysconfigs/edit'),params,function(data,textStatus){
          layer.close(loading);
          var json = WST.toAdminJson(data);
          if(json.status==1){
        	  WST.msg(json.msg,{icon:1});
          }
   });
}


$(function(){
	$('#watermarkColor').colpick({
	layout:'hex',
	submit:1,
	colorScheme:'dark',
	onChange:function(hsb,hex,rgb,el,bySetColor) {
		$(el).css('border-color','#'+hex);
	},
	onSubmit:function(hsb,hex,rgb,el,bySetColor){
		if(!bySetColor) $(el).val('#'+hex);
		$(el).colpickHide();
	}
	}).keyup(function(){
		$(this).colpickSetColor(this.value);
		$(this).colpickHide();
	});

});