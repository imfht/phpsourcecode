var WST = WST?WST:{};
WST.wxv = '2.1.1_0220';
WST.toJson = function(str,notLimit){
	var json = {};
	if(str){
	try{
		if(typeof(str )=="object"){
			json = str;
		}else{
			json = eval("("+str+")");
		}
		if(!notLimit){
			if(json.status && json.status=='-999'){
				WST.inLogin();
			}
		}
	}catch(e){
		alert("系统发生错误:"+e.getMessage);
		json = {};
	}
	return json;
	}else{
		return;
	}
}
//登录
WST.inLogin = function(){
	var urla = window.location.href;
	$.post(WST.U('mobile/index/sessionAddress'),{url:urla},function(data,textStatus){});
	var url = WST.U('mobile/users/login',true);
	window.location.href = url;
}
//底部的tab
WST.initFooter = function(tab){
    var homeImage = (tab=='home') ? 'home-active' : 'home';
    var categoryImage = (tab=='category') ? 'category-active' : 'category';
    var cartImage = (tab=='cart') ? 'cart-active' : 'cart';
    var followImage = (tab=='brand') ? 'follow-active' : 'follow';
    var usersImage = (tab=='user') ? 'user-active' : 'user';
    $('#home').append('<span class="icon '+homeImage+'"></span><span class="'+homeImage+'-word">首页</span>');
    $('#category').append('<span class="icon '+categoryImage+'"></span><span class="'+categoryImage+'-word">分类</span>');
    $('#cart').prepend('<span class="icon '+cartImage+'"></span><span class="'+cartImage+'-word">购物车</span>');
    $('#follow').append('<span class="icon '+followImage+'"></span><span class="'+followImage+'-word">关注</span>');
    $('#user').append('<span class="icon '+usersImage+'"></span><span class="'+usersImage+'-word">我的</span>');
}
//变换选中框的状态
WST.changeIconStatus = function (obj, toggle, status){
    if(toggle==1){
        if( obj.attr('class').indexOf('ui-icon-unchecked-s') > -1 ){
            obj.removeClass('ui-icon-unchecked-s').addClass('ui-icon-success-block wst-active');
        }else{
            obj.removeClass('ui-icon-success-block wst-active').addClass('ui-icon-unchecked-s');
        }
    }else if(toggle==2){
        if(status == 'wst-active'){
            obj.removeClass('ui-icon-unchecked-s').addClass('ui-icon-success-block wst-active');
        }else{
            obj.removeClass('ui-icon-success-block wst-active').addClass('ui-icon-unchecked-s');
        }
    }
}
WST.changeIptNum = function(diffNum,iptId,id,func){
	var suffix = (id)?"_"+id:"";
	var iptElem = $(iptId+suffix);
	var minVal = parseInt(iptElem.attr('data-min'),10);
	var maxVal = parseInt(iptElem.attr('data-max'),10);
	var num = parseInt(iptElem.val(),10);
	num = num?num:1;
	num = num + diffNum;
	if(maxVal<=num)num=maxVal;
	if(num<=minVal)num=minVal;
	if(num==0)num=1;
	iptElem.val(num);
	if(suffix!='')WST.changeCartGoods(id,num,-1);
	if(func){
		var fn = window[func];
		fn();
	}
}
WST.changeCartGoods = function(id,buyNum,isCheck){
	$.post(WST.U('mobile/carts/changeCartGoods'),{id:id,isCheck:isCheck,buyNum:buyNum,rnd:Math.random()},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.status!=1){
	    	 WST.msg(json.msg,'info');
	     }
	});
}
// 批量修改购物车状态
WST.batchChangeCartGoods = function(ids,isCheck){
	$.post(WST.U('mobile/carts/batchChangeCartGoods'),{ids:ids,isCheck:isCheck},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.status!=1){
	    	 WST.msg(json.msg,'info');
	     }
	});
}
//商品主页
WST.intoGoods = function(id){
	location.href = WST.U('mobile/goods/detail','goodsId='+id,true);
};
//店铺主页
WST.intoShops = function(id){
	location.href = WST.U('mobile/shops/index','shopId='+id,true);
};
//首页
WST.intoIndex = function(){
	location.href = WST.U('mobile/index/index',true);
};
//搜索
WST.searchPage = function(type,state){
	if(state==1){
		$("#wst-"+type+"-search").show();
		$('#goodsTab').hide();
	}else{
		$("#wst-"+type+"-search").hide();
		$('#goodsTab').show();
	}
};
WST.search = function(type){
	var data = $('#wst-search').val();
	if(type==1){
		location.href = WST.U('mobile/shops/shopstreet','keyword='+data,true);//店铺
	}else if(type==0){
		location.href = WST.U('mobile/goods/search','keyword='+data,true);//商品
	}else if(type==2){
		var shopId = $('#shopId').val();
		location.href = WST.U('mobile/shops/goods','goodsName='+data+'&shopId='+shopId,true);//店铺商品
	}
};
//关注
WST.favorites = function(sId,type){
    $.post(WST.U('mobile/favorites/add'),{id:sId,type:type},function(data){
        var json = WST.toJson(data);
        if(json.status==1){
            WST.msg(json.msg,'success');
            if(type==1){
                $('#fStatus').html('已关注');
                $('#fBtn').attr('onclick','WST.cancelFavorite('+json.data.fId+',1)');
                $('.j-shopfollow').addClass('follow');
                var followNum = parseInt($('#followNum').val())+1;
				$('#followNum').val(followNum);
                $('#followText').html('收藏'+ followNum);
            }else{
            	$('.imgfollow').removeClass('nofollow').addClass('follow');
            	$('.imgfollow').attr('onclick','WST.cancelFavorite('+json.data.fId+',0)');
            }
        }else{
            WST.msg(json.msg,'info');
        }
    })
}
// 取消关注
WST.cancelFavorite = function(fId,type){
    $.post(WST.U('mobile/favorites/cancel'),{id:fId,type:type},function(data){
    var json = WST.toJson(data);
    if(json.status==1){
      WST.msg(json.msg,'success');
      if(type==1){
          $('#fStatus').html('关注店铺');
          $('#fBtn').attr('onclick','WST.favorites('+$('#shopId').val()+',1)');
          $('.j-shopfollow').removeClass('follow');
          $('#followNum').val(parseInt($('#followNum').val())-1);
		  $('#followText').html('收藏店铺');
      }else{
    	  $('.imgfollow').removeClass('follow').addClass('nofollow');
    	  $('.imgfollow').attr('onclick','WST.favorites('+$('#goodsId').val()+',0)');
      }
    }else{
      WST.msg(json.msg,'info');
    }
  });
}
//刷新验证码
WST.getVerify = function(id){
    $(id).attr('src',WST.U('mobile/index/getVerify','rnd='+Math.random()));
}
//返回当前页面高度
WST.pageHeight = function(){
	if(WST.checkBrowser().msie){ 
		return document.compatMode == "CSS1Compat"? document.documentElement.clientHeight : 
		document.body.clientHeight; 
	}else{ 
		return self.innerHeight; 
	} 
};
//返回当前页面宽度 
WST.pageWidth = function(){ 
	if(WST.checkBrowser().msie){ 
		return document.compatMode == "CSS1Compat"? document.documentElement.clientWidth : 
		document.body.clientWidth; 
	}else{ 
		return self.innerWidth; 
	} 
};
WST.checkBrowser = function(){
	return {
		mozilla : /firefox/.test(navigator.userAgent.toLowerCase()),
		webkit : /webkit/.test(navigator.userAgent.toLowerCase()), 
	    opera : /opera/.test(navigator.userAgent.toLowerCase()), 
	    msie : /msie/.test(navigator.userAgent.toLowerCase())
	}
}
//只能輸入數字
WST.isNumberKey = function(evt){
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode > 31 && (charCode < 48 || charCode > 57)){
		return false;
	}else{		
		return true;
	}
}
WST.isChinese = function(obj,isReplace){
 	var pattern = /[\u4E00-\u9FA5]|[\uFE30-\uFFA0]/i
 	if(pattern.test(obj.value)){
 		if(isReplace)obj.value=obj.value.replace(/[\u4E00-\u9FA5]|[\uFE30-\uFFA0]/ig,"");
 		return true;
 	}
 	return false;
}
//适应图片大小正方形
WST.imgAdapt = function(name){
	var w = $('.'+name).width();
	$('.'+name).css({"width": w+"px","height": w+"px"});
	$('.'+name+' a').css({"width": w+"px","height": w+"px"});
	$('.'+name+' a img').css({"max-width": w+"px","max-height": w+"px"});
}
//显示隐藏
WST.showHide = function(t,str){
	var s = str.split(',');
	if(t){
		for(var i=0;i<s.length;i++){
		   $(s[i]).show();
		}
	}else{
		for(var i=0;i<s.length;i++){
		   $(s[i]).hide();
		}
	}
	s = null;
}
/**
 * 提示信息
 * @param content   	内容
 * @param type          info/普通,success/成功,warn/错误
 * @param stayTime      显示时间
 */
