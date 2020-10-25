/** 
 * Index/TeCenter 模块公共前端库
 * 2016年9月24日 星期六-与jQuery独立
 */
/******************************************************JavaScript 原型扩展 Begin */
// Array 获取最后一个元素
Array.prototype.last = function(){
	var len = this.length;
	if(len == 0) return '';
	return this[len-1];
};
// 删除数组中的任何一个指定元素
Array.prototype.unset = function(value){
	var arr = new Array();
	for(var i=0; i<this.length; i++){
		if(this[i] == value) continue;
		arr.push(this[i]);
	}
	return arr;
};
// 时间原型
Date.prototype.sysdate = function(format){
	format = format? format:"y-m-d h:i:s";
	var fullYear = this.getFullYear();
	var M = this.getMonth()+1;
	var D = this.getDate();
	var H = this.getHours();
	var I = this.getMinutes();
	var S = this.getSeconds();
	var sorce = {
		'y':fullYear,'Y':parseInt((fullYear).toString().slice(2))
		,'m':(M<10? '0'+M:M),'M':M
		,'d':(D<10? '0'+D:D),'D':D
		,'h':(H<10? '0'+H:H),'H':H
		,'i':(I<10? '0'+I:I),'I':I
		,'s':(S<10? '0'+S:S),'S':S
		,'w':this.getDay()
		,'ms':this.getMilliseconds()
	};
	if(format.indexOf("ms") > -1) format = format.replace(new RegExp('ms','g'),sorce['ms']);
	for(var k in sorce){
		if(format.indexOf(k) == -1 || k == "ms") continue;
		if(format.indexOf(k) > -1) format = format.replace(new RegExp(k,'g'),sorce[k]);
	}
	return format;
};
//
/******************************************************JavaScript 原型扩展 End */
function Conero(){
    // 私有成员
    var Csl = console;
    // 首页导航自适应
    this.navAbjust = function(){
        var path = location.pathname;
        path = path.replace('/TeCenter/','');
        var arr = path.split('/'),name;
        if(arr[1]){
            name = arr[1];
            name = name.replace('.html','');
        }else name = 'index';
        $('#navbar .navbar-nav').find('li.'+name).addClass(name+' active');
    };
	// dataid 属性操作
	this.dataid = function(dom){
		if(this.empty(dom)) return '';
		if(this.is_object(dom)){
			return dom.attr('dataid');
		}
		return $(dom).attr('daraid');
	};
    // storage 对象
    this.storage = function(engine){
        return new jutilStorage(engine);
    };
	//  自动生成描点
    this.createHash = function(name,feek){
        if(name){
            var href = location.href;
            var arr = href.split('#');
            href = arr[0]+'#'+name;
            if(feek) return href;
            location.href = href;
        }
        return '';
    };
	// $_GET// URL 解析
	// fname=RDpccGhwc2VydmVyXGFwcFxjb25lcm9cRmlsZXMvX19jYWNoZS9iNjAzYWY1Yzk1NWM1NmYxNGI0ZDYyMzg3MzE3Yzg2NQ== 解析失败
	/**
	 * 解析 url 地址中的 GET 参数
	 * @param {string|undefind} key 键值
	 * @param {string|undefined} url 为空时默认为 location.search
	 * @return {string|JSON}
	 */
	this.getQuery = function(key, url){
		/*
		if('#' == key){
			var hash = location.hash;
			hash = hash.replace(new RegExp('#','g'),'');
			return hash;
		}
		*/
		// 获取描点
		if('#' == key){
			var href = url? url: location.href;
			if(href.indexOf('#')>-1){
				var arr = href.split('#');
				return arr[1];
			}
			return '';
		}
		var ser = location.search;
		if(url){
			var idx = url.indexOf('?');
			ser = idx > -1? url.substr(idx): false;
		}
		if(ser){
			ser = ser.replace('?','');
			ser = ser.replace(new RegExp('=','g'),'":"');
			ser = ser.replace(new RegExp('&','g'),'","');
			ser = '{"'+ser+'"}';
			var GET = JSON.parse(ser);
			if(key){
				if(GET[key]) return GET[key];
				return '';
			}
			return GET;
		}
		return '';
	};
	// $_GET// Url 字符串解析法
	this.getSearch = function(key){
		var search = location.search;
		if(this.empty(search)) return '';
		var tmpArr = search.split('&');
		var ret = '',value;
		for(var k=0; k<tmpArr.length; k++){
			value = tmpArr[k];
			if(value.indexOf(key+'=')>-1){
				ret = $.trim(value.replace(key+'=',''));
				break;
			}
		}
		return ret;
	};
	// getUrlBind - 对应PHP版函数 -- $name=null,$ogri=false,$position='LEFT'
	this.getUrlBind = function(name,ogri,position){
		position = position? 'RIGHT':'LEFT';
		var path = location.pathname;
		var data = path.split('/');
		var ret,index;
		if(name){
			for(var i=0; i<data.length; i++){
				if(name == data[i]){
					index = (position == 'LEFT')? (i+1):(i-1);
					if(index >= 0 && index < data.length){
						ret = data[index];
						if(!ogri){
							var reg = /(\.shtml)|(\.html)|(\.htm)/;
							ret = ret.replace(reg,'');
						}						
					}
					else ret = '';
					return ret;
				}
			}
			return '';
		}
		else ret = name;
		return ret;
	};
	// 2017年1月18日 星期三 url 参数 get - post 切换
	this.queryBuild = function(data){
		data = data? data:location.href;
		if(this.is_string(data)){ // get-> post
			var index = data.indexOf('?') + 1;
			var url = data.substr(index);
			var tmpArr = url.split('&');
			var json = {},str,key,value;
			for(var i=0; i<tmpArr.length; i++){
				str = tmpArr[i];
				key = str.substr(0,str.indexOf('='));
				value = str.substr(str.indexOf('=')+1);
				// json[key] = decodeURI(value);
				json[key] = decodeURIComponent(value);
			}
			return json;
		}
		else if(this.is_object(data)){ // post -> get
			var tmpArr = [];
			for(var k in data){
				tmpArr = k + '=' + data[k];
			}
			return '?' + tmpArr.join('&');
		}
	};
	// json/array 求并集
	// 参数第一个参数的类型，后面的参数可传json字符串
	this.array_merge = function()
	{
		var newArr,tmpArr;
		var isArray = false;
		for(var ctt=0; ctt<arguments.length; ctt++){
			if(this.empty(newArr)){
				newArr = arguments[ctt];
				if(this.is_array(newArr)) isArray = true;
			}
			else{
				tmpArr = arguments[ctt];
				if(this.is_string(tmpArr)) tmpArr = JSON.parse(tmpArr);
				if(isArray == true){
					for(var i=0; i<tmpArr.length; i++){
						newArr.posh(tmpArr[i]);
					}
				}
				else{
					for(var k in tmpArr){
						newArr[k] = tmpArr[k];
					}
				}
			}
		}
		return newArr;
	};
	// select 绑定输入框
	this.selectBindEl = function(selector,bindEl){
		var el = this.is_object(selector)? selector:$(selector);
		if(!el.is("select")) return;
		var text = el.find("option:selected").text();
		var value = el.find("option:selected").val();
		if(this.empty(text)) return;
		if(this.is_function(bindEl)){
			bindEl(value,text);return;
		}	
		$(bindEl).val(text);
	};
	// 字符串根据分割符获取最后字段
	this.strLastValue = function(value,delimiter){
		delimiter = delimiter? delimiter:'/';
		var arr = value.split(delimiter);
		return arr[arr.length-1];
	};
	/*
	// 1
	var pathname = "/conero/admin/{{name}}/{{pTo}}.html";
	var reg = /(\{\{)([a-z])+(\}\})/gi;
	pathname.match(reg);
	// 2
	var ret = "{{eteY85}}";
	var reg = /\{|\}/g;
	ret.replace(reg,'');
	* //
	// 模板方向获取值（适用于短/规律明确的文本） =>  tpl:  /conero/admin/oikeuejjjjdm/jsnnhhss.html , /conero/admin/{{name}}/{{lili}}.html => {name=>'oikeuejjjjdm',lili=>'jsnnhhss'}
	this.getValue4Tpl = function(tpl,text){
		if(tpl && text){
			var reg = /\{\{([a-z0-9])+\}\}/gi;
			var array = tpl.match(reg);
			var reg2 = /\{|\}/g;
			var retValue = {},
				len = array.length,
				key,value
			;
			for(var i=0; i<len; i++){
				key = array[i].replace(reg2,'');
				value = '';
				if(len == 1) return value;
				retValue[key] = value;
			}
			return retValue;
		}
		return '';
	}
	*/
	// 模板赋值 - 模板符号 - {{key}}  - 纯数字key 无效
	this.render4Tpl = function(data,text){
		if(data && text){
			var reg = /\{\{([a-z\d])+\}\}/gi;
			var array = text.match(reg);
			this.log(array);
			var reg2 = /(\{\{)|(\}\})/g;
			var	len = array.length,
				key,value
			;
			for(var i=0; i<len; i++){
				key = array[i].replace(reg2,'');
				this.log(key);
				value = (data && data[key])? data[key]:'';this.log(new RegExp('{{'+key+'}}','g'));
				text = text.replace(new RegExp('\{\{'+key+'\}\}','g'),value);			
			}
			return text;
		}
		return '';
	};
	// js/模拟保单----------------------------------------------------------------------->
	this.form = function(url,data,method){
		if(this.empty(url) || this.empty(data)) return false;
		if(this.is_string(data)){
			try {
				data = JSON.parse(data);
			} catch (error) {
				return error;
			}
		}
		if(method) method = method.toLowerCase();
		method = this.empty(method)? 'post':method;		
		if(method && (method != "post" && method != "get")) method = "post";
		var form = document.createElement("form");
		form.action = url;
		form.method = method;
		form.style = "display:none;";
		var ipt;
		if($.isArray(data)){// Array 对象 - 包含 - prototype新增的扩展对象 last/unset
			for(var k=0; k<data.length; k++){
				ipt = document.createElement("textarea");
				ipt.name = k;
				ipt.value = data[k];
				form.appendChild(ipt); 
			}
		}
		else{
			for(var k in data){
				ipt = document.createElement("textarea");
				ipt.name = k;
				ipt.value = data[k];
				form.appendChild(ipt); 
			}
		}
		document.body.appendChild(form);
		form.submit();
		return form;
	};
	this.post = function(url,data){return this.form(url,data,"post");};
	this.get = function(url,data){return this.form(url,data,"get");};
	// js/模拟保单	<-----------------------------------------------------------------------
	// 获取formJson - 选择器下元素的值
	this.formJson = function(selector){
		var el = this.is_object(selector)? selector:$(selector);
		if(el.length>0){
			var saveData = {};
			// input
			var ipts = el.find("input");
			var El,key,i=0;
			for(i=0; i<ipts.length; i++){
				El = $(ipts[i]);				
				if(El.attr('disabled')) continue; // 忽略禁用元素
				if(El.attr('type') == 'checkbox' && !El.is(':checked')) continue;// 忽略未被选中的复选框
				if(El.attr('type') == 'radio' && !El.is(':checked')) continue;// 忽略未被选中的单选框
				key = El.attr("name");
				if(this.empty(key)) continue;
				saveData[key] = El.val();
			}
			// textarea
			ipts = el.find('textarea');
			for(i=0; i<ipts.length; i++){
				El = $(ipts[i]);
				key = El.attr("name");
				if(this.empty(key)) continue;
				saveData[key] = El.val();
			}
			// select
			var sels = el.find("select");
			for(i=0; i<sels.length; i++){
				El = $(sels[i]);
				key = El.attr("name");
				if(this.empty(key)) continue;
				saveData[key] = El.find('option:selected').val();
			}			
			return saveData;
		}
		return null;
	};
	/*
	// 表单HTML附带val的值
	this.formHtmlCopyer = function(selectors){
		var el = this.is_object(selectors)? selectors:$(selectors);
		var html = '';
		if(el.length>0){
			html = el.html();
			var Json = this.formJson(selectors);
			var Dom = $(html);
			var value;
			for(var k in Json){
				value = Json[k];
				if(value) Dom.find('[name="'+k+'"]').val(value);
			}
			this.log(html);
		}
		return html;
	}
	*/
	// 表单更新器 - 2017年2月15日 星期三	
	this.formJsonUpdate = function(selector,json,callback){
		if(this.empty(selector) || this.empty(json) || !this.is_object(json)) return;
		var el = this.is_object(selector)? selector:$(selector);
		if(el.length>0){
			var value;
			for(var k in json){
				value = json[k];
				el.find('[name="'+k+'"]').val(value);
			}
			if(this.is_function(callback)) callback(el);
		}
		this.log(el);
	};
	// 表单非空检测 required 通过则放回-数据key-value 数据(object)/否则返回-bool类型
	this.formRequired = function(selector,feekJson)
	{
		feekJson = this.empty(feekJson)? true:false;
		var form = this.is_object(selector)? selector:$(selector);
		if(form.length > 0){
			var ipt = form.find('[required]'), el,value,name,type;
			for(var i =0; i<ipt.length; i++){
				el = $(ipt[i]);
				if(el.attr("disabled")) continue;// 禁用的元素跳过
				// input 
				if(el.is('input')){
					// checkbox
					type = el.attr('type');
					type = type.toLowerCase();
					if(el.attr('type') == 'checkbox' || el.attr('type') == 'radio') value = el.find(':checked').val();						
					else value = el.val();					
				}
				// select
				else if(el.is('select')) value = el.find('option:selected').val();
				else if(el.is('textarea')) value = el.val();
				value = $.trim(value);
				if(this.empty(value)){
					name = el.attr('name');
					name = this.empty(name)? true:name;
					el.focus();return name;
				}
			}
			if(feekJson) return this.formJson(selector); 
		}
		return false;
	};
    // PHP+js+Base64
    var _jsVar;
    this.getJsVar = function(key){
		if(typeof coneroJsVar == 'undefined') return '';// undefind 函数无效
		if(this.empty(coneroJsVar)) return '';
        if(this.is_string(coneroJsVar) && !this.is_object(_jsVar)){
            _jsVar = JSON.parse(Base64.decode(coneroJsVar));
        }
        if(this.is_object(_jsVar)){
			if(this.undefind(key)) return _jsVar;
            if(this.empty(_jsVar[key])) return '';
            return _jsVar[key];
        }
		return '';
    };
	// 与 php bsjson 函数匹配
	this.bsjson = function(value){
		if(value){
			// 解密并返回 对象
			if(this.is_string(value)){
				try {
					value = Base64.decode(value);
					return JSON.parse(value);
				} catch (error) {
					this.error(error);
				}
			}
			else if(this.is_object(value) && this.objectLength(value) >0){
				var str = JSON.stringify(value);
				return Base64.encode(str);
			}
		}
		return '';
	};
	/*
	// 文件处理
	this.filepath = function(fname,key){
		if(fname){
			// fname = fname.replace(/\\/g,'/');
			this.log(fname);
			fname = fname.replace(/\\/g,'-');
			// fname = fname.replace(new RegExp("\\","g"),'/');
		}
		return fname;
	}
	*/
	// message Api/ 窗口通信
	this.uWin = function(eleId){
		var th = this;
		var _uWin = function(id){
			var dom;
			dom = (typeof id == 'string')? document.getElementById(id) : false;
			// 发送信息-> get 窗体
			this.post = function(data,url){
				url = url? url: location.href;
				if(dom) dom.contentWindow.postMessage(data,url);
			}
			// 更新原始选择器
			this.setElement = function(ele){
				if(typeof ele == 'string') dom = document.querySelector(ele);
				else if(typeof ele == 'object') dom = ele;
				return this;
			}
			// 响应数据
			this.response = function(func){
				window.addEventListener("message",func);
			}
			/***************************************************************** iframe <<begin>> ****************************************************************/
			// 窗体对象
			var winObject = null;
			// iframe parent 类窗口
			this.pWin = function(callback){
				winObject = window.parent;
				if(th.is_function(callback)) return callback(winObject,this);	// 窗口调用接口转移为回调函数
				return winObject;
			}
			// 子窗口调用接口
			//this.pWinRun = function(){}
			// iframe 之窗口
			this.cWin = function(selector,callback){
				selector = selector? (th.is_string(selector)? $(selector):selector):null;
				id = th.is_object(selector)? selector:id;
				var dom = th.is_object(id)? id.get(0): null; // 若为 jQuery对象，则装换为 dom 对象
				if(th.empty(dom)) dom = document.getElementById(id); // 为字符创则默认为 通过id 获取 dom
				var win = dom.window || dom.contentWindow;
				winObject = win;
				if(th.is_function(callback)) return callback(winObject);	// 窗口调用接口转移为回调函数
				return winObject;
			}
			/***************************************************************** iframe <<end>> ****************************************************************/			
		}
		return new _uWin(eleId);
		
	};
	// 扩展机制
	this.extends = function(callback){
		if(this.is_function(callback)) return new callback(this);
	};
	this._extends = function(){
		this.extends = (this.prototype);
	};
    //--------------------------调试帮助
    //this.log = function(value){Csl.log(value);}
	this.log = Csl.log;
	this.error = function(msg){
		throw new Error( "Syntax error, unrecognized expression(Conero): " + msg);
	};
	this.alertTest = function(stopTest){if(stopTest) return;alert(Math.random()*(Math.pow(10,Math.ceil(Math.random()*10))));}// 弹出测试信息
    this.is_string = function(value){
        if(typeof(value) == 'string') return true;
        return false;
    };
    this.is_object = function(value){
        if(typeof(value) == 'object') return true;
        return false;
    };
	this.is_array = Array.isArray;
	// 查看array/json 是否存在值
	this.inArray = function(key,arr){
		var ret = false;
		if(key && this.is_object(arr)){
			if(this.is_array(arr)){
				for(var k=0; k<arr.length; k++){
					if(arr[k] == key) return true;
				}
			}
			else{
				for(var k in arr){
					if(arr[k] == key) return true;
				}
			}
		}
		return false;
	};
	// 求数组或json的长度
	this.objectLength = function(value){
		var len = 0;
		// 对象长度
		if(this.is_object(value)){
			if(this.is_array(value)) return value.length;
			// json 通过遍历获取			
			for(var k in value){
				len = len + 1;
			}
			return len;
		}
		// 其他具有length 属性的对象
		else if(value && value.length) return value.length;
		return len;
	};
	this.is_function = function(value){
		if(typeof(value) == 'function') return true;
		return false;
	};
	this.is_number = function(value){
		var peg = /^[0-9]+$/;
		if(value){
			value = value.replace(/\s/g,''); //  删除空格
			return peg.test(value)
		}
		return false;
	};
    this.undefind = function(value){
        if(typeof(value) == 'undefined') return true;
        return false;
    };
    this.empty = function(value){
        if(this.undefind(value)) return true;
		else if(value == '') return true;
		// else if(value == 0) return true; // "00" 无法通过
		else if(value == null) return true;
		return false;
    };
	// 元素 XMLHttpRequest 对象处理 - http - file - ftp
	// 2017年1月18日 星期三
	this.ajax = function(paramUtl)
	{
		var __ajaxFunction = function(th) {
			// url 地址， async 同步异步选择-> 同步
			this.get = function(url,async)
			{
				async = async? true:false;
				var http = new XMLHttpRequest();				
				url = url? url:paramUtl;
				http.open('get',url,async);
				http.send(null);
				if(async == false) return http.responseText;
			}
			// 返回头部
			this.getHeader = function(url,key)
			{
				url = url? url:paramUtl;
				if(url){
					var http = new XMLHttpRequest();
					http.open('get',url,false);
					http.send(null);
					// http.close();
					if(key) return http.getHeader(key);
					return http.getAllResponseHeaders();
				}
				return '';
			}
		};
		return new __ajaxFunction(this);
	};
	// 私有类扩展-当前具体项目类
	var _privateApp = function(obj){
		// 数据存在性检测 - N/Y
		obj.dataInDb = function(table,wh,func){
			if(table && this.is_object(wh) && this.is_function(func)){
				var where = Base64.encode(JSON.stringify(wh));
				$.post('/conero/index/common/dataInDb.html',{'table':table,'where':where},func);
			}
		};
		// 动态获取项目名称 - 非独立端口 - 	/conero/finance.html
		obj._project = function(){
			var path = location.pathname;
			var data = path.split('/');
			return data[1];
		};
		obj._baseurl = '/'+obj._project()+'/';
		// - 缓存------------------------------------------------------> begin
		// 缓存值获取或写入
		obj.cache = function(key,value){
			var _cache_ = $('#cache_helper');
			if($('#cache_helper').length == 0){
				$('body').append('<div class="hidden" id="cache_helper"></div>');
				_cache_ = $('#cache_helper');
			}
			if(obj.empty(key)) return "";
			var el = _cache_.find('span[dataid="'+key+'"]');
			var having = (el.length == 0)? false:true;
			// 获取缓存值
			if(obj.undefind(value)){				
				if(!having) return "";
				value = el.text();
				if(obj.empty(value)) return "";
				return Base64.decode(value);
			}
			// 回调函数
			if(obj.is_function(value)){
				return value(el);
			}
			// 设置缓存值
			if(having){return el.text(Base64.encode(value));}
			else{
				_cache_.append('<span dataid="'+key+'">'+Base64.encode(value)+'</span>');
			}
		};
		obj.cacheAttr = function(name,attr,value){
			var _cache_ = $('#cache_helper');
			return this.cache(name,function(el){
				var having = (el.length == 0)? false:true;
				if(obj.empty(attr)) return "";
				// 获取attr的值
				if(obj.undefind(value)){
					if(!having) return "";
					value = el.attr(attr);
					if(obj.empty(value)) return "";
					return Base64.decode(value);
				}
				// 设置 attr的值
				if(having){
					value = obj.empty(value)? "":Base64.encode(value);
					return el.attr(attr,value);
				}
				return _cache_.append('<span dataid="'+name+'" '+attr+'="'+Base64.encode(value)+'"></span>');
			});
		};
		// - 缓存------------------------------------------------------> end
	};
	// Bootstrap 扩展机制
	var _privateBootstrap = function(obj){
		// option = [title,id+,content,footer,header+]
		// 内嵌式模板窗口生成器- fn - model(show/hide)
		obj.modal = function(option,btpOpt,fn){
			if(obj.is_string(option)){
				var having = $(option).length > 0? true:false;
				if(having == true){
					$(option).modal(btpOpt);
				}
				return having;
			}
			option = typeof(option) == 'undefined'?{}:option;
			var title = obj.empty(option.title)? '模式窗口':option.title;
			var id = obj.empty(option.id)? 'page_modal':option.id;
			var content = obj.empty(option.content)? '模式内容':option.content;
			var header = obj.empty(option.header)? '':option.header;
			var footer = obj.empty(option.footer)? '':option.footer;
			var largeSize = obj.empty(option.large)? '':' modal-lg'; // 支持控制
			var saveBtn = !obj.empty(option.save) && obj.is_function(option.save)? true:false;
			var container = '<div class="modal fade" id="'+id+'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
			var html = ''
				+ '<div class="modal-dialog'+largeSize+'">'
				+    '<div class="modal-content">'
				+      '<div class="modal-header">'
				+        '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'
				+        header
				+        '<h4 class="modal-title">'+title+'</h4>'
				+      '</div>'
				+      '<div class="modal-body">'+content+'</div>'
				+      '<div class="modal-footer">'
				+        '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'
				+        (saveBtn? '<button type="button" class="btn btn-default" dataid="default_save">确定</button>':'')
				+        footer
				+      '</div>'
				+    '</div>'
				+ '</div>'
				;
			id = '#'+id;
			var having = $(id).length == 0? false:true;
			if(having){
				$(id).html(html);
				$(id).attr('class','modal fade');
			}
			else{
				html = container + html +'</div>';
				$('body').append(html);
			}
			if(btpOpt) $(id).modal(btpOpt);
			else $(id).modal();
			// 事件绑定处理
			if(saveBtn){
				$(id+' [dataid="default_save"]').off("click");
				$(id+' [dataid="default_save"]').on('click',option.save);// 通过[dataid]属性事件绑定
			}
			if(obj.is_object(fn)){
				if(obj.is_object(fn.bindEvent)){
					var arr = fn.bindEvent;
					for(var i=0; i<arr.length; i++){
						$(id+' [dataid="'+arr[i]+'"]').off("click");
						$(id+' [dataid="'+arr[i]+'"]').on('click',fn[arr[i]]);// 通过[dataid]属性事件绑定
					}
				}
				else if(obj.is_string(fn.bindEvent)){
					var dataid = fn.bindEvent;
					$(id+' [dataid="'+dataid+'"]').off('click'); // 绑定前解绑-避免重复绑定
					$(id+' [dataid="'+dataid+'"]').on('click',fn[dataid]);// 通过[dataid]属性事件绑定
				}
			}
			return $(id); // 返回模态窗 对象
		};
		// 内嵌式是alter
		obj.alert = function(el,content,title){
			// 扩展 标题 可窗口数字用于定时自动清除
			var times = isNaN(title)? 0 : title;
			if(times > 0)  title = null;
			title = obj.empty(title)? '警告':title;
			var type = 'warning';
			if(obj.is_object(content)){
				if(content['title']) title = content['title'];
				if(content['times']) times = content['times'];
				if(content.type) type = content.type;
				content = content['text'];
			}
			content = obj.empty(content)? ' 这是一个警告提示框示例！':content;			
			var html = ''
				+ '<div class="alert alert-'+type+' alert-dismissible fade in" role="alert">'
				+ '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'
				+ '<span class="glyphicon glyphicon-question-sign"></span> <strong>'+title+'</strong> '
				+ content
				+ '</div>'
				;
			// 回调函数
			if(obj.is_function(el)){
				el(html);return;
			}
			if(obj.is_string(el) && 'feek' == el.toLowerCase()) return html;// 返回字符串
			if(obj.empty(el)){// 测试示例
				$('body').append(html);return;
			}
			el = obj.is_string(el)? $(el):el;
			el.html(html);
			if(!isNaN(times) && times > 0){
				var clearAlert = function(){
					el.html('');
				};
				setTimeout(clearAlert,times*1000);
			}
		};
		// modal -alert
		obj.modal_alert = function(text,title){
			if(this.empty(text)) return;
			var title = this.empty(title)? '警告':title;
			this.modal({
				id:		'btsp_modal_alter',
				title:	'Error-CONERO@...',
				content: '<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-info-sign"></span> <strong>'+title+'!</strong> '+text+'</div>'
			});
		};
		/**
		 * 动态进度条 2017年1月7日 星期六
		 * option:JSON {
		 * 			id: elId		元素ID
		 * 			max: 100		最大值
		 * 			start: 0		起始值
		 * 			sec: 1s			执行相间时间/ s
		 * 			rate: (1-10) 	增加幅度 - 默认 1: 10% 百分之10的新增速度
		 * 			type: success/info/warning/danger
		 * 			html: selector/element	 元素插入值
		 * 			append: selector/element	 元素插入值
		 * }
		 * close
		 */
		var _pGridSIntervalId;
		obj.progressGrid = function(option,clearMk){			
			option = obj.is_object(option)? option:{};
			var id = option.id? option:'btsp_dynamic_progress';
			var max = option.max? option.max : 100;
			var start = option.start? option.start : 0;
			var bar = $('#'+id);
			var type = option.type? option.type:'success';
			var rate = (option.rate && !isNaN(option.rate) && (option.rate>0 && option.rate <11))? parseInt(option.rate):1;
			rate = Math.ceil(max*rate*0.1);
			// 清除当前正在运行的定时器
			clearMk = option.close? true : clearMk;
			if(!obj.empty(clearMk)){
				if(_pGridSIntervalId) clearInterval(_pGridSIntervalId);
				if(bar.length > 0) bar.remove();		// 删除元素
				return true;
			}
			else if(_pGridSIntervalId) clearInterval(_pGridSIntervalId);
			// 生成
			var progressBar = 
				'<div class="progress-bar progress-bar-'+type+' progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="'+max+'" style="width: 40%">'
				+	'<span class="sr-only">40% Complete ('+type+')</span>'
				+'</div>'
				;
			var xhtml = 
				'<div class="progress" id="'+id+'">'
				+ progressBar
				+'</div>'
				;
			var el;
			if(option.html){
				el = obj.is_object(option.html)? option.html: $(option.html);
				if(bar.length == 0) el.html(xhtml);
				else bar.html(progressBar);
			}
			else{
				if(option.append) el = obj.is_object(option.append)? option.append: $(option.append);
				else el = $('body');
				if(bar.length == 0) el.append(xhtml);
				else bar.html(progressBar);				
			}
			// 定时器执行
			bar = bar.length == 0? $('#'+id):bar;
			var lineEl = bar.find('div[role="progressbar"]');
			var initValue = start;
			var dynamicFn = function(){								
				if(initValue > max) initValue = start;
				lineEl.attr("aria-valuenow",initValue);
				lineEl.css({'width':initValue+'%'});
				lineEl.find('span.sr-only').text(initValue+'% Complete ('+type+')');
				initValue = initValue + rate;				
			};
			var sec = option.sec && !isNaN(option.sec)? option.sec:1;
			sec = sec * 1000;			
			_pGridSIntervalId = setInterval(dynamicFn,sec);   		// clearInterval() ->
			return _pGridSIntervalId;
		};
		// confirm - modal 插件确认按钮 - 文本，回调函数，标题
		obj.confirm = function(text,callback,title){
			text = text? text:'数据确认框';
			title = title? title:'Confirm-CONERO@...';
			var content = '<div class="alert alert-danger" role="alert"><strong>(^_^)</strong> '+text+'</div>';
			this.modal({
				id:	'btsp_modal_confirm',
				title:title,
				content:content,
				save:callback
			});
		};
		// prompt 绑定输入框
		obj.prompt = function(text,callback){
			text = text? text : 'prompt 输入示例';
			var content = ''
				+ '<div class="has-error">'
				+ '<label for="btsp_mdprompt_impt">'+text+'</label>'
				+ '<input id="btsp_mdprompt_impt" class="form-control">'
				+ '</div>'
				;
			var title = "输入信息确认";
			var afterCheckEven = function(){
				var value = $('#btsp_mdprompt_impt').val();
				var defaultFn = function(){
					$('#btsp_modal_prompt').modal('hide');
				};
				if(obj.is_function(callback)) callback(value);
				defaultFn();
			};
			this.modal({
				id:	'btsp_modal_prompt',
				title:title,
				content:content,
				save : afterCheckEven
			});
		};
		/**
		 *	2016年12月3日 星期六
		 *	option = {post,field},fn = {serach:function(){},save:function(){},selected:function(){},next:function(){}}
		 *  option = {title:'账单选择器',
		 * 			field:{use_date:'日期',finc_no:'hidden',name:'名称'},
		 * 			post:{table:'finc_set',order:'use_date desc',map:'center_id="'+Cro.uInfo.cid+'"'},
		 * 			pupopId: 控件ID - 默认为/ 表名称
		 * 			single:单选};
		 **/
		obj.pupop = function(option,fn){			
			option = obj.is_object(option)? option:{};
			fn = obj.is_object(fn)? fn:{};
			var post = option.post;
			var field = option.field, postField = new Array();
			var table = '',value ='';
			var mulSelected = obj.empty(option.single)? true:false;		// 多选
			var largeSize = option.largeSize;
			var id = option.pupopId? option.pupopId:post.table;	// 控件ID
			var cols = 0;
			for(var k in field){
				cols = 1 + cols;
				postField.push(k);
				value = field[k];
				table += '<th'+(value == 'hidden'? ' class="hidden"':'')+'>'+value+'</th>';
			}
			table = '<table class="table"><tr><th>#</th>'+table+'<th>选择</th></tr>';
			post.field = postField.join(',');
			// 原始条件 - 只读
			var _sourcePostMap = post.map;
			var _sourceSearchMap = _sourcePostMap;		// 固定执行的查询条件-用于翻页
			// 单项选择涉及多次绑定事件
			var mulBindSelectEvent = function(){
				// 选择事件绑定
				if(obj.is_function(fn.selected)){
					$('#'+id+' [dataid="selected"]').unbind('click'); // 绑定前解绑-避免重复绑定
					$('#'+id+' [dataid="selected"]').on('click',fn.selected);// 单选择
				}
			};
			$.post('/conero/index/common/popup.html',post,function(data){
				// 生成表格喊函数 data 数据； startRow 最大行
				var createTabel = function(newData,startRow){
					var data = newData;
					if(obj.empty(data)) return;// 不存在时不执行函数
					var result = JSON.parse(data);
					data = result.data;
					var trs = '',i = 1,td = '',isbreak;
					if(startRow){
						i = $('#'+id).find('tr').length;
					}
					for(var k in data){
						if(obj.is_function(data[k])) continue;// for(遍历有函数对象)
						td = '<tr class="datarow"><td>'+i+'</td>';
						isbreak = false;
						for(var kk in field){
							if(obj.empty(data[k])){
								isbreak = true;
								break;
							}
							value = field[kk];
							td += '<td class="'+(value == 'hidden'? 'hidden':kk)+'">'+data[k][kk]+'</td>';
						}
						if(isbreak) break;
						trs += td+'<td>'+((mulSelected == true)? '<input type="checkbox" name="popupchecked">':'<a href="javascript:void(0);" dataid="selected">选择</a>')+'</td></tr>';
						//trs += td+'<td><input type="button" name="popup_checked"></td></tr>';
						i++;
					}
					if(startRow){// 翻页时使用
						var alertDiv = $('#'+id).find('div.alert');
						alertDiv.find('[dataname="no"]').text(result.no);
						alertDiv.find('[dataname="pages"]').text(result.pages);
						var datatable = $('#'+id).find('table');
						datatable.append(trs);
						// 选择事件重新绑定
						mulBindSelectEvent();
						return trs;
					}
					var info = ' 数据条数<span dataname="count">'+result.count+'</span>,当前分页：<span dataname="no">'+result.no+'</span>/<span dataname="pages">'+result.pages+'</span>';		
					var html = table+trs+
						'</table>'+
						'<div class="alert alert-success" role="alert">'+
						'<a href="javascript:void(0);"><button type="button" class="btn btn-primary" dataid="nextpage">+</button></a> '+info+
						'</div>'
						;			
					return html;
				};			
				// 搜索框	
				var opts = '';
				for(var kk in field){
					if('hidden' != field[kk]) opts += '<option value="'+kk+'">'+field[kk]+'</option>'
				}
				var search = '<div class="form-inline">'+											
						'<select class="form-control" name="skey">'+opts+'</select>'+
						'<input type="text" class="form-control" name="svalue">'+
						'<button type="button" class="btn btn-primary" dataid="search">查找</button>'+
						'</div>';
				var content = search + createTabel(data);
				var popup = {
					content:content,
					footer:(mulSelected == false? '':'<button type="button" class="btn btn-default" dataid="save">确认</button>'),
					id: id,
					large:(largeSize? true:null)
				};
				// 列数过长时自动切换成-大尺寸/ modal
				if(cols>3 && obj.empty(popup.large)) popup.large = true;
				if(!obj.empty(option.title)) popup.title = option.title;
				obj.modal(popup);
				// 事件绑定 回调对象
				var callSearch = true,callNext = true;				
				if(obj.is_object(fn)){
					//if(obj.is_function(fn.search)) $(document).on('click','#'+id+' [dataid="search"]',fn['search']);// 搜索  $(document).on - 无法解绑，此处可能引起事件重复绑定/一次触发引发多次响应
					if(obj.is_function(fn.search)){ // 搜索
						callSearch = false;
						$('#'+id+' [dataid="search"]').unbind('click'); // 绑定前解绑-避免重复绑定
						$('#'+id+' [dataid="search"]').on('click',fn.search);
					}
					/*
					if(obj.is_function(fn.selected)){
						$('#'+id+' [dataid="selected"]').unbind('click'); // 绑定前解绑-避免重复绑定
						$('#'+id+' [dataid="selected"]').on('click',fn.selected);// 单选择
					}
					*/
					mulBindSelectEvent();
					if(obj.is_function(fn.save)){
						$('#'+id+' [dataid="save"]').unbind('click'); // 绑定前解绑-避免重复绑定
						$('#'+id+' [dataid="save"]').on('click',fn.save);// 保存
					}
					if(obj.is_function(fn.next)){ // 数据加载
						callNext = false; 
						$('#'+id+' [dataid="nextpage"]').unbind('click'); // 绑定前解绑-避免重复绑定
						$('#'+id+' [dataid="nextpage"]').on('click',fn.next);
					}
				}
				// 搜索事件自动生成
				if(callSearch == true){					
					$('#'+id+' [dataid="search"]').unbind('click'); // 绑定前解绑-避免重复绑定
					$('#'+id+' [dataid="search"]').on('click',function(){
						var form = $(this).parents('div.form-inline');
						//var datatable = $('#'+id).find('table');
						var skey = form.find('select option:selected').val();
						var input = form.find('input[name="svalue"]');
						var svalue = input.val();
						if(obj.empty(svalue)){input.focus();return;}
						var searchPost = post;
						var map = _sourcePostMap, wh;
						if(obj.is_object(map)){
							map[skey] = ['like','%'+svalue+'%'];
							searchPost.map = map;
						}
						else if(obj.is_string(map) && map){
							searchPost.map = _sourcePostMap + ' and '+skey+' like \'%'+svalue+'%\'';
						}
						// 从第首页开始
						searchPost.page = 1;
						_sourceSearchMap = searchPost.map;
						$.post('/conero/index/common/popup.html',searchPost,function(data){							
							var html = createTabel(data);
							var body = $('#'+id).find('div.modal-body');
							body.find('table').remove();
							body.find('div.alert').remove();
							body.append(html);		
							// 选择事件重新绑定
							mulBindSelectEvent();	
							// 翻页处理事件 - 重新更改数据记录
							$('#'+id+' [dataid="nextpage"]').unbind('click');
							$('#'+id+' [dataid="nextpage"]').on('click',fn.next);			
						});
					});
				}
				// 页码翻页
				if(callNext == true){
					$('#'+id+' [dataid="nextpage"]').unbind('click'); // 绑定前解绑-避免重复绑定
					// 默认翻页函数
					if(!obj.is_function(fn.next)){
						fn.next = function(){
							var alertDiv = $('#'+id).find('div.alert');//obj.log(alertDiv,alertDiv.find('[dataname="no"]').text(),alertDiv.find('[dataname="pages"]').text());
							var no = parseInt(alertDiv.find('[dataname="no"]').text());
							var pages = parseInt(alertDiv.find('[dataname="pages"]').text());
							var page = 1;
							var form = $('#'+id).find('div.form-inline');
							//var datatable = $('#'+id).find('table');
							var skey = form.find('select option:selected').val();
							var input = form.find('input[name="svalue"]');
							var svalue = input.val();
							var serachPost = post;
							/*
							var map = post.map, wh = '';
							if(obj.is_object(map)) map[skey] = ['like','%'+svalue+'%'];
							else if(obj.is_string(map) && map){
								wh = ' and '+skey+' like \'%'+svalue+'%\'';
							}
							serachPost.map = map + wh;map = '';
							*/
							serachPost.map = _sourceSearchMap;
							// map 在ajax请求错误时 会覆盖会叠加原来的值 ??

							if(no < pages) page = no + 1;
							else return;
							serachPost.page = page;	
							$.post('/conero/index/common/popup.html',serachPost,function(data){
								createTabel(data,true);								
							});
						};
					}
					$('#'+id+' [dataid="nextpage"]').on('click',fn.next);
				}
			});			
		};
		/*  2016年12月13日 星期二
		 *	表格多记录事件处处理
		 *	option{ 基础配置 json - * 表示必填 + 可选项
			 table* 表格选择器 ; addBtn+,delBtn+,saveBtn+,uarrBtn 上移,duarrBtn 下移, selAllCkbox 全选,  新增/删除/保存 按钮选择器-可默认	- row_add_btn/row_del_btn/row_save_btn/row_uarr_btn/row_darr_btn/row_allsel_ckbox
			 least1:true, 删除式至少保留一列
			 form:table,
			 url:form.action,
			 rowselecter: true 开启列选择
			}
		 *	event{	// 事件组
			 		AddCheck(最大行对象):新增检测时间,
					afterAddRow(lastRow-新增行对象): 新增行以后的操作
					beforeSaveData(data): 提交以前的操作
			}
		 */
		obj.formListEvent = function(option,event){
			event = obj.is_object(event)? event:{};
			if(obj.empty(option)) return;
			var table = $(option.table);
			var addBtn = obj.empty(option.addBtn)? '#row_add_btn':option.addBtn;
			var delBtn = obj.empty(option.delBtn)? '#row_del_btn':option.delBtn;
			var saveBtn = obj.empty(option.saveBtn)? '#row_save_btn':option.saveBtn;
			var uarrBtn = option.uarrBtn? option.uarrBtn:'#row_uarr_btn';
			var darrBtn = option.darrBtn? option.darrBtn:'#row_darr_btn';
			var selAllCkbox = option.selAllCkbox? option.selAllCkbox:'#row_allsel_ckbox';
			var least1 = obj.empty(option.least1)? true:false;	// 至少保留一列
			var form = obj.empty(option.form)? option.table:option.form;
			var url = obj.empty(option.url)? null:option.url;
			var pk = obj.empty(option.pk)? null:option.pk; // 主键
			var rowselecter = obj.empty(option.rowselecter)? false:true;
			if(table.find('.rowno .rowselecter').length > 0 && rowselecter == false) rowselecter = true;
			if(obj.empty(url) && $(form).is('form')) url = $(form).attr("action");
			var deleteList = new Array();	// 删除列表 ID - [{pk:'value','type':'D'},{pk:'主键','type':'D'}]
			
			// 私有函数 - 新增列后编号生成器
			function _afterAddRowNoMaker(lastRow,len){
				if(rowselecter) lastRow.find('td[class="rowno"]').html('<input type="checkbox" class="rowselecter"> ' + len);
				else lastRow.find('td[class="rowno"]').text(len);
				lastRow.attr('dataid',len);
			}

			// 对象
			var formActon = function(th){
				// 通过 记录新增数据 - 
				this.addRowByRecord = function(record,before,after,primarykey){
					primarykey = primarykey? primarykey:pk;
					if(th.empty(primarykey)){
						th.modal_alert('无法发现数据主键，函数初始化时!');return;
					}				
					record = th.is_string(record)? JSON.parse(record):record;
					if(th.is_function(before)) before(record);
					var len = table.find("tr").length;				
					if(record[primarykey] && !th.is_object(record[primarykey]) && this.havePkey(record[primarykey])) return;
					// 检测上一列数据合法后新增否则组织本次新增
					var lastRow = table.find('tr[dataid="'+(len-1)+'"]'), Pkey,mRecord;
					Pkey = lastRow.find('[name="'+primarykey+'"]');
					if(Pkey.length > 0 && !th.empty(Pkey.val())){
						$(addBtn).click();
						len = table.find("tr").length;
						lastRow = table.find('tr[dataid="'+(len-1)+'"]');
					}
					var isMutilRow = false;			
					for(var k in record){						
						if(th.is_number(k) && th.is_object(record[k])){	// 多列
							isMutilRow = true;
							Pkey = lastRow.find('[name="'+primarykey+'"]');
							if(Pkey.length > 0 && !th.empty(Pkey.val())){
								$(addBtn).click();
								len = table.find("tr").length;
								lastRow = table.find('tr[dataid="'+(len-1)+'"]');
							}						
							mRecord = record[k];							
							for(var mK in mRecord){
								if(mRecord[primarykey] && !th.is_object(mRecord[primarykey]) && this.havePkey(mRecord[primarykey])) continue;
								Pkey = lastRow.find('[name="'+primarykey+'"]');
								if(Pkey.length == 0) lastRow.find('td:first-child').append('<input type="hidden" name="'+primarykey+'" value="'+record[primarykey]+'">');
								lastRow.find('[name="'+mK+'"]').val(mRecord[mK]);
								if(th.is_function(after)) after(lastRow);
							}
						}
						else{// 单列					
							Pkey = lastRow.find('[name="'+primarykey+'"]');
							if(Pkey.length == 0) lastRow.find('td:first-child').append('<input type="hidden" name="'+primarykey+'" value="'+record[primarykey]+'">');
							lastRow.find('[name="'+k+'"]').val(record[k]);							
						}
					}
					if(th.is_function(after) && isMutilRow == false) after(lastRow);
				};
				// 数据重复性检测
				this.havePkey = function(value,primarykey){
					primarykey = primarykey? primarykey:pk;
					if(value && primarykey){
						var rows = table.find('[value="'+value+'"]'),Rd,name;
						for(var i=0; i<rows.length; i++){
							Rd = $(rows[i]);
							name = Rd.attr("name");
							if(primarykey == name) return true;
						}
					}
					return false;
				};
				// 手动新增列 
				this.addRow = function(callback){
					len = table.find("tr").length;
					lastRow = table.find('tr[dataid="'+(len-1)+'"]');					
					var html = '<tr dataid="'+len+'">'+lastRow.html()+'</tr>';
					table.append(html);
					lastRow = table.find('tr[dataid="'+len+'"]');
					_afterAddRowNoMaker(lastRow,len);
					if(th.is_function(callback)){						
						callback(lastRow);
					}
				};
				// 从列中插入数据 - 只能选择一列
				this.insertRow = function(dataid,lastAble){
					lastAble = lastAble? true:false;
					if(rowselecter && obj.empty(dataid)){
						var rsels = table.find('input.rowselecter');
						var ckbox;
						for(var k=0; k<rsels.length; k++){
							ckbox = $(rsels[k]);
							if(rsels[k].checked){
								dataid = ckbox.parents('tr').attr("dataid");
								break;
							}
						}
					}								
					var maxRow = table.find("tr").length;	
					var curTr,isAppend = false;
					if(dataid && dataid < (maxRow-1)){
						curTr = table.find('tr[dataid="'+dataid+'"]');
						isAppend = true;
					}
					else if(lastAble) curTr = table.find('tr[dataid="'+(maxRow-1)+'"]');
					if(obj.formRequired(curTr,true)){return;}
					if(obj.is_function(event.AddCheck)){
						if(event.AddCheck(table.find(curTr))) return;
					}
					var len = isAppend? parseInt(curTr.attr("dataid")) + 1 : maxRow;
					var html = '<tr dataid="'+len+'">'+curTr.html()+'</tr>';
					if(isAppend){
						curTr.after(html);
						this.resetRowOrder();
					}
					else table.append(html);
					lastRow = table.find('tr[dataid="'+len+'"]');
					_afterAddRowNoMaker(lastRow,len);					
					if(obj.is_function(event.afterAddRow)) event.afterAddRow(lastRow); // 事件绑定- 新增以后的事件处理
					// 主键清除
					if(!obj.empty(pk) && lastRow.find('input[name="'+pk+'"]').length > 0){
						lastRow.find('input[name="'+pk+'"]').remove();
					}
				};
				// 手动删除列 - dataid array[int]/int 为指定列默认为末尾列
				this.delRow = function(dataid,closeReset){
					closeReset = closeReset? closeReset:false;
					var maxRow = table.find("tr").length;
					if(obj.empty(dataid)) dataid = maxRow - 1;
					else if(obj.is_array(dataid)){ // 递归执行
						for(var k = 0; k<dataid.length; k++) this.delRow(dataid[k],true);
						return this.resetRowOrder();
					}
					// 至少保留一列
					if(least1 && maxRow == 2){
						return;
					}
					var rowDom = table.find('tr[dataid="'+dataid+'"]');
					if(rowDom.length > 0){
						this.appendDeleteList(rowDom);							
						rowDom.remove();
						if(dataid < (maxRow -1) && closeReset == false){						
							this.resetRowOrder();
						}
					}
				};
				// 输入tr的dataid值获取对象
				this.getRow = function(dataid){
					dataid = dataid? dataid:1;
					return table.find('tr[dataid="'+dataid+'"]');	
				};
				// 删除列表增加
				this.appendDeleteList = function(rowDom){
					if(obj.empty(rowDom)) return ;
					if(!obj.empty(pk) && rowDom.length > 0){
						var pkDom = rowDom.find('[name="'+pk+'"]');
						var hasingPk = pkDom.length > 0 ? true:false;
						if(hasingPk){
							var model = {'type':'D'};
							model[pk] = pkDom.val();
							deleteList.push(model);
						}			
					}	
				};
				// 获取已选择的列
				this.getRowSel = function(callback){
					if(rowselecter){
						var rsels = table.find('input.rowselecter');
						var List = [];	// 列编号列
						var td,ckbox,no;
						for(var k=0; k<rsels.length; k++){
							// bug 此处 jQuery获取元素失败，而直接用dom则成功 ???? - 2017年2月15日 星期三
							ckbox = $(rsels[k]);
							// obj.log(ckbox,ckbox.attr("checked"),ckbox.is(":checked"),ckbox.length,ckbox.checked,rsels[k],rsels[k].checked);
							// if(ckbox.attr("checked")){
							if(rsels[k].checked){
								// ckbox = $(rsels);
								// ckbox = jQuery(rsels);
								no = ckbox.parents('tr').attr("dataid");
								if(obj.is_function(callback)){
									callback(this.getRow(no));
								}
								List.push(no);
							}
						}
						return List;
					}			
					return false;		 
				};
				// 取消选择
				// onlyOne 仅仅单选
				this.canselSel = function(onlyOne){
					onlyOne = onlyOne? true:false;
					if(rowselecter){
						var rsels = table.find('input.rowselecter');					
						for(var k = 0; k<rsels.length; k++){
							if(rsels[k].checked && onlyOne == true){
								onlyOne = false;
								continue;
								/*
								if(onlyOne){onlyOne = false;}
								else rsels[k].checked = false;
								*/
							}
							rsels[k].checked = false;
						}
					}
				};
				// 设置选中 -> 有值时全选，否则...
				this.selCkbox = function(dataid){
					if(rowselecter){
						var rsels = table.find('input.rowselecter');	
						var dataList;				
						for(var k = 0; k<rsels.length; k++){
							if(dataid){
								dataList = obj.is_array(dataid)? dataid:[dataid];
								rsels[k].checked = obj.inArray(k,dataList)? true:false;
								// $(rsels[k]).attr('checked',obj.inArray(k,dataList)? true:false);
								// obj.log(dataList,k,rsels[k].checked);
							}
							else rsels[k].checked = true;
						}
					}
				};
				// 获取单个选择值
				this.getSingleRow = function(){
					var rowno = false;
					if(rowselecter){
						var rsels = table.find('input.rowselecter');
						var List = [];	// 列编号列
						var td,ckbox,no;
						for(var k=0; k<rsels.length; k++){
							// bug 此处 jQuery获取元素失败，而直接用dom则成功 ???? - 2017年2月15日 星期三
							ckbox = $(rsels[k]);
							if(rsels[k].checked){
								rowno = parseInt(ckbox.parents('tr').attr("dataid"));
								break;
							}
						}
					}	
					return rowno;	
				};
				// 列重排序
				this.resetRowOrder = function(){
					var trs = table.find('tr');
					var curTr;
					for(var k=0; k<trs.length; k++){
						if(k == 0) continue;
						curTr = $(trs[k]);
						_afterAddRowNoMaker(curTr,k);
						// curTr.attr("dataid",k);
						// curTr.find('td.rowno').text(k);
					}
				};
				// 列对象
				this.rowObj = function(curTr){
					if(obj.is_object(curTr)){
						return new function(){
							// 列子设置或获取
							this.val = function(name,value){
								var td = curTr.find('input[name="'+name+'"]');
								if(obj.undefind(value)){
									return td.val();
								}else{
									td.val(value);
								}
							}
						};
					}
				};
			};
			
			var _formGrid = new formActon(this);
			if($(addBtn).length > 0){
				// +
				$(addBtn).click(function(){	
					_formGrid.insertRow(null,true);
					/*
					var len = table.find("tr").length;
					// 检测上一列数据合法后新增否则组织本次新增
					var lastRow = table.find('tr[dataid="'+(len-1)+'"]');
					if(obj.formRequired(lastRow,true)){return;}
					if(obj.is_function(event.AddCheck)){
						if(event.AddCheck(table.find(lastRow))) return;
					}
					var html = '<tr dataid="'+len+'">'+lastRow.html()+'</tr>';
					table.append(html);
					lastRow = table.find('tr[dataid="'+len+'"]');
					_afterAddRowNoMaker(lastRow,len);					
					if(obj.is_function(event.afterAddRow)) event.afterAddRow(lastRow); // 事件绑定- 新增以后的事件处理
					*/
				});
			}
			if($(delBtn).length > 0){
				// -
				$(delBtn).click(function(){					
					// 优先删除已选择的列
					if(rowselecter){
						var seledList = _formGrid.getRowSel(function(tr){
							_formGrid.appendDeleteList(tr);
							tr.remove();
						});
						if(obj.is_array(seledList) && seledList.length>0){
							_formGrid.resetRowOrder();
							return;
						}
					}
					_formGrid.delRow();
				});
			}
			if($(saveBtn).length > 0){
				// 保存
				$(saveBtn).click(function(){
					var len = table.find("tr").length;
					var tr, saveData = new Array(),data;
					for(var i = 1; i<len; i++){
						tr = table.find('tr[dataid="'+i+'"]');
						data = obj.formRequired(tr);
						if(!obj.is_object(data)) return;
						saveData.push(JSON.stringify(data));
					}
					// 删除记录还原
					if(deleteList && deleteList.length > 0){
						for(var i=0; i<deleteList.length; i++){
							saveData.push(JSON.stringify(deleteList[i]));
						}
					}
					// 数据提交以前的操作-用在修改保存的数据
					if(obj.is_function(event.beforeSaveData)){
						var newSaveData = event.beforeSaveData(saveData);
						if(obj.is_object(newSaveData) && obj.objectLength(newSaveData) > 0){// 构造新的保存数据
							obj.post(url,newSaveData);return false;
						}
						// 用于调试作用
						else if(newSaveData) return false;
					}
					obj.post(url,saveData);
					return false;
				});
			}
			if($(uarrBtn).length > 0 && rowselecter){
				// 上移 - 开启可选行 
				$(uarrBtn).click(function(){
					var rowno = _formGrid.getSingleRow();
					if(rowno && rowno > 1){
						var targetNo = rowno - 1;
						var targetDom = table.find('tr[dataid="'+targetNo+'"]');
						var targetJson = obj.formJson(targetDom);
						var cDom = table.find('tr[dataid="'+rowno+'"]');
						var cJson = obj.formJson(cDom);
						obj.formJsonUpdate(targetDom,cJson);
						obj.formJsonUpdate(cDom,targetJson);	
						// _formGrid.selCkbox(targetNo);			
						// _formGrid.selCkbox(targetNo+1);			
						_formGrid.selCkbox(targetNo-1);			
					}
					else _formGrid.canselSel();
				});
			}
			if($(darrBtn).length > 0 && rowselecter){
				// 下移 - 客气可选行
				$(darrBtn).click(function(){
					var rowno = _formGrid.getSingleRow();
					var maxRow = table.find('tr').length - 1;
					if(rowno && rowno < maxRow){
						var targetNo = rowno + 1;
						var targetDom = table.find('tr[dataid="'+targetNo+'"]');
						var targetJson = obj.formJson(targetDom);
						var cDom = table.find('tr[dataid="'+rowno+'"]');
						var cJson = obj.formJson(cDom);
						obj.formJsonUpdate(targetDom,cJson);
						// obj.log(cDom,targetJson,targetDom,cJson);
						// obj.log(rowno,targetNo);
						obj.formJsonUpdate(cDom,targetJson);	
						// _formGrid.selCkbox(targetNo);
						_formGrid.selCkbox(rowno);
					}
					else _formGrid.canselSel();
				});
			}
			if($(selAllCkbox).length > 0 && rowselecter){
				// 全选按钮
				$(selAllCkbox).change(function(){
					var isChecked = $(this).is(':checked');
					if(isChecked) _formGrid.selCkbox();
					else _formGrid.canselSel();
				});
			}
			return _formGrid;
		};
		/*	
		@ 时间： 2017年1月6日 星期五
		option = {
			form: 表单头部
			formTagOpen: 如果 form 属性存在时- true表不关闭尾部标签
			param:[
				{
					*name: 名称
					id: 为空时- 构造/ {name}_ipter
					label: 标签
					*type: text(默认),hidden,textarea,static
					staticText: 静态文本
					formType:success/warning/error
					tipText: 辅助提示文本
					disabled:true
					readonly:true
					require:true
					key: - 当record的键值不是 - name - 时/利用 key					
				}
			]
			record:{name:value} - 数据值
		}
		*/
		// Bootstrap 表单空间生成器
		obj.formGroup = function(option){			
			option = obj.is_object(option)? option:{};
			var record = option.record? option.record:{};	// 数据记录 - JSON
			var param = option.param? option.param:[];		// 表格参数 - Array		
			option.formTagOpen = option.formTagOpen? false:true;	
			// textarea 生成器
			var _textareaMaker = function(json) {
				return '<textarea class="form-control" id="'+json.id+'" name="'+json.name+'">'+(record[json.key]? record[json.key]:'')+'</textarea>';
			};
			// 属性生成器
			var _attrMaker = function(json){
				var html = '';
				var tmpArray = [];
				for(var k in json){
					tmpArray.push(k+'="'+json[k]+'"');
				}
				if(tmpArray>0) html = ' '+tmpArray.join(' ');
				return html;
			};
			var xhtml = (option.form? option.form:''),
				json,name,id,type,formType,tipText,key;
			for(var i=0; i<param.length; i++){
				json = param[i];
				if(json.require) json.formType = 'success';
				else json.formType = null;				
				name = json.name;
				id = json.id? json.id:name+'_ipter';
				json.id = id;				
				type = json.type? json.type:'text';
				formType = json.formType? ' has-'+json.formType:'';
				tipText = json.tipText? json.tipText:null;
				key = json.key? json.key:name;
				json.key = key;
				if(type == 'hidden'){
					xhtml += '<input type="hidden" name="'+name+'" value="'+record[key]+'">';
				}
				// 静态文本
				else if(type == 'static'){
					var staticText = option.staticText? option.staticText:record[key];
					xhtml += 
						'<div class="form-group">'
						+'	<label class="col-sm-2 control-label">'+json.label+'</label>'
						+'	<div class="col-sm-10">'
						+'	<p class="form-control-static">'+staticText+'</p>'
						+'	</div>'
						+ '</div>'
					;
				}
				else
				xhtml += '<div class="form-group'+formType+'">'
					  + '<label class="control-label" for="'+id+'">'+json.label+'</label>'
					  + (type == 'textarea'? 
					  	  _textareaMaker(json):
						  '<input type="'+type+'" class="form-control" name="'+name+'" id="'+id+'"'+(record[key]? ' value="'+record[key]+'"':'')+(obj.is_object(option.attrs)? attrMaker(option.attrs):'')+(option.disabled? ' disabled="true"':'')+(option.readonly? ' readonly="true"':'')+'>')
					  + (tipText? '<span class="help-block">'+tipText+'</span>':'')
					  + '</div>'
					;
			}
			if(option.form && option.formTagOpen) xhtml += '</form>';
			return xhtml;
		};
		/**
		 * 2017年1月12日 星期四
		 * 面板隐藏于显示
		 */
		obj.panelToggle = function(selectors) {
			selectors = obj.is_string(selectors)? [selectors]:selectors;
			for(var i=0; i<selectors.length; i++){
				$(selectors[i]).click(function(){
					$(this).parents("div.panel").find("div.panel-body").toggleClass('hidden');
				});
			}
		};
		/**
		 * 2017年3月10日 星期五
		 * pupop 窗 清除链接  help-block 下
		 */
		obj.relieve_lnk = function(selector,formSelctor,beforeRelieve){
			if(obj.empty(selector)) return;
			var selector = obj.is_object(selector)? selector:$(selector);
			if(selector.length > 0){
				selector.click(function(){
					if(obj.is_function(beforeRelieve)){
						var ret = beforeRelieve();
						if(ret) return;
					}
					if(!obj.empty(formSelctor) && obj.is_array(formSelctor)){
						for(var k=0; k<formSelctor.length; k++){
							$(formSelctor[k]).val('');
						}
					}
					else{
						var formGroup = $(this).parents('div.form-group');
						var ipts = formGroup.find('input'),
							el,
							type
							;
						for(var k=0; k<ipts.length; k++){
							el = $(ipts[k]);
							type = el.attr("type");
							if(obj.empty(type) || type == 'radio' || type == 'checkbox' || type == 'button' || type == 'submit') continue;
							el.val('');
						}
						var textarea = formGroup.find('textarea');
						if(textarea.length > 0) textarea.val('');
					}
				});
			}
		}
	};
	// 插件
	function _privatePlugin(th){
		// 需要引入 tinymce 用于统一 富文本样式
		th.tinymce = function(selector){
			tinymce.init({
				selector: selector,
				plugins: [
					'advlist autolink lists link image charmap print preview anchor',
					'searchreplace visualblocks code fullscreen',
					'insertdatetime media table contextmenu paste code'
				]
			});
		}
	}
	_privateApp(this);
	_privateBootstrap(this);
	_privatePlugin(this);
}

