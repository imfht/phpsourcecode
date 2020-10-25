/*
* Copyright (C) xiuno.com
*/

var is_ie = $.browser.msie;
var is_ie6 = $.browser.msie && $.browser.version == '6.0';
var is_ie9 = $.browser.msie && $.browser.version == '9.0';
var is_w3c = window.getSelection && !is_ie9; // 是否为标准浏览器

/*
window.onerror = function() {

}
*/

function htmlspecialchars(s) {
	s = s.replace(/</gi, "&lt;" );
	s = s.replace(/>/gi, "&gt;" );
	return s;
}

function urlencode(s) {
	s = encodeURIComponent(s);
	s = s.replace(/\-/, '%2D');
	return s;
}

function urldecode(s) {
	s = decodeURIComponent(s);
	s = s.replace(/\%2D/, '-');
	return s;
}

function nl2br(s) {
	s = s.replace(/\r\n/ig, "\n");
	s = s.replace(/\n/ig, "<br />");
	s = s.replace(/\t/ig, "&nbsp; &nbsp; &nbsp; &nbsp; ");
	return s;
}

function isset(k) {
	var t = typeof k;
	return t != 'undefined' && t != 'unknown';
}

function trace(s) {
	if(typeof console != 'undefined') {
		console.log(s);
	}
	print_r(s);
}

function print_r(arrlist, level) {
	var s = print_r_real(arrlist, 1);
	$('body').append('<div style="background: #FFFFFF; width: 90%;">'+s+'</div>');
}

function print_r_real(arrlist, level) {
	if(level > 2) {
		try {
			arrlist += '';	// toString();
			if(arrlist.length > 50) arrlist = arrlist.substr(0, 50) + '...'; 
		} catch(e) {
			arrlist = typeof arrlist;
		}
		return '<span class="grey">'+htmlspecialchars(arrlist)+'</span>';
	}
	var padding = level * 16;
	var s = '';
	var type = typeof arrlist;
	if(type == 'string' || type == 'number') {
		arrlist += '';
		if(arrlist.length > 50) arrlist = arrlist.substr(0, 50) + '...'; 
		s += htmlspecialchars(arrlist);
		return  '<span class="grey2">'+s+'</span>';
	} else if(type == 'object') {
		if(arrlist == document.all) {
			return '<span class="grey2">document.all</span>';
		}
		if(arrlist == null) {
			return '<span class="grey2">null</span>';
		}
		s += '<div style="padding-left:'+padding+'px">Array (<br />';
		for(k in arrlist) {
			try {var s2 = arrlist[k];} catch(e) {continue;}
			if(s2 == arrlist) continue;	// 避免递归定义
			s += '<div style="padding-left: 16px">['+k+'] =&gt; '+print_r_real(s2, level+1) + '</div>';
		}
		s += ')</div>';
		return s;
	} else if(type == 'function') {
		return '<span class="grey2">function() {}</span>';
	} else  {
		return type;
	}
}

// 转换为 int
function intval(s) {
	var i = parseInt(s);
	return isNaN(i) ? 0 : i;
}

function empty(s) {
	return (typeof s == 'undefined') || !s || s == '0';
}

function urlencode(s) {
	s = encodeURIComponent(s);
	s = s.replace(/-/ig, '%2D');
	s = s.replace(/\./ig, '%2E');
	return s;
}

// 对字符串转义。只保留字母数字加下划线
$.escape = function(s) {
	return s ? s.replace(/[^\w]/ig, '_') : '';
}

// time 单位为秒，与php setcookie, 和  misc::setcookie() 的 time 参数略有差异。
$.cookie = function(name, value, time, path) {
	if(typeof value != 'undefined') {
		if (value === null) {
			var value = '';
			var time = -1;
		}
		if(typeof time != 'undefined') {
			date = new Date();
			date.setTime(date.getTime() + (time * 1000));
			var time = '; expires=' + date.toUTCString();
		} else {
			var time = '';
		}
		var path = path ? '; path=' + path : '';
		//var domain = domain ? '; domain=' + domain : '';
		//var secure = secure ? '; secure' : '';
		document.cookie = name + '=' + encodeURIComponent(value) + time + path;
	} else {
		var v = '';
		if(document.cookie && document.cookie != '') {
			var cookies = document.cookie.split(';');
			for(var i = 0; i < cookies.length; i++) {
				var cookie = $.trim(cookies[i]);
				if(cookie.substring(0, name.length + 1) == (name + '=')) {
					v = decodeURIComponent(cookie.substring(name.length + 1)) + '';
					break;
				}
			}
		}
		return v;
	}
};

