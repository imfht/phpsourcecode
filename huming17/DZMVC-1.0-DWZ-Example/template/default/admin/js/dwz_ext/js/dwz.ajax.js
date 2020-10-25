/**
 * @author huming17@126.com
 */


/**
 * dialog dialog上的表单提交回调函数
 * 服务器转回指定dialog，可以重新载入指定的navTab. statusCode=DWZ.statusCode.ok表示操作成功, 自动关闭当前dialog
 * 
 * form提交后返回json数据结构,json格式和navTabAjaxDone一致
 * Create by HumingXu @ huming17@126.com
 */

function ddialogAjaxDone(json){
	DWZ.ajaxDone(json);
	if (json.statusCode == DWZ.statusCode.ok){
		if (json.navTabId){
			$.pdialog.reload(json.forwardUrl, {dialogId: json.navTabId});
			navTab.reloadFlag(json.navTabId);
		} else if (json.rel) {
			var $pagerForm = $("#pagerForm", $.pdialog.getCurrentPanel());
			var args = $pagerForm.size()>0 ? $pagerForm.serializeArray() : {}
			navTabPageBreak(args, json.rel);
		}
		if ("closeCurrent" == json.callbackType) {
			$.pdialog.closeCurrent();
		}
	}
}

function dialogajaxTodo(url, callback){
	var $callback = callback || ddialogAjaxDone;
	if (! $.isFunction($callback)) $callback = eval('(' + callback + ')');
	$.ajax({
		type:'POST',
		url:url,
		dataType:"json",
		cache: false,
		success: $callback,
		error: DWZ.ajaxError
	});
}

$.fn.extend({
	dialogajaxTodo:function(){
		return this.each(function(){
			var $this = $(this);
			$this.click(function(event){
				var url = unescape($this.attr("href")).replaceTmById($(event.target).parents(".unitBox:first"));
				DWZ.debug(url);
				if (!url.isFinishedTm()) {
					alertMsg.error($this.attr("warn") || DWZ.msg("alertSelectMsg"));
					return false;
				}
				var title = $this.attr("title");
				if (title) {
					alertMsg.confirm(title, {
						okCall: function(){
							dialogajaxTodo(url, $this.attr("callback"));
						}
					});
				} else {
					dialogajaxTodo(url, $this.attr("callback"));
				}
				event.preventDefault();
			});
		});
	}
});