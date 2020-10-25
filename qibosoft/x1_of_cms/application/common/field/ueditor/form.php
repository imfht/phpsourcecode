<?php
function_exists('urls') || die('ERR');



$jscode_pc = $jscode_wap = '';
if(fun('field@load_js',$field['type'])){
	$serverurl = urls("index/attachment/upload","dir=images&from=ueditor&module=".request()->dispatch()['module'][0]);
	$editor_tpl = iurl('index/editor/index');
	$systype = modules_config('bbs')?'bbs':'cms';
	$jscode_pc = <<<EOT
<script type="text/javascript">
var editor_a = [];
var editor_i = 0;
jQuery(document).ready(function() {
	$('.js-ueditor').each(function(){
		//$('.ueditor').width($('.ListType .Right').width());	//重新定义编辑器的宽度＝表单提交容器标签的宽度
		var edit = UE.getEditor($(this).attr('name'), {
            initialFrameHeight:350,  //初始化编辑器高度,默认320
            autoHeightEnabled:false,  //是否自动长高
            maximumWords: 50000, //允许的最大字符数
            serverUrl: '{$serverurl}',
			//toolbars: [ ['fullscreen', 'source', 'undo', 'redo', 'bold','italic','fontsize','forecolor']]
        });
		editor_a.push(edit);
	});
});
</script>
<script src="__STATIC__/libs/ueditor/ueditor.config.js"></script>
<script src="__STATIC__/libs/ueditor/ueditor.all.min.js"></script>

 
<script type="text/javascript"> 
jQuery(document).ready(function() {
	$('.addMvUrl').each(function(i){	//添加站外视频
		$(this).click(function(){
			editor_i = i;
			layer.prompt({
					title: '请输入优酷或腾讯视频网址',
					formType: 0
			   }, function(value){
				 layer.closeAll();
				 var str = '<br>[iframe_mv]'+value+'[/iframe_mv]<br><br>';
				 editor_a[editor_i].execCommand('insertHtml',str);
			});
		});		
	});

	$('.addTopicLink').each(function(i){	//添加站内引用
		$(this).click(function(){
			editor_i = i;
			layer.open({
				type:2,
				title: "站内引用",
				area: ['850px', '650px'],
				content: "/member.php/member/quote/index.html?type={$systype}&uid=0",
			});
		});		
	});

});

function insert_topic(content){
	editor_a[editor_i].execCommand('insertHtml',"<br>"+content+"<br>");
}

</script>

<!--pc布局模板开始-->
<script type="text/javascript"> 
jQuery(document).ready(function() {
	$('.slectEditMode').each(function(i){
		$(this).click(function(){
			editor_i = i;
			showEditMode();
		});		
	});
})
function insertHtml(nums) {
	var strs=$('.stylemode'+nums).html();
	editor_a[editor_i].execCommand('insertHtml',strs);
	hide_nav($('#editmodes'),$('#fullbg1'));
}
function showEditMode(){
	$.get("{$editor_tpl}",function(d){
		$('#editmodes').html(d);
	});
	show_nav($('#editmodes'),$('#fullbg1'));
	$('#fullbg1').height($(window).height());
	$('#editmodes').height($(window).height());
}
function show_nav(node,fullbg){
	fullbg.css({'display':'block'}).stop().animate({'opacity':.6},500,function(){
		node.stop().animate({'width':'300px','padding':'0px 10px 0 10px'},300);
	});
}
function hide_nav(node,fullbg){
	fullbg.animate({'opacity':0},300,function(){
		$(this).css({'display':'none'});
	});
	setTimeout(function(){
		node.html('');
		node.stop().animate({'width':'0px','padding':'0px 0px 0 0px'},300);
	}, 500)
}
</script>

<style type="text/css">
.ue_btn{
	padding:5px 0 5px 0;
    float:left;
    margin-right:10px;
}
.ue_btn a{
	display:inline-blodk;
	padding:5px 10px;
	background:green;
	border-radius:5px;
	color:#FFF;
}
.fullbg { 
	background-color:#000; 
	opacity:0; 
	top:0; 
	left:0; 
	width:100%; 
	height:100%; 
	z-index:1001; 
	position:fixed;
	display:none;
}
#editmodes{ 
	position:fixed; 
	top:0;  
	right:0; 
	z-index:1002; 
	height:100%;
	width:0px;
	overflow:auto;
	overflow-x:hidden;
	scrollbar-face-color: #FFFFFF;
	scrollbar-shadow-color: #eee;
	scrollbar-highlight-color: #eee;
	scrollbar-3dlight-color: #FFFFFF;
	scrollbar-darkshadow-color: #FFFFFF;
	scrollbar-track-color: #FFFFFF;
	scrollbar-arrow-color: #D2E5F4; 
	background:#FFF;
}
</style>
<div id="editmodes"></div>
<div class="fullbg" id="fullbg1" onclick="hide_nav($('#editmodes'),$('#fullbg1'))"></div>
<!--pc布局模板结束-->

