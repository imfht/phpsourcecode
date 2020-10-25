artDialog.notice = function (options) {
	var opt = options || {},
		api, aConfig, hide, wrap, top,
		duration = 800;
		
	var config = {
		// id: 'Notice',
		left: '100%',
		top: '100%',
		fixed: true,
		drag: false,
		resize: false,
		follow: null,
		lock: false,
		init: function(here){
			api = this;
			aConfig = api.config;
			wrap = api.DOM.wrap;
			top = parseInt(wrap[0].style.top);
			hide = top + wrap[0].offsetHeight;
			
			wrap.css('top', hide + 'px')
				.animate({top: top + 'px'}, duration, function () {
					opt.init && opt.init.call(api, here);
				});
		},
		close: function(here){
			wrap.animate({top: hide + 'px'}, duration, function () {
				opt.close && opt.close.call(this, here);
				aConfig.close = $.noop;
				api.close();
			});
			
			return false;
		}
	};	
	
	for (var i in opt) {
		if (config[i] === undefined) config[i] = opt[i];
	};
	
	return artDialog(config);
};

/**
 * 警告
 * @param	{String}	消息内容
 */
artDialog.alert = function (content, callback) {
	return artDialog({
		id: 'Alert',
		icon: 'warning',
		fixed: true,
		lock: true,
		content: content,
		ok: true,
		close: callback
	});
};


/**
 * 确认
 * @param	{String}	消息内容
 * @param	{Function}	确定按钮回调函数
 * @param	{Function}	取消按钮回调函数
 */
artDialog.confirm = function (content, yes, no) {
	return artDialog({
		id: 'Confirm',
		icon: 'question',
		title:'温馨提示',
		fixed: true,
		lock: true,
		opacity: .1,
		content: content,
		ok: function (here) {
			return yes.call(this, here);
		},
		cancel: function (here) {
			return no && no.call(this, here);
		}
	});
};


/**
 * 提问
 * @param	{String}	提问内容
 * @param	{Function}	回调函数. 接收参数：输入值
 * @param	{String}	默认值
 */
artDialog.prompt = function (content, yes, value) {
	value = value || '';
	var input;
	
	return artDialog({
		id: 'Prompt',
		icon: 'question',
		fixed: true,
		lock: true,
		opacity: .1,
		content: [
			'<div style="margin-bottom:5px;font-size:12px">',
				content,
			'</div>',
			'<div>',
				'<input value="',
					value,
				'" style="width:18em;padding:6px 4px" />',
			'</div>'
			].join(''),
		init: function () {
			input = this.DOM.content.find('input')[0];
			input.select();
			input.focus();
		},
		ok: function (here) {
			return yes && yes.call(this, input.value, here);
		},
		cancel: true
	});
};


/**
 * 短暂提示
 * @param	{String}	提示内容
 * @param	{Number}	显示时间 (默认1.5秒)
 */
artDialog.tips = function (content, time) {
	return artDialog({
		id: 'Tips',
		title: false,
        cancel: false,
		fixed: true,
		lock: true,
		opacity: .3,
	})
    .content('<div style="padding: 0 1em;">' + content + '</div>')
	.time(time || 1);
};