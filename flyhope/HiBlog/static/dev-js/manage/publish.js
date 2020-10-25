$(function(){
	//绑定AJAX提交表单，发布成功后跳转至执行页面
	var $form_manage_publish = $("#form-manage-publish");
	$form_manage_publish.ajaxSubmit(function(o) {
		location.href = $CONFIG.path + "manage/publish/execute";
	});
	
	//绑定全选插件
	$form_manage_publish.find("[node-type=publish-category]").selectAll("#publish-all", "[name='publish[]']");
	
	//发布中，自动跳转
	$("[node-type=auto-go]").each(function() {
		var href = $(this).attr("href");
		var timeout = parseInt($(this).data("timeout"));
		setTimeout(function() {
			location.href=href;
		}, timeout);
	});
});