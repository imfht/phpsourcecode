$(function() {
	var $form_result = $("#form-manage-category-result");
	
	$form_result.delegate("[action-type=publish]", "click", function() {
		//代理发布事件
		var $tr = $(this).parents("tr:first");
		var href = $CONFIG.path + "aj/manage/publish/article";
		$.post(href, {"id" : $tr.data("id")}, $.ajaxCallbackDefault);
	}).delegate("[action-type=delete]", "click", function() {
		//代理删除事件
		var href = $CONFIG.path + "aj/manage/article/destroy";
		var article_id = $(this).parents("tr:first").data("id");
		$.confirm("确定要删除吗? (ID:" + article_id + ")", "确认", function() {
			$.post(href, {"id" : article_id}, $.ajaxCallbackDefault);
		});
	});
});
