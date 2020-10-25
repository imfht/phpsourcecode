(function ($, doc) {
	var openWindow = $.openWindow,
		getExtras = function (url) {
			var extras = {},
				is_need_plus = url ? false : window.plus;
			if (is_need_plus) {
				var variable,
					webview = plus.webview.currentWebview();
				for (variable in webview) {
					webview.hasOwnProperty(variable) && (extras[variable] = webview[variable]);
				}
			} else {
				(url || window.location.search.substring(1)).split("&").forEach(function (v) {
					v = v.split('=');
					(v[1] !== undefined) && (extras[v[0]] = v[1]);
				});
			}
			// id不作为参数
			delete extras.id;
			return extras;
		};
	/**
	 * 打开新窗口（传参兼容web&plus版本）
	 * @param {string} url 要打开的页面地址
	 * @param {string} id 指定页面ID
	 * @param {object} options 可选:参数,等待,窗口,显示配置{params:{},waiting:{},styles:{},show:{}}
	 */
	$.openWindow = function (url, id, options) {
			if (typeof url === 'object') {
				options = url;
				url = options.url;
				id = options.id || url;
			} else {
				if (typeof id === 'object') {
					options = id;
					id = options.id || url;
				} else {
					id = id || url;
				}
			}
			if (!$.os.plus) {
				if ($.type(options.extras) === 'object' && !$.isEmptyObject(options.extras)) {
					delete options.extras.id;
					options.url += (options.url.indexOf('?') >= 0 ? '&' : '?') + $.param(options.extras);
				}
			} else {
				if (options.url.indexOf('?') >= 0) {
					options.extras = $.extend(options.extras, getExtras(options.url.split('?')[1]));
				}
			}
			return openWindow(options);
		}
		/**
		 * 获取页面传参，callback为“location.search字符串”时，为mui.param逆操作。同步执行时，无需传入回调，异步执行时，回调的唯一一个参数为同步执行的返回值
		 * @param {function} 异步回调函数
		 * @return {object} 同步执行时的返回值
		 */
	$.getExtras = function (callback) {
		if ($.type(callback) == 'function') {
			if ($.os.plus) {
				$.plusReady(function () {
					callback(getExtras());
				});
			} else {
				// 非plus下为伪异步执行
				callback(getExtras());
			}
		} else {
			// 如果不是回调,当成location.search来处理
			return getExtras($.type(callback) !== 'string' ? undefined : (callback.indexOf('?') >= 0 ? callback.split('?')[1] : callback));
		}
	}
})(mui, document);
