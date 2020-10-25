
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

function EcmsTempEditorGetCs(){
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
arraycs=EcmsTempEditorGetCs();

arraycs[0]=arraycs[0].replace('tempeditor/ckeditor.js','');

arraycs[1]=document.getElementById('doecmseditor_eaddcs').value;
arraycs[1]=EcmsEditorDoCKhtml(arraycs[1]);


CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	config.enterMode = CKEDITOR.ENTER_BR;
	config.shiftEnterMode = CKEDITOR.ENTER_P;

	config.allowedContent= true;
	
	config.font_names='宋体/宋体;黑体/黑体;仿宋/仿宋_GB2312;楷体/楷体_GB2312;隶书/隶书;幼圆/幼圆;微软雅黑/微软雅黑;'+ config.font_names;
	
	
	// Toolbar
config.toolbar = [
	{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
	{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
	{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
	'/',
	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ] },
	{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
	'/',
	{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
	{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
	{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
	{ name: 'others', items: [ '-' ] },
	{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak', 'Iframe', 'einsertbr' ] }
];


	config.extraPlugins = 'einsertbr';



};




