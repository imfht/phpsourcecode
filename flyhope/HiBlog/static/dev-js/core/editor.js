(function($) {
	
	/**
	 * 添加JQuery editor方法绑定编辑器
	 */
	$.fn.extend({
		/**
		 * 加载CKEditor
		 */
		"editor" : function() {
			//加载本地Plugin
			CKEDITOR.plugins.addExternal( 'pbckcode', '/static/lib/ckeditor_plugin/pbckcode/', 'plugin.js' );
			
			$(this).each(function() {
				CKEDITOR.replace( $(this)[0], {
					"extraPlugins" : "pbckcode",
					"pbckcode" : {
						"highlighter" : "SYNTAX_HIGHLIGHTER",
						"tab_size" : "4",
						"modes" :  [
				            ['HTML', 'html'],
				            ['CSS', 'css'],
				            ['PHP', 'php'],
				            ['JS', 'javascript']
			            ],
			            
			            //ACE使用国内七牛CDN公共库加速
			            "js" : "http://libs.cncdn.cn/ace/1.2.2/"
					},
					
					"font_defaultLabel" : "Simsun",
					"font_names" : "Simsun;SimHei;KaiTi;Microsoft Yahei;Arial;Times New Roman;Verdana;",
					"fontSize_defaultLabel" : "14px",
					"toolbar" :        [
				        ["Styles", "Format", "Font", "FontSize"],
				        ["Bold", "Italic", "Underline", "Strike", "-", 'TextColor','BGColor'],
				        ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', "-", 'NumberedList', 'BulletedList', '-', 'Link', "Unlink"],
				        ["Image", "Flash", "Table", "HorizontalRule", "SpecialChar", "Iframe", "pbckcode"],
				        ["Maximize", "-" ,"Source"]
				    ],
				    
				    /*
				    "filebrowserImageBrowseUrl" : $CONFIG.path + 'editor/browser?type=images',
					"filebrowserFlashBrowseUrl" : $CONFIG.path + 'editor/browser?type=flash',
					"filebrowserUploadUrl" : $CONFIG.path + 'editor/upload?type=files',
					*/
					"filebrowserImageUploadUrl" : $CONFIG.path + 'editor/upload?type=images',
					"filebrowserFlashUploadUrl" : $CONFIG.path + 'editor/upload?type=flash',
					"filebrowserWindowWidth" : '800',
					"filebrowserWindowHeight" : '500'
				} );
			})

		}

	});
})(jQuery);
