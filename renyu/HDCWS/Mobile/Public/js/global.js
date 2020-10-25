$.fn.slideMove = function(opts){

	var start_x, start_y, end_x, end_y;

	return $(this).each(function(){

		var $this = $(this);
	
		$this.on('touchstart', function(e){

			if(opts.startStop) e.preventDefault();

			end_x = undefined;

			end_y = undefined;

			start_x = e.originalEvent.targetTouches[0].clientX;

			start_y = e.originalEvent.targetTouches[0].clientY;

			if(typeof opts.startCall == 'function') opts.startCall(start_x, start_y);
	 
		});

		$this.on('touchmove', function(e){

			if(opts.moveStop) e.preventDefault(); //阻止触摸时浏览器的缩放、滚动条滚动等  

			end_x = e.originalEvent.targetTouches[0].clientX;

			end_y = e.originalEvent.targetTouches[0].clientY;

			if(typeof opts.moveCall == 'function') opts.moveCall(start_x, start_y, end_x, end_y);

		});

		$this.on('touchend', function(e){

			if(opts.endStop) e.preventDefault();

			if(typeof opts.endCall == 'function') opts.endCall(start_x, start_y, end_x, end_y);

		});

		$this.on('touchcancel', function(e){

			if(opts.cancelStop) e.preventDefault();

			if(typeof opts.cancelCall == 'function') opts.cancelCall(start_x, start_y, end_x, end_y);

		});
	
	});

};

$.fn.photoSlide = function(){

	return $(this).each(function(){

		$(this).data('relNum', Math.random()).click(function(){

			var $this = $(this),

				$list = $('div.words img'),
				
				$mask = $('<div class="photo-mask"></div>').appendTo('body'),
				
				$photoBox = $('<div class="photo-box"><div class="photo-list"></div></div>').appendTo('body'),

				$photoList = $photoBox.find('div.photo-list'),

				clientWidth = parseFloat($(window).width()),

				clientHeight = parseFloat($(window).height()),

				$photoStr = '', relNum = $this.data('relNum'),
				
				num = 0, len = $list.length - 1;

			$list.each(function(i, item){

				var $this = $(this),
					
					src = $this.attr('src'),

					img = $this.get(0),
						
					bestSize = getBestSize(img.naturalWidth, img.naturalHeight, clientWidth, clientHeight);

				if($this.data('relNum') == relNum) num = i;

				$photoStr += '<div class="photo-wall" style="width:' + clientWidth + 'px;"><img style="left:' + bestSize.left + 'px;top:' + bestSize.top + 'px;" width="' + bestSize.w + '" height="' + bestSize.h + '" src="' + src + '" /></div>';

				function getBestSize(imgW, imgH, maxW, maxH){
				
					var imgPer = imgW / imgH, maxPer = maxW / maxH, o;

					if(imgPer > maxPer){

						if(imgW >= maxW){

							o = {w : maxW, h : maxW / imgPer};
					
							o.left = 0;
							
							o.top = (maxH - o.h) / 2;

						}else{
							
							o = {w : imgW, h : imgW / imgPer};

							o.left = (maxW - imgW) / 2;

							o.top = (maxH - o.h) / 2;

						}

					}else{

						if(imgH >= maxH){
							
							o = {w : maxH * imgPer, h : maxH};

							o.left = (maxW - o.w) / 2;
							
							o.top = 0;
					
						}else{
							
							o = {w : imgH * imgPer, h : imgH};

							o.left = (maxW - o.w) / 2;
							
							o.top = (maxH - imgH) / 2;
					
						}

					}

					return o;
				
				}

			});

			$photoList.append($photoStr);

			$photoList.css('left', '-' + (num * clientWidth) + 'px');

			$photoList.find('div.photo-wall').each(function(){

				var $this = $(this);

				$this.slideMove({

					startStop : true,

					moveStop : true,

					endStop : true,

					moveCall : function(start_x, start_y, end_x, end_y){

						var levelNum = end_x - start_x,
							
							left = 0 - num * clientWidth;

						$photoList.css('left', left + (levelNum * 0.5) + 'px');
					
					},

					endCall : function(start_x, start_y, end_x, end_y){

						if(typeof end_x == 'undefined' || end_x == start_x){

							destroy();
							
							return false;

						}

						var levelNum = end_x - start_x,
								
							halfWidth = clientWidth / 3.5,
								
							result;

						//向右滑
						if(levelNum < (0 - halfWidth)){

							num += 1;

							if(len <= num) num = len;

						}
							
						//向左滑
						else if(levelNum > halfWidth){

							num -= 1;

							if(num <= 0) num = 0;

						}

						result = '-' + (num * clientWidth);

						$photoList.animate({left : result + 'px'});

					}
				
				});

				function destroy(){

					$photoBox.fadeOut('fast', function(){
					
						$(this).remove();

						$mask.fadeOut('fast', function(){
						
							$(this).remove();
						
						});
					
					});
				
				}

			});

			$mask.fadeTo('fast', 0.8, function(){
			
				$photoBox.fadeTo('fast', 1);
			
			});
		
		});
	
	});

};