window.xzyn = {
    // 	/*===============ajax 封装 ===========*/
    // 	/*2.给全局对象定义一个属性*/
	ajax: function(options){
		/*3.options对象传参 */
		/*如果用户不传或者传的不是对象停止执行*/
		if(!options || typeof options != 'object') return false;
		/*4.默认参数的处理*/
		var type = options.type == 'post' ? 'post' : 'get';
		var url = options.url || location.pathname;
		var async = options.async === false ? false : true;
		var data = options.data || {};
		/* 对象形式的数据 需要转换成键值对的数据字符串  XHR对象需要 */
		var data2str = '';
		for(var key in data){
			data2str += key+'='+data[key]+'&';
		}
		/*需要去掉最后一个&*/
		data2str = data2str && data2str.slice(0,-1);
		/*请求发送之前*/
		if(options.beforeSend){
			var flag = options.beforeSend();
			if(flag === false){
				return false;
			}
		}
		/*5.ajax 编程*/
		/*5.1 初始化对象*/
		var xhr = new XMLHttpRequest();
		/*5.2 设置请求行*/
		xhr.open(type,type == 'get' ? (url+'?'+data2str) : url,async);
		/*5.3 设置请求头*/
		if(type == 'post'){
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
            if( options.headers ){
                for (var k in options.headers) {
                    xhr.setRequestHeader(k, options.headers[k]);
                }
            }
		}
		/*5.4 设置请求主体 发送*/
		xhr.send(type == 'get'?null:data2str);
		xhr.onreadystatechange = function(){
			/*5.5 通讯完成*/
			if(xhr.readyState == 4){
				/*5.6 成功的*/
				if(xhr.status == 200){
					/*需要考虑问题 数据格式问题*/
					/*1.怎么确定后台返回的数据格式？*/
					/*2.后台有写接口的时候 规范  xml application/xml json application/json*/
					var result = null;
					var contentType = xhr.getResponseHeader('Content-Type');
					/*3.如果有xml 就是xml格式数据*/
					if(contentType.indexOf('xml') > -1){
						result = xhr.responseXML;
					}
					/*4.如果有json 就是json格式数据*/
					else if(contentType.indexOf('json') > -1){
						result = JSON.parse(xhr.responseText);
					}
					/*5.如果有标识  普通文本*/
					else{
						result = xhr.responseText;
					}
					/*调用成功回调函数  把数据传递过去*/
					options.success && options.success(result);
				}
				/*5.7 失败的*/
				else{
					/*调用失败回调函数*/
					options.error && options.error({status:xhr.status,statusText:xhr.statusText});
				}
			}
		};
	},
	/*get*/
	get: function(options){
		options.type = 'get';
		this.ajax(options);
	},
	/*post*/
	post: function(options){
		options.type = 'post';
		this.ajax(options);
	},
	getCookie:function (cname) {	//获取cookie
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i = 0; i < ca.length; i++) {
	        var c = ca[i].trim();
	        if(c.indexOf(name) == 0) return c.substring(name.length, c.length);
	    }
	    return "";
	},
	setCookie:function (cname, cvalue, exdays) {	//设置cookie setCookie(名称,值,过期时间)
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays * 1000));
	    var expires = "expires=" + d.toGMTString();
	    document.cookie = cname + "=" + cvalue + "; " + expires;
	},
	iconArr:function (){	//获取icon图标数组
    	var iconlistarr = [];
	    if (iconlistarr.length == 0 ) {
		    $.get("/static/xzyn/css/variables.less", function (ret) {
		        var exp = /fa-var-(.*):/ig;
		        var result;
		        while ((result = exp.exec(ret)) != null) {
		            iconlistarr.push('fa-' + result[1]);
		       	}
		   });
		}
	    return iconlistarr;
	},
	daojishi:function (time=10,demo='',text='剩余'){		//倒计时
    	var timer = null;
       	var num=time;
       	var tests = $(demo).text();
		$(demo).attr("disabled",'disabled');
		var href = $(demo).attr('href');
		if( href != null){
			$(demo).removeAttr('href',false);
		}
       	timer = setInterval(function() {
            $(demo).text(text+'('+num+')秒');// 你倒计时显示的地方元素
            num--;
            if(num < 0){
                clearInterval(timer);
                $(demo).attr("disabled", false);
                if( href != null){
					$(demo).attr('href',href);
				}
				$(demo).text(tests);
            }
        },1000);
    },
    x_delimg:function(_this){	//删除图片
    	var fid = _this.data('fid');
		var imgurl = $('#'+fid).find('img').attr('src');
		var n = imgurl.match(/uploads/);
    	if( n == null ){
    		$('#'+fid).remove();	//删除元素
    		xzyn.msg('文件删除成功');
    	}else{	//删除图片
			$.ajax({
	    	    url: "/index/uploads/delimg", //请求url
	    	    type: "post",  //请求的类型
	    	    dataType: "json",  //数据类型
	    	    data: {'imgurl':imgurl}, //发送到服务器的数据
	    	    success:function(data) { //成功后执行
	    	        if(data.code == 200){
	    	        	xzyn.msg(data.msg);
	    	        	$('#'+fid).remove();	//删除元素
	    	        }else{
	    	        	xzyn.msg(data.msg);
	    	        }
	    	    },
	    	    error:function(data) { //失败后执行
	    	        console.log(data);
	    	    }
	    	});
    	}
    },
	/*加载一批js css文件，_files:文件路径数组,可包括js,css,less文件,succes:加载成功回调函数*/
   	load:function(_files,succes){
	    var FileArray = [];
	    if(typeof _files === "object"){
	      	FileArray = _files;
	    }else{
	      	/*如果文件列表是字符串，则用,切分成数组*/
	      	if(typeof _files === "string"){
	        	FileArray = _files.split(",");
	      	}
	    }
	    if(FileArray != null && FileArray.length > 0){
	      	var LoadedCount = 0;
	      	for(var i = 0; i < FileArray.length; i++){
	        	loadFile(FileArray[i],function(){
		          	LoadedCount++;
		          	if(LoadedCount == FileArray.length){
		            	succes();
		          	}
	        	})
	      	}
	    }
	    /*加载JS文件,url:文件路径,success:加载成功回调函数*/
	    function loadFile(url, success) {
		    var ThisType = GetFileType(url);
		    if (!FileIsExt(ThisType,url)) {
		        var fileObj = null;
		        if(ThisType == ".js"){
		          	fileObj = document.createElement('script');
		          	fileObj.src = url;
		        }else if(ThisType == ".css"){
		          	fileObj = document.createElement('link');
		          	fileObj.href = url;
		          	fileObj.type = "text/css";
		          	fileObj.rel = "stylesheet";
		        }else if(ThisType == ".less"){
		          	fileObj = document.createElement('link');
		          	fileObj.href = url;
		          	fileObj.type = "text/css";
		          	fileObj.rel = "stylesheet/less";
		        }
		        success = success || function(){};
		        fileObj.onload = fileObj.onreadystatechange = function() {
		           	if (!this.readyState || 'loaded' === this.readyState || 'complete' === this.readyState) {
						success();
		          	}
		        }
		        document.getElementsByTagName('head')[0].appendChild(fileObj);
		    }else{
		        success();
		    }
	    }
	    /*获取文件类型,后缀名，小写*/
	    function GetFileType(url){
	      	if(url != null && url.length > 0){
	        	return url.substr(url.lastIndexOf(".")).toLowerCase();
	      	}
	      	return "";
	    }
	    /*文件是否已加载*/
	    function FileIsExt(type,_url){
			if(type == '.js'){
				var script_arr = document.getElementsByTagName('script');
				var lenss = script_arr.length;
		        for (var r = 0; r < lenss; r++) {
		          	if (script_arr[r].getAttribute("src") == _url) {
						return true;
		          	}
		        }
			}else{
				var link_arr = document.getElementsByTagName('link');
				var lenss = link_arr.length;
		        for (var r = 0; r < lenss; r++) {
		          	if (link_arr[r].getAttribute("href") == _url) {
						return true;
		          	}
		        }
			}
			return false;
	    }
	},
	  // 加载动画
	loader:function(){
		var fdiv = document.createElement("div");
		if(document.getElementsByClassName("x-loader").length > 0 ){
			document.getElementsByTagName("body")[0].removeChild(document.getElementsByClassName("x-loader")[0]);
		}else{
			var att=document.createAttribute("class");
			att.value = 'x-loader';
			fdiv.setAttributeNode(att);
			document.getElementsByTagName("body")[0].appendChild(fdiv);
			for (var i = 1; i < 6; i++) {
				var div = document.createElement("div");
				var attr = document.createAttribute("class");
				attr.value = 'rect'+i;
				div.setAttributeNode(attr);
				fdiv.appendChild(div);
			}
		}
		console.log(document.getElementsByClassName("x-loader").length);
	}



}





//jquery扩展
// $.fn.extend({
//     x_open_dh: function (animationName,animationOutName='',xopen,xback) {	//添加动画
// 	    var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
//     	if(!animationOutName){	//点击添加动画
// 	        $(this).addClass('donghua ' + animationName).one(animationEnd, function() {
// 	            $(this).removeClass('donghua ' + animationName);
// 	        });
//     	}else{
//     		if($(this).css('display') == 'none'){	// 显示/隐藏
//     			$(this).css('display','block');
//     			$(this).addClass('donghua ' + animationName).one(animationEnd, function() {
// 		            $(this).removeClass('donghua ' + animationName);
// 		            if(xopen){
// 						xopen();
// 					}
// 		        });
//     		}else{
//     			$(this).addClass('donghua ' + animationOutName).one(animationEnd, function() {
// 		            $(this).removeClass('donghua ' + animationOutName);
//     				$(this).css('display','none');
// 					if(xback){
// 						xback();
// 					}
// 		        });
//     		}
//     	}
//     }
// });



