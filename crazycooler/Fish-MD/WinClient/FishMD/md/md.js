/**
 * Created by coder on 2017/9/20.
 */
var mdEditor = null;
var isEditorInit = false;
var editorNeedResize = false;
var preventChangeEventOnce = false;

var editorInitStr = '';

var previewStr = "";
var doc = null;
var initData = null;


emitEventToDoc = function(action,value,mback){
	if(!value){value = "";}
	if(!mback){mback = "";}

	doc.onEventNotify(action,value,mback);
};

var doAction = function(action,value,mback){

	if(action == "changeMode"){
		changeMode(value);
		return;
	} else if(action == "getmode"){
		emitEventToDoc("getmode",currentMode());
		return;
	}

	var mode = currentMode();
	if(mode == 'edit'){
		if(action == "redo" || action == "undo" || action == "preview"){
			$.proxy(mdEditor.toolbarHandlers[action], mdEditor)(mdEditor.cm);
		} else if(action == "showToolbar"){
			mdEditor.showToolbar();
		} else if(action == "hideToolbar"){
			mdEditor.hideToolbar();
		} else if(action == "getvalue"){
			emitEventToDoc("getvalue",mdEditor.getValue(),mback);
			console.log(action,mode,mdEditor.getValue());
		} else if(action == "setvalue"){
			if(isEditorInit){
				mdEditor.setValue(value);
			}else{
				editorInitStr = value;
			}
			console.log(action,mode,value);
		} else if(action == "setValueNoEvent"){
			if(isEditorInit){
				mdEditor.setValue(value);
			}else{
				editorInitStr = value;
			}
			preventChangeEventOnce = true;
			console.log(action,mode,value);
		} else if(action == "addImage"){
			var imageFormat = '![image](' + value + ')';
			mdEditor.insertValue(imageFormat);
		}
	} else {
		if(action == "setvalue"){
			setPreviewContent(value);
			$('#goEditor').css('display','block');
			console.log(action,mode,value);
		} else if(action == "getvalue"){
			emitEventToDoc("getvalue",previewStr,mback);
		}
	}

};

var currentMode = function(){
	if($('#edit-editormd').css('display') == 'none'){
		return 'preview';
	} else {
		return 'edit';
	}
};

var changeMode = function(mode,newPage){
	var curMode = currentMode();
	if(mode == curMode){
		return;
	}

	emitEventToDoc("changeMode",mode);

	if(mode == 'preview'){
		$('#edit-editormd').css('display','none');
		$('#preview-editormd-container').css('display','block');

		if(newPage || !mdEditor){
			createWelcomePage();
		} else {
			setPreviewContent(mdEditor.getValue());
		}
	} else {
		$('#edit-editormd').css('display','block');
		$('#preview-editormd-container').css('display','none');

		var md = "";
		if(!newPage){
			md = previewStr;
		}

		if(!mdEditor){
			editorInitStr = md;
			mdEditor = createEditor(md);

		} else {
			if(isEditorInit){
				mdEditor.setValue(md);
				if(editorNeedResize){
					editorInitStr = false;
					mdEditor.resize();
				}
			}else{
				editorInitStr = value;
			}


		}

	}
};

var setPreviewContent = function(markdown){
	previewStr = markdown;
	$("#preview-editormd").children().remove();
	editormd.markdownToHTML("preview-editormd", {
		markdown        : markdown ,//+ "\r\n" + $("#append-test").text(),
		//htmlDecode      : true,       // 开启 HTML 标签解析，为了安全性，默认不开启
		htmlDecode      : "style,script,iframe",  // you can filter tags decode
		//toc             : false,
		tocm            : true,    // Using [TOCM]
		//tocContainer    : "#custom-toc-container", // 自定义 ToC 容器层
		//gfm             : false,
		//tocDropdown     : true,
		// markdownSourceCode : true, // 是否保留 Markdown 源码，即是否删除保存源码的 Textarea 标签
		//emoji           : true,
		taskList        : true,
		tex             : true,  // 默认不解析
		flowChart       : true,  // 默认不解析
		sequenceDiagram : true   // 默认不解析
	});
};

