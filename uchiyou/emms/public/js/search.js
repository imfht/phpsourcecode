
// 提交 搜素表单
$('#searchForm').ajaxForm({
	type : "get",
	dataType : "text",
	beforeSubmit : function (formData, jqForm, options) {
		var name = $(jqForm).find("input[name='content']").val();
		var type = $(jqForm).find("select[name='type']").val();
		if(name == ""){
			formError.text('名称不能为空');
			layer.msg('搜素内容不能为空');
			return false;
		}
		var url = '/admin/search?content='+name+'&type='+type;
		 $("#J_iframe").attr('src',url);
		return false;
	},
	success : function (res, statusText, xhr, $form) {
		$("#J_iframe").html('haha');
	}
});