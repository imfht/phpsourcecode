/*
* Copyright (C) xiuno.com
*/
 
/*
	拖拽调整元素顺序
	
	用法：
		第一个参数为 jquery 选择器表达式
		第二个参数为拖拽成功以后的回调函数
		
		$.drag('span.drag_item', function(curr){
			var jprev = $(curr).prev();
			var jnext = $(curr).next();
		});
*/

$.fn.drag = function(recall) {
	this.each(function() {
		$.drag($(this).children(), recall);
	});
	return this;
};

$.drag = function(content, recall) {
	var jcontent = $(content);
	var jparent = jcontent.parent();	// 不能超出父容器的x, y , w, h
	if(!window.jcorn) window.jcorn = $('<span class="icon icon-drag" style="position: absolute; display: none; z-index: 12;"></span>').appendTo('body');
	var jcorn = window.jcorn;
	// 获取宽度高度
	jcontent.each(function() {
		this._w = $(this).width();
		this._h =  $(this).height();
	});
	
	jcontent.each(function() {
		(function(curr) {
			var jcurr = $(curr);
			
			// 闭包，鼠标连续移动，可能触发多个 实例运行，保证一个时刻只能有一个实例运行！ 此处速度需要优化
			var document_mousemove = function (e) {
				if(window.mousemoving) return;
				window.mousemoving = 1;
				
				var x = e.pageX - curr.mouse_offset_x + 8 - curr._w;
				var y = e.pageY - curr.mouse_offset_y + 8;
				var x2 = e.pageX - curr.mouse_offset_x;
				var y2 = e.pageY - curr.mouse_offset_y;
				
				// 设置节点为绝对定位
				curr.style.position = 'absolute';
				jcurr.css({left: x, top: y, 'z-index': 11, width:curr._w, height:curr._h});
				jcorn.css({left: x2, top: y2});
				
				// 在目标对象上插入虚线框！
				// 遍历每个元素，判断当前正在移动的对象中心点是否在其上！
				jcontent.each(function() {
					if(this == curr) return;
					var pos = $(this).position();
					var _x = pos.left;
					var _y = pos.top;
					var w = curr._w;
					var h = curr._h;
					
					var center_x = x + w / 2;
					var center_y = y + h / 2;
					if(center_x > _x && center_x < _x + w && center_y > _y  && center_y < _y + h) {
						
						// 隐藏上一个 shadow
						//trace('curr: '+$('input', this).val()+', x:'+x+', y:'+y+', _x:'+_x+', _y:'+_y+', w:'+w+', h:'+h+'');
						// 此处速度需要优化
						//if(!window.jshadow) {
							if(window.jshadow) window.jshadow.remove();
							window.jshadow = jcurr.clone().addClass('red_border1').css('position', 'relative').css('left', 0).css('top', 0).show();
							$(this).before(window.jshadow);
						//}
					}
				});
				
				window.mousemoving = 0;
			};
			
			var document_mouseup = function(e) {
				window.draging = null;
				document.unselectable = 'off';
				document.body.onselectstart = function() {return true;};
				
				if(window.jshadow) {
					jcurr.css('position', 'relative').css('left', 0).css('top', 0).css('z-index', 1).removeClass('red_border1');
					window.jshadow.before(jcurr);
					window.jshadow.remove();
					
					// 业务逻辑回调
					if(recall) recall(curr);
				}
				
				$(document).unbind('mousemove');
				$(document).unbind('mouseup');
			};
			
			var jcorn_mousedown = function (e) {
				window.draging = jcurr;
				document.unselectable = 'on';
				document.body.onselectstart = function() {return false;};
				
				jcurr.addClass('red_border1');
				
				curr.mouse_offset_x = e.pageX - jcorn.attr('offsetLeft'); // 鼠标在 corn 上的偏移量
				curr.mouse_offset_y = e.pageY - jcorn.attr('offsetTop'); // 鼠标在 corn 上的偏移量
				
				$(document).unbind('mousemove').bind('mousemove', document_mousemove);
				$(document).unbind('mouseup').bind('mouseup', document_mouseup);
			};
			
			jcurr.mouseover(function() {
				// corn 重新定位，重新绑定事件
				var pos = jcurr.position();
				jcorn.css({'left':pos.left + curr._w - 8, top:pos.top - 8}).show();
				jcurr.addClass('red_border1');
				jcorn.unbind('mousedown').bind('mousedown', jcorn_mousedown);
				return false;
			});
			
			// 移出虚线框
			jcurr.mouseout(function() {
				jcurr.removeClass('red_border1');
				//jcorn.hide();
			});
			
		})(this);
	});
}