<link rel="stylesheet" href="__PUBLIC__/css/bootstrap/css/bootstrap.css">
<link rel="stylesheet" href="__APPURL__/css/appmsg.css">
<style>
	body{
		padding: 20px;
		border-radius:10px; 
	}
	label{
	display: inline-block;
}
.help-inline{
	vertical-align: top;
}
.row{
	padding-top: 20px;
	padding-bottom: 20px;
}
.control-group img{
	max-width: 600px;
}
.jcbtncls{
	background:blue;
	border-radius:8px;
	color: #fff;
	padding:5px 12px;
	line-height: 30px;
	font-size: 16px;
	font-family: 'Microsoft Yahei';
}
</style>
<title>图文素材管理页面</title>
<script type="text/javascript" src="__APPURL__/js/articlecom.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/kindeditor/kindeditor-min.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/upload/upload.full.min.js"></script>

<script type="text/javascript">
function hidepicname(url,jo,name){
	$('#'+name+'_name').html('');
	$('#'+name+'_bfb').html('');
	cutok();
}
$(function(){
	window.dqdiv = $('#nrdiv1');
	$('#title').on('change keyup blur',function(){
		window.dqdiv.find('.m-title').text($(this).val());
	});
	$('#desc').on('change keyup blur',function(){
		window.dqdiv.find('.m-desc').text($(this).val());
	});
	$('#url').on('change keyup blur',function(){
		window.dqdiv.find('.m-url').text($(this).val());
	});   
});

//存贮校验
function czjy(){
	window.dqdiv.find('.m-con').html(editor.html());
	var tit = $.trim(window.dqdiv.find('.m-title').text());
	var img = window.dqdiv.find('img').attr('src');
	var desc = window.dqdiv.find('.m-desc').text();
	var url = $.trim(window.dqdiv.find('.m-url').text());
	var con = $.trim(window.dqdiv.find('.m-con').html());
	if(tit==''){
		alert('请填写标题');
		return false;
	}
	if(url=='' && con==''){
		alert('请编辑正文或者链接地址');
		return false;
	}
	return true;
}

function savedate(){
	if(czjy()){
	    window.dqdiv = $('#nrdiv1');
		var id = $.trim($('.msg-item-wrapper').attr('relid'));
		var tit = $.trim(window.dqdiv.find('.m-title').text());
		var img = window.dqdiv.find('img').attr('src');
		var desc = window.dqdiv.find('.m-desc').text();
		var url = $.trim(window.dqdiv.find('.m-url').text());
		var con = $.trim(window.dqdiv.find('.m-con').html());
		$.post("{url('index/article_details_add',array(action=>add))}",{id:id,tit:tit,pic:img,desc:desc,url:url,con:con},function(data){
			alert(data);
			window.location.href="{url('index/article')}";
		});
	}
}

//调用编辑器
var KE = KindEditor.ready(function(K) {
	window.editor = K.create('#editor', {
	    cssPath : '__PUBLIC__/js/kindeditor/plugins/code/prettify.css',
		uploadJson : '__PUBLIC__/js/kindeditor/php/upload_json.php',
		fileManagerJson : '__PUBLIC__/js/kindeditor/php/file_manager_json.php',
		width: '96%',
		height: '460px',
		resizeType: 1,
		themeType: 'simple',
		urlType: 'relative',
		allowFileManager : true,
	});
});

</script>
</head>