EOT;

$jscode_wap = <<<EOT

				<link rel="stylesheet" href="__STATIC__/libs/summernote/bootstrap.min.css" />
				<script type="text/javascript" src="__STATIC__/libs/summernote/bootstrap.min.js"></script>
				<link rel="stylesheet" href="__STATIC__/libs/summernote/summernote.css">
				<script type="text/javascript" src="__STATIC__/libs/summernote/summernote.js"></script>
				<script type="text/javascript" src="__STATIC__/libs/summernote/lang/summernote-zh-CN.js"></script>
				<script type="text/javascript">
				var editor_a = [];
				var editor_i = 0;
					$(document).ready(function(i){
					  $('.summernote').each(function(){							
						var edit = $(this).summernote({
							lang: 'zh-CN',
							height: 200,
							callbacks: {
								onImageUpload: function (files) {
									sendFile(edit, files[0]);
								}
							},
							toolbar: [
										['codeview',['fullscreen','undo','redo', 'clear','codeview']], //查看html代码
										//['fontname', []], //字体系列                                 
										['style', ['bold', 'italic', 'underline','strikethrough','hr','link','picture']], // 字体粗体、字体斜体、字体下划线、字体格式清除       
										//['font', ['strikethrough', 'superscript', 'subscript']], //字体划线、字体上标、字体下标   
										//['fontsize', ['fontsize','color']], //字体大小                                
									   // ['color', []], //字体颜色                             
										//['style', ['style']],//样式
										//['para', [ 'paragraph']], //无序列表、有序列表、段落对齐方式'ul', 'ol',
										//['height', ['height']], //行高                  
										//['table',['table']], //插入表格    
										//['hr',[]],//插入水平线                
										//['link',['hr','link','picture']], //插入链接                
										//['picture',[]], //插入图片                
										//['video',['video']], //插入视频
										 
										//['fullscreen',[]], //全屏
										
										//['undo',[]], //撤销
										//['redo',[]], //取消撤销
									   // ['help',['help']], //帮助
									 ],

						  });
						  editor_a.push(edit);
						  var sendFile = function(o,files){
							  var formData = new FormData();
								formData.append("file", files);
								$.ajax({
								url: "{$serverurl}?action=uploadimage",
									data: formData,
									cache: false,
									contentType: false,
									processData: false,
									type: 'POST',
									success: function (data) {
										o.summernote('insertImage', data.url);
									}
								});
							  
						  }
					  });
					});
				</script>

<script type="text/javascript"> 
var cache_summernote_code = '';
jQuery(document).ready(function() {		//加入站外视频
	$('.addMvUrl').each(function(i){
		$(this).click(function(){
			editor_i = i;
			layer.prompt({
					title: '请输入优酷或腾讯视频网址',
					formType: 0
			   }, function(value){
				 layer.closeAll();
				 var str = '[iframe_mv]'+value+'[/iframe_mv]';
				 editor_a[editor_i].summernote('insertText',str);
			});
		});		
	});

	$('.addTopicLink').each(function(i){	//添加站内引用
		$(this).click(function(){
			editor_i = i;
			if(temp_content==''){
				layer.open({
					type:2,
					title: "请选择要插入的数据",
					area: ['90%', '80%'],
					content: "/index.php/index/msg/index.html#/public/static/libs/bui/pages/hack/index?type={$systype}&uid=0",
				});
			}else{
				insert_topic(temp_content)
			}
			
		});		
	});
})
</script>

<!--wap布局模板开始-->
<script type="text/javascript"> 
var cache_summernote_code = '';
jQuery(document).ready(function() {
	$('.slectEditMode').each(function(i){
		$(this).click(function(){
			editor_i = i;
			showEditMode();
		});		
	});
});

