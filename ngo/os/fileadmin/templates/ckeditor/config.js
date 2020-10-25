/**
 * @license Copyright (c) 2003-2018, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.toolbar = [
		{ name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
		{ name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
		{ name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ,'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript'] },
		{ name: 'basicstyles', items: [ 'CopyFormatting', 'RemoveFormat' ] },
		// { name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
		{ name: 'insert', items: [ 'Image', 'Flash', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe','TextColor', 'BGColor', 'ShowBlocks' ,'-','About' ,'-','Link', 'Unlink', 'Anchor','-','NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-',  'Blockquote', 'CreateDiv'] },
		{ name: 'paragraph', items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ] },
		// { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
		
		// { name: 'lineheight',items:['lineheight']},
		{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize','lineheight' ] },
		// { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
		// { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
		// { name: 'about', items: [ 'About' ] }
	];
	config.font_names = '宋体;楷体 _GB2312;新宋体;黑体;隶书;幼圆;微软雅黑;Arial;Comic Sans MS;Courier New;Tahoma;Times New Roman;Verdana';
	config.image_previewText=' '; //预览区域显示内容 
	config.toolbarCanCollapse = 'false';
	config.extraPlugins += (config.extraPlugins ? ',lineheight' : 'lineheight');
	config.justifyClasses = [ 'AlignLeft', 'AlignCenter', 'AlignRight', 'AlignJustify' ];
	config.height = 600; //高度
	config.font_style = {element : 'div'};
};
