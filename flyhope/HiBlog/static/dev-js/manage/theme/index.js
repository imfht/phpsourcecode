$(function() {
	
	//复制模板弹层事件
	$("#modal-theme-copy").on("show.bs.modal", function(event) {
		//复制模板
		var button = $(event.relatedTarget);
		var $theme_node = button.parents("[node-type=theme-node]");
		var theme_id = $theme_node.data("theme-id");
		
		$(this).find("[name=alias_id]").val(theme_id);
	});
	
	//复制模板表单提交事件
	$("#form-theme-copy").ajaxSubmit();
	
	
	$("#theme-container").delegate("[action-type=destroy]", "click", function() {
		//删除主题
		var $theme_node = $(this).parents("[node-type=theme-node]");
		var theme_id = $theme_node.data("theme-id");
		var href = $CONFIG.path + "aj/manage/theme/destroy";
		
		if($.confirm("确定要删除吗?", "确认", function() {
			$.post(href, {"id" : theme_id}, function (o) {
				$.ajaxCallback(o, function() {
					$theme_node.fadeOut();
				});
			});
		}));
	}).delegate("[action-type=use]", "click", function() {
		//使用主题
		var href = $CONFIG.path + "aj/manage/theme/use";
		var $theme_node = $(this).parents("[node-type=theme-node]");
		var theme_id = $theme_node.data("theme-id");
		//使用主题
		$.post(href, {"theme_id":theme_id}, $.ajaxCallbackDefault);
	});
	
});