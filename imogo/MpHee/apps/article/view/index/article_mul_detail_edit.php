<link rel="stylesheet" href="__PUBLIC__/css/bootstrap/css/bootstrap.css">
<link rel="stylesheet" href="__APPURL__/css/appmsg.css">
<link rel="stylesheet" href="__APPURL__/css/appmsg-mul.css">
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

<script>
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
		afterBlur : function(){
			conok();
        }
	});
});

$(function(){
	$('body').delegate('.appmsgItem','mouseenter',function(){
		$(this).addClass('sub-msg-opr-show');
		window.curedt = $(this);
	});
	$('body').delegate('.appmsgItem','mouseleave',function(){
		$(this).removeClass('sub-msg-opr-show');
	});
	
	window.dqdiv = $('#appmsgItem1');
	$('#title').on('change keyup blur',function(){
		window.dqdiv.find('.m-title').text($(this).val());
	});
	$('#url').on('change keyup blur',function(){
		window.dqdiv.find('.m-url').text($(this).val());
	});
	$('#desc').on('change keyup blur',function(){
		window.dqdiv.find('.m-desc').text($(this).val());
	});	

    	window.curedt = $('body').find('.appmsgItem').eq(0);
		var ysdata = $.trim($('#ysdata').val());
		if(ysdata!=''){
			var jcon = $.evalJSON(ysdata);
			for(var i=0;i<jcon.length;i++){
				if(i>1){
					addaline();
				}
				var itm = $('.appmsgItem').eq(i);
				itm.find('.m-id').text(jcon[i].id)
				itm.find('.m-title').text(jcon[i].tit)
				itm.find('.m-desc').html(jcon[i].desc)
				itm.find('.m-url').text(jcon[i].url)
				itm.find('.m-img').attr('src',jcon[i].pic)
				itm.find('.m-con').html(jcon[i].con)				
			}
		}
	
});

	
//存贮校验
function czjy(){
	var tit = $.trim(window.dqdiv.find('.m-title').text());
	var img = window.dqdiv.find('img').attr('src');
	var url = $.trim(window.dqdiv.find('.m-url').text());
	var desc = $.trim(window.dqdiv.find('.m-desc').text());
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
function edititem(o,first){
		if(first){
			window.dqdiv = $('#appmsgItem1');
			$('.msg-editer-wrapper').css('marginTop','40px');
		}else{
			window.dqdiv = window.curedt;
			$('.msg-editer-wrapper').css('marginTop',($(o).offset().top-99)+'px');
		}
			
		$('#title').val(window.dqdiv.find('.m-title').text());
		$('#url').val(window.dqdiv.find('.m-url').text());
		$('#desc').val(window.dqdiv.find('.m-desc').text());
		editor.html(window.dqdiv.find('.m-con').html());
}

var delid=[];

function delitem(o){
	if(window.dqdiv[0] == window.curedt[0]){
		edititem(null,true);
	}
	if($('.appmsgItem').size()<3){
		alert('多图文最少不得少于两篇内容');
		return
	}
	if(confirm("确认删除此消息？")){
	var $msgItem=$(o).parent().parent().parent();
	var did=$(".m-id",$msgItem).text();
	if($(".m-id",$msgItem).size()>0){delid.push(did)}
	$msgItem.remove();
	}
}

function addaline(){
	var theitem = $('.msg-item-wrapper').find('.appmsgItem').eq(1);
	var bzitem = theitem.clone();
	bzitem.find('.m-id').text('');
	bzitem.find('.m-title').text('标题');
	bzitem.find('.m-url').text('');
	bzitem.find('.m-desc').text('');
	bzitem.find('.m-con').html('');
	bzitem.find('.m-img').attr('src','__APPURL__/images/ddspic.png');
	$('.sub-add').before(bzitem);
}

//图片剪裁成功后调用
function cutok(url){
	window.dqdiv.find('.m-img').attr('src',url);
}

//编辑器成功后调用
function conok(){
	window.dqdiv.find('.m-con').html(editor.html());
}

function savedate(){
	if(czjy()){
		var id = $.trim($('.msg-item-wrapper').attr('relid'));
		var data = [];
		$('.appmsgItem').each(function(){
			var sd = {};
			sd.id = $.trim($(this).find('.m-id').text());
			sd.tit = $.trim($(this).find('.m-title').text());
			sd.pic = $(this).find('img').attr('src');
			sd.url = $.trim($(this).find('.m-url').text());
			sd.desc = $.trim($(this).find('.m-desc').text());
			sd.con = $.trim($(this).find('.m-con').html());
			data[data.length] = sd;
		});
		$.post("{url('index/article_details_add',array(action=>adds))}",{delid:delid,id:id,data:data},function(data){
			alert(data);
			window.location.href="{url('index/article')}";
		});
	}
}
</script>

<textarea id="ysdata" style="display: none;">
{$ysdata}
</textarea>
<div class="row">
		<div class="col-md-4 msg-preview" id="nrdiv1">
			<div class="msg-item-wrapper" relid="<?php echo $_GET['id']?>">
				<div id="appmsgItem1" class="appmsgItem msg-item">
					<p class="msg-meta">
						<span class="msg-date"><?php echo date('Y-m-d'); ?></span>
					</p>
					<div class="cover">
						<img class="i-img m-img" src="__APPURL__/images/fmdtp.jpg" style="width: 100%;height: 200px;">
						<h4 class="msg-t">
							<span class="i-title m-title">标题</span>
						</h4>
						<ul class="abs tc sub-msg-opr">                 
							<li class="b-dib sub-msg-opr-item">                   
								<a href="javascript:;" onclick="edititem(this);" class="th opr-icon edit-icon">编辑</a>                 
							</li>               
						</ul>
					</div>
					<div rel="desc" class="m-desc"></div>
					<div rel="con" class="m-con" style="display: none;"></div>
					<div rel="url" class="m-url" style="display: none;"></div>
					<div rel="id" class="m-id" style="display: none;"></div>
				</div>
				
				<div class="rel sub-msg-item appmsgItem">              
					<span class="thumb">                    
					<img class="i-img m-img" src="__APPURL__/images/ddspic.png" style="width: 72px;height: 72px;">
					</span>       
					<h4 class="msg-t">                    
					<span class="i-title m-title">标题</span>                
					</h4>       
					<ul class="abs tc sub-msg-opr">         
						<li class="b-dib sub-msg-opr-item">           
							<a href="javascript:;" onclick="edititem(this);" class="th opr-icon edit-icon">编辑</a>         
						</li>         
						<li class="b-dib sub-msg-opr-item">           
							<a href="javascript:;" onclick="delitem(this);" class="th opr-icon del-icon">删除</a>         
						</li>       
					</ul>    
					<div rel="desc" class="m-desc"></div>
					<div rel="con" class="m-con" style="display: none;"></div>
					<div rel="url" class="m-url" style="display: none;"></div>
					<div rel="id" class="m-id" style="display: none;"></div>
				</div>
				
				<div class="sub-add">            
				<a href="javascript:;" class="block tc sub-add-btn" onclick="addaline();">
				<span class="vm dib sub-add-icon"></span>增加一条</a>           
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
						    	<input type="text" value="" id="title" class="form-control" style="width: 482px;" name="title">
						    </div>
					    </div>
					    <div class="control-group" id="picsethere">
							<label class="control-label">封面</label><span class="maroon">*</span><span class="help-inline">(必须上传一张图片)</span>    
							<div class="controls">
								<div class="cover-area" style="height: 40px;overflow: hidden;">
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
								<textarea name="summary" id="desc" class="msg-txta" placeholder="摘要可以为空"></textarea>    
							</div>
						</div>
					  	<div class="control-group">
						<label class="control-label">正文</label> <span class="maroon">*</span><span class="help-inline">(正文为空时则直接跳转到来源地址)</span>     
						    <div class="controls">
								<TEXTAREA id="editor" name="content"></TEXTAREA>
							</div>
						</div>
					  	<div id="url-block" style="" class="control-group">
							<label class="control-label">地址</label> <span class="help-inline">(请输入正确的URL链接格式)</span>          
						    <div class="controls">								
								<input type="text" id="url" class="form-control" value="" style="width: 482px;" name="source_url">
							</div>
						</div>
					</form> 
				</div>
				<span class="abs msg-arrow a-out" style="margin-top: 0px;"></span>
				<span class="abs msg-arrow a-in" style="margin-top: 0px;"></span>
			</div>
		</div>
	</div>
	<div class="control-group">
		<div class="controls text-center">
			<button id="save-btn" type="button" onclick="savedate();" class="btn btn-primary btn-large">保存</button>
			<button id="cancel-btn" type="button" onclick="conok();" class="btn btn-large">取消</button>
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
			cutok(imgurl);
            },
 
            Error: function(up, args) {
				alert('文件：'+args.file.name+',错误：'+args.message);
            }
        }
    });
 
 
    uploader.init();

</script>