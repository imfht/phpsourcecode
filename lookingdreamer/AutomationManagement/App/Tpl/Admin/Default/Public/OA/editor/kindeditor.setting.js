var editor;
function editor_init() {
	if (is_mobile()) {
		settings = {
			resizeType : 1,
			filterMode : true,
			uploadJson : upload_url,
			width : '100%',
			items : [],
			afterBlur : function() {
				this.sync();
			}
		};
	} else {
		settings = {
			resizeType : 1,
			filterMode : true,
			uploadJson : upload_url,
			width : '100%',
			afterBlur : function() {
				this.sync();
			}
		};
	}
	editor = new KindEditor.create(".editor", settings);
	
	if (is_mobile()) {
		settings = {
			resizeType : 1,
			filterMode : true,
			uploadJson : upload_url,
			width : '100%',
			items : [],
			afterBlur : function() {
				this.sync();
			}
		};
	} else {
		settings = {
			width : '100%',
			resizeType : 1,
			allowPreviewEmoticons : true,
			uploadJson : upload_url,
			allowImageUpload : true,
			syncType : 'form',
			height : 200,
			items : ['fontsize', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', 'removeformat', '|', 'image', '|', 'fullscreen'],
			afterBlur : function() {
				this.sync();
			}
		};
	}
	editor = new KindEditor.create(".simple",settings);	
}