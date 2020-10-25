define(['jquery', 'layer', 'message'], function ($, layer) {
	var sent = {
		config: {
			keyName: 'sent_'
		},
		init: function () {
			if ($("form[role=form]").length > 0) {
				require(['form'], function (form) {
					form.api.bindevent($("form[role=form]"));
				})
			}
			sent.api.bindButtonAction();  //绑定元素get/post请求操作

			//全选的实现
			$(".check-all").click(function() {
				$(this).parents('table').find('tbody td input[type=checkbox]').prop("checked", this.checked);
			});
			$("table tbody td input[type=checkbox]").click(function() {
				var checked = true;
				$("table tbody td input[type=checkbox]").each(function(i) {
					if (!this.checked) {
						checked = false;
					}
				});
				$(".check-all").prop("checked", checked);
			});
		},
		msg: function (text, type) {
			text = (type == 'success') ? text + ' 页面即将自动跳转~' : text;
			if (typeof type != 'undefined') {
				var message = $.messager.show(text, {
					placement: 'bottom',
					type: type
				});
			} else {
				var message = $.messager.show(text, {
					placement: 'bottom'
				})
			}
			message.show();
		},
		parseUrl: function (url) {
			if (url.indexOf("?") === -1) {
				return {};
			}
			url = decodeURIComponent(url);
			var query = url.split("?")[1];
			var queryArr = query.split("&");
			var obj = {};
			queryArr.forEach(function (item) {
				var key = item.split("=")[0];
				var value = item.split("=")[1];
				obj[key] = decodeURIComponent(value);
			});
			return obj;
		},
		store: {
			set: function (params = {}) {
				var {
					name,
					content,
					type
				} = params;
				name = sent.config.keyName + name
				var obj = {
					dataType: typeof (content),
					content: content,
					type: type,
					datetime: new Date().getTime()
				}
				if (type) window.sessionStorage.setItem(name, JSON.stringify(obj));
				else window.localStorage.setItem(name, JSON.stringify(obj));
			},
			get: function (params = {}) {
				var {
					name,
					debug
				} = params;
				name = sent.config.keyName + name
				var obj = {},
					content;
				obj = window.sessionStorage.getItem(name);
				if (sent.utils.validatenull(obj)) obj = window.localStorage.getItem(name);
				if (sent.utils.validatenull(obj)) return;
				try {
					obj = JSON.parse(obj);
				} catch (error) {
					return obj;
				}
				if (debug) {
					return obj;
				}
				if (obj.dataType == 'string') {
					content = obj.content;
				} else if (obj.dataType == 'number') {
					content = Number(obj.content);
				} else if (obj.dataType == 'boolean') {
					content = eval(obj.content);
				} else if (obj.dataType == 'object') {
					content = obj.content;
				}
				return content;
			},
			remove: function (params = {}) {
				let {
					name,
					type
				} = params;
				name = sent.config.keyName + name
				if (type) {
					window.sessionStorage.removeItem(name);
				} else {
					window.localStorage.removeItem(name);
				}
			},
			all: function (params = {}) {
				let list = [];
				let {
					type
				} = params;
				if (type) {
					for (let i = 0; i <= window.sessionStorage.length; i++) {
						list.push({
							name: window.sessionStorage.key(i),
							content: getStore({
								name: window.sessionStorage.key(i),
								type: 'session'
							})
						})
					}
				} else {
					for (let i = 0; i <= window.localStorage.length; i++) {
						list.push({
							name: window.localStorage.key(i),
							content: getStore({
								name: window.localStorage.key(i),
							})
						})

					}
				}
				return list;
			},
			clear: function (params = {}) {
				let {
					type
				} = params;
				if (type) {
					window.sessionStorage.clear();
				} else {
					window.localStorage.clear()
				}
			}
		},
		events: {
			//请求成功的回调
			onAjaxSuccess: function (ret, onAjaxSuccess) {
				var data = typeof ret.data !== 'undefined' ? ret.data : null;
				var msg = typeof ret.msg !== 'undefined' && ret.msg ? ret.msg : __('Operation completed');

				if (typeof onAjaxSuccess === 'function') {
					var result = onAjaxSuccess.call(this, data, ret);
					if (result === false)
						return;
				}
				sent.msg(msg, 'success');
			},
			//请求错误的回调
			onAjaxError: function (ret, onAjaxError) {
				var data = typeof ret.data !== 'undefined' ? ret.data : null;
				if (typeof onAjaxError === 'function') {
					var result = onAjaxError.call(this, data, ret);
					if (result === false) {
						return;
					}
				}
				sent.msg(ret.msg, 'error');
			},
			//服务器响应数据后
			onAjaxResponse: function (response) {
				try {
					var ret = typeof response === 'object' ? response : JSON.parse(response);
					if (!ret.hasOwnProperty('code')) {
						$.extend(ret, {
							code: -2,
							msg: response,
							data: null
						});
					}
				} catch (e) {
					var ret = {
						code: -1,
						msg: e.message,
						data: null
					};
				}
				return ret;
			}
		},
		api: {
			//发送Ajax请求
			ajax: function (options, success, error) {
				options = typeof options === 'string' ? {
					url: options
				} : options;
				var index;
				if (typeof options.loading === 'undefined' || options.loading) {
					index = layer.load(options.loading || 0);
				}
				options = $.extend({
					type: "POST",
					dataType: "json",
					success: function (ret) {
						index && layer.close(index);
						ret = sent.events.onAjaxResponse(ret);
						if (ret.code === 1) {
							sent.events.onAjaxSuccess(ret, success);
							if (ret.url) {
								setTimeout(function() {
									location.href = ret.url;
								}, 1500);
							}
						} else {
							sent.events.onAjaxError(ret, error);
						}
					},
					error: function (xhr) {
						index && layer.close(index);
						var ret = {
							code: xhr.status,
							msg: xhr.statusText,
							data: null
						};
						sent.events.onAjaxError(ret, error);
					}
				}, options);
				return $.ajax(options);
			},
			bindButtonAction: function(){
				if($('a.ajax-get, button.ajax-get, a.ajax-post, button.ajax-post').length > 0){
					$('a.ajax-get, button.ajax-get, a.ajax-post, button.ajax-post').click(function(e){
						e.preventDefault();
						var target, type, form, query;
						var nead_confirm = false;
						if ($(this).hasClass('confirm')) {
							if (!confirm('确认要执行该操作吗?')) {
								return false;
							}
						}
						if ($(this).hasClass('ajax-post')) {
							type = "post";
							form = $('.' + $(this).data('form'));
							if ($(this).attr('hide-data') === 'true') { //无数据时也可以使用的功能
								form = $('.hide-data');
								query = form.serialize();
							} else if (form.get(0) == undefined) {
								return false;
							} else if (form.get(0).nodeName == 'FORM') {
								if ($(this).attr('url') !== undefined) {
									target = $(this).attr('url');
								} else {
									target = form.get(0).action;
								}
								query = form.serialize();
							} else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
								query = form.serialize();
							} else {
								query = form.find('input,select,textarea').serialize();
							}
						}else{
							type = "get";
						}
						if ((target = $(this).attr('href')) || (target = $(this).attr('url'))) {
							sent.api.ajax({url: target, type: type, data: query})
						}
					})
				}
			}
		},
		utils: {
			//比较2个对象是否相同
			isObjEqual:function(o1,o2){
				var props1 = Object.getOwnPropertyNames(o1);
				var props2 = Object.getOwnPropertyNames(o2);
				if (props1.length != props2.length) {
					return false;
				}
				for (var i = 0,max = props1.length; i < max; i++) {
					var propName = props1[i];
					if (o1[propName] !== o2[propName]) {
						return false;
					}
				}
				return true;
			},
			validatenull: function (val) {
				if (typeof val == 'boolean') {
					return false;
				}
				if (typeof val == 'number') {
					return false;
				}
				if (val instanceof Array) {
					if (val.length == 0) return true;
				} else if (val instanceof Object) {
					if (JSON.stringify(val) === '{}') return true;
				} else {
					if (val == 'null' || val == null || val == 'undefined' || val == undefined || val == '') return true;
					return false;
				}
				return false;
			}
		}
	};
	window.sent = sent;
	window.layer = layer;

	sent.init();
	return sent;
});