<body>
<div class="row">
		<div class="col-md-4 msg-preview" id="nrdiv1">
			<div class="msg-item-wrapper" relid="{$info['id']}">
				<div id="appmsgItem1" class="msg-item">
					<h4 class="msg-t">
						<span class="i-title m-title" rel="title">{$info['tit']}</span>
					</h4>
					<p class="msg-meta">
					</p>
					<div class="cover">
						{if empty($info['pic'])}
						<img id="imgshow" class="i-img m-img" src="__APPURL__/images/fmdtp.jpg" style="width: 100%;height: 180px;">
						{else}
						<img id="imgshow" class="i-img m-img" src="{$info['pic']}" style="width: 100%;height: 180px;">
						{/if}
					</div>
					<p class="msg-text m-desc" rel="desc">{$info['desc']}</p>
					<div rel="con" class="m-con" style="display: none;">{$info['con']}</div>
					<div rel="url" class="m-url" style="display: none;">{$info['url']}</div>
				</div>
				<div class="msg-hover-mask"></div>
				<div class="msg-mask">
					<span class="dib msg-selected-tip"></span>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="msg-editer-wrapper">
				<div class="msg-editer">
					<form id="appmsg-form" class="form">
						<div class="control-group">
							<label class="control-label">标题</label><span class="maroon">*</span><span class="help-inline">(必填,不能超过64个字)</span>
							<div class="controls">
						    	<input type="text" value="{$info['tit']}" id="title" class="form-control" style="width: 482px;" name="title">
						    </div>
					    </div>
					    <div class="control-group">
							<label class="control-label">封面</label><span class="maroon">*</span><span class="help-inline">(必须上传一张图片)</span>    
							<div class="controls">
								<div class="cover-area">
									<div class="cover-hd" >
										<div class="button" id='image_upload' >选择图片</div>
									</div>
									<p id="upload-tip" class="upload-tip">大图片建议尺寸：700像素 * 300像素</p>
								</div>
							</div>
						</div>
					  	<div id="desc-block" style="" class="control-group">
							<label class="control-label">摘要</label> <span class="help-inline">(不能超过120个字)</span>          
						    <div class="controls">
								<textarea name="summary" id="desc" class="msg-txta" placeholder="摘要可以为空">{$info['desc']}</textarea>    
							</div>
						</div>
					  	<div class="control-group">
						<label class="control-label">正文</label><span class="maroon">*</span><span class="help-inline">(正文和链接地址至少填写一项)</span>     
						    <div class="controls">
						    <TEXTAREA id="editor" name="content">{$info['con']}</TEXTAREA>
							</div>
						</div>
					  	<div id="url-block" class="control-group">
							<label class="control-label">地址</label> <span class="help-inline">(请输入正确的URL链接格式)</span>          
						    <div class="controls">
								<input type="text" id="url" class="form-control" value="{$info['url']}" style="width: 482px;" name="source_url">
							</div>
						</div>
					</form> 
				</div>
			</div>
		</div>
	</div>
	<div class="control-group">
		<div class="controls text-center">
			<button id="save-btn" type="button" onclick="savedate();" class="btn btn-primary btn-large">保存</button>
			<button id="cancel-btn" type="button" class="btn btn-large">取消</button>
		</div>
	</div>

<script type="text/javascript">
//调用上传图片插件
var uploader = new plupload.Uploader({
				runtimes : 'html5,flash,silverlight,html4',
				browse_button : 'image_upload', 
				url : "{url('admin/index/upload')}",
				multi_selection:false,
				resize : { quality : 70 },
				
				filters : {
					max_file_size : '3mb',
					
					mime_types: [
						{title : "Image files", extensions : "jpg,gif,png,bmp"},
						{title : "Zip files", extensions : "zip"}
					]
				},
				
				flash_swf_url : '__PUBLIC__/js/upload/Moxie.swf',
								
		init : {
            FilesAdded: function(up, files) {
				uploader.start();
            },

            FileUploaded: function(up, file, info) {
			var response = eval('(' + info.response + ')');
			var imgurl = response.file.savepath+response.file.savename;
			$("#imgshow").attr({"src":imgurl});
			$("#fengmian").attr("value",imgurl);
			
            },
 
            Error: function(up, args) {
				alert('文件：'+args.file.name+',错误：'+args.message);
            }
        }
    });
 
 
    uploader.init();

</script>