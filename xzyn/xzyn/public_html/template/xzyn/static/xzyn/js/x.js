/*
x.js 0.0.1
作者 : 戏中有你
官网 : http://www.xzyn.cn
*/
var x = (function (selector, undefined) {
	var isId = /^#([\w-]+)$/, isClass = /^\.([\w-]+)$/, isTag = /^[\w-]+$/;
	var hbase = function (selector) {
		if (!selector) { return addFuns(new Array(document)); }
		if (typeof (selector) == 'string') { selector = selector.trim(); return getDoms(selector); }
		if (typeof (selector) == 'object') { return addFuns(new Array(selector)); }
		return null;
	}
	var getDoms = function (selector) {
		var selectorArray = selector.split(' ');
		if (selectorArray.length < 2) {
			if (isId.test(selector)) {
				var dom = document.getElementById(RegExp.$1);
				var doms = new Array(); if (dom) { doms[0] = dom; }
				return addFuns(doms);
			}
			if (isClass.test(selector)) {
				var doms = document.getElementsByClassName(RegExp.$1);
				return addFuns(doms);
			}
			if (isTag.test(selector)) {
				var doms = document.getElementsByTagName(selector);
				return addFuns(doms);
			}
		} else {
			var lastDoms = x(selectorArray[0]);
			for (var i = 1; i < selectorArray.length; i++) { lastDoms = lastDoms.find(selectorArray[i]); }
			return lastDoms;
		}
		return addFuns(null);
	}
	var addFuns = function (doms) {
		if (!doms) { doms = new Array(); } if (!doms[0]) { doms = new Array(); }
		var reObj = { dom: doms, length: doms.length }; reObj.__proto__ = hExtends; return reObj;
	}
	var hExtends = {
		val: function (vars) {
			if (typeof (vars) != 'undefined') { for (var i = 0; i < this.length; i++) { this.dom[i].value = vars; } return this; }
			return this.dom[0].value;
		},
		hasClass: function (cls) {
			if (this.length != 1) { return false; }
			if (this.dom[0].className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'))) {
				return true;
			}
			return false;
		},
		addClass: function (cls) {
			if (this.length < 1) { return this; }
			for (var i = 0; i < this.length; i++) {
				if (!this.dom[i].className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'))) { this.dom[i].className += " " + cls; }
			}
			return this;
		},
		removeClass: function (cls) {
			if (this.length < 1) { return this; } var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
			for (var i = 0; i < this.length; i++) { this.dom[i].className = this.dom[i].className.replace(reg, ' '); }
			return this;
		},
		hide: function () {
			if (this.length < 1) { return this; }
			for (var i = 0; i < this.length; i++) { this.dom[i].style.display = 'none'; }
			return this;
		},
		show: function () {
			if (this.length < 1) { return this; }
			for (var i = 0; i < this.length; i++) { this.dom[i].style.display = 'block'; } return this;
		},
		each: function (callBack) { for (var i = 0; i < this.length; i++) { this.dom[i].index = i; callBack(this.dom[i]); } },
		size: function () { return this.length; },
		html: function (html) {
			if (this.length < 1) { return this; }
			if (typeof (html) != 'undefined') { for (var i = 0; i < this.length; i++) { this.dom[i].innerHTML = html; } return this; }
			return this.dom[0].innerHTML;
		},
		find: function (selector) {
			if (this.length != 1) { return this; }
			if (isId.test(selector)) { var dom = document.getElementById(RegExp.$1); var doms = new Array(); if (dom) { doms[0] = dom; } return addFuns(doms); }
			if (isClass.test(selector)) { var doms = this.dom[0].getElementsByClassName(RegExp.$1); return addFuns(doms); }
			if (isTag.test(selector)) { var doms = this.dom[0].getElementsByTagName(selector); return addFuns(doms); }
		},
		eq: function (index) { return addFuns(new Array(this.dom[index])); },
		last: function () { return addFuns(new Array(this.dom[this.length - 1])); },
		first: function () { return addFuns(new Array(this.dom[0])); },
		next: function () { return addFuns(new Array(this.dom[0].nextElementSibling || this.dom[0].nextSibling)); },
		parent: function () { return addFuns(new Array(this.dom[0].parentNode)); },
		siblings: function () {
			if (!this.dom[0]) { return addFuns(); }
			var nodes = [], startNode = this.dom[0], nextNode, preNode;
			var currentNode = startNode;
			while (nextNode = currentNode.nextElementSibling) { nodes.push(nextNode); currentNode = nextNode; }
			currentNode = startNode;
			while (preNode = currentNode.previousElementSibling) { nodes.push(preNode); currentNode = preNode; }
			return addFuns(nodes);
		},
		index: function () {
			if (this.length != 1) { return null; }
			var nodes = [], startNode = this.dom[0], preNode;
			while (preNode = startNode.previousElementSibling) { nodes.push(preNode); startNode = preNode; }
			return nodes.length;
		},
		css: function (cssObj) {
			if (this.length < 1) { return this; }
			for (var i = 0; i < this.length; i++) { var styleObj = this.dom[i].style; for (var k in cssObj) { eval('styleObj.' + k + ' = "' + cssObj[k] + '";'); } } return this;
		},
		clone: function () { if (this.length < 1) { return this; } var nodeClone = this.dom[0].cloneNode(true); return addFuns(new Array(nodeClone)); },
		appendTo: function (parentObj) {
			if (this.length < 1) { return this; }
			if (typeof (parentObj) == 'object') { parentObj.dom[0].appendChild(this.dom[0]); } else if (typeof (parentObj) == 'string') {
				var parentDom = x(parentObj); if (parentDom.length >= 1) { parentDom.dom[0].appendChild(this.dom[0]); }
			}
		},
		prependTo: function (parentObj) {
			if (this.length < 1) { return this; }
			if (typeof (parentObj) == 'object') { parentObj.dom[0].insertBefore(this.dom[0], parentObj.dom[0].firstChild); }
			else if (typeof (parentObj) == 'string') {
				var parentDom = x(parentObj); if (parentDom.length >= 1) { parentDom.dom[0].insertBefore(this.dom[0], parentDom.dom[0].firstChild); }
			}
		},
		animate: function (animateObj, timer, callBack) {
			if (this.length != 1) { return this; } if (!timer) { timer = 300; }
			var interVal = null, styleObj = this.dom[0].style, i = 0, start = {};
			if (this.dom[0].getAttribute('isAnimate')) { return false; }
			this.dom[0].setAttribute('isAnimate', 'Yes');
			var thisObj = this, styleVal = 0;
			for (var k in animateObj) {
				if (k.indexOf('scroll') != -1) {
					eval('styleVal = thisObj.dom[0].' + k);
					eval('start.' + k + ' = Number(styleVal);');
				} else {
					eval('styleVal = styleObj.' + k);
					if (!styleVal) { styleVal = 0; } else { styleVal = styleVal.toLowerCase(); styleVal = styleVal.replace(/px|%/, ''); }
					eval('start.' + k + ' = Number(styleVal);');
				}
			}
			interVal = setInterval(function () {
				for (var k in animateObj) {
					eval('var startVal = start.' + k + ';');
					var endVal = animateObj[k];
					if (k.indexOf('scroll') != -1) {
						console.log(endVal);
						if (startVal != endVal) { eval('thisObj.dom[0].' + k + ' = "' + (startVal + (endVal - startVal) * i / timer) + '";'); }
					} else {
						endVal = endVal.toString();
						if (endVal.indexOf('px') != -1) {
							endVal = Number(endVal.replace('px', ''));
							if (startVal != animateObj[k]) { eval('styleObj.' + k + ' = "' + (startVal + (endVal - startVal) * i / timer) + 'px";'); }
						} else if (endVal.indexOf('%') != -1) {
							endVal = Number(endVal.replace('%', ''));
							if (startVal != animateObj[k]) { eval('styleObj.' + k + ' = "' + (startVal + (endVal - startVal) * i / timer) + '%";'); }
						} else {

							if (startVal != animateObj[k]) { eval('styleObj.' + k + ' = "' + (startVal + (endVal - startVal) * i / timer) + '";'); }
						}
					}
				}
				if (i >= timer) { clearInterval(interVal); thisObj.dom[0].removeAttribute('isAnimate'); if (callBack) { callBack(); } }; i += 20;
			}, 20);
		},
		remove: function () { if (this.length < 1) { return this; } for (var i = 0; i < this.length; i++) { this.dom[0].parentNode.removeChild(this.dom[0]); } },
		attr: function (attrName, val) {
			if (this.length < 1) { return this; }
			if (typeof (val) != 'undefined') { for (var i = 0; i < this.length; i++) { this.dom[i].setAttribute(attrName, val); } return this; }
			return this.dom[0].getAttribute(attrName);
		},
		removeAttr: function (attrName) {
			if (this.length < 1) { return this; } for (var i = 0; i < this.length; i++) { this.dom[i].removeAttribute(attrName); }
			return this;
		},
		height: function (isOffset) {
			if (this.length != 1) { return 0; }
			if (isOffset) { return this.dom[0].offsetHeight; } return this.dom[0].clientHeight;
		},
		width: function (isOffset) {
			if (this.length != 1) { return 0; }
			if (isOffset) { return this.dom[0].offsetWidth; } return this.dom[0].clientWidth;
		},
		offset: function () { if (this.length != 1) { return { left: 0, top: 0 }; } return x.offset(this.dom[0]); },
		isShow: function () {
			if (this.length != 1) { return true; }
			if (this.dom[0].currentStyle) {
				var showRes = this.dom[0].currentStyle.display;
			} else {
				var showRes = getComputedStyle(this.dom[0], null).display;
			}
			if (showRes == 'none') { return false; } return true;
		},
		tap: function (callBack) {
			if (this.length < 1) { return true; }
			this.dom[0].addEventListener('tap', callBack);
		},
		click: function (callBack) {
			for(var i = 0; i < this.length; i++){
				if(callBack == undefined){hbase(this.dom[i]).trigger('click');}
				this.dom[i].addEventListener('click', callBack);
			}
		},
		scroll: function (callBack) {
			for(var i = 0; i < this.length; i++){
				this.dom[i].addEventListener('scroll', callBack);
			}
		}

	}
	hbase.extend = function (funName, fun) { eval('hExtends.' + funName + ' = fun;'); }
	hbase.offset = function (e) {
		var offset = { left: 0, top: 0 }; offset.left = e.offsetLeft; offset.top = e.offsetTop;
		while (e = e.offsetParent) { offset.top += e.offsetTop; offset.left += e.offsetLeft; } return offset;
	}
	hbase.scrollTop = function (val) { document.body.scrollTop = val; };
	hbase.winInfo = function () {
		var winInfo = { height: 0, width: 0, scrollTop: 0 };
		if (window.innerHeight) { winInfo.height = window.innerHeight; } else if ((document.body) && (document.body.clientHeight)) { winInfo.height = document.body.clientHeight; }
		if (window.innerWidth) { winInfo.width = window.innerWidth; } else if ((document.body) && (document.body.clientWidth)) { winInfo.width = document.body.clientWidth; }
		if (document.documentElement && document.documentElement.scrollTop) { winInfo.scrollTop = document.documentElement.scrollTop; } else if (document.body) { winInfo.scrollTop = document.body.scrollTop; }
		return winInfo;
	}
	hbase.getItem = function (keyName) { return plus.storage.getItem(keyName); }
	hbase.setItem = function (keyName, val) { plus.storage.setItem(keyName, val); }
	hbase.removeItem = function (keyName) { plus.storage.removeItem(keyName); }
	hbase.clearItem = function (keyName) { plus.storage.clear(); }
	hbase.currentView = function () { return plus.webview.currentWebview(); }
	hbase.indexView = function () { return plus.webview.getLaunchWebview(); }
	hbase.topView = function () { return plus.webview.getTopWebview(); }
	hbase.device = function () {
		return { imei: plus.device.imei, imsi: plus.device.imsi, model: plus.device.model, vendor: plus.device.vendor, uuid: plus.device.uuid };
	};
	hbase.version = function () { return plus.runtime.version; }
    hbase.createDom = function (domTag) { return document.createElement(domTag); };
    // 	/*===============ajax 封装 ===========*/
    // 	/*2.给全局对象定义一个属性*/
	hbase.ajax = function(options){
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
		/*5.ajax 编程*/
		/*5.1 初始化对象*/
		var xhr = new XMLHttpRequest();
		/*请求发送之前*/
		if(options.beforeSend){
			var flag = options.beforeSend();
			if(flag === false){
				return false;
			}
		}
		/*5.2 设置请求行*/
		xhr.open(type,type == 'get' ? (url+'?'+data2str) : url,async);
		/*5.3 设置请求头*/
		// if(type == 'post'){
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
            if( options.headers ){
                for (var k in options.headers) {
                    xhr.setRequestHeader(k, options.headers[k]);
                }
            }
		// }
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
    };
    hbase.ready = function( fn ) {
        if ( "function" !== typeof fn ) {
            return;
        }
        //添加监听事件，当所有的DOM加载完成时触发。
        if ( document.addEventListener ) {
            // Use the handy event callback
            document.addEventListener( "DOMContentLoaded", fn, false );
        // If IE event model is used
        } else if ( document.attachEvent ) {
            // maybe late but safe also for iframes
            document.attachEvent( "onreadystatechange", fn );
        }
    };
	/*加载一批js css文件，_files:文件路径数组,可包括js,css,less文件,succes:加载成功回调函数*/
    hbase.load = function(_files,succes){
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
	};
	// 临时存储
	hbase.get_session = function(key){	// 获取
		var info_obj = JSON.parse( window.sessionStorage.getItem(key) );
		return info_obj;
	};
	hbase.set_session = function(key,val){	//存储
		window.sessionStorage.setItem(key,JSON.stringify(val));
	};
	hbase.del_session = function(key,all = ''){	// 删除
		if( all ){
			window.sessionStorage.clear();
		}else{
			window.sessionStorage.removeItem(key);
		}
	};
	// 本地存储
	hbase.get_lsession = function(key){	// 获取
		var info_obj = JSON.parse( window.localStorage.getItem(key) );
		return info_obj;
	};
	hbase.set_lsession = function(key,val){	//存储
		window.localStorage.setItem(key,JSON.stringify(val));
	};
	hbase.del_lsession = function(key = ''){	// 删除
		if( key ){
			window.localStorage.removeItem(key);
		}else{
			window.localStorage.clear();
		}
	};

	return hbase;
})(document);