WST.msg = function(content,type,stayTime){
	if(!stayTime){
		stayTime = '1200';
	}
	var el = Zepto.tips({content:content,type:type,stayTime:stayTime});
    return  el;
}
//提示对话框
WST.dialog = function(content,event,title){
	$("#wst-dialog").html(content);
	$("#wst-dialog-title").html(title);
	$("#wst-event2").attr("onclick","javascript:"+event);
	$("#wst-di-prompt").dialog("show");
}
//提示分享对话框
WST.share = function(){
	$("#wst-di-share").dialog("show");
}
/**
 * 隐藏对话框
 * @param event   	prompt/提示对话框
 * @param event   	share/提示对话框
 */
WST.dialogHide = function(event){
	$("#wst-di-"+event).dialog("hide");
}
//加载中
WST.load = function(content){
	$('#Loadl').css('display','-webkit-box');
	$('#j-Loadl').html(content);
}
WST.noload = function(){
	$('#Loadl').css('display','none');
}
//滚动到顶部
WSTrunToTop = function (){  
	currentPosition=document.documentElement.scrollTop || document.body.scrollTop; 
	currentPosition-=20;
	if(currentPosition>0){
		window.scrollTo(0,currentPosition);  
	}  
	else{  
		window.scrollTo(0,0);  
		clearInterval(timer); 
	}  
}
WST.inArray = function(str,arrs){
	if(typeof(arrs) != "object")return -1;
	for(var i=0;i<arrs.length;i++){
		if(arrs[i]==str)return i;
	}
	return -1;
}