if($.browser.msie) document.documentElement.addBehavior("#default#userdata");
$.pdata = function(key, value) {
	// HTML 5
	try {
		// ie10 需要 try 一下
		if(window.localStorage){
			if(typeof value == 'undefined') {
				return localStorage.getItem(key);
			} else {
				return localStorage.setItem(key, value);
			}
		}
	} catch(e) {}

	// HTML 4
	if(is_ie6 && (!document.documentElement || typeof document.documentElement.load == 'unknown' || !document.documentElement.load)) {
		return '';
	}
	// get
	if(typeof value == 'undefined') {
		if(is_ie) {
			try {
				document.documentElement.load(key);
				return document.documentElement.getAttribute(key);
			} catch(e) {
				//alert('$.pdata:' + e.message);
				return '';
			}
		} else {
			try {
				return sessionStorage.getItem(key) && sessionStorage.getItem(key).toString().length == 0 ? '' : (sessionStorage.getItem(key) == null ? '' : sessionStorage.getItem(key));
			} catch(e) {
				return '';
			}
		}
	// set
	} else {
		if(is_ie){
			try {
				// fix: IE TEST for ie6 崩溃
				document.documentElement.load(key);
				document.documentElement.setAttribute(key, value);
				document.documentElement.save(key);
				return  document.documentElement.getAttribute(key);
			} catch(error) {/*alert('setdata:'+error.message);*/}
		} else {
			sessionStorage.setItem(key, value);
		}
	}
};

/*
	status: true: 保存, false: 去掉
*/
$.pdata_keep = function(arrname, id, status) {
	var s = $.pdata(arrname);
	var o = $.parseJSON($.pdata(arrname));
	if(!o) o = [];
	pos = $.inArray(id, o);
	if(status) {
		if(pos == -1) {
			o[o.length++] = id;
			$.pdata(arrname, $.toJSON(o));
		}
	} else {
		if(pos != -1) {
			o.splice(pos, 1);
			$.pdata(arrname, $.toJSON(o));
		}
	}
	return o;
};

// 创建一个层，用来显示内容, recall 如果为数字，这为 timeout
$.alert = function(s, type, recall) {
	// 避免重复创建
	$('#alertdialog').remove();
	
	if(!type) type = 'ok';  // ok|error|notice
	s = '<div class="'+type+'">'+s+'</div>';
	s += '<div style="margin-top: 4px; text-align: center;"><a id="alertclose" href="javascript:return false;" class="button bigblue"><span>关闭</span></a></div>';
	jdialog = $('<div class="dialog bg2 border shadow" title="对话框标题" id="alertdialog" style="display: none;">'+ s +'</div>').appendTo('body');
	jdialog.dialog({open: true, width: jdialog.width()});
	$('#alertclose').unbind('click').click(function() {$('#alertdialog').dialog('close')});
	if(typeof recall == 'number') {
		setTimeout(function() {$('#alertdialog').remove();}, recall);
	} else if(recall) {
		$('#alertclose').click(function() {recall();});
	}
}

$.fn.disable = function(s) {
	var tag = $(this).get(0);
	var tagname = tag.tagName.toLowerCase();
	if(tagname == 'input') {
		$(tag).attr('oldval', $(tag).val());
		$(tag).val(s ? s : '正在提交...');
		return this;
	} else {
		var jspan = $('span', this);
		jspan.attr('oldhtml', jspan.html());
		jspan.html(s ? s : '正在提交...');
		return this;
	}
}

$.fn.enable = function() {
	var tag = $(this).get(0);
	if(!tag) return this;
	var tagname = tag.tagName.toLowerCase();
	if(tagname == 'input') {
		$(tag).val($(tag).attr('oldval'));
		return this;
	} else {
		var jspan = $('span', this);
		jspan.html(jspan.attr('oldhtml'));
		return this;
	}
}


