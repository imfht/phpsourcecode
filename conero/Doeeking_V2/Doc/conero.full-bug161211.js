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
}
// 删除数组中的任何一个指定元素
Array.prototype.unset = function(value){
	var arr = new Array();
	for(var i=0; i<this.length; i++){
		if(this[i] == value) continue;
		arr.push(this[i]);
	}
	return arr;
}
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
}
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
    }
	// dataid 属性操作
	this.dataid = function(dom){
		if(this.empty(dom)) return '';
		if(this.is_object(dom)){
			return dom.attr('dataid');
		}
		return $(dom).attr('daraid');
	}
    // storage 对象
    this.storage = function(engine){
        return new jutilStorage(engine);
    }
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
    }
	// $_GET// URL 解析
	this.getQuery = function(key){
		/*
		if('#' == key){
			var hash = location.hash;
			hash = hash.replace(new RegExp('#','g'),'');
			return hash;
		}
		*/
		// 获取描点
		if('#' == key){
			var href = location.href;
			if(href.indexOf('#')>-1){
				var arr = href.split('#');
				return arr[1];
			}
			return '';
		}
		var ser = location.search;
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
	}
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
	}
	// 字符串根据分割符获取最后字段
	this.strLastValue = function(value,delimiter){
		delimiter = delimiter? delimiter:'/';
		var arr = value.split(delimiter);
		return arr[arr.length-1];
	}
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
		for(var k in data){
			ipt = document.createElement("textarea");
			ipt.name = k;
			ipt.value = data[k];
			form.appendChild(ipt); 
		}
		document.body.appendChild(form);
		form.submit();
		return form;
	}
	this.post = function(url,data){return this.form(url,data,"post");}
	this.get = function(url,data){return this.form(url,data,"get");}
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
	}
	// 表单非空检测 required 通过则放回-数据key-value 数据(object)/否则返回-bool类型
	this.formRequired = function(selector,feekJson)
	{
		feekJson = this.empty(feekJson)? true:false;
		var form = this.is_object(selector)? selector:$(selector);
		if(form.length > 0){
			var ipt = form.find('[required]'), el,value,name,type;
			for(var i =0; i<ipt.length; i++){
				el = $(ipt[i]);
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
	}
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
    }
	// message Api/ 窗口通信
	this.uWin = function(id){
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
			this.response = function(func){
				window.addEventListener("message",func);
			}
		}
		return new _uWin(id);
		
	}
	// 扩展机制
	this.extends = function(data){}
	this._extends = function(){
		this.extends = (this.prototype);
	}
    //--------------------------调试帮助
    //this.log = function(value){Csl.log(value);}
	this.log = Csl.log;
	this.alertTest = function(){alert(Math.random()*(Math.pow(10,Math.ceil(Math.random()*10))));}// 弹出测试信息
    this.is_string = function(value){
        if(typeof(value) == 'string') return true;
        return false;
    }
    this.is_object = function(value){
        if(typeof(value) == 'object') return true;
        return false;
    }
	this.is_function = function(value){
		if(typeof(value) == 'function') return true;
		return false;
	}
    this.undefind = function(value){
        if(typeof(value) == 'undefined') return true;
        return false;
    }
    this.empty = function(value){
        if(this.undefind(value)) return true;
		else if(value == '') return true;
		else if(value == 0) return true;
		return false;
    }
	// 私有类扩展-当前具体项目类
	var _privateApp = function(obj){
		// 数据存在性检测
		obj.dataInDb = function(table,wh,func){
			if(table && this.is_object(wh) && this.is_function(func)){
				var where = Base64.encode(JSON.stringify(wh));
				$.post('/conero/index/common/dataInDb',{'table':table,'where':where},func);
			}
		}
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
		}
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
		}
		// - 缓存------------------------------------------------------> end
	};
	// Bootstrap 扩展机制
	var _privateBootstrap = function(obj){
		// 内嵌式模板窗口生成器- fn
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
			if(obj.is_object(fn)){
				if(obj.is_object(fn.bindEvent)){
					var arr = fn.bindEvent;
					for(var i=0; i<arr.length; i++){
						/*
						$(id+' [dataid="'+arr[i]+'"]').off('click',fn[arr[i]]); // 绑定前解绑-避免重复绑定
						$(id+' [dataid="'+arr[i]+'"]').off('click').click(fn[arr[i]]); // 绑定前解绑-避免重复绑定
						$(id+' [dataid="'+arr[i]+'"]').off('click',"**"); // 绑定前解绑-避免重复绑定
						$(id+' [dataid="'+arr[i]+'"]').unbind();this.log("绑定次数");
						$(document).on('click',id+' [dataid="'+arr[i]+'"]',fn[arr[i]]);// 通过[dataid]属性事件绑定
						$(id+' [dataid="'+arr[i]+'"]').on('click',fn[arr[i]]);// 通过[dataid]属性事件绑定
						$('click',id+' [dataid="'+arr[i]+'"]',fn[arr[i]]).off("click").click(fn[arr[i]]);// 通过[dataid]属性事件绑定

							$(document).on(event,selector,eventHandler) <-> $(selector).off/$(selector).unbind 无效
							$(selector).on(event,eventHandler)	<-> $(selector).off/$(selector).unbind 有效
						*/
						$(document).on('click',id+' [dataid="'+arr[i]+'"]',fn[arr[i]]);// 通过[dataid]属性事件绑定
					}
				}
				else if(obj.is_string(fn.bindEvent)){
					var dataid = fn.bindEvent;
					$(document).on('click',id+' [dataid="'+dataid+'"]',fn[dataid]);// 通过[dataid]属性事件绑定
				}
			}
		}
		// 内嵌式是alter
		obj.alert = function(el,content,title){
			title = obj.empty(title)? '警告':title;
			content = obj.empty(content)? ' 这是一个警告提示框示例！':content;			
			var html = ''
				+ '<div class="alert alert-warning alert-dismissible fade in" role="alert">'
				+ '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'
				+ '<strong>'+title+'</strong>'
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
			el.html(html)			
		}
		// modal -alert
		obj.modal_alert = function(text,title){
			if(this.empty(text)) return;
			var title = this.empty(title)? '警告':title;
			this.modal({
				id:		'btsp_modal_alter',
				title:	'Error-CONERO@...',
				content: '<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-info-sign"></span> <strong>'+title+'!</strong> '+text+'</div>'
			});
		}
		/**
		 *	2016年12月3日 星期六
		 *	option = {},fn = {serach:function(){},save:function(){},selected:function(){},next:function(){}}
		 **/
		obj.pupop = function(option,fn){			
			option = obj.is_object(option)? option:{};
			var post = option.post;
			var field = option.field, postField = new Array();
			var table = '',value ='';
			var mulSelected = obj.empty(option.single)? true:false;// 多选
			for(var k in field){
				postField.push(k);
				value = field[k];
				table += '<th'+(value == 'hidden'? ' class="hidden"':'')+'>'+value+'</th>';
			}
			table = '<table class="table"><tr><th>#</th>'+table+'<th>选择</th></tr>';
			post.field = postField.join(',');
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
				var id = post.table;
				var popup = {
					content:content,
					footer:'<button type="button" class="btn btn-default" dataid="save">确认</button>',
					id: id
				};
				if(!obj.empty(option.title)) popup.title = option.title;
				obj.modal(popup);
				// 事件绑定 回调对象
				var callSearch = true,callNext = true;
				if(obj.is_object(fn)){
					//if(obj.is_function(fn.search)) $(document).on('click','#'+id+' [dataid="search"]',fn['search']);// 搜索
					if(obj.is_function(fn.search)){callSearch = false;$(document).on('click','#'+id+' [dataid="search"]',fn.search);}// 搜索
					if(obj.is_function(fn.selected)) $(document).on('click','#'+id+' dataid="selected"',fn.selected);// 单选择
					if(obj.is_function(fn.save)) $(document).on('click','#'+id+' [dataid="save"]',fn.save);// 保存
					if(obj.is_function(fn.next)){callNext = false; $(document).on('click','#'+id+' [dataid="nextpage"]',fn.next);}// 数据加载
				}
				// 搜索事件自动生成
				if(callSearch == true){
					$(document).on('click','#'+id+' [dataid="search"]',function(){
						var form = $(this).parents('div.form-inline');
						//var datatable = $('#'+id).find('table');
						var skey = form.find('select option:selected').val();
						var input = form.find('input[name="svalue"]');
						var svalue = input.val();
						if(obj.empty(svalue)){input.focus();return;}
						var serachPost = post;
						var map = post.map, wh;
						if(obj.is_object(map)) map[skey] = ['like','%'+svalue+'%'];
						else if(obj.is_string(map) && map){
							wh = ' and '+skey+' like \'%'+svalue+'%\'';
						}
						serachPost.map = map + wh;map = '';
						// map 在ajax请求错误时 会覆盖会叠加原来的值 ??
						$.post('/conero/index/common/popup.html',serachPost,function(data){							
							var html = createTabel(data);
							var body = $('#'+id).find('div.modal-body');
							body.find('table').remove();
							body.find('div.alert').remove();
							body.append(html);
						});
					});
				}
				// 页码翻页
				if(callNext == true){
					$(document).on('click','#'+id+' [dataid="nextpage"]',function(){
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
						var map = post.map, wh = '';
						if(obj.is_object(map)) map[skey] = ['like','%'+svalue+'%'];
						else if(obj.is_string(map) && map){
							wh = ' and '+skey+' like \'%'+svalue+'%\'';
						}
						serachPost.map = map + wh;map = '';
						// map 在ajax请求错误时 会覆盖会叠加原来的值 ??
						if(no < pages) page = no + 1;
						serachPost.page = page;	
						$.post('/conero/index/common/popup.html',serachPost,function(data){
							var html = createTabel(data,true);
						});
					});
				}
			});			
		}
	};
	_privateApp(this);
	_privateBootstrap(this);
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