WST.blank = function(str,defaultVal){
	if(str=='0000-00-00')str = '';
	if(str=='0000-00-00 00:00:00')str = '';
	if(!str)str = '';
	if(typeof(str)=='null')str = '';
	if(typeof(str)=='undefined')str = '';
	if(str=='' && defaultVal)str = defaultVal;
	return str;
}

/**
* 上传图片
*/
WST.upload = function(opts){
  var _opts = {};
  _opts = $.extend(_opts,{duplicate:true,auto: true,swf: WST.conf.STATIC +'/plugins/webuploader/Uploader.swf',server:WST.U('mobile/orders/uploadPic')},opts);
  var uploader = WebUploader.create(_opts);
  uploader.on('uploadSuccess', function( file,response ) {
      var json = WST.toJson(response._raw);
      if(_opts.callback)_opts.callback(json,file);
  });
  uploader.on('uploadError', function( file ) {
    if(_opts.uploadError)_opts.uploadError();
  });
  uploader.on( 'uploadProgress', function( file, percentage ) {
    percentage = percentage.toFixed(2)*100;
    if(_opts.progress)_opts.progress(percentage);
  });
    return uploader;
}

//返回键
function backPrevPage(url){
	window.location.hash = "ready";
	window.location.hash = "ok";
    setTimeout(function(){
		$(window).on('hashchange', function(e) {
			var hashName = window.location.hash.replace('#', '');
			hashName = hashName.split('&');
			if( hashName[0] == 'ready' ){
			    location.href = url;
			}
		});
    },50);
}

//图片切换
WST.replaceImg = function(v,str){
	var vs = v.split('.');
    return v.replace("."+vs[1],str+"."+vs[1]);
}

/**
 * 截取字符串
 */
WST.cutStr = function (str,len)
{
    if(!str || str=='')return '';
    var strlen = 0;
    var s = "";
    for(var i = 0;i < str.length;i++)
    {
        if(strlen >= len){
            return s + "...";
        }
        if(str.charCodeAt(i) > 128)
            strlen += 2;
        else
            strlen++;
        s += str.charAt(i);
    }
    return s;
}

