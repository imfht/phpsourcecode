tinymce.PluginManager.add("imageupload", function(editor, url) {
		
	editor.on('init', function() {		
		//绑定各种事件，并在事件监听函数中做你想做的事
	});

	editor.addButton("imageupload", {
		text : "",
		icon : 'image',
		tooltip : '上传图片',
		onclick : function() {
			editor.focus();
			$("#img_upload").click();
		}
	});
});