var createEditor = function(md){
	return editormd("edit-editormd", {
		width: "100%",
		//height: 740,
		//autoHeight : true,
		delay:0,
		path : './lib/thirdPart/',
		theme : initData.theme.toolbar,
		previewTheme : initData.theme.preview,
		editorTheme : initData.theme.editor,
		markdown : md,
		codeFold : true,
		preview:true,
		//syncScrolling : false,
		saveHTMLToTextarea : true,    // 保存 HTML 到 Textarea
		searchReplace : true,
		//watch : false,                // 关闭实时预览
		htmlDecode : "style,script,iframe|on*",            // 开启 HTML 标签解析，为了安全性，默认不开启
		//toolbar  : false,             //关闭工具栏
		//previewCodeHighlight : false, // 关闭预览 HTML 的代码块高亮，默认开启
		emoji : true,
		taskList : true,
		tocm            : true,         // Using [TOCM]
		tex : true,                   // 开启科学公式TeX语言支持，默认关闭
		flowChart : true,             // 开启流程图支持，默认关闭
		sequenceDiagram : true,       // 开启时序/序列图支持，默认关闭,
		//dialogLockScreen : false,   // 设置弹出层对话框不锁屏，全局通用，默认为true
		//dialogShowMask : false,     // 设置弹出层对话框显示透明遮罩层，全局通用，默认为true
		//dialogDraggable : false,    // 设置弹出层对话框不可拖动，全局通用，默认为true
		//dialogMaskOpacity : 0.4,    // 设置透明遮罩层的透明度，全局通用，默认值为0.1
		//dialogMaskBgColor : "#000", // 设置透明遮罩层的背景颜色，全局通用，默认为#fff
		//imageUpload : true,
		//imageFormats : ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
		//imageUploadURL : "./php/upload.php",
		onload : function() {
			isEditorInit = true;
			mdEditor.setValue(editorInitStr);
			emitEventToDoc("onload");

			$('.markdown-body.editormd-preview-container').delegate('a', 'click', function(e) {
				e.preventDefault();
				emitEventToDoc("openWeb",$(this).attr('href'));
			});

		},
		onchange : function(){
			/*if(preventChangeEventOnce){
				preventChangeEventOnce = false;
			} else{
				emitEventToDoc("onchange");
			}*/
			//emitEventToDoc("onchange");
		},
		onscroll : function(event) {
		},
		onfinish : function(){
			emitEventToDoc("finish",mdEditor.getValue());
			changeMode('preview');
		}
	});
};

var createWelcomePage = function(){
	var welcome = '# 欢迎使用Fish-MD\n新建一个文档来进行体验吧';
	setPreviewContent(welcome);
};



$(function() {
	new QWebChannel(qt.webChannelTransport,function(channel){
		doc = channel.objects.doc;
		doc.doActionEmit.connect(doAction);
		initData = JSON.parse(doc.initData);
		console.log('initData',initData);

		createWelcomePage();
		$('.markdown-body.editormd-html-preview').delegate('a', 'click', function(e) {
			e.preventDefault();
			emitEventToDoc("openWeb",$(this).attr('href'));
		});
	});

	$('#goEditor').click(function(){
		changeMode('edit');
	});

	$('#edit-editormd').bind('paste',function(event){
		var items = (event.clipboardData || event.originalEvent.clipboardData).items;
		for(var i in items){
			var item = items[i];
			if(item.kind === 'file' && item.type.indexOf('image') === 0){
				emitEventToDoc("pasteImage");
				break;
			}
		}
	});

	$(window).resize(function(){
		if(isEditorInit && currentMode() == 'preview'){
			editorNeedResize = true;
		}
	});
});