$(function(){
	echo.init();//图片懒加载
    // 滚动到顶部	
    $(window).scroll(function(){
        if( $(window).scrollTop() > 200 ){
            $('#toTop').show();
        }else{
            $('#toTop').hide();
        }
    });
    $('#toTop').on('click', function() {
    	timer=setInterval("WSTrunToTop()",1);
	});
	/**
	 * 获取WSTMart基础配置
	 * @type {object}
	 */
	WST.conf = window.conf;
	/* 基础对象检测 */
	WST.conf || $.error("WSTMart基础配置没有正确加载！");
	if(WST.conf.ROUTES)WST.conf.ROUTES = eval("("+WST.conf.ROUTES+")");
	/**
	 * 解析URL
	 * @param  {string} url 被解析的URL
	 * @return {object}     解析后的数据
	 */
	WST.parse_url = function(url){
		var parse = url.match(/^(?:([a-z]+):\/\/)?([\w-]+(?:\.[\w-]+)+)?(?::(\d+))?([\w-\/]+)?(?:\?((?:\w+=[^#&=\/]*)?(?:&\w+=[^#&=\/]*)*))?(?:#([\w-]+))?$/i);
		parse || $.error("url格式不正确！");
		return {
			"scheme"   : parse[1],
			"host"     : parse[2],
			"port"     : parse[3],
			"path"     : parse[4],
			"query"    : parse[5],
			"fragment" : parse[6]
		};
	}

	WST.parse_str = function(str){
		var value = str.split("&"), vars = {}, param;
		for(var i=0;i<value.length;i++){
			param = value[i].split("=");
			vars[param[0]] = param[1];
		}
		return vars;
	}
	WST.initU = function(url,vars){
		if(typeof vars === "string"){
			vars = this.parse_str(vars);
		}
		var newUrl = WST.conf.ROUTES[url];
		if(newUrl.indexOf('>')>-1 && newUrl.indexOf('-<')>-1){
			newUrl = newUrl.replace('-<','-:').replace('>','');
		}
	    var urlparams = newUrl.match(/:(\w+(\??))/g);
	    urlparams = (urlparams==null)?[]:urlparams;
	    var tmpv = null;
		for(var v in vars){
			tmpv = ':'+v;
			if(WST.inArray(tmpv,urlparams)>-1){
				newUrl = newUrl.replace(tmpv,vars[v]);
				delete vars[v];
			}
		}
		tmpv = urlparams = null;
		if(false !== WST.conf.SUFFIX){
			newUrl += "." + WST.conf.SUFFIX;
		}
		if($.isPlainObject(vars)){
			var tmp = $.param(vars);
			if(tmp!='')newUrl += "?"+tmp;
			tmp = null;
		}
		//url = url.replace(new RegExp("%2F","gm"),"+");
		newUrl = WST.conf.APP + "/"+newUrl;
		return newUrl;
	}
	
	WST.U0 = function(url, vars){
		if(!url || url=='')return '';
		var info = this.parse_url(url), path = [], reg;
		/* 验证info */
		info.path || $.error("url格式错误！");
		url = info.path;
		/* 解析URL */
		path = url.split("/");
		path = [path.pop(), path.pop(), path.pop()].reverse();
		path[1] || $.error("WST.U(" + url + ")没有指定控制器");

		/* 解析参数 */
		if(typeof vars === "string"){
			vars = this.parse_str(vars);
		}
		/* 解析URL自带的参数 */
		info.query && $.extend(vars, this.parse_str(info.query));
		if(false !== WST.conf.SUFFIX){
			url += "." + WST.conf.SUFFIX;
		}
		if($.isPlainObject(vars)){
			var tmp = $.param(vars);
			if(tmp!='')url += "?"+tmp;
			tmp = null;
		}
		//url = url.replace(new RegExp("%2F","gm"),"+");
		url = WST.conf.APP + "/"+url;
		return url;
	}
	WST.U = function(url,vars){
		if(WST.conf.ROUTES && WST.conf.ROUTES[url]){
		    return WST.initU(url,vars);
		}else{
			return WST.U0(url, vars);
		}
	}
	WST.AU = function(url, vars){
        if(!url || url=='')return '';
        var info = this.parse_url(url);
        url = info.path;
        path = url.split("/");
        url = "addon/";
        path = [path.pop(), path.pop()].reverse();
        path[0] || $.error("WST.AU(" + url + ")没有指定控制器");
        path[1] || $.error("WST.AU(" + url + ")没有指定接口");
        url  = url + info.scheme + "-" + path.join('-');
        /* 解析参数 */
		if(typeof vars === "string"){
			vars = this.parse_str(vars);
		}
		info.query && $.extend(vars, this.parse_str(info.query));
		if(false !== WST.conf.SUFFIX){
			url += "." + WST.conf.SUFFIX;
		}
		if($.isPlainObject(vars)){
			var tmp = $.param(vars);
			if(tmp!='')url += "?"+tmp;
			tmp = null;
		}
		return WST.conf.APP + "/"+url;
	}
});

WST.location = function(callback){
    var geolocation = new qq.maps.Geolocation(WST.conf.MAP_KEY, "ShangTaoTX");
    var options = {timeout: 8000};
    geolocation.getLocation(showPosition, showErr, options);
    function showPosition(position) {
        if(typeof(callback)=='function')callback({latitude:position.lat,longitude:position.lng});
    };
    function showErr() {
        if(typeof(callback)=='function')callback({latitude:0,longitude:0});
    };
}

WST.setCookie = function(key,val,time){
	if(time>0){
		var date=new Date();
		var expiresDays=time;
		date.setTime(date.getTime()+expiresDays*24*3600*1000);
		document.cookie=key + "=" + val +";expires="+date.toGMTString();
	}else{
		document.cookie=key + "=" + val ;
	}

}

WST.getCookie = function(key){
	/*获取cookie参数*/
	var getCookie = document.cookie.replace(/[ ]/g,"");
	var arrCookie = getCookie.split(";");
	var tips;
	for(var i=0;i<arrCookie.length;i++){
		var arr=arrCookie[i].split("=");
		if(key==arr[0]){
			tips=arr[1];
			break;
		}
	}
	return tips;
}
WST.delCookie = function(key){
	var date = new Date();
	date.setTime(date.getTime()-10000); //将date设置为过去的时间
	document.cookie = key + "=v; expires =" +date.toGMTString();
}

//选中底部的自定义tab
WST.selectCustomMenuPage = function(menu){
	$(".wst-custom-menus a").each(function(idx,item){
		if($(item).attr('menu-flag') == menu){
			$(item).find('.custom-menu-select-icon').removeClass('wst-none');
			$(item).find('.custom-menu-icon').addClass('wst-none');
			$(item).find('.custom-menu-select-text').removeClass('wst-none');
			$(item).find('.custom-menu-text').addClass('wst-none');
		}else{
			$(item).find('.custom-menu-select-icon').addClass('wst-none');
			$(item).find('.custom-menu-icon').removeClass('wst-none');
			$(item).find('.custom-menu-select-text').addClass('wst-none');
			$(item).find('.custom-menu-text').removeClass('wst-none');
		}
	});
}

//底部的自定义tab跳转
WST.toCustomMenuPage = function(obj){
	var link = $(obj).attr('link');
	location.href = WST.U(link);
}

WST.getParams = function(obj){
	var params = {};
	var chk = {},s;
	$(obj).each(function(){
		if($(this)[0].type=='hidden' || $(this)[0].type=='number' || $(this)[0].type=='tel' || $(this)[0].type=='password' || $(this)[0].type=='select-one' || $(this)[0].type=='textarea' || $(this)[0].type=='text'){
			params[$(this).attr('id')] = $.trim($(this).val());
		}else if($(this)[0].type=='radio'){
			if($(this).attr('name')){
				params[$(this).attr('name')] = $('input[name='+$(this).attr('name')+']:checked').val();
			}
		}else if($(this)[0].type=='checkbox'){
			if($(this).attr('name') && !chk[$(this).attr('name')]){
				s = [];
				chk[$(this).attr('name')] = 1;
				$('input[name='+$(this).attr('name')+']:checked').each(function(){
					s.push($(this).val());
				});
				params[$(this).attr('name')] = s.join(',');
			}
		}
	});
	chk=null,s=null;
	return params;
}

//只能輸入數字和小數點
WST.isNumberdoteKey = function(evt){
	var e = evt || window.event;
	var srcElement = e.srcElement || e.target;

	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode > 31 && ((charCode < 48 || charCode > 57) && charCode!=46)){
		return false;
	}else{
		if(charCode==46){
			var s = srcElement.value;
			if(s.length==0 || s.indexOf(".")!=-1){
				return false;
			}
		}
		return true;
	}
}
WST.limitDecimal = function(obj,len){
	var s = obj.value;
	if(s.indexOf(".")>-1){
		if((s.length - s.indexOf(".")-1)>len){
			obj.value = s.substring(0,s.indexOf(".")+len+1);
		}
	}
	s = null;
}
