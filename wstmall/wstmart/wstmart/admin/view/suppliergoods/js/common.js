$(function(){

	$('.goodsImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 100,placeholder:window.conf.RESOURCE_PATH+'/'+window.conf.GOODS_LOGO});//商品默认图片
	$('.shopsImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 100,placeholder:window.conf.RESOURCE_PATH+'/'+window.conf.SHOP_LOGO});//店铺默认头像
});
$.fn.TabPanel = function(options){
	var defaults = {tab: 0}; 
	var opts = $.extend(defaults, options);
	var t = this;
	
	$(t).find('.wst-tab-nav li').click(function(){
		$(this).addClass("on").siblings().removeClass();
		var index = $(this).index();
		$(t).find('.wst-tab-content .wst-tab-item').eq(index).show().siblings().hide();
		if(opts.callback)opts.callback(index);
	});
	$(t).find('.wst-tab-nav li').eq(opts.tab).click();
}
/**
 * 去除url中指定的参数(用于分页)
 */
WST.splitURL = function(spchar){
	var url = location.href;
	var urlist = url.split("?");
	var furl = new Array();
	var fparams = new Array();
		furl.push(urlist[0]);
	if(urlist.length>1){
		var urlparam = urlist[1];
			params = urlparam.split("&");
		for(var i=0; i<params.length; i++){
			var vparam = params[i];
			var param = vparam.split("=");
			if(param[0]!=spchar){
				fparams.push(vparam);
			}
		}
		if(fparams.length>0){
			furl.push(fparams.join("&"));
		}
		
	}
	if(furl.length>1){
		return furl.join("?");
	}else{
		return furl.join("");
	}
}


WST.supplierQQ = function(val){
	if(WST.blank(val) !=''){
      return [
              '<a href="tencent://message/?uin='+val+'&Site=QQ交谈&Menu=yes">',
		      '<img border="0" src='+window.conf.__HTTP__+'wpa.qq.com/pa?p=1:'+val+':7" alt="QQ交谈" width="71" height="24" />',
		      '</a>'
		      ].join('');
	}else{
		return '';
	}
}
WST.supplierWangWang = function(val){
	if(WST.blank(val) !=''){
		return [
	           '<a target="_blank" href='+window.conf.__HTTP__+'www.taobao.com/webww/ww.php?ver=3&touid='+val+'&siteid=cntaobao&status=1&charset=utf-8">',
		       '<img border="0" src='+window.conf.__HTTP__+'amos.alicdn.com/realonline.aw?v=2&uid='+val+'&site=cntaobao&s=1&charset=utf-8" alt="和我联系" />',
	           '</a>'
		       ].join('');
	}else{
		return '';
	}
}