/**----------------------新扩展--------------------------2016年8月30日 星期二>>  				类似数据库处理操作// js面向对象式编程
*	engine 引擎|| session/local，默认前者
*	add()		插入数据到storage内，会覆盖历史数据
*	update()	更新数据到storage内，支持 json/string，可实现删除内部数据
*	select()	storage数据获取
*	get()		简单数据获取法
*	
*	table()		get/set storage数据键名
*	error()		get/set storage数据异常
*	session()/local()	storage数据设置获取函数(原型)
*	undefind()	数据格式检测
*	object(),empty()
*/
function jutilStorage(engine){
	if('session' != engine && 'local' != engine) this.engine = 'session';
	else this.engine = engine;
	this.table = function(tb){
		if(this.undefind(tb)){
			var table = this._table;
			if(this.undefind(table)) this._table = '';
			return this._table;
		}else{
			this._table = tb;
			return this; // 链式数据处理
		}
	}
	//	会覆盖原来的值，若存在原来的json数据/JSON
	this.add = function(data,table){
		if(this.empty(table)) table = this.table();
		if(this.empty(table)){
			this.error('add## 无法获取到table的值！');
			return '';
		}
		if(!this.object(data)){
			this.error('add## 存储数据必须为JSON格式数据！');
			return '';
		}
		var str = JSON.stringify(data);
		if(this.engine == 'session') this.session(table,str);
		else this.local(table,str);
		return true;
	}
	//	更新数据/可更新不存在的数据
	this.update = function(key,value){
		var tb = this.table();
		if(this.empty(tb)){
			this.error('select## 无法获取到table的值！');
			return false;
		}
		var data = this.select();
		if(this.empty(key)){
			this.error('update## 无法获取到key的值,请设置key（json/string）值！');
			return false;
		}
		if(!this.object(data)) data = {};
		if(this.object(key)){			
			for(var k in key){data[k] = key[k];}
		}
		if(this.undefind(value)){
			delete data[key];
		}else{
			data[key] = value;
		}
		var str = JSON.stringify(data);
		return this.engine == 'session'? this.session(tb,str):this.local(tb,str);
	}
	this.select = function(key,value){
		var tb = this.table();
		if(this.empty(tb)){
			this.error('select## 无法获取到table的值！');
			return '';
		}
		var str = this.engine == 'session'? this.session(tb):this.local(tb);
		try{
			var data = JSON.parse(str);
		}catch(e){
			this.error(e);
			var data = {};
		}
		if(this.empty(key)) return data;//	返回整个json数据
		else if(this.undefind(value)){//	返回单个json数据
			if(!this.empty(data) && data[key]) return data[key];
			return '';
		}
		data[key] = value;//	设置json的属性值
	}
	// 分隔符解析数组
	this.array = function(key,value,delimiter){
		if(this.empty(key)) return;
		delimiter = delimiter? delimiter:',';
		if(this.undefind(value)){// 获取值
			var tmp = this.get(key);
			if(tmp) return tmp.split(delimiter);
			return;
		}
		if(this.empty(value)) return;
		var tmp = this.get(key);
		if(tmp.indexOf(value) == -1){			
			var arr = tmp.split(delimiter);
			arr.push(value);
			this.update(key,arr.join(delimiter));
		}
	}
	
	// 函数分隔符数组中指定的属性值
	this.removeArray = function(key,value,delimiter){
		var arr = this.array(key);
		var newArr = new Array();
		for(var i=0; i<arr.length; i++){
			if(arr[i] == value) continue;
			newArr.push(arr[i]);
		}
		delimiter = delimiter? delimiter:',';
		return this.update(key,newArr.join(delimiter));		
	}
	this.get = function(key){
		return this.select(key);
	}
	this.undefind = function(value){
		if(typeof(value) == 'undefind') return true;
		else if(value == null) return true;
		return false;
	}
	this.object = function(data){
		if(null == data) return false;
		if(typeof(data) == 'object') return true;
		return false;
	}
	this.empty = function(value){
		if(this.undefind(value)) return true;
		else if(value == '') return true;
		else if(value == 0) return true;
		return false;
	}
	this.session = function(name,value)
	{
		if(this.undefind(window.sessionStorage)){this.error('浏览器不支持 sessionStorage');}
		if(this.empty(name)) return null;
		if(this.empty(value))	return sessionStorage.getItem(name);
		sessionStorage.setItem(name,value);
		return true;
	}
		//loaclstroge 本地存储	2016/4/8
	this.local = function(name,value)
	{
		if(this.undefind(window.localStorage)){this.error('浏览器不支持 localStorage');}
		if(this.empty(name)) return false;
		if(this.empty(value))	return localStorage.getItem(name);
		localStorage.setItem(name,value);
		return true;
	}
	// 删除storage
	this.delete = function(tb){
		tb = tb || this._table;
		if(this.empty(tb)) return false;
		if(engine == 'local'){
			if(localStorage.getItem(tb)){
				localStorage.removeItem(tb);
				return true;
			}
		}
		else if(sessionStorage.getItem(td)){
			sessionStorage.removeItem(td);
			return true;
		}
		return false;
	}
	this.error = function(err){
		if(this.undefind(err)){
			var message = this._error;
			if(this.undefind(message)) this._error = '0';
			//	浏览器自动调试输出
			try{console.log(this._error)}catch(e){}
			return this._error;
		}else this._error = err;
	}
	this.is_string = function(value){
		if(typeof value == 'string') return true;
		return flase;
	}
	this.is_object = function(value){
		if(typeof value == 'object') return true;
		return flase;
	}	
}

var Base64 = {
	// private property
	_keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
 
	// public method for encoding
	encode: function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;
		input = this._utf8_encode(input);
		while (i < input.length) {
			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);
			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;
			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}
			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
		}
		return output;
	},
 
	// public method for decoding
	decode: function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;
		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
		while (i < input.length) {
			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));
			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;
			output = output + String.fromCharCode(chr1);
			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}
		}
		output = this._utf8_decode(output);
		return output;
	},
 
	// private method for UTF-8 encoding
	_utf8_encode:function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";
		for (var n = 0; n < string.length; n++) {
			var c = string.charCodeAt(n);
			if (c < 128) {
				utftext += String.fromCharCode(c);
			} else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			} else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
 
		}
		return utftext;
	},
 
	// private method for UTF-8 decoding
	_utf8_decode:function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
		while ( i < utftext.length ) {
			c = utftext.charCodeAt(i);
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			} else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			} else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
		}
		return string;
	}
};
