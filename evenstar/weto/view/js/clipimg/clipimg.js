/*
 * Copyright (C) xiuno.com
 */

// 重复加载
if(!$.xnclip) {
	
// 全局路径设置
$.xnclip = function(clipdivid, min_width, min_height, baseurl) {
	var jclipdiv = $('#'+clipdivid);
	var jbackimg = $('img:first', jclipdiv);
	
	if(jbackimg.width() < min_width) {
		jbackimg.width(min_width);
		jclipdiv.width(min_width);
	}
	if(jbackimg.height() < min_height) {
		jbackimg.height(min_height);
		jclipdiv.height(min_height);
	}
	
	var jbackimg_width = jbackimg.width();
	var jbackimg_height = jbackimg.height();
	
	// 获取定位
	var poffset = jclipdiv.offsetParent().offset();	// 弹出层离页面左侧的位移，position:absolute, 里面的绝对定位的参考元素(parentElement)为它。
	var offset = jclipdiv.position();		//
	var offset_left = offset.left;			// jfrontdiv 距离弹出层内部的位移 
	var offset_top = offset.top;
	
	// 按照最小宽高比例
	var frontdiv_width = Math.max(min_width, jbackimg.width() / 2);	
	var frontdiv_height = (min_height / min_width) * frontdiv_width;
	
	// 创建 overlay
	$('<div class="overlay" style="width: '+jbackimg_width+'px; height: '+jbackimg_height+'px; z-index: 12; left:'+offset_left+'px; top:'+offset_top+'px"></div>').appendTo(jclipdiv);
	
	// 创建 前端剪切层
	var jfrontdiv = $('<div style="width: '+frontdiv_width+'px; height: '+frontdiv_height+'px; overflow: hidden; position: absolute; left: '+offset_left+'px; top: '+offset_top+'px; z-index: 20; border: 1px dashed #f00;" UNSELECTABLE="on"><img src="'+jbackimg.attr('src')+'?'+Math.random()+'" width="'+jbackimg_width+'" height="'+jbackimg_height+'" UNSELECTABLE="on" onmousedown="return false;" style="margin-left: -0px; margin-top: -0px;" /></div>').appendTo(jclipdiv);
	var jfrontimg = $('img:first', jfrontdiv);
	
	// 创建 放大缩小
	var jcorn = $('<img src="'+baseurl+'drag.gif" width="11" height="11" style="position: absolute; z-index: 21; cursor:se-resize" UNSELECTABLE="on" onmousedown="return false;" />').appendTo(jclipdiv);
	var jcorn_width = jcorn.width();
	var jcorn_height = jcorn_width;
	jcorn.css({'left': frontdiv_width - jcorn_width + offset_left, 'top': frontdiv_height - jcorn_width + offset_top});

	function jcorn_mousemove(e) {
		// 改变 jfrontdiv 的宽高，检测边界，改变 jcorn 的位置
		if(jcorn.startdrag) {
			var x = e.pageX - jcorn.mouse_offset_x - poffset.left - offset_left;
			var y = e.pageY - jcorn.mouse_offset_y - poffset.top - offset_top;
		
			jfrontdiv_left = parseInt(jfrontdiv.css('left')) - offset_left;
			jfrontdiv_top = parseInt(jfrontdiv.css('top')) - offset_top;
			
			// 等比缩放，趋小算法
			var w = x + jcorn_width - jfrontdiv_left;
			var h = y + jcorn_height - jfrontdiv_top;
			var w2 = (h / min_height) * min_width;
			var h2 = (w / min_width) * min_height;
			if(w2 / h2 > w / h) {
				y = h2 - jcorn_width + jfrontdiv_top;
			} else {
				x = w2 - jcorn_height + jfrontdiv_left;
			}
			
			//trace('x: '+x+' , y: '+y+',  w2: '+w2+', h2:' + h2);
			
			// 边界检查
			if(x - jfrontdiv_left + jcorn_width < min_width) x = min_width + jfrontdiv_left;
			if(y - jfrontdiv_top + jcorn_width < min_height) y = min_height + jfrontdiv_top;
			maxx = jbackimg_width - jcorn_width;
			maxy = jbackimg_height - jcorn_width;
			if(x > maxx) x = maxx;
			if(y > maxy) y = maxy;
			
			jcorn.css({'left': x + offset_left, 'top': y + offset_top});
			jfrontdiv.width(x + jcorn_width - jfrontdiv_left);
			jfrontdiv.height(y + jcorn_width - jfrontdiv_top);
			return true;
		}
		return true;
	}
	
	function jcorn_mouseup(e) {
		jcorn.startdrag = 0;
		$(document).unbind('mousemove', jcorn_mousemove);
		$(document).unbind('mouseup', jcorn_mouseup);
		frontdiv_width = jfrontdiv.width();
		frontdiv_height = jfrontdiv.height();
		return false;
	}
	
	jcorn.mousedown(function(e) {
		jcorn.startdrag = 1;
		jcorn.mouse_offset_x = $.browser.msie ? e.offsetX : e.layerX;
		jcorn.mouse_offset_y = $.browser.msie ? e.offsetY : e.layerY;
		$(document).bind('mousemove', jcorn_mousemove);
		$(document).bind('mouseup', jcorn_mouseup);
	});
	
	function jfrontdiv_mousemove(e) {
		if(jcorn.startdrag) return;
		if(jfrontdiv.startdrag) {
			var x = e.pageX - jfrontdiv.mouse_offset_x - poffset.left - offset_left;
			var y = e.pageY - jfrontdiv.mouse_offset_y - poffset.top - offset_top;
		
			// 边界检查
			if(x < 0) x = 0;
			if(y < 0) y = 0;
			maxx = jbackimg_width - frontdiv_width;
			maxy = jbackimg_height - frontdiv_height;
			if(x > maxx) x = maxx;
			if(y > maxy) y = maxy;
			
			//trace("x: "+x+" e.pageX: " + e.pageX + ' jfrontdiv.mouse_offset_x' + jfrontdiv.mouse_offset_x + ' poffset.left:' + poffset.left);
			
			// 修改背景偏移 margin-left margin-top;
			jfrontimg.css({'margin-left': -x, 'margin-top': -y});
			jfrontdiv.css({'left': x + offset_left, 'top': y + offset_top});
			jcorn.css({'left': x + frontdiv_width - jcorn_width + offset_left, 'top': y + frontdiv_height - jcorn_width + offset_top});
			return true;
		}
		return true;
	}
	
	function jfrontdiv_mouseup() {
		jfrontdiv.startdrag = 0;
		$(document).unbind('mousemove', jfrontdiv_mousemove);
		$(document).unbind('mouseup', jfrontdiv_mouseup);
	}
	
	jfrontdiv.mousedown(function(e) {
		if(jcorn.startdrag) return;
		jfrontdiv.startdrag = 1;
		// ie：<img> 有 margin-left margin-top 需要参与计算
		if($.browser.msie) {
			jfrontdiv.mouse_offset_x = e.offsetX + parseInt(jfrontimg.css('margin-left'));
			jfrontdiv.mouse_offset_y = e.offsetY + parseInt(jfrontimg.css('margin-top'));
		} else {
			jfrontdiv.mouse_offset_x = e.layerX ;
			jfrontdiv.mouse_offset_y = e.layerY;
		}
		$(document).bind('mousemove', jfrontdiv_mousemove);
		$(document).bind('mouseup', jfrontdiv_mouseup);
	});
	
	this.get = function() {
		var x = parseInt(jfrontdiv.css('left')) - offset_left;
		var y = parseInt(jfrontdiv.css('top')) - offset_top;
		var w = jfrontdiv.width();
		var h = jfrontdiv.height();
		var r = {"x":x, "y":y, "w":w, "h":h};
		return r;
	};
	return this;
}

// xnclip + swfupload
/*
	实例代码：
	<img src="xxx.gif" width="100" height="100" id="img1" />
	<input type="button" class="button smallblue" id="button1" value="上传头像" />
	
	$('#img').clipimg({'buttonid': 'button1', 'uploadurl': '', 'clipurl': '', 'baseurl': ''});
*/
$.clipimg = function(img, settings) {
	// button, uploadurl, clipurl
	var min_width = Math.max(11, $(img).width());		// 宽高比例
	var min_height = Math.max(11, $(img).height());		// 宽高比例
	var jbutton = $('#' + settings.buttonid);
	var uploadurl = settings.uploadurl;
	var clipurl = settings.clipurl;
	var baseurl = settings.baseurl;
	var success_recall = settings.success_recall;
	var swfid;	// flash object id
	var loading;	// 正在上传进度条
	var _this = this;
	
	this.init = function() {
		var swf_settings = {
			flash_url : baseurl+'../swfupload/swfupload.swf',
			upload_url: uploadurl,
			prevent_swf_caching : false,
			preserve_relative_urls : false,
			post_params: settings.postparams,		// swfupload 传参有些环境会失败，直接通过URL传递吧，不要指望通过flash能传递什么参数了，COOKIE不可靠，POST也不可靠，还不如不加这个参数，浪费时间，shit。
			//file_size_limit : '4M',
			file_types : "*.jpg;*.gif;*.png;*.bmp",
			file_types_description : "图片文件",
			file_upload_limit : 100,
			file_queue_limit : 0,
			custom_settings : {
				thumbnail_height: 120000,
				thumbnail_width: 1600,
				thumbnail_quality: 90
			},
			debug: false,
			button_image_url: baseurl+"../swfupload/uploadfile.png",
			button_width: "74",
			button_height: "22",
			button_placeholder_id: settings.buttonid,
			//button_text: '<span class="theFont">上传图片</span>',
			//button_text_style: ".theFont {font-size: 12px;}",
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,	// chrome may be does not work!
			
			file_dialog_complete_handler : function(numFilesSelected, numFilesQueued) {
				this.startUpload();
			},
			upload_start_handler : function(file) {
				$('<span class="loading"><img src="'+baseurl+'loading.gif" width="16" height="16" /></span>').insertAfter($('#SWFUpload_0'));
				return true;
			},
			upload_progress_handler : function(file, bytesLoaded, bytesTotal) {
				var w = Math.ceil((bytesLoaded / bytesTotal) * 26);
				$('span.imageprocess_body', _this.toolbar).width(w);
			},
			upload_error_handler : function(file, errorCode, message) {
				alert('upload_error: file:'+file+', errorcode:'+errorcode+', message:'+message);
			},
			upload_success_handler : function(file, serverData) {
				$('#SWFUpload_0').next('span.loading').hide();
				var json = json_decode(serverData);
				if(error = json_error(json)) {alert(error); return false;}
				if(json.status <= 0) {alert(json.message); return false;}
				var r = json.message;
				
				
				// 弹出层，开始编辑
				var jdialog = $('<div class="dialog bg2 border shadow" title="剪切图片">\
					<div id="'+settings.buttonid+'_clipdiv" style="width: '+r.width+'px; height: '+r.height+'px"><img src="'+r.body+'?'+Math.random()+'" width="'+r.width+'" height="'+r.height+'" /></div>\
					<div style="margin-top: 4px; white-space: nowrap;">\
						<a href="javascript:void(0)" class="button smallblue" id="'+settings.buttonid+'_confirm"><span>确定</span></a>\
						<a href="javascript:void(0)" class="button smallgrey" id="'+settings.buttonid+'_cancel"><span>取消</span></a>\
					</div>\
					</div>').appendTo('body');
				var dialogwidth = r.width + 80;
				jdialog.dialog({width: dialogwidth, modal: true, open: true, closedestory: true});
				var c = $.xnclip(settings.buttonid+'_clipdiv', min_width, min_height, baseurl);
				$('#'+settings.buttonid+'_confirm').click(function() {
					var r = c.get();
					//var s = "x="+r.x+"&y="+r.y+"&w="+r.w+"&h="+r.h;
					jdialog.dialog('close');
					$.post(settings.clipurl, r, function(s) {
						var json = json_decode(s);
						if(error = json_error(json)) {alert(error); return false;}
						if(json.status > 0) {
							if($(img).attr('src')) {
								img.src = json.message + '?' + Math.random();
							} else {
								$(img).css('background-repeat', 'no-repeat');
								$(img).css('background-image', 'url('+json.message + '?' + Math.random() + ')');
							}
							success_recall(json.message, r);
						} else {
							alert(json.message);
						}
					});
				});
				$('#'+settings.buttonid+'_cancel').click(function() {
					jdialog.dialog('close');
				});
				return true;
			},
			file_queue_error_handler : function(file, errorCode, message) {
				if(errorCode == SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT) {
					alert('您选择的文件：'+file+' 尺寸太大！错误信息：'+message);
					$('.toolbar a.imageloading', _this).hide();
					return true;
				} else {
					alert('upload_queue_error: file:'+file+', errorCode:'+errorCode+', message:'+message);
				}
				return false;
			},
			queue_complete_handler : function(numFilesUploaded) {
				//$('a.image', _this.toolbar).width(49);
				//$('a.imageloading', _this.toolbar).hide();
			}
		};
		
		var swfu = new SWFUpload(swf_settings);
		
	}
	return this;
}

// function extended jquery
$.fn.clipimg = function(settings) {
	settings = $.extend({
		buttonid: '',
		uploadurl: '',
		clipurl: '',
		baseurl: '', 
		success: null
	}, settings);
	
	this.each(function() {
		//if(!$.nodeName(this, 'IMG')) return;
		var clipimg = new $.clipimg(this, settings);
		if(clipimg.init()) {
			this.clipimg = clipimg;
		}
	});
	
	return this;
}

}