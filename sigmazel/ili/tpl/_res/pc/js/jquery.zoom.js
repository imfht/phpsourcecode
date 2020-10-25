(function($){
	$.fn.zooming = function(options){
		var defaults = {	
			id: 'zooming',
			parent: 'body',
			append: true,
			preload: '加载中...',
			error: '加载图片错误.',
			zoom:1
		}; 
		
		var self;
		var img = new Image();
		var timeout;
		var w1, w2, h1, h2, rw, rh;
		
		var loaded = false;
		var found = true;
		var over = false;
		
		var options = $.extend(defaults, options); 
		
		function _zoomStart(evt){
			_zoomHide();
			
			var html = $('<div id="'+ options.id +'">'+ options.preload +'</div>');
			
			if(options.append) html.appendTo(options.parent);
			else html.prependTo(options.parent);
			
			if(!found) _zoomError(evt);
			else{
				if(loaded) _zoomShow(evt);
				else _zoomLoop(evt);
			}
		}
		
		function _zoomLoop(evt){
			if(loaded){
				_zoomShow(evt);
				clearTimeout(timeout);
			}else{
				timeout = setTimeout(function(){
					_zoomLoop(evt);
				}, 200);
			}
		}
		
		function _zoomShow(evt){
			over = true;
			
			$(img).css({position:'absolute', top:'0', left:'0'});
			$('#'+ options.id).html('').append(img);
			
			w1 = $('img', self).width();
			h1 = $('img', self).height();
			w2 = $('#'+ options.id).width();
			h2 = $('#'+ options.id).height();
			
			$(img).width($('img', self).width() * options.zoom);
			$(img).height($('img', self).height() * options.zoom);
			
			w3 = $(img).width();
			h3 = $(img).height();
			w4 = $(img).width() - w2;
			h4 = $(img).height() - h2;
			
			rw = w4/w1;
			rh = h4/h1;
			
			_zoomMove(evt);
		}
		
		function _zoomHide(evt){
			over = false;
			$('#'+ options.id).remove();
		}
		
		function _zoomError(evt){
			$('#'+ options.id).html(options.error);
		}
		
		function _zoomMove(evt){
			if(!over) return;
			
			var p = $('img', self).offset();
			var pl = evt.pageX - p.left;
			var pt = evt.pageY - p.top;	
			var xl = pl*rw;
			var xt = pt*rh;
			xl = (xl > w4) ? w4 : xl;
			xt = (xt > h4) ? h4 : xt;	
			$('#'+ options.id + ' img').css({'left':xl * (-1),'top': xt * (-1)});
		}
		
		this.each(function(){ 
			self = this;
			
			var href = $(self).attr('href');
			img.src = href;
			
			$(img).error(function(){
				found = false;
			});
			
			img.onload = function(){
				loaded = true;	
				img.onload = function(){};
			}
			
			if($.browser.msie && 7 <= $.browser.version) loaded = true;
			
			$(this).css('cursor', 'crosshair').click(function(evt){
				evt.preventDefault();
			}).mouseover(function(evt){
				_zoomStart(evt);
			}).mouseout(function(evt){
				_zoomHide(evt);
			}).mousemove(function(evt){
				_zoomMove(evt);
			});
		});
	}
})(jQuery);