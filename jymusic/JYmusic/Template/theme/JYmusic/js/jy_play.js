/*旋转插件*/
(function(k){for(var d,f,l=document.getElementsByTagName("head")[0].style,h=["transformProperty","WebkitTransform","OTransform","msTransform","MozTransform"],g=0;g<h.length;g++)void 0!==l[h[g]]&&(d=h[g]);d&&(f=d.replace(/[tT]ransform/,"TransformOrigin"),"T"==f[0]&&(f[0]="t"));eval('IE = "v"=="\v"');jQuery.fn.extend({rotate:function(a){if(0!==this.length&&"undefined"!=typeof a){"number"==typeof a&&(a={angle:a});for(var b=[],c=0,d=this.length;c<d;c++){var e=this.get(c);if(e.Wilq32&&e.Wilq32.PhotoEffect)e.Wilq32.PhotoEffect._handleRotation(a);
else{var f=k.extend(!0,{},a),e=(new Wilq32.PhotoEffect(e,f))._rootObj;b.push(k(e))}}return b}},getRotateAngle:function(){for(var a=[],b=0,c=this.length;b<c;b++){var d=this.get(b);d.Wilq32&&d.Wilq32.PhotoEffect&&(a[b]=d.Wilq32.PhotoEffect._angle)}return a},stopRotate:function(){for(var a=0,b=this.length;a<b;a++){var c=this.get(a);c.Wilq32&&c.Wilq32.PhotoEffect&&clearTimeout(c.Wilq32.PhotoEffect._timer)}}});Wilq32=window.Wilq32||{};Wilq32.PhotoEffect=function(){return d?function(a,b){a.Wilq32={PhotoEffect:this};
this._img=this._rootObj=this._eventObj=a;this._handleRotation(b)}:function(a,b){this._img=a;this._onLoadDelegate=[b];this._rootObj=document.createElement("span");this._rootObj.style.display="inline-block";this._rootObj.Wilq32={PhotoEffect:this};a.parentNode.insertBefore(this._rootObj,a);if(a.complete)this._Loader();else{var c=this;jQuery(this._img).bind("load",function(){c._Loader()})}}}();Wilq32.PhotoEffect.prototype={_setupParameters:function(a){this._parameters=this._parameters||{};"number"!==
typeof this._angle&&(this._angle=0);"number"===typeof a.angle&&(this._angle=a.angle);this._parameters.animateTo="number"===typeof a.animateTo?a.animateTo:this._angle;this._parameters.step=a.step||this._parameters.step||null;this._parameters.easing=a.easing||this._parameters.easing||this._defaultEasing;this._parameters.duration=a.duration||this._parameters.duration||1E3;this._parameters.callback=a.callback||this._parameters.callback||this._emptyFunction;this._parameters.center=a.center||this._parameters.center||
["50%","50%"];this._rotationCenterX="string"==typeof this._parameters.center[0]?parseInt(this._parameters.center[0],10)/100*this._imgWidth*this._aspectW:this._parameters.center[0];this._rotationCenterY="string"==typeof this._parameters.center[1]?parseInt(this._parameters.center[1],10)/100*this._imgHeight*this._aspectH:this._parameters.center[1];a.bind&&a.bind!=this._parameters.bind&&this._BindEvents(a.bind)},_emptyFunction:function(){},_defaultEasing:function(a,b,c,d,e){return-d*((b=b/e-1)*b*b*b-
1)+c},_handleRotation:function(a,b){d||this._img.complete||b?(this._setupParameters(a),this._angle==this._parameters.animateTo?this._rotate(this._angle):this._animateStart()):this._onLoadDelegate.push(a)},_BindEvents:function(a){if(a&&this._eventObj){if(this._parameters.bind){var b=this._parameters.bind,c;for(c in b)b.hasOwnProperty(c)&&jQuery(this._eventObj).unbind(c,b[c])}this._parameters.bind=a;for(c in a)a.hasOwnProperty(c)&&jQuery(this._eventObj).bind(c,a[c])}},_Loader:function(){return IE?function(){var a=
this._img.width,b=this._img.height;this._imgWidth=a;this._imgHeight=b;this._img.parentNode.removeChild(this._img);this._vimage=this.createVMLNode("image");this._vimage.src=this._img.src;this._vimage.style.height=b+"px";this._vimage.style.width=a+"px";this._vimage.style.position="absolute";this._vimage.style.top="0px";this._vimage.style.left="0px";this._aspectW=this._aspectH=1;this._container=this.createVMLNode("group");this._container.style.width=a;this._container.style.height=b;this._container.style.position=
"absolute";this._container.style.top="0px";this._container.style.left="0px";this._container.setAttribute("coordsize",a-1+","+(b-1));this._container.appendChild(this._vimage);this._rootObj.appendChild(this._container);this._rootObj.style.position="relative";this._rootObj.style.width=a+"px";this._rootObj.style.height=b+"px";this._rootObj.setAttribute("id",this._img.getAttribute("id"));this._rootObj.className=this._img.className;for(this._eventObj=this._rootObj;a=this._onLoadDelegate.shift();)this._handleRotation(a,
!0)}:function(){this._rootObj.setAttribute("id",this._img.getAttribute("id"));this._rootObj.className=this._img.className;this._imgWidth=this._img.naturalWidth;this._imgHeight=this._img.naturalHeight;var a=Math.sqrt(this._imgHeight*this._imgHeight+this._imgWidth*this._imgWidth);this._width=3*a;this._height=3*a;this._aspectW=this._img.offsetWidth/this._img.naturalWidth;this._aspectH=this._img.offsetHeight/this._img.naturalHeight;this._img.parentNode.removeChild(this._img);this._canvas=document.createElement("canvas");
this._canvas.setAttribute("width",this._width);this._canvas.style.position="relative";this._canvas.style.left=-this._img.height*this._aspectW+"px";this._canvas.style.top=-this._img.width*this._aspectH+"px";this._canvas.Wilq32=this._rootObj.Wilq32;this._rootObj.appendChild(this._canvas);this._rootObj.style.width=this._img.width*this._aspectW+"px";this._rootObj.style.height=this._img.height*this._aspectH+"px";this._eventObj=this._canvas;for(this._cnv=this._canvas.getContext("2d");a=this._onLoadDelegate.shift();)this._handleRotation(a,
!0)}}(),_animateStart:function(){this._timer&&clearTimeout(this._timer);this._animateStartTime=+new Date;this._animateStartAngle=this._angle;this._animate()},_animate:function(){var a=+new Date,b=a-this._animateStartTime>this._parameters.duration;if(b&&!this._parameters.animatedGif)clearTimeout(this._timer);else{if(this._canvas||this._vimage||this._img)a=this._parameters.easing(0,a-this._animateStartTime,this._animateStartAngle,this._parameters.animateTo-this._animateStartAngle,this._parameters.duration),
this._rotate(~~(10*a)/10);this._parameters.step&&this._parameters.step(this._angle);var c=this;this._timer=setTimeout(function(){c._animate.call(c)},10)}this._parameters.callback&&b&&(this._angle=this._parameters.animateTo,this._rotate(this._angle),this._parameters.callback.call(this._rootObj))},_rotate:function(){var a=Math.PI/180;return IE?function(a){this._angle=a;this._container.style.rotation=a%360+"deg";this._vimage.style.top=-(this._rotationCenterY-this._imgHeight/2)+"px";this._vimage.style.left=
-(this._rotationCenterX-this._imgWidth/2)+"px";this._container.style.top=this._rotationCenterY-this._imgHeight/2+"px";this._container.style.left=this._rotationCenterX-this._imgWidth/2+"px"}:d?function(a){this._angle=a;this._img.style[d]="rotate("+a%360+"deg)";this._img.style[f]=this._parameters.center.join(" ")}:function(b){this._angle=b;b=b%360*a;this._canvas.width=this._width;this._canvas.height=this._height;this._cnv.translate(this._imgWidth*this._aspectW,this._imgHeight*this._aspectH);this._cnv.translate(this._rotationCenterX,
this._rotationCenterY);this._cnv.rotate(b);this._cnv.translate(-this._rotationCenterX,-this._rotationCenterY);this._cnv.scale(this._aspectW,this._aspectH);this._cnv.drawImage(this._img,0,0)}}()};IE&&(Wilq32.PhotoEffect.prototype.createVMLNode=function(){document.createStyleSheet().addRule(".rvml","behavior:url(#default#VML)");try{return!document.namespaces.rvml&&document.namespaces.add("rvml","urn:schemas-microsoft-com:vml"),function(a){return document.createElement("<rvml:"+a+' class="rvml">')}}catch(a){return function(a){return document.createElement("<"+
a+' xmlns="urn:schemas-microsoft.com:vml" class="rvml">')}}}())})(jQuery);