var temp_content = '';
function insert_topic(content){
	temp_content = content;
	editor_a[editor_i].summernote('insertText', ' ');	//焦点获取失败,避免下面的报错
	editor_a[editor_i].summernote('pasteHTML', '<!--#@#@#@#@#-->');
	cache_summernote_code = editor_a[editor_i].summernote('code');
	if(cache_summernote_code.indexOf('<!--#@#@#@#@#-->')==-1){
		layer.alert("焦点获取失败,请重新点击选择位置,要在哪插入模板!");
		return false;
	}
	temp_content = '';
	editor_a[editor_i].summernote('code', cache_summernote_code.replace(/<!--#@#@#@#@#-->/, '<br>'+content+'<br>'));
}

function insertHtml(nums) {
	var strs=$('.stylemode'+nums).html();
	//editor_a[editor_i].execCommand('insertHtml',strs);
	//editor_a[editor_i].summernote('pasteHTML', 'gggggggg');
	editor_a[editor_i].summernote('code', cache_summernote_code.replace(/<!--#@#@#@#@#-->/, strs+'<br>'));
	hide_nav($('#editmodes'),$('#fullbg1'));
}
function showEditMode(){
	//editor_a[editor_i].summernote('createRange') 选中区域
	editor_a[editor_i].summernote('insertText', ' ');	//焦点获取失败,避免下面的报错
	editor_a[editor_i].summernote('pasteHTML', '<!--#@#@#@#@#-->');
	cache_summernote_code = editor_a[editor_i].summernote('code');
	if(cache_summernote_code.indexOf('<!--#@#@#@#@#-->')==-1){
		layer.alert("焦点获取失败,请重新点击选择位置,要在哪插入模板!");
		return false;
	}

	$.get("{$editor_tpl}",function(d){
		$('#editmodes').html(d);
	});
	show_nav($('#editmodes'),$('#fullbg1'));
	$('#fullbg1').height($(window).height());
	$('#editmodes').height($(window).height());
}
function show_nav(node,fullbg){
	fullbg.css({'display':'block'}).stop().animate({'opacity':.6},500,function(){
		node.stop().animate({'width':'300px','padding':'0px 10px 0 10px'},300);
	});
}
function hide_nav(node,fullbg){
	fullbg.animate({'opacity':0},300,function(){
		$(this).css({'display':'none'});
	});
	setTimeout(function(){
		node.html('');
		node.stop().animate({'width':'0px','padding':'0px 0px 0 0px'},300);
	}, 500)
}
</script>

<style type="text/css">
.ue_btn{
	padding:5px 0 5px 0;
    float:left;
    margin-right:10px;
}
.ue_btn a{
	display:inline-blodk;
	padding:5px 10px;
	background:green;
	border-radius:5px;
	color:#FFF;
}
.fullbg { 
	background-color:#000; 
	opacity:0; 
	top:0; 
	left:0; 
	width:100%; 
	height:100%; 
	z-index:1001; 
	position:fixed;
	display:none;
}
#editmodes{ 
	position:fixed; 
	top:0;  
	right:0; 
	z-index:1002; 
	height:100%;
	width:0px;
	overflow:auto;
	overflow-x:hidden;
	scrollbar-face-color: #FFFFFF;
	scrollbar-shadow-color: #eee;
	scrollbar-highlight-color: #eee;
	scrollbar-3dlight-color: #FFFFFF;
	scrollbar-darkshadow-color: #FFFFFF;
	scrollbar-track-color: #FFFFFF;
	scrollbar-arrow-color: #D2E5F4; 
	background:#FFF;
}
</style>
<div id="editmodes"></div>
<div class="fullbg" id="fullbg1" onclick="hide_nav($('#editmodes'),$('#fullbg1'))"></div>
<!--wap布局模板结束-->

EOT;

}

$field['input_width'] && $field['input_width']="width:{$field['input_width']};";
$field['input_width'] || $field['input_width']='max-width:80%;';
$field['input_height'] && $field['input_height']="width:{$field['input_height']};";

if(IN_WAP===true){

	return <<<EOT

<textarea id="{$name}" name="{$name}" class="summernote" placeholder="请输入内容">{$info[$name]}</textarea>
$jscode_wap
<div class="ue_btn slectEditMode"><a href="javascript:;">布局模板</a></div> <div class="ue_btn addMvUrl"><a href="javascript:;">站外视频</a></div> <div class="ue_btn addTopicLink"><a href="javascript:;">站内引用</a></div>

EOT;
;

}else{

	return <<<EOT

<div style="{$field['input_width']}{$field['input_height']}" class="layui-textarea c_{$name}  {$field['css']}">
<script id="{$name}" class="js-ueditor" name="{$name}" type="text/plain">{$info[$name]}</script>
$jscode_pc
</div>

<div class="ue_btn slectEditMode"><a href="javascript:;">布局模板</a></div> <div class="ue_btn addMvUrl"><a href="javascript:;">站外视频</a></div> <div class="ue_btn addTopicLink"><a href="javascript:;">站内引用</a></div>

EOT;
;

}