// 鼠标离开 obj(<A>) 后，几秒消失, 消失后回调 recall
$.fn.mouseout_hide = function(timeout, obj, recall) {
	if(!timeout) { return this;}
	var _this = this;
	// 如果有 obj, 一般为A标签，则鼠标放在A标签上时不启动定时器。
	if(obj) {
		$(obj).hover (
			function() { if(_this.htime) { clearTimeout(_this.htime); _this.htime = false; } return false; },
			function() { 
				if(_this.htime) { clearTimeout(_this.htime); _this.htime = false; }
				if(!_this.htime) { _this.htime = setTimeout(function() { _this.fadeOut(); _this.htime = false; if(recall) recall();}, timeout); return false;}
			}
		);
	// 否则直接启动定时器
	} else {
		_this.htime = setTimeout(function() { _this.fadeOut(); _this.htime = false;}, timeout);
	}
	$(this).hover (
		function() { if(_this.htime) { clearTimeout(_this.htime); _this.htime = false; } return false; },
		function() { 
			if(_this.htime) { clearTimeout(_this.htime); _this.htime = false; }
			if(!_this.htime) { _this.htime = setTimeout(function() { _this.fadeOut(); _this.htime = false; if(recall) recall();}, timeout); return false;}
		}
	);
	return this;
};

// 子窗口
$.fn.alert = function(s, setting) {
	setting = $.extend({
		width: 0,
		pos: 2,
		delay: 3000,
		alerticon: 1
	}, setting);
	
	// 查找用于定位的父节点, 可能会有，也可能没有 position: absolute
	var pthis = this.offsetParent().offset();
	
	var offset = this.offset();
	var left = offset.left - pthis.left;	// 相对于父容器的偏移量
	var top = offset.top - 32 - pthis.top;// 默认
	
	var width = setting.width ? setting.width : this.width() < 150 ? 150 : this.width();
	var alerticon = setting.alerticon ? '' : 'background-image: none; text-indent: 0px; ';
	var closeicon = setting.alerticon ? '<a href="javascript: void(0)" class="icon icon-close" style="float:right;"></a>' : '';
	
	// 避免重复创建
	var alertdiv = $(this).next();
	if(!alertdiv.hasClass('alert')) {
		alertdiv = $('<div class="alert" style="'+alerticon+'width: ' + width + 'px; display: none; position: absolute; left: ' + left + 'px; top: ' + top + 'px; z-index: 100">'+ closeicon + '<div class="iconbody">' + s + '</div>' + '</div>').insertAfter(this);
	} else {
		// 更新内容
		$('div.iconbody', alertdiv).html(s);
	}
	
	if(setting.pos == 2) {
		var menuleft = left;
		var menutop = offset.top - alertdiv.outerHeight() - pthis.top - 7;// 默认
		var arrowleft = 6;
		var arrowtop = alertdiv.outerHeight() - 2;
		var arrowclass = 'alert_arrow_down';
	} else if(setting.pos == 7) {
		var alertwidth = alertdiv.outerWidth();
		var menuleft = left - alertwidth + this.width();
		//var menutop = offset.top + alertdiv.outerHeight() + pthis.top;// 默认
		var menutop = offset.top + this.outerHeight() + pthis.top + 7;// 默认
		var arrowleft = alertwidth - this.width();
		var arrowtop = -6;
		var arrowclass = 'alert_arrow_up';
	} else if(setting.pos == 8) {
		var menuleft = left;
		//var menutop = offset.top + alertdiv.outerHeight() + pthis.top;// 默认
		var menutop = offset.top + this.outerHeight() + pthis.top + 7;// 默认
		var arrowleft = 6;
		var arrowtop = -6;
		var arrowclass = 'alert_arrow_up';
	}
	alertdiv.show().css({'left': menuleft + 'px', 'top': menutop + 'px'});
	
	// 避免重复创建
	var alertarrow = $('div.'+arrowclass, alertdiv);
	if(alertarrow.length == 0) {
		var alertarrow = $('<div class="'+arrowclass+'" style="position: absolute; left: '+arrowleft+'px; top: ' + arrowtop + 'px; z-index: 100"></div>').appendTo(alertdiv);
	}
	alertarrow.show();
	
	if(setting.delay) {
		alertdiv.mouseout_hide(setting.delay, this);
	}
	
	$('a.icon-close', alertdiv).click(function() {alertdiv.hide(); alertarrow.hide();});
	return this;
}

