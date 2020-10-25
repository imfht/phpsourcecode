$(function() {

	//加载编辑器
	$("#input_content").editor();
	
	$("#form-manage-article-create").submit(function(){
		var method = $(this).attr("method");
		var action = $(this).attr("action");
		var data = $(this).serializeArray();
		data.push({
			"name"  : "content",
			"value" : CKEDITOR.instances.input_content.getData()
		});
		
		$.ajax(action, {
			"cache" : false,
			"data" : data,
			"type" : method,
			"success" : function(response) {
				$.ajaxCallback(response, function(o) {
					location.href = o.data.href;
				});
			}
		});
	});
	
});
