/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {


		config.width = 900;

		config.height = 300;
		  
	
		config.toolbar=[
{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Preview',  'Templates' ] },
{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat' ] },
{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote',  '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight'] },
{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
{ name: 'insert', items: [ 'Image', 'addpic','Flash','Smiley','Table', 'HorizontalRule' ] },
{ name: 'styles', items: [  'Format', 'FontSize' ] },
{ name: 'colors', items: [ 'TextColor' ] },


{ name: 'about', items: [ 'About' ] }
	],		
	    config.extraPlugins = 'addpic';

		config.filebrowserImageUploadUrl = "./index.php?articleadmin/upload/?";
		config.image_previewText = ' '; // 预览区域显示内容

		// Remove some buttons provided by the standard plugins, which are
		// not needed in the Standard(s) toolbar.
		config.removeButtons = 'Underline,Subscript,Superscript';

		// Set the most common block elements.
		config.format_tags = 'p;h1;h2;h3;pre';

		// Simplify the dialog windows.
		config.removeDialogTabs = 'image:advanced;link:advanced';

};