// 创建一个层，点击确定后，POST 该 URL。
$.confirm = function(url, s, type, recall) {
	// 避免重复创建
	$('#confirmdialog').remove();
	
	s = '<div class="'+type+'">'+s+'</div>';
	s += '<div style="margin-top: 4px; text-align: center;"><form action="'+url+'" method="post" id="confirmform"><input type="hidden" name="formhash" value="js" /><a id="confirmsubmit" href="javascript:return false;" class="button bigblue"><span>确定</span></a> <a id="confirmclose" href="javascript:return false" class="button biggrey"><span>关闭</span></a></form></div>';
	jdialog = $('<div class="dialog bg2 border shadow" title="提示信息" id="confirmdialog" style="display: none;">'+ s +'</div>').appendTo('body');
	jdialog.dialog({open: true, width: jdialog.width()});
	$('#confirmsubmit').unbind('click').click(function() {
		if(!recall) {
			$('#confirmform').submit();
			$('#confirmdialog').dialog('close');
		} else {
			recall();
		}
		return false;
	});
	$('#confirmclose').unbind('click').click(function() {
		$('#confirmdialog').dialog('close');
		return false;
	});
}

// 将菜单浮动于 this 对象的周围, pos 默认为 8
/*
	1		2		3
	4		<this>		6
	7		8		9
*/
$.fn.xn_menu = function(menuid, pos, timeout) {
	var menu = $(menuid);
	var offset = this.position();
	
	if(!pos) pos = 8;
	if(pos == 8) {
		var offsettop = offset.top + this.height() + 2;   
		var offsetleft = offset.left;
	} else if(pos == 6) {
		var offsettop = offset.top;   
		var offsetleft = offset.left + this.width();
	}
	menu.css({position: "absolute", top: offsettop, left : offsetleft, 'z-index':10000});
	//menu.show();
	menu.fadeIn('fast');
	if(timeout) {
		$(menu).mouseout_hide(timeout, this);
	}
	return this;
};

$.fn.xn_delay = function(time) {
	if($.browser.msie && $.browser.version == '6.0') {
		return this;
	} else {
		this.animate({left:'+=0'}, time);
		return this;
	}
};
/*
$.fn.xn_fadeIn = function(time) {
	if($.browser.msie && $.browser.version == '6.0' ||  $.browser.version == '7.0') {
		return this;
	} else {
		this.animate({left:'+=0'}, time);
		return this;
	}
};*/

// 闪烁效果，用来特别提示输入框
/*
$.fn.flash_border = function(open) {
	var _this = this;
	if(open) {
		var t = setInterval(function() {
			_this.toggleClass('border');
		}, 1000);
		_this.get(0).flash_border_time_handle = t;
	} else {
		if(_this.get(0).flash_border_time_handle) {
			clearInterval(_this.get(0).flash_border_time_handle);
			_this.get(0).flash_border_time_handle = null;
		}
	}
	return this;
};
*/


