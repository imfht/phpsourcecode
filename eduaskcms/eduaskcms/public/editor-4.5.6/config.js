/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.stylesSet.add( 'eduask_style',
[
	{ name : '行高100%', element : 'span', styles : { 'line-height' : 1 } },
	{ name : '行高120%', element : 'span', styles : { 'line-height' : 1.2 } },
	{ name : '行高140%', element : 'span', styles : { 'line-height' : 1.4 } },
	{ name : '行高160%', element : 'span', styles : { 'line-height' : 1.6 } },
	{ name : '行高180%', element : 'span', styles : { 'line-height' : 1.8 } },
	{ name : '行高200%', element : 'span', styles : { 'line-height' : 2 } },
	{ name : '行高220%', element : 'span', styles : { 'line-height' : 2.2 } },
	{ name : '行高240%', element : 'span', styles : { 'line-height' : 2.4 } },
	{ name : '行高260%', element : 'span', styles : { 'line-height' : 2.6 } },
	{ name : '行高280%', element : 'span', styles : { 'line-height' : 2.8 } },
	{ name : '行高300%', element : 'span', styles : { 'line-height' : 3 } },  
    { name : '文字阴影-1', element : 'span', styles : { 'text-shadow' : '0 0 2px rgba(0,0,0,0.4)' } },  
    { name : '文字阴影-2', element : 'span', styles : { 'text-shadow' : '2px 2px 2px rgba(0,0,0,0.4)' } },
    { name : '文字阴影-3', element : 'span', styles : { 'text-shadow' : '0 0 2px #fb8506' } },  
    { name : '文字阴影-4', element : 'span', styles : { 'text-shadow' : '2px 2px 2px #fb8506' } },
    { name : '文字阴影-5', element : 'span', styles : { 'text-shadow' : '0 0 2px #ff0000' } },  
    { name : '文字阴影-6', element : 'span', styles : { 'text-shadow' : '2px 2px 2px #ff0000' } },
    { name : '文字阴影-7', element : 'span', styles : { 'text-shadow' : '0 0 2px #00ff00' } },  
    { name : '文字阴影-8', element : 'span', styles : { 'text-shadow' : '2px 2px 2px #00ff00' } },
    { name : '文字阴影-9', element : 'span', styles : { 'text-shadow' : '0 0 2px #0113fc' } },  
    { name : '文字阴影-10', element : 'span', styles : { 'text-shadow' : '2px 2px 2px #0113fc' } },
	{ name : '图片左环绕', element : 'img', styles : { 'margin-right':'10px','float':'left' } },
	{ name : '图片右环绕', element : 'img', styles : { 'margin-left':'10px','float':'right'} },
    { name : '图片圆角5px', element : 'img', styles : { 'border-radius':'5px' } },
    { name : '图片圆角10px', element : 'img', styles : { 'border-radius':'10px' } },
    { name : '图片圆角50%', element : 'img', styles : { 'border-radius':'50%' } },
    { name : '盒子阴影-1', element : 'p', styles : { 'box-shadow' : '0 0 4px rgba(0,0,0,0.4)'} },
    { name : '盒子阴影-2', element : 'p', styles : { 'box-shadow' : '4px 4px 4px rgba(0,0,0,0.4)'} },
]);

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    config.skin = 'moonocolor';
    config.language = 'zh-cn';
    config.uiColor = '#CCEAEE';
    
    
    config.toolbar_AdminFull=[
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
		//{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
		{ name: 'insert', items: [ 'Image', 'Slideshow', 'cssanim', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe','-','Chart' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
		'/',
		{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
		{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
		{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor','lightbox' ] },
        '/',
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'/*, '-', 'BidiLtr', 'BidiRtl', 'Language'*/ ] },
		{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
		{ name: 'others', items: [ '-' ] },
		{ name: 'about', items: [ 'About' ] }
	];
	
	config.toolbar_FrontFull =
	[
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
		'/',
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
		{ name: 'insert', items : [ 'Image','Table','HorizontalRule','Smiley'] },
		{ name: 'styles', items : ['Styles', 'Format', 'Font','FontSize' ] },
		{ name: 'colors', items : [ 'TextColor','BGColor' ] },
		{ name: 'tools', items : [ 'Maximize','-','About' ] }
	];
	config.toolbar_FrontSimple =
	[
		{ name: 'styles', items : ['Styles', 'Format', 'Font','FontSize' ] },
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
		{ name: 'insert', items : [ /*'Image',*/'Table','HorizontalRule','Smiley'] },
		{ name: 'colors', items : [ 'TextColor','BGColor' ] }
	];
    
    
    
	config.font_names='宋体/"宋体"; 微软雅黑/"微软雅黑"; 楷体/"楷体"; 隶书/"隶书";'+config.font_names;
    config.fontSize_sizes = config.fontSize_sizes+';1.1em/1.1em;1.5em/1.5em;2em/2em;3em/3em' ;
	config.stylesSet = 'eduask_style';
	config.resize_enabled=false;
    
    
   config.extraPlugins  = 'tableresize';
   //config.extraPlugins += ',chart';//Chart
    
    
   //config.extraPlugins += ',cssanim';
   //config.extraPlugins += ',slideshow';
   config.extraPlugins += ',imagerotate';
   config.extraPlugins += ',backgrounds'; //table bg
   
    
    
    
};
