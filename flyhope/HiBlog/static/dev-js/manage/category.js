$(function(){
	
	//绑定全选事件
	$("#form-manage-category-result").selectAll("#category-batch-all", "[name='ids[]']");
	
	//绑定创建表单AJAX提交事件
	$("#form-manage-category-create").ajaxSubmit();
	
	//绑定批量删除事件
	$("#form-manage-category-result").ajaxSubmit();
	
});