/*
* Copyright (C) xiuno.com
*/
 
/*
	基于 jquery 1.4 对话框	
	用法：
		<div class="dialog bg2 border shadow" title="对话框标题" id="dialog1" style="display: none;">文字HTML等内容...</div>
		<script>
		$('#id1').dialog();
		$('#id1').dialog('open');
		$('#id1').dialog('close');
		
		$('#id1').dialog({ modal: true });
		$('#id1').dialog({ position: 'top' }); 
			'center'
			['left', 'center']	// ['left|center|right', 'top|center|bottom']
			[350, 100]
		$('#id1').dialog({ title: 'Dialog Title' }); 
		$('#id1').dialog({ width: 460}); 
		</script>
		
*/

// 兼容 jquery 自带的 dialog
if(!$.fn.dialog) {
		
	$.fn.dialog = function(settings) {
		if(!settings) {
			settings = {};
		} else if(settings == 'open') {
			settings = {open: true};
		} else if(settings == 'close') {
			settings = {open: false};
		}
		
		// 此处 this 为 jquery 集合
		this.each(function() {
			// 此处 this 为 <div> 元素
			if(this.dialog) {
				var oldbody = settings ? settings.body : '';
				settings = $.extend(this.dialog.settings, settings);
				if(oldbody == '') settings.body = '';
				this.dialog.set(settings);
			} else {
				settings = $.extend({
					width: 300,
					height: 'auto',
					modal: false,
					open: true,
					closedestory: false,
					drag: true,
					position: 'center',
					fullicon: false,
					fullscreen: false,
					timeout: 0,
					showtitle: true,
					title: '',
					body: '',
					zIndex: 100
				}, settings);
				
			 	this.dialog = new $.dialog(this, settings);
			 	//$(this).dialog = this.dialog;
			 	this.dialog.init();
			}
		});
		return this;
	};
	
	// class dialog
	$.dialog = function(div, settings) {
		var _this = this;	// this pointer
		var _div = div;
		this.settings = settings;
		
		//var _register_remove_object = [];
		
		// 创建对话框, 初始化对话框, 但是不显示
		this.init = function() {
			
			// 构造对话框
			if($('#overlay').length == 0) $('<div class="overlay" id="overlay" ref="0" ></div>').hide().appendTo('body').height($(document).height()).keydown(function(e) {e.stopPropagation();}).keyup(function(e) {e.stopPropagation();});
			
			var jdiv = $(div);
			var divcontent = '<div class="header bg5"'+(settings.showtitle ? '' : ' style="display: none;"')+'><a href="javascript: void(0)" class="icon icon-close" style="float:right; margin-right: 4px; margin-top: 2px;" title="关闭"></a><a href="javascript: void(0)" class="icon icon-max" style="float:right; margin-right: 4px; margin-top: 2px; display: none;" title="最大/小化"></a><span>' + div.title + '</span></div>' + '<div class="body">' + jdiv.html() + '</div>';
			jdiv.html(divcontent);
			
			// 按照参数进行设置
			this.set(_this.settings);
			
			// 层拖动效果 -------------> start
			var title = $($('div.header', _div).get(0));
			title.css('cursor', 'move');
			function title_mousemove(e) {
				if(title.startdrag) {
					var x = e.pageX - title.mouse_offset_x;
					var y = e.pageY - title.mouse_offset_y;
					$(_div).css({ left: x, top: y});
				}
			}
			function title_mouseup() {
				$(document).unbind('mousemove', title_mousemove);	// 比较耗费资源，用完 unbind 掉。
				$(document).unbind('mouseup', title_mouseup);		// 比较耗费资源，用完 unbind 掉。
				title.startdrag = 0;
				
				document.unselectable = 'off';
				document.body.onselectstart = function() {return true;};
			}
			title.mousedown(function(e) {
				if(_this.settings.drag) {
					title.startdrag = 1;
					document.unselectable = 'on';
					document.body.onselectstart = function() {return false;};
				} else {
					title.startdrag = 0;
					return false;
				}
				title.mouse_offset_x = e.pageX - $(_div).attr('offsetLeft');
				title.mouse_offset_y = e.pageY - $(_div).attr('offsetTop');
				
				// 保存 <body> style overflow 属性，设置为 overflow: hidden;
				//$('body').css('overflow', 'hidden');
				$(document).bind('mousemove', title_mousemove);
				$(document).bind('mouseup', title_mouseup);
			});
			
			// ----------------> end
			
			// 追加关闭按钮事件
			$('div.header:first a.icon-close', jdiv).click(function() {
				_this.close();
			});
			
			// 追加关闭按钮事件
			$('div.header:first a.icon-max', jdiv).click(function() {
				// 保存当前状态
				_this.set_fullscreen($(this).hasClass('icon-max'));
				$(this).toggleClass('icon-max');
				$(this).toggleClass('icon-min');
			});
			
			// 点击层时，调整当前层的z-index。
			jdiv.mousedown(function() {
				// 查找所有层中的最大值，最大值 +1
				_this.set_top(this);
				return true;
			});
			return true;	
		};
		
		this.set = function(settings) {
			_this.settings = $.extend(_this.settings, settings);
			if(settings.width)  this.set_width(settings.width);
			if(settings.height)  this.set_height(settings.height);
			if(settings.title)  this.set_title(settings.title);
			//alert('set body before');
			if(settings.body)  this.set_body(settings.body);
			//alert('set body after');
			if(isset(settings.modal))  _this.settings.modal = settings.modal;
			if(settings.open)  this.open();
			if(!settings.open)  this.close();
			if(settings.position)  this.set_position(settings.position);
			if(settings.fullicon)  this.show_fullicon();
			if(settings.timeout)  this.set_timeout();
		};
		
		// 打开对话框
		this.open = function() {
			// 清除上面的定时器
			if(_div.htime) {
				clearTimeout(_div.htime);
				_div.htime = null;
			}
			
			// 已经打开
			if($(_div).css('display') != 'none') {
				return;
			}
			if(_this.settings.modal) {
				var layref = parseInt($('#overlay').attr('ref')) + 1;
				$('#overlay').width('100%').show().attr('ref', layref);
			} else {
				//$('#overlay').width(0).hide();
			}
			
			$(_div).show();
			//$(_div).fadeIn('middle');
			_this.set_top(_div);
		};
		
		// destory 是否销毁对话框，还是隐藏
		this.close = function(destory) {
			if(_this.settings.modal) {
				var layref = parseInt($('#overlay').attr('ref'));
				$('#overlay').attr('ref', --layref);
				if(layref < 1) $('#overlay').width(0).hide();
			}
			if(destory || settings.closedestory) {
				_div.dialog = null;
				// 删除缓存 ?
				$(_div).remove();
			} else {
				$(_div).fadeOut('slow');
				//$(_div).hide();
			}
		};
		
		/*
			X: 为点击对象， 1 - 9 表示它的周围的位置，再加一个 center, 默认屏幕居中。
			
			1	2	3
			4	XXX	6
			7	8	9
			
		*/
		this.set_position = function(position) {
			
			// <a> 标签所在位置，决定了弹出层的位置，如果 options.position != 'center'
			if(position == 1) {
				var jcaller = $(_this.settings.xcaller);
				var offset = jcaller.offset();
				var v_left = offset.left - $(_div).width();
				var v_top = offset.top - $(_div).height();
				v_top = Math.max(0, v_top);
				$(_div).css({ top: v_top, left: v_left });
			}
			if(position == 6) {
				var jcaller = $(_this.settings.xcaller);
				var offset = jcaller.offset();
				if($(window).width() - offset.left < $(_div).width()) {
					position = 3;
				}
			}
			if(position == 3) {
				var jcaller = $(_this.settings.xcaller);
				var offset = jcaller.offset();
				var v_left = offset.left - $(_div).width() + 4;
				var v_top = offset.top;
				$(_div).css({ top: v_top, left: v_left });
			} else if(position == 6) {
				var jcaller = $(_this.settings.xcaller);
				var offset = jcaller.offset();
				var v_left = offset.left + jcaller.width() + 4;
				var v_top = offset.top;
				$(_div).css({ top: v_top, left: v_left });
			// 叠加的位置
			} else if(position == 5) {
				var jcaller = $(_this.settings.xcaller);
				var offset = jcaller.offset();
				var v_left = offset.left;// - jcaller.width()
				var v_top = offset.top;
				$(_div).css({ top: v_top, left: v_left });
			// 居中 'center'
			} else if(position == 'center'){
				var divheight = $(_div).height();
				var winheight = $(window).height();
				var v_top = ($(window).height() / 2 - divheight / 2) + $(document).scrollTop();
				var v_left = ($(window).width() / 2 - $(_div).width() / 2);
				if(v_top < 0) v_top = 10;
				if(v_top > 100 && winheight - divheight > 120) v_top -= 100;
				$(_div).css({ top: v_top, left: v_left });
			} else if(typeof position == 'object') {
				$(_div).css({ top: position.top, left: position.left });
			}
			_this.settings.position = position;
		};
		
		this.set_width = function(width) {
			$(_div).width(width);
			var jcontent = $('div.body', _div);
			var subpadding = parseInt(jcontent.css('padding-left')) + parseInt(jcontent.css('padding-right')) + parseInt(jcontent.css('margin-left')) + parseInt(jcontent.css('margin-right'));
			jcontent.width(width - subpadding); // $(_div).width() 转换为绝对宽度，ie6 会有问题。ie6取出来的宽度为未缩小的宽度，也就是实际撑开的宽度。需要设置 overflow: ?
		};
		
		this.set_height = function(height) {
			$(_div).height(height);
		};
		
		this.set_title = function(title) {
			$('div.header:first span', $(_div)).html(title);
		};
		
		this.set_body = function(s) {
			try {
				$('div.body', _div).html(s);
			} catch(e) {
				alert('dialog.set_body() error: ' + e.message + "\nbody:" + s);
			}
		};
		
		// 设置 div 为顶层的 div
		this.set_top = function(_div) {
			var maxzindex = 1;
			$('div.dialog').each(function() {
				if($(this).not(_div).css('z-index') >= maxzindex) maxzindex = $(this).css('z-index') + 1;
			});
			if(maxzindex > $(_div).css('z-index')) {
				$(_div).css('z-index', maxzindex);
			}
		};
		
		this.show_fullicon = function() {
			$('div.header a.icon-max', _div).show();
		};
		
		this.set_fullscreen = function(fullscreen) {
			if(fullscreen) {
				document.body.style.overflow = 'hidden';
				_this.set_width($(window).width() - 8);
				_this.set_height($(window).height() - 8);
				if(_this.settings.onmax) {
					_this.settings.onmax();
				}
			} else {
				document.body.style.overflow = 'auto';
				_this.set_width(_this.settings.width);
				_this.set_height(_this.settings.height);
				if(_this.settings.onmax) {
					_this.settings.onmin();
				}
			}
			_this.set_position();
		};
		
		this.set_timeout = function() {
			if(_this.settings.timeout) {
				$(_div).hover(function() {
					if(_div.htime) {
						clearTimeout(_div.htime);
						_div.htime = null;
					}
					return true;
				}, function() {
					if(!_div.htime) {
						_div.htime = setTimeout(function() {
							_this.close();
							_div.htime = null;
						}, _this.settings.timeout);
					}
					return true;
				});
			}
		};
		
		// 关闭其他 dialog
		this.close_other = function() {
			$('div.dialog').not(_div).dialog('close');
		};
		
		// ESC 关闭
		function document_key_esc(e) {
			//e = e || document.parentWindow.event;
			var e = e ? e : window.event;
	                var kc = e.keyCode ? e.keyCode : e.charCode;
			if(kc == 27) {
				_this.close();
				$(document).unbind('keyup', document_key_esc);
			}
			return true;
		}
		
		$(document).bind('keyup', document_key_esc);
		
		return this;
	};
}