(function($){
	$.lrc = {
		handle: null, /* 定时执行句柄 */
		list: [], /* lrc歌词及时间轴数组 */
		regex: /^[^\[]*((?:\s*\[\d+\:\d+(?:\.\d+)?\])+)([\s\S]*)$/, /* 提取歌词内容行 */
		regex_time: /\[(\d+)\:((?:\d+)(?:\.\d+)?)\]/g, /* 提取歌词时间轴 */
		regex_trim: /^\s+|\s+$/, /* 过滤两边空格 */
		callback: null, /* 定时获取歌曲执行时间回调函数 */
		interval: 0.3, /* 定时刷新时间，单位：秒 */
		format: '<li>{html}</li>', /* 模板 */
		prefixid: 'lrc', /* 容器ID */
		hoverClass: 'hover', /* 选中节点的className */
		hoverTop: 100, /* 当前歌词距离父节点的高度 */
		duration: 0, /* 歌曲回调函数设置的进度时间 */
		__duration: -1, /* 当前歌曲进度时间 */
		/* 歌词开始自动匹配 */
		start: function(txt, callback) {
			if(typeof(txt) != 'string' || txt.length < 1 || typeof(callback) != 'function') return;
			/* 停止前面执行的歌曲 */
			this.stop();
			this.callback = callback;
			var item = null, item_time = null, html = '';
			/* 分析歌词的时间轴和内容 */
			txt = txt.split("\n");
			for(var i = 0; i < txt.length; i++) {
				item = txt[i].replace(this.regex_trim, '');
				if(item.length < 1 || !(item = this.regex.exec(item))) continue;
				while(item_time = this.regex_time.exec(item[1])) {
					this.list.push([parseFloat(item_time[1])*60+parseFloat(item_time[2]), item[2]]);
				}
				this.regex_time.lastIndex = 0;
			}
 
			/* 有效歌词 */
			if(this.list.length > 0) {
				/* 对时间轴排序 */
				this.list.sort(function(a,b){ return a[0]-b[0]; });
				if(this.list[0][0] >= 0.1) this.list.unshift([this.list[0][0]-0.1, '']);
				this.list.push([this.list[this.list.length-1][0]+1, '']);
				for(var i = 0; i < this.list.length; i++)
					html += this.format.replace(/\{html\}/gi, this.list[i][1]);
 
				/* 赋值到指定容器 */
				$('#'+this.prefixid+'_list').html(html).animate({ marginTop: 0 }, 100).show();
				/* 隐藏没有歌词的层 */
				$('#'+this.prefixid+'_nofound').hide();
				/* 定时调用回调函数，监听歌曲进度 */
				this.handle = setInterval('$.lrc.jump($.lrc.callback());', this.interval*1000);
			}else{ /* 没有歌词 */
				$('#'+this.prefixid+'_list').hide();
				$('#'+this.prefixid+'_nofound').show();
			}
		},
		/* 跳到指定时间的歌词 */
		jump: function(duration) {
			if(typeof(this.handle) != 'number' || typeof(duration) != 'number' || !$.isArray(this.list) || this.list.length < 1) return this.stop();
 
			if(duration < 0) duration = 0;
			if(this.__duration == duration) return;
			duration += 0.2;
			this.__duration = duration;
			duration += this.interval;
 
			var left = 0, right = this.list.length-1, last = right
				pivot = Math.floor(right/2),
				tmpobj = null, tmp = 0, thisobj = this;
 
			/* 二分查找 */
			while(left <= pivot && pivot <= right) {
				if(this.list[pivot][0] <= duration && (pivot == right || duration < this.list[pivot+1][0])) {
					//if(pivot == right) this.stop();
					break;
				}else if( this.list[pivot][0] > duration ) { /* left */
					right = pivot;
				}else{ /* right */
					left = pivot;
				}
				tmp = left + Math.floor((right - left)/2);
				if(tmp == pivot) break;
				pivot = tmp;
			}
 
			if(pivot == this.pivot) return;
			this.pivot = pivot;
			tmpobj = $('#'+this.prefixid+'_list').children().removeClass(this.hoverClass).eq(pivot).addClass(thisobj.hoverClass);
			tmp = tmpobj.next().offset().top-tmpobj.parent().offset().top - this.hoverTop;
			tmp = tmp > 0 ? tmp * -1 : 0;
			this.animata(tmpobj.parent()[0]).animate({marginTop: tmp + 'px'}, this.interval*1000);
		},
		/* 停止执行歌曲 */
		stop: function() {
			if(typeof(this.handle) == 'number') clearInterval(this.handle);
			this.handle = this.callback = null;
			this.__duration = -1;
			this.regex_time.lastIndex = 0;
			this.list = [];
		},
		animata: function(elem) {
			var f = j = 0, callback, _this={},
				tween = function(t,b,c,d){ return -c*(t/=d)*(t-2) + b; }
			_this.execution = function(key, val, t) {
				var s = (new Date()).getTime(), d = t || 500,
				    b = parseInt(elem.style[key]) || 0,
				    c = val-b;
				(function(){
					var t = (new Date()).getTime() - s;
					if(t>d){
						t=d;
						elem.style[key] = tween(t,b,c,d) + 'px';
						++f == j && callback && callback.apply(elem);
						return true;
					}
					elem.style[key] = tween(t,b,c,d)+'px';
					setTimeout(arguments.callee, 10);
				})();
			}
			_this.animate = function(sty, t, fn){
				callback = fn;
				for(var i in sty){
					j++;
					_this.execution(i,parseInt(sty[i]),t);
				}
			}
			return _this;
		}
	};
})(jQuery);
var playId=false,imgurl,musicData,$this,$parent,$playObj=false,f,s,angle = 0;
$(document).ready(function(){	
	//初始化------------
	$("#jplayer_N").jPlayer({ 
			swfPath: 'js/jPlayer',	//swfUrl,
			volume: 0.6,
			//id:'jp_container';
			supplied: "mp3,m4a",
			wmode: "window",
			cssSelectorAncestor: "#jp_container_N",
			wmode: "window",
			smoothPlayBar: true,
			keyEnabled: true,
			remainingDuration: true,
			toggleDuration: true,
			loop:false,
			timeupdate: function(event) {
				time = event.jPlayer.status.currentTime;
			},
			ended:function(){//设置循环播放	
				if($('.jp-repeat').is(':visible')){	
					$('.play_name').stop(true);//停止闪烁
					if ($playObj){$playObj.css({'color':'#e74c3c','opacity':'1'});	}	
				   	var obj = $('.p-active').next();
			   		if(obj.length>0){
						//$('.jp-play-me').removeClass('active');
						obj.find('.jp-play-me').trigger('click');
					}else{
						var $parent = $('.p-active').parent();
						$parent.find('li:first').find('a.jp-play-me').trigger('click');
					}
				}
			}
	});
		
	//暂停事件
  	$(document).on($.jPlayer.event.pause, $.jPlayer.cssSelector,  function(){
    	$this.removeClass('active');
    	$this.parents('li').addClass('pause');
		window.clearInterval(f);
		$('.play_name').stop(true);//停止闪烁
		if ($playObj){$playObj.css({'color':'#e74c3c','opacity':'1'});}	
  	});
		//播放事件
  	$(document).on($.jPlayer.event.play, $.jPlayer.cssSelector,  function(){
		$this.addClass('active');
		$this.parents('li').removeClass('pause');
		$('.play_name').stop(true);//停止闪烁
		setCover(imgurl);
  	});
  	//下一曲
  	$(document).on('click', '.jp-next',  function(){
  		$('.p-active').next().find('.jp-play-me').trigger('click');

  	});
  	//上一曲
  	$(document).on('click', '.jp-previous',  function(){
  		$('.p-active').prev().find('.jp-play-me').trigger('click');
  	});

 	$(document).on('click', '.jp-play-me', function(e){  		
 		e && e.preventDefault();
    	$this = $(this);
    	var num = $this.find('.num').html();
		$this.find('.num').html(Number(num)+1);
 		//获取歌曲Id
 		playId = $(this).attr('data-id');
		$.post(U("Music/getData"), {"id": playId},function(data){;
			if(data){
				if(data.artist_name == ''){data.artist_name="网络"}					
				$("#jplayer_N").jPlayer("setMedia", {mp3:data.music_url,title:data.artist_name+' - '+data.name});
				$("#jplayer_N").jPlayer("play");
				imgurl = data.cover_url;//设置封面
				var lrc = data.lrc;
				if('null' !=  $.type(lrc) ) {
					$('#lrc_list').html('歌词加载中....');
					$('.jp-title').empty();
					$('.lrc-content').text(lrc);
					$.lrc.start(lrc, function() {
						return time;
					});
				}else{
					$('.lrc-content').text('');
					$('#lrc_list').empty();
					$.lrc.stop();
				}
			}
   		   		  		
   		}, "json");
    	if (!$this.is('a')) $this = $this.closest('a');
    	if ($this.parents('li').length > 0){
    		 $parent = $this.parents('li')
    	}else{
    		$parent = $this.parents('.play_box')    		    		
    	} 
    	$('.p-active').removeClass('p-active');	
    	$parent.addClass('p-active');
    	$('.jp-play-me').not($this).removeClass('active');
    	$this.toggleClass('active');
    	//弹出播放器
		$('#footer-player').animate({bottom: 0}, 600 );		
  	});
	
	$("#player_off").find('a').click(function(){
		var btn  = $("#player_off").find('.lock-on');
		if(btn.is(':visible')){
			$('#footer-player').animate({  bottom: -61}, 600 );
			btn.hide();
			$("#player_off").find('.lock-off').show();
		}else{
			$('#footer-player').animate({  bottom: 0}, 600 );
			btn.show();
			$("#player_off").find('.lock-off').hide();
		}
		return  false;
	});
	
	//专辑详细页播放歌曲
	$('.list_play').click(function () {
		$('.a_s_list li:first-child').find('.jp-play-me').click();//播放第一首歌曲
	})
	
	/*专辑播放*/	
	$('.album_play').click(function () {
		var album_id =  $(this).attr('data-id'), title = $(this).attr('title');
		$.post(U("/Music/albumSongs"), {"id": album_id},function(data){;
			if(data){	
				var html='',listObj=$('#songs_list');
				for (i = 0; i < data.length; i++) {					 
					html+= '<li class="m_bottom_10">'+
								'<a class="jp-play-me m-r-sm pull-left m_right_10" href="javascript:;" data-id="'+data[i].id+'">'+
									'<i class="fa fa-play sow"></i>'+
									'<i class="fa fa-pause hde"></i>'+
								'</a>'+
								'<span class="text-ellipsis  color_dark play_name">'+data[i].name+'</span>'+						
							'</li>';
				}
				//添加内容/设置滚动条
				listObj.css('display','block');
				var ul = $('#list_mini').find('ul');
				ul.html(html).customScrollbar({preventDefaultScroll: true});
				$('#list_mini').prev().html(title);				
				if (!listObj.hasClass('opened') )listObj.find('.sw_button').click();
				$('#list_mini li:first-child').find('a').click();//播放第一首歌曲
				
			}
   		}, "json");
		return false;
	})
	
	//封面旋转+名称闪烁
	function setCover(imgUrl,obj) {
		window.clearInterval(f);
		if ($playObj){$playObj.css({'color':'#e74c3c','opacity':'1'});}
		var self = $("#p-artist img"),
			prevObj = $this.prevAll('.play_name'),
			nextObj = $this.next('.play_name'),
			prevObj2 = $this.parent().prev().find('.play_name');
		if (prevObj.length > 0){
			$playObj = prevObj;
		}else if(nextObj.length > 0){
			$playObj = nextObj;
		}else if(prevObj2.length > 0){			
			$playObj = prevObj2;	
		}
		if(self) {self.attr('src',imgUrl);}
		f = setInterval(function(){
		      angle+=3;
		     if(self.length > 0) {self.rotate(angle);}
		     if ($playObj && $playObj.length > 0){
		     	$playObj.css('color','#e74c3c').fadeToggle(1000);
		     }
		},40);
	}
});




