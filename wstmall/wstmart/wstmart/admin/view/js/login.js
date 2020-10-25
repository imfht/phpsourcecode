function login(){
    var public_key=$('#token').val();
    var exponent="10001";
    var res = '';
    if(WST.conf.IS_CRYPT=='1'){
   	    var rsa = new RSAKey();
        rsa.setPublic(public_key, exponent);
        var res = rsa.encrypt($.trim($('#loginPwd').val()));
    }else{
        res = $.trim($('#loginPwd').val());
    }
    var loading = WST.msg('加载中', {icon: 16,time:60000,offset: '200px'});
	var params = WST.getParams('.ipt');
	params.loginPwd = res;
	$.post(WST.U('admin/index/checkLogin'),params,function(data,textStatus){
		layer.close(loading);
		var json = WST.toAdminJson(data);
		if(json.status=='1'){
			WST.msg("登录成功",{icon:1,offset: '200px'},function(){
				if(parent){
					parent.location.href=WST.U('admin/index/index');;
				}else{
                    location.href=WST.U('admin/index/index');
				}
			});
		}else{
			getVerify('#verifyImg');
			WST.msg(json.msg,{icon:2,offset: '200px'});			
		}
	});
}
var getVerify = function(img){
	$(img).attr('src',WST.U('admin/index/getVerify','rnd='+Math.random()));
	$("#verifyCode").val("");
}
$(document).keypress(function(e) { 
	if(e.which == 13) {  
		login();  
	} 
}); 
