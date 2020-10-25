<?php
function_exists('urls') || die('ERR');

$cuturl = iurl('index/image/cutimg');
$width = IN_WAP===true?'95%':'900px';
$height = IN_WAP===true?'100%':'800px';
$display = IN_WAP===true?'':'display:none;';

$jscode = '';
if(fun('field@load_js',$field['type'])){

	$serverurl = urls('index/attachment/upload','dir=images&from=base64&module='.request()->dispatch()['module'][0]);

	$jscode = <<<EOT

<style type="text/css">
.uploadImg ol{
	line-height: 35px;
	font-size: 16px;
}
.uploadImg .upbtn{
	width:80px;
	border:#DDD dotted 1px;
	text-align: center;
	padding: 15px 0;
	background: #FFF;	
	cursor: pointer;
	color: #999;
	position:relative;
}
.uploadImg .upbtn i:first-child{
	font-size:32px;
}
.uploadImg .upbtn i:last-child{
	position:absolute;
	right:0;
	top:0;
	z-index:1;
	font-size:20px;
	background:#ff8300;
	padding:2px;
	color:#fff;
}
.uploadImg .upbtn i:last-child:hover{
	background:green;
}
.uploadImg li:hover{
	border: #F60 dotted 1px;
	color: #F60;
}

.ListImgs:after{
	content: '';
	display: block;
	clear: both;
}
.ListImgs div{
	width:80px;
	float: left;
	position: relative;
	margin: 10px 10px 0 0;
}
.ListImgs div span{
	display: block;
	position: relative;
	background: #FFF;
	box-shadow: 0px 0px 1px #BBB;
}
.ListImgs div span:before{
	content: '';
	display: block;
	padding-top: 100%;
}
.ListImgs div span img{
	position:absolute;
	max-width:100%;
	max-height: 100%;
	left:50%;
	top: 50%;
	border:0;
  -webkit-transform: translate3D(-50%, -50%, 0);
      -ms-transform: translate3D(-50%, -50%, 0);
          transform: translate3D(-50%, -50%, 0);
} 
.ListImgs div em{
	position: absolute;
	width:25px;
	height: 25px;
	text-align: center;
	line-height: 25px;
	background:rgba(120,120,120,0.6);
	color: #FFF;
	right: 0px;
	top:0px;
	cursor: pointer;
}
.ListImgs div em.cut{
	left: 0px;
}
.ListImgs div em.drag{
	left: 0px;
	top:55px;
	cursor: move;
}
.ListImgs div em:hover{
	background:rgba(255,60,0,0.6);
}
.ListImgs div em{
	{$display}
}
</style>
<script src="__STATIC__/libs/jquery-ui/jquery-ui.min.js"></script>  
<script type="text/javascript" src="__STATIC__/js/exif.js"></script>
<script type="text/javascript">
var severUrl = "$serverurl";
</script>

EOT;

}

$info[$name] = json_decode($info[$name],true)?$info[$name]:''; //做个判断,避免JS出错

$dir = config('system_dirname')?:'pasepic';
$cut_pic_save_url = iurl('index/attachment/upload',"dir={$dir}&from=base64&module={$dir}");

return <<<EOT