// $.toJSON, json_encode()
$.type = function(o) { 
	var _toS = Object.prototype.toString; 
	var _types = { 
		'undefined': 'undefined', 
		'number': 'number', 
		'boolean': 'boolean', 
		'string': 'string', 
		'[object Function]': 'function', 
		'[object RegExp]': 'regexp', 
		'[object Array]': 'array', 
		'[object Date]': 'date', 
		'[object Error]': 'error' 
	}; 
	return _types[typeof o] || _types[_toS.call(o)] || (o ? 'object' : 'null'); 
};
var json_replace_chars = function(chr) {
	var specialChars = { '\b': '\\b', '\t': '\\t', '\n': '\\n', '\f': '\\f', '\r': '\\r', '"': '\\"', '\\': '\\\\' }; 
	return specialChars[chr] || '\\u00' + Math.floor(chr.charCodeAt() / 16).toString(16) + (chr.charCodeAt() % 16).toString(16); 
};
$.toJSON = function(o) { 
	var s = []; 
	switch ($.type(o)) { 
		case 'undefined': 
			return 'undefined'; 
			break; 
		case 'null': 
			return 'null'; 
			break; 
		case 'number': 
		case 'boolean': 
		case 'date': 
		case 'function': 
			return o.toString(); 
			break; 
		case 'string': 
			return '"' + o.replace(/[\x00-\x1f\\"]/g, json_replace_chars) + '"'; 
			break; 
		case 'array': 
			for (var i = 0, l = o.length; i < l; i++) { 
				s.push($.toJSON(o[i])); 
			} 
			return '[' + s.join(',') + ']'; 
			break; 
		case 'error': 
		case 'object': 
			for (var p in o) { 
				s.push('"' + p + '"' + ':' + $.toJSON(o[p])); 
			} 
			return '{' + s.join(',') + '}'; 
			break; 
		default: 
			return ''; 
			break; 
	} 
};

$.loaded = []; // 已经加载的JS
$.xload = function() {
	var args = null;
	if(typeof arguments[0] == 'object') {
		args = arguments[0];
		if(arguments[1]) args.push(arguments[1]);
	} else {
		args = arguments;
	}
		
	// 去除重复
	//args; // 参数列表
	this.load = function(args, i) {
		if(typeof args[i] == 'string') {
			var js = args[i];
			if($.inArray(js, $.loaded) != -1) {
				if(i < args.length) {
					this.load(args, i+1);
				}
				return;
			}
			$.loaded.push(js);
			
			var script = document.createElement("script");
		       	script.src = js;
			// recall next
			if(i < args.length) {
				var _this = this;
				if(is_ie) {
		       			script.onreadystatechange = function() {
		       				if(script.readyState == 'loaded' || script.readyState == 'complete') {
		       					_this.load(args, i+1);
		       					script.onreadystatechange = null;
		       				}
		       			};
		       			script.onerror = function() {
		       				alert('script load error:'+js);
		       			}
		       		} else {
		       			script.onload = function() {
		       				_this.load(args, i+1);
		       			};
		       		}
			}
			document.getElementsByTagName('head')[0].appendChild(script);
			
		} else if(typeof args[i] == 'function'){
			var f = args[i];
			f();
			if(i < args.length) {
				this.load(args, i+1);
			}
		} else {
		}
	};
	this.load(args, 0);
}

function xiuno_load_css(filename) {
	// 判断重复加载
	var tags = document.getElementsByTagName('link');
	for(var i=0; i<tags.length; i++) {
		if(tags[i].href.indexOf(filename) != -1) {
			return false;
		}
	}
	
	var link = document.createElement("link");
	link.rel = "stylesheet";
	link.type = "text/css";
	link.href = filename;
	document.getElementsByTagName('head')[0].appendChild(link);
}

function url_to_id(url) {
	return escape(url).replace(/[%.\/\-]/ig, '_');	// 此处不过滤特殊字符在 jquery 下会有奇怪的bug, $('abc%.') 会让 jquery 彻底傻掉，1.4.3 通过，1.6未测试。
}

// 添加参数到 url，去除重复
/*
	兼容以下格式：
	?a-1-b-2.htm
	a-1-b-2.htm
	/?a-1-b-2.htm
	/a-1-b-2.htm
	/?a=1&b=2
	?a=1&b=2
*/
function url_add_arg(url, k, v) {
	if(url.indexOf('/?') != -1) {
		var sep = url.indexOf('/?');
		var prefix = url.substr(0, sep);
		var suffix = url.substr(sep, url.length);
	} else if(url.indexOf('?') != -1) {
		var sep = url.indexOf('?');
		var prefix = url.substr(0, sep);
		var suffix = url.substr(sep, url.length);
	} else {
		var prefix = '';
		var suffix = url;
	}
	if(suffix.substr(suffix.length - 4, 4) == '.htm') {
		suffix = suffix.substr(0, suffix.length - 4) + '-'+k+'-'+v+'.htm';
	} else {
		suffix += (suffix.indexOf('?') == -1 ? '?' : '&') + k + '=' + v;
	}
	return prefix + suffix;
}

// 检查 cache，如果存在，则先从CACHE中取
function ajaxdialog_request(url, recall, options) {
	// 如果有cache 直接显示 cache 数据
	var url = url_add_arg(url, 'ajax', 1);
	var dialogid = url_to_id(url);
	var dialogdiv = document.getElementById(dialogid);
	if(!dialogdiv) {
		var s = '<div class="dialog bg2 border shadow" title="正在加载..." id="'+dialogid+'" style="overflow: visible;">正在加载...<'+'/div>';
		var dialogdiv = $(s).appendTo('body').get(0);
	}
	var jdialog = $(dialogdiv);
	
	// 默认是开启缓存的，除非 options.cache=0，强制关闭 cache
	if(jdialog.data(url) && (options == undefined || options.cache == undefined || options && options.cache)) {
		var json = jdialog.data(url);
		var dialogdiv = json.dialogdiv;
		
		// 会初始化两次，导致关闭按钮第二次点击会出问题
		//jdialog.dialog(options);
		
		// 修正第二次定位不准
		jdialog[0].dialog.open();
		jdialog[0].dialog.settings.xcaller = options.xcaller;
		jdialog[0].dialog.set_position(options.position);
	// 没有 cache, ajax 请求 url
	} else {
		// 弹出对话框
		var optionsbefore = $.extend({width: 700, modal: true, open: true}, options);
		jdialog.dialog(optionsbefore);
		// 追加 -ajax-1 参数
		
		$.get(url, function(s) {
			var json = json_decode(s);
			if((error = json_error(json)) || json.status <= 0) {
				error = error.replace(/\n/ig, '<br />');
				var errstr = error ? '<span class="clear">' + error.replace(/\n/, '<br />') + '</span>' : (json.status <= 0 ? '<div class="error">' + json.message + '</div>' : '');
				var body = errstr + (json && json.status <= 0 ? '' : '<br /><br /><b>URL:<'+'/b> <a href="' + url + '" target="_blank">' + url + '<'+'/a>');
				jdialog.dialog({width: 700, title: "错误信息：", body: body, open: true});
				return false;
			}
			
			// 如果为普通信息提示，则没有 body
			if(json.status == 1 && json.message && !json.message.body) {
				json.message = {title: '提示信息', body: json.message};
			}
			
			// 缓存非错误数据到 body 节点
			json.dialogdiv = dialogdiv;
			$(dialogdiv).data(url, json);
			json = json.message;
			json.title = json.title ? json.title : '提示信息：';
			
			jdialog.attr('title', '');
			
			// 弹出层
			options = $.extend({open: true, width: json.width, title: json.title, body: json.body }, options);
			jdialog.dialog(options);
			
			// 如果在不同的域，firefox 下需要 settimeout，同域则不需要。
			// 可能含有脚本，晚执行，约定函数名字为 delay_execute()
			// 兼容IE6： typeof delay_execute != 'undefined'
			if(typeof delay_execute != 'undefined') delay_execute(jdialog[0].dialog, recall);
			
		});
	}
}

function ajaxdialog_confirm(e) {
	var e = e ? e : window.event;// 兼容 event
	var url = $(this).attr('href');
	var options = $(this).attr('ajaxconfirm');//获取并判断外部设置参数并转换为对象
	if(options != null && options.length > 0) {
		eval("var options = " + options);//alert(typeof(options));
		options.xcaller = this;
	}
	
	var message = options.message ? options.message : '';
	var type = options.type ? options.type : 'ok';
	var url = $(this).attr('href');
	$.confirm(url, message, type);
	return false;
}

// 参考文档：http://jqueryui.com/demos/dialog/#option-width
function ajaxdialog_click(e) {
	var e = e ? e : window.event;// 兼容 event
	var url = $(this).attr('href');
	var options = $(this).attr('ajaxdialog');//获取并判断外部设置参数并转换为对象
	if(options != null && options.length > 0) {
		eval("var options = " + options);//alert(typeof(options));
		options.xcaller = this;
	}
//	var offset = $(this).offset();
//	alert(offset.top);	
	
	//if(options == undefined || options.cache == undefined) options.cache = true;	// 默认开启 cache
	
	// 点击<a> 弹出 dialog, a
	//options.caller = this;
	
	var recall = this.recall ? this.recall : null;
	ajaxdialog_request(url, recall, options);
	return false;
}

// 鼠标放上去
function ajaxdialog_mouseover(e) {
	var _this = this;
	if(this.htime) return true;
	this.htime = setTimeout(function() {
		ajaxdialog_click.call(_this, e);
		this.htime = null;
	}, 500);
	return true;
}

function ajaxdialog_mouseout(e) {
	if(this.htime) {
		clearTimeout(this.htime);
		this.htime = null;
	}
	return true;
}

// button 的切换：用作 关注/取消关注 等操作
function ajaxtoggle_event(e) {
	var href = $(this).attr('href');
	var href2 = $(this).attr('href2');
	//var value = $('>span', this).html();
	var value = $(this).attr('value');
	var value2 = $(this).attr('value2');
	var classname = $(this).attr('class');
	var classname2 = $(this).attr('class2');
	$('>span', this).html('正在请求...');
	var _this = this;	// <a> 标签
	$.get(href, function(s) {
		var json = json_decode(s);
		if(error = json_error(json)) {
			alert(error);
			$('>span', _this).html(value);
			return false;
		}
		if(json.status <= 0) {
			alert(json.message);
			$('>span', _this).html(value);
		} else {
			$(_this).attr('value', value2);
			$(_this).attr('value2', value);
			$(_this).attr('href', href2);
			$(_this).attr('href2', href);
			$(_this).attr('class', classname2);
			$(_this).attr('class2', classname);
			$('>span', _this).html(value2);
			_this.className = classname2;
		}
	});
	return false;
}

// document.body.scrollWidth-document.body.clientWidth>0

// --------------------> 三大页面需要的 js

// 判断主题是否已读，lastpost 为最后回复的时间
function tid_is_read(tid, lastpost) {
	var arr = json_decode($.pdata(cookie_pre + 'readtids'));
	if(!arr) arr = {};
	for(var tid2 in arr) {
		if(tid2 != tid) continue;
		if(arr[tid2] && lastpost && arr[tid2] > lastpost) return true;
	}
	return false;
}

// 加入的同时，删除一天前的记录。
function tid_add_read(tid, time) {
	var arr = json_decode($.pdata(cookie_pre + 'readtids'));
	if(!arr || arr == 'null') arr = {};
	for(var k in arr) {
		// 删除30天以前的数据
		if(time - arr[tid] > 2592000) delete arr[k];
	}
	arr[tid] = time;
	// 保存最后阅读的时间
	$.pdata(cookie_pre + 'readtids', $.toJSON(arr));
	
}

/*function fid_update_lasttime(fid, time) {
	var o = $.parseJSON($.pdata(cookie_pre + 'readfids'));
	if(!o) o = {};
	o[fid] = time;
	$.pdata(cookie_pre + 'readfids', $.toJSON(o));
}
*/
/*function fid_get_lasttime(fid) {
	var o = $.parseJSON($.pdata(cookie_pre + 'readfids'));
	if(!o) return 0;
	return o[fid] ? o[fid] : 0;
}
*/
/*
用途：
	对应服务端的 core::init_get() 算法，返回一个对象
实例：
	xn_parse_url('http://xxx.com/index.php?a-b-page-1.htm');
返回：
	Array (
	    [a] => b
	    [page] => 1
	)
*/
function xn_parse_url(url) {
	var url = url.substr(url.lastIndexOf('/') + 1, url.length);
	url = url.replace(/^(index\.php)?\?/ig, '');
	url = url.replace(/(\.htm)$/ig, '');
	var arr = url.split('-');
	var r = {};
	if(arr[0]) r[0] = arr[0];
	if(arr[1]) r[1] = arr[1];
	for(var i=0; i<arr.length; i+=2) {
		r[arr[i]] = arr[i+1];
	}
	return r;
}

/* 
	js 版本的翻页函数，对应 misc::page()
	var s = pages('http://xiuno.net/?pmlist-uid-2-page-3.htm', 10, 4);
	alert(s);
*/
function pages(url, totalpage, page) {
	var urladd = '';
	if(url.indexOf('.htm') != -1) {
		var arr = url.split('.htm');
		var url = arr[0];
		var urladd = '.htm' + arr[1];
		url = url.replace(/-page-\d+/ig, '');
		var rewritepage = '-page-';
	} else {
		url = url.replace(/&page=\d+/ig, '');
		url = url + (url.indexOf('?') == -1 ? '?' : '&');
		var rewritepage = 'page=';
	}
	if(totalpage < 2) return '';
	
	var page = Math.min(totalpage, page);
	var shownum = 5;	// 显示多少个页 * 2
	
	var start = Math.max(1, page - shownum);
	var end = Math.min(totalpage, page + shownum);
	
	// 不足 $shownum，补全左右两侧
	var right = page + shownum - totalpage;
	if(right > 0) start = Math.max(1, start -= right);
	left = page - shownum;
	if(left < 0) end = Math.min(totalpage, end -= left);
	
	var s = '';
	if(page != 1) s += '<a href="' + url + rewritepage + (page - 1) + urladd + '">◀</a>';
	if(start > 1) s += '<a href="' + url + rewritepage + '1' + urladd + '">1 ' + (start > 2 ? '... ' : '') + '</a>';
	for(var i=start; i<=end; i++) {
		if(i == page) {
			s += '<a href="' + url + rewritepage + i + urladd + '" class="checked">' + i + '</b>';// checked
		} else {
			s += '<a href="' + url + rewritepage + i + urladd + '">' + i + '</a>';
		}
	}
	if(end != totalpage) s += '<a href="' + url + rewritepage + totalpage + urladd + '">' + (totalpage - end > 1 ? '... ' : '') + totalpage + '</a>';
	if(page != totalpage) s += '<a href="' + url + rewritepage + (page + 1) + urladd + '">▶</a>';
	return s;
}

// 带事件的翻页
function pages_add_event(url, totalpage, page, jshowdiv, jpagediv, recall) {
	var s = pages(url, totalpage, page);
	jpagediv.html(s);
	$('a', jpagediv).click(function() {
		var href = $(this).attr('href');
		var arr = xn_parse_url(href);
		var currpage = arr['page'];
		var s2 = pages(url, totalpage, currpage);
		jpagediv.html(s2);
		jshowdiv.html('正在加载...');
		$.get(href, function(s) {
			var json = json_decode(s);
			if(error = json_error(json)) {alert(error); return false;}
			if(json.status <= 0) {alert(json.message);return false;}
			jshowdiv.html(json.message.body);
			if(recall) recall();
		});
		
		pages_add_event(url, totalpage, currpage, jshowdiv, jpagediv, recall);
		
		return false;
	});
}

function json_encode(o) {
	return $.toJSON(o);
}

function json_decode(s) {
	if(s == '') {
		return '';
	} else {
		try {
			// 去掉广告代码。这行代码挺无语的，为了照顾国内很多人浏览器中广告病毒的事实。
			s = s.replace(/\}\s*<script[^>]*>[\s\S]*?<\/script>\s*$/ig, '}');
			var json = $.parseJSON(s);
			return json;
		} catch(e) {
			//alert('JSON格式错误：' + s);
			//window.json_error_string = s;	// 记录到全局
			return s + '';
		}
	}
}

function json_error(json) {
	if(typeof json === 'string' || typeof json === 'number') {
		if(json == '') {
			return '服务端数据为空，可能PHP碰到什么错误，您可以尝试通过后台“PHP错误日志”了解详情。';
		} else {
			return '服务端返回的 JSON 数据有误:' + json;
		}
	} else if(typeof json != 'object') {
		return json;
	}
	
	if(json.servererror) {return json.servererror.replace(/\\n/, "\n");}
	return '';
}

function humansize(num) {
	if(num > 1000000) {
		num = num / 1000000;
		num = num.toFixed(2)
		return num + 'M';
	} else if(num > 1000) {
		num = num / 1000;
		num = num.toFixed(2)
		return num + 'K';
	} else {
		return num+'B';
	}
}

// 快捷键翻页，只有当第一页，并且焦点不在表单控件上时候激发。
function bind_document_keyup_page() {
	//var arr = xn_parse_url(href);
	//var currpage = arr['page'];
	
	$(document).keyup(function(e) {
		if(document.activeElement && document.activeElement.tagName){
			var tagname = document.activeElement.tagName.toUpperCase();
			if(tagname == 'INPUT' || tagname == 'TEXTAREA' || tagname == 'IFRAME') {
				return true;
			}
		}
		var url = window.location.toString();
		var r = url.match(/page-(\d+)/i);
		var e = e || event,      
		keycode = e.which || e.keyCode;
		if(r && r[1]) {
			page = r[1];
			if(keycode == 33 || keycode == 37 || keycode == 34 || keycode == 39) {
				// 没有弹出层
				if($('#overlay').length > 0) return true;
				try {
					//$('div.dialog')
					var pnode = window.getSelection ? window.getSelection().focusNode.parentNode : document.selection.createRange().parentElement();
					if(pnode && (pnode.nodeName == 'input' || pnode.nodeName == 'textarea')) {
						return true;
					}
				} catch(e) {}
			}
			
			if(keycode == 33 || keycode == 37) {
				if(page > 1) window.location = url.replace(/page-(\d+)/i, "page-"+(intval(page)-1));
			} else if(keycode == 34 || keycode == 39) {
				window.location = url.replace(/page-(\d+)/i, "page-"+(intval(page)+1));
			}
		}
		// 显示正在加载...
		return true;
	});
}

// cookie 时效: 一个小时，每隔一个小时判断一次客户端的时区，默认：+8，据说带夏令营时差，没有测试过。
function set_client_zone() {
	var munites = new Date().getTimezoneOffset();
	var hour = parseInt(munites / 60);
	var prefix = "-";
	if(hour < 0) {
		prefix = "+";
		hour = -hour;
	}
	hour = hour + "";
	if(hour.length == 1) {
		hour = "0" + hour;
	}
	var timeoffset =  prefix + hour;
	$.cookie('timeoffset', timeoffset, 3600);
}

set_client_zone();


// fix chrome bug
//$.event.props = $.event.props.join('|').replace('layerX|layerY|', '').split('|');