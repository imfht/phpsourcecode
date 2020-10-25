
function EcmsEditorDoCKhtml(htmlstr){
	if(htmlstr.indexOf('"')!=-1)
	{
		return '';
	}
	if(htmlstr.indexOf("'")!=-1)
	{
		return '';
	}
	if(htmlstr.indexOf("/")!=-1)
	{
		return '';
	}
	if(htmlstr.indexOf("\\")!=-1)
	{
		return '';
	}
	if(htmlstr.indexOf("[")!=-1)
	{
		return '';
	}
	if(htmlstr.indexOf("]")!=-1)
	{
		return '';
	}
	if(htmlstr.indexOf(":")!=-1)
	{
		return '';
	}
	if(htmlstr.indexOf("%")!=-1)
	{
		return '';
	}
	if(htmlstr.indexOf("<")!=-1)
	{
		return '';
	}
	if(htmlstr.indexOf(">")!=-1)
	{
		return '';
	}
	return htmlstr;
}

function EcmsEditorGetCs(){
	var js=document.getElementsByTagName("script");
	for(var i=0;i<js.length;i++)
	{
		if(js[i].src.indexOf("ckeditor.js")>=0)
		{
			var arraytemp=new Array();
			arraytemp=js[i].src.split('?');
			return arraytemp;
		}
	}
}

var arraycs=new Array();
arraycs=EcmsEditorGetCs();

arraycs[0]=arraycs[0].replace('infoeditor/ckeditor.js','');

arraycs[1]=document.getElementById('doecmseditor_eaddcs').value;
arraycs[1]=EcmsEditorDoCKhtml(arraycs[1]);


CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	config.filebrowserImageUploadUrl = '';
	config.filebrowserFlashUploadUrl = arraycs[0];
	config.filebrowserImageBrowseUrl = arraycs[1];
	config.filebrowserFlashBrowseUrl = arraycs[1];
	
	config.enterMode = CKEDITOR.ENTER_BR;
	config.shiftEnterMode = CKEDITOR.ENTER_P;

	config.allowedContent= true;
	
	config.font_names='宋体/宋体;黑体/黑体;仿宋/仿宋_GB2312;楷体/楷体_GB2312;隶书/隶书;幼圆/幼圆;微软雅黑/微软雅黑;'+ config.font_names;
	
	// Toolbar
	config.toolbar_full = [
	{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Preview', 'Print' ] },
	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
	
	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ] },
	'/',
	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat', 'ecleanalltext', 'autoformat' ] },
	
	{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
	{ name: 'insert', items: [ 'Image', 'etranmore', 'Flash', 'etranmedia', 'etranfile', '-', 'Table', 'HorizontalRule', 'SpecialChar', 'equotetext', 'einserttime', 'einsertpage', 'einsertbr' ] },
	'/',
	{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
	{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
	{ name: 'tools', items: [ 'ShowBlocks', 'NewPage', 'Templates' ] },
	{ name: 'others', items: [ '-' ] },
	{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', 'Maximize' ] }
];


	// Toolbar
	config.toolbar_basic = [
	{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source' ] },
	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
	{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
	{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
	{ name: 'tools', items: [ 'Maximize' ] },
	{ name: 'others', items: [ '-' ] },
	'/',
	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Strike', '-', 'RemoveFormat' ] },
	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
	{ name: 'styles', items: [ 'Styles', 'Format' ] }
];


	config.extraPlugins = 'etranfile,etranmedia,etranmore,autoformat,ecleanalltext,einsertbr,einsertpage,einserttime,equotetext';
	
	
	config.toolbar = 'full';
	
	
	
};