$jscode
<script type="text/javascript">
jQuery(document).ready(function() {
	$(".uploadImge_{$name}").each(function(){
		var pics = [];
		var that = $(this);

		that.find(".upbtn").click(function(e){
			that.find('input[type="file"]').click();
		});


		var j=0;
		that.find(".upbtn i:last").click(function(event){
			j++;
			if(j==1){
				paseImg($(this));
			}
			layer.msg("先按住“Ctrl+Alt+A”截图完毕,再点击这个图标（或者先复制图片网址）,最后按住“Ctrl+V”,即可上传截图",{time:5000});
			$(this).focus();
			event.stopPropagation();
			
		});

		//截图后再上传
		function paseImg(that){		
			var imgReader = function(item) {	
				var blob = item.getAsFile(),	
					reader = new FileReader();	
				reader.onloadend = function(e) {
					$.ajax({
						url: '{$cut_pic_save_url}',	
						type: 'POST',	
						data: {	
							imgBase64: e.target.result	
						},	
						success: function(res) {	
							layer.msg(res.info);	
							if (res.code == 1) {	
								var url = res.path;	
								if (url.indexOf('://') == -1 && url.indexOf('/public/') == -1) {	
									url = (typeof(web_url) != 'undefined' ? web_url : '') + '/public/' + url;	
								}

								//显示与赋值
								//that.parent().parent().find(".input_value").val( pics.join(',') );
								pics.push({"picurl":res.path,"title":"","url":""});	//组图
								viewpics(pics);
							}
						}	
					})	
				};	
				reader.readAsDataURL(blob);	
			};
			that.get(0).addEventListener("paste", function(e) {
				var clipboardData = e.clipboardData,	
					i = 0,	
					items,	
					item,	
					types;	
				if (clipboardData) {	
					items = clipboardData.items;
					if (!items) {	
						return;	
					}	
					item = items[0];	
					types = clipboardData.types || [];
					for (; i < types.length; i++) {	
						if (types[i] === 'Files') {	
							item = items[i];	
							break;	
						}	
					}
					if (item && item.kind === 'file' && item.type.match(/^image\//i)) {	
						imgReader(item);	
					}else if(item.kind === "string"){
						item.getAsString(function (pic_url) {
							if( pic_url.match(/^(http|https):/i) ){
								pic_url = pic_url.replace(/,/g,'%2C');
								//显示与赋值
								pics.push({"picurl":pic_url,"title":"","url":""});	//组图
								viewpics(pics);
							}
						});
					}
				}	
			});
		};
		
		//对已上传的图片进行截图
		var add_cutimg = function(e){
			that.find('.cut').each(function () {
				var cthis = $(this);
				cthis.on('click',function(){
					var pic = cthis.parent().find("img").attr('src');
					var opt = cthis.data('options');
					layer.open({
						type: 2,
						title: '截图',
						area: ["{$width}", "{$height}"],
						scrollbar: false,
						content: '{$cuturl}?picurl='+pic+'&opt='+opt,
						end: function () {
							check_value();	//重新核对数据
						}
					});
				});
			});
		}

		//鼠标经过时显示操作菜单
		var showmenu = function(){
			that.find('.ListImgs div').each(function(){
				var obj = $(this);
				obj.hover(  
						function(){  
							obj.find('em').show();  
						},
						function(){  
							obj.find('em').hide();  
						}   
				) ;
			});
		}
		
		//拖拽排序
		var drag_move = function(){
			that.find('.ListImgs').sortable({
					//connectWith: ".ListImgs div",
					handle: '.drag',
					stop: function () { 
						check_value();
					}
				}).disableSelection();
		}

		//删除图片事件
		var delpic = function(){
			that.find(".ListImgs em.del").click(function(e){
				//这里删除的图片没有真正从服务器删除
				$(this).parent().remove();
				check_value();
			});
		};
		
		//核对数据
		var check_value = function(){
			that.find(".ListImgs img").each(function(e){
				pics = [];	//重新设置值
				var obj = that.find(".ListImgs img");				
				obj.each(function(e){
					var img = $(this).attr("src");
					if(img.indexOf('://') == -1)img = img.replace('/public/','');
					pics.push({'picurl':img,
						'title':that.find(".ListImgs input[data-i='title"+$(this).data("i")+"']").val(),
						'url':that.find(".ListImgs input[data-i='url"+$(this).data("i")+"']").val(),
						});					
				});				
				if(obj.length==0){
					that.find(".input_value").val('');
				}else{
					that.find(".input_value").val( JSON.stringify(pics) );
				}		
			});
			that.find(".ListImgs img").length>0 || that.find(".input_value").val('');
		};

		$(document).on("keypress", "input", function(event) { 
			return event.keyCode != 13;	//回车不能提交表单,请点击提交按钮!
		});
		
		//介绍或网址输入结束后的事件
		var mouseout = function(){
			that.find(".ListImgs input").blur(function(e){
				check_value();
			});

			that.find(".ListImgs input").bind('keyup',function(e){
				if (event.keyCode == "13") {
					layer.alert('请点击底部的提交按钮来提交表单!');
				}
			});

		}

		that.find('input[type="file"]').change(function(){
            uploadBtnChange($(this).get(0),pics,viewpics);
        });

		if(that.find(".input_value").val()!=''){
			pics = $.parseJSON( that.find(".input_value").val() );
		}
		
		var viewpics = function(pic_array,pic_url){
			var html = '';
			pic_array.forEach(function(rs,i){
				var sear=new RegExp('http');
				if(sear.test(rs.picurl)){
		　　			html += '<div><span><img src="'+rs.picurl+'" data-i="'+i+'"></span><em class="del"><i class="fa fa-remove"></i></em><em class="cut" data-options=""><i class="fa fa-cut"></i></em><em class="drag"><i class="fa fa-arrows"></i></em><input placeholder="介绍" style="width:80px;margin:5px 0 5px 0;border:1px solid #eee;" data-i="title'+i+'" value="'+rs.title+'"><br><input value="'+rs.url+'" data-i="url'+i+'" placeholder="网址" style="width:80px;border:1px solid #eee;" ></div>';
	　　			}else{
					html += '<div><span><img src="/public/'+rs.picurl+'" data-i="'+i+'"></span><em class="del"><i class="fa fa-remove"></i></em><em class="cut" data-options=""><i class="fa fa-cut"></i></em><em class="drag"><i class="fa fa-arrows"></i></em><input placeholder="介绍" style="width:80px;margin:5px 0 5px 0;border:1px solid #eee;" data-i="title'+i+'" value="'+rs.title+'"><br><input value="'+rs.url+'" data-i="url'+i+'" placeholder="网址" style="width:80px;border:1px solid #eee;" ></div>';
				}			 
			});
			that.find(".ListImgs").html(html);
			delpic();
			mouseout();
			add_cutimg();
			drag_move();
			showmenu();
			check_value(); //新上传要用到
		};
		viewpics(pics);

		var uploadBtnChange = function (filefield,pics,callback){
            var scope = this;
			//var pics = [];
            if(window.File && window.FileReader && window.FileList && window.Blob){ 
                //获取上传file
                //var filefield = document.getElementById(fileName),
                file = filefield.files[0];
				var oj = filefield.files;
				for(var i=0;i<oj.length;i++){
					processfile(oj[i],pics,callback);
				}
            }else{
                alert("此浏览器不完全支持压缩上传图片");
            }
        };

        var processfile = function (file,pics,callback) {
			var Orientation = 0;
			var alltags = {};
			//获取图片的参数信息			
			EXIF.getData(file, function(){
				alltags = EXIF.pretty(this);
				//EXIF.getAllTags(this);
				Orientation = EXIF.getTag(this, 'Orientation');
			});			

            var reader = new FileReader();

			var nopress = false;	//不要压缩
			if(file.size<500000 || (file.name.substr(file.name.lastIndexOf(".")+1)).toLowerCase()=='gif'){
				reader.readAsDataURL(file);
				nopress = true;
			}

            reader.onload = function (event) {

				if(nopress==true){	//不要压缩
					$.post(severUrl, {'imgBase64':event.target.result,'Orientation':Orientation,'tags':alltags}).done(function (res) {
							layer.closeAll();
							 if(res.code==1){
								 pics.push({"picurl":res.path,"title":"","url":""});	//组图
								 //pics[0] = res.path;	//单图
								 //textObj.val( pics.join(',') );
								 if(typeof callback == 'function'){
									callback(pics,res.url);
								 }
							 }
					}).fail(function () {
							layer.closeAll();
							layer.alert('操作失败，请跟技术联系');
					});
					return ;
				}

                var blob = new Blob([event.target.result]); 
                window.URL = window.URL || window.webkitURL;
                var blobURL = window.URL.createObjectURL(blob); 
                var image = new Image();
                image.src = blobURL;
                image.onload = function() {
                    var resized = resizeUpImages(image , (file.name.substr(file.name.lastIndexOf(".")+1)).toLowerCase() );					
					if(resized){
						layer.load(1);
						$.post(severUrl, {'imgBase64':resized,'Orientation':Orientation,'tags':alltags}).done(function (res) {
							layer.closeAll();
							 if(res.code==1){
								 pics.push({"picurl":res.path,"title":"","url":""});	//组图
								 //pics[0] = res.path;	//单图
								 //textObj.val( pics.join(',') );
								 if(typeof callback == 'function'){
									callback(pics,res.url);
								 }
							 }
						}).fail(function () {
							layer.closeAll();
							layer.alert('操作失败，请跟技术联系');
						});	
						
					}
                }
            };
            if(nopress==false)reader.readAsArrayBuffer(file);
        };

        var resizeUpImages = function (img,type) {
            //压缩的大小
            var max_width = 1920; 
            var max_height = 1080; 

            var canvas = document.createElement('canvas');
            var width = img.width;
            var height = img.height;
            if(width > height) {
                if(width > max_width) {
                    height = Math.round(height *= max_width / width);
                    width = max_width;
                }
            }else{
                if(height > max_height) {
                    width = Math.round(width *= max_height / height);
                    height = max_height;
                }
            }

            canvas.width = width;
            canvas.height = height;

            var ctx = canvas.getContext("2d");
            ctx.drawImage(img, 0, 0, width, height);
            if(type=='jpg'||type==''||type==undefined){
				type='jpeg';
			}
            //压缩率
            return canvas.toDataURL("image/"+type,0.72); 
        };

	});
});
</script>
<div class="uploadImge_{$name}">
		<ul class="uploadImg">
			<div style="display:none;">
				<input type="file" accept="image/*" multiple/> 
				<textarea name="{$name}" id="atc_{$name}" class="input_value" rows="5" cols="100">{$info[$name]}</textarea>			
			</div>			 
			<li class="upbtn"><i class="si si-camera"></i><i class="fa fa-crop"></i></li>
		</ul>
		<div class="ListImgs"></div>
</div>

EOT;
;