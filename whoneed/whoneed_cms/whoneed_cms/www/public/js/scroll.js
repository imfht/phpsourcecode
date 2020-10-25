//版本2.3.1 作者：huanghm
(function(){
	var nextFrame = (function() {
		return window.requestAnimationFrame|| window.webkitRequestAnimationFrame|| window.mozRequestAnimationFrame|| window.oRequestAnimationFrame|| window.msRequestAnimationFrame|| function(callback) { return setTimeout(callback, 1); }
		})(),
	cancelFrame = (function () {
		return window.cancelRequestAnimationFrame|| window.webkitCancelAnimationFrame|| window.webkitCancelRequestAnimationFrame|| window.mozCancelRequestAnimationFrame|| window.oCancelRequestAnimationFrame|| window.msCancelRequestAnimationFrame|| clearTimeout
		})(),
		isTouchPad = (/hp-tablet/gi).test(navigator.appVersion),
		hasTouch = 'ontouchstart' in window && !isTouchPad,
		RESIZE_EV = 'onorientationchange' in window ? 'orientationchange' : 'resize',
		START_EV = hasTouch ? 'touchstart' : 'mousedown',
		MOVE_EV = hasTouch ? 'touchmove' : 'mousemove',
		END_EV = hasTouch ? 'touchend' : 'mouseup',
		CANCEL_EV = hasTouch ? 'touchcancel' : 'mouseup';
	function tScroll(args) {
		aiWeb().assignment(tScroll,this,args);//将传递的参数赋予this
		var that=this;
		this.x=0;
		this.y=0;
		this.steps=[];
		this.bind(START_EV);
		this.minScrollY = this.topOffset || 0;
		this.minScrollX = this.leftOffset || 0;
		this.pullUpOffset = this.pullUpOffset || 0;
		this.pullRiOffset = this.pullRiOffset || 0;
		this.hScroll=this.limitX||false;
		this.vScroll=this.limitY||false;
		this.ing=true;//换屏过程禁止其他动画执行
		this.currPageX= 1;//为点击图片浏览而准备的，初始应为点击图片在整组图片中的排序
		this.pullLock= this.pullLock||false;//为点击图片浏览而准备的，初始应为点击图片在整组图片中的排序
		this.donTFollow=this.donTFollow||false;//滑动图片时默认跟随移动
		this.obj.style['webkitTransform'] = 'translate3d(0px, 0px, 0px)';
		this.refresh();
		if(this.ingTime){//是否自动播放
			this.timing=setInterval(function(){
				that.automatic();
			},this.ingTime);
		}
		
		window.addEventListener("resize", function(){//窗口尺寸改变时执行的函数（手机横向和纵向摆放主要用于图片查看器）
			that.refresh();
			if (that.windowResize) that.windowResize(that);//自定义函数
		}, false);	
	}
	tScroll.prototype={
		handleEvent: function (evt) {//方便绑定事件
			var that = this;
			switch(evt.type) {
				case START_EV:that.scrollStart(evt); break;
				case MOVE_EV: that.scrollMove(evt); break;
				case END_EV:
				case CANCEL_EV: that.scrollEnd(evt); break;
			}
		},
		scrollStart:function (evt) {
		
			this.refresh();
			if(!hasTouch){evt.preventDefault();}//PC端预览
			var evt = hasTouch ? evt.touches[0] : evt;
			if(this.screenWidth){//取得目前滑动对象的状态，包括当前页数，当前x轴坐标,目的：为了在除此类以外任何方法改变图片轮换的状态时都能在正常识别轮换状态
				var page=(-this.nowX()/this.screenWidth)+1;
				this.currPageX=Math.round(page*Math.pow(10,0))/Math.pow(10,0);
				this.x=-(this.currPageX-1)*this.screenWidth;
			}
			if(this.timing) clearInterval(this.timing);//清除自动播放的动画
			if(!this.change){//清除滚动动画
				cancelFrame(this.aniTime);
				this.animating = false;		
			}
			this.absDistX = 0;
			this.absDistY = 0;
			this.distX = 0;
			this.distY = 0;
			this.startTime = evt.timeStamp || Date.now();//取得开始的时间
			this.pagex=evt.pageX;
			this.pagey=evt.pageY;//取得触摸的位置，用于惯性移动
			this.pagex_move=evt.pageX;
			this.pagey_move=evt.pageY;//取得触摸的位置，用于随指移动
			this.bx=this.nowX();
			this.unbind(MOVE_EV); //防止多次绑定
			this.unbind(END_EV);
			this.bind(END_EV);
			this.bind(MOVE_EV);//绑定事件
			this.obj.style['webkitTransitionDuration'] = '0ms';//防止多种动画冲突
		},
		scrollMove:function (e) {
				
			var evt = hasTouch ? e.touches[0] : e;
			var that = this;
			this.flag=true;//用于决定是否启用	touchend函数
			
			this.topY=evt.pageY-this.pagey_move;//开始触摸和结束触摸的距离
			this.topX=evt.pageX-this.pagex_move;//开始触摸和结束触摸的距离
			
			if(that.pullLock) {//是否在横向拉动时取消默认动作，目的：为了在左右滑动时不影响页面整体的上下滑动
				if(Math.abs(this.topX)>=Math.abs(this.topY)) e.preventDefault();//取消默认动作
			}else{
				e.preventDefault();//取消默认动作
			}
			this.top=this.topY+this.y;//随指移动的距离
			this.left=this.topX+this.x;//随指移动的距离
				
			if(this.left>this.minScrollX||Math.abs(this.left)>this.objOffsetWidth-this.objPOffsetWidth){//x超额，将原本应该移动的距离减半
				this.left=this.topX*0.5+this.nowX();	
				if(this.left>this.minScrollX&&this.screenWidth) this.overLeft=1;
				if(Math.abs(this.left)>this.objOffsetWidth-this.objPOffsetWidth&&this.screenWidth) this.overRight=-(this.objOffsetWidth-this.objPOffsetWidth);
			}
			if(this.top>this.minScrollY||Math.abs(this.top)>this.objOffsetHeight-this.objPOffsetHeight){//y超额，将原本应该移动的距离减半
				this.top=this.topY*0.5+this.nowY();	
	  		}
		
			this.pagex_move = evt.pageX;
			this.pagey_move = evt.pageY;
			if (that.absDistX < 6 && that.absDistY < 6&&!this.donTFollow) {//当手指位移超过6时才进行移动，否则返回
				that.distX += that.topX;
				that.distY += that.topY;
				that.absDistX = Math.abs(that.distX);
				that.absDistY = Math.abs(that.distY);
				return;
			}
			if (this.onScrollMove) this.onScrollMove();//自定义函数
			
			if(!this.freedom){
				if( this.absDistX > this.absDistY + 5){//判断用户的滑动意图，当用户的左右滑动比上下滑动大5时，说明用户是在左右滑动
					this.top=this.y;
				}else if (this.absDistY > this.absDistX + 5) {
					this.left=this.x;
				}
			}
			if(!this.donTFollow){
				this.pos(this.left,this.top);//移动	
			}else{
				this.recordXY(this.left,this.top);//不移动只更新位置
			}
			
			var timestamp = evt.timeStamp || Date.now();//取得移动的当前时间
			if (timestamp - this.startTime > 300) {//当前时间和开始时间一旦相差300毫秒，即说明用户的动作不是快速移动
				this.startTime  = timestamp;//将开始时间设置为当前时间
				this.pagey = evt.pageY;		//将开始位置设置为当前位置，开始另一次判断
				this.pagex = evt.pageX;		//将开始位置设置为当前位置，开始另一次判断
			}
			this.pageX_end=evt.pageX;//保留touchmove的最后坐标，用户惯性滑动
			this.pageY_end=evt.pageY;//保留touchmove的最后坐标，用户惯性滑动
			
		},
		scrollEnd:function (evt) {
			
			var evt = hasTouch ? evt.touches[0] : evt;
			var that=this;
			this.unbind(MOVE_EV);
			this.unbind(END_EV);
			if(this.overLeft){//左超额
				this.terseScroll(0,0,400);
				this.overLeft=0;
			}
			if(this.overRight){//右超额
				this.terseScroll(this.overRight,0,400);11
				this.overRight=0;
			}
			if(this.change&&this.ing){
				if((this.bx-this.x)>0){//下一张
					if(this.currPageX < this.screenNum){
						this.currPageX+=1;
						this.terseScroll(-(this.currPageX-1)*this.screenWidth,0,300);
					}else if(this.lastPage) {
						this.lastPage.call(this, evt);//最后一张时执行函数
					}
				}else if((this.bx-this.x)<0){//上一张
					if(this.currPageX>1){
						this.currPageX-=1;
						this.terseScroll(-(this.currPageX-1)*this.screenWidth,0,300);
					}else if (this.firstPage){
						this.firstPage.call(this, evt);//第一张时执行函数
					}
				}
			}else if(!this.change){
				if(this.flag==true){
					var duration = (Date.now()) - this.startTime;//开始触摸和结束触摸所用的时间
					if (duration < 300) {
						
						this.journey=this.pageY_end-this.pagey;//取得快速移动的距离
						var LowerY=this.objOffsetHeight+(-this.minScrollY)+(-this.pullUpOffset)-this.objPOffsetHeight+this.y;//冲力函数所需参数--剩余和移动距离
						var momentumY=this.momentum(this.journey, duration, -this.y, LowerY, this.objPOffsetHeight,this.y);//用冲力函数计算惯性距离和时间
						
						this.journex=this.pageX_end-this.pagex;//取得快速移动的距离
						var LowerX=this.objOffsetWidth+(-this.minScrollX)+(-this.pullRiOffset)-this.objPOffsetWidth+this.x;//冲力函数所需参数--剩余和移动距离
						var momentumX=this.momentum(this.journex, duration, -this.x, LowerX, this.objPOffsetWidth,this.x);//用冲力函数计算惯性距离和时间
						newDuration = Math.max(Math.max(momentumX.time, momentumY.time), 10);
						this.goscroll(momentumX.dist,momentumY.dist,newDuration);
						
					}
					
				}
				this.resetPos(200,"Linear");
			}
			
			if(this.ingTime){
				this.timing=setInterval(function(){
					that.automatic();
				},this.ingTime);
			}
			
			this.flag=false;
			
			if (this.onScrollEnd) this.onScrollEnd.call(this, evt);//自定义函数
			
			
		},
		nowX:function () {//返回目前的x轴位置
			var matrix = getComputedStyle(this.obj, null)['webkitTransform'].replace(/[^0-9-.,]/g, '').split(',');
			var	matrix = matrix[4] * 1;//取得目前的位置
			return matrix;
		},
		nowY:function () {//返回目前的x轴位置
			var matrix = getComputedStyle(this.obj, null)['webkitTransform'].replace(/[^0-9-.,]/g, '').split(',');
			var	matrix = matrix[5] * 1;//取得目前的位置
			return matrix;
		},
		momentum:function(dist, time, maxDistUpper, maxDistLower, size,nowy) {//触摸点距可动区域顶部的距离//触摸所用时间//上剩余//下剩余//外层高度
			var deceleration = this.force||0.002,
				speed = Math.abs(dist) / time,
				newDist = (speed * speed) / (2 * deceleration),
				newTime = 0, outsideDist = 0;
				
			if (dist > 0 && newDist > maxDistUpper) {
				outsideDist = size / (6 / (newDist / speed * deceleration));
				maxDistUpper = maxDistUpper + outsideDist*0.3;
				speed = speed * maxDistUpper / newDist;
				newDist = maxDistUpper;
			} else if (dist < 0 && newDist > maxDistLower) {
				outsideDist = size / (6 / (newDist / speed * deceleration));
				maxDistLower = maxDistLower + outsideDist*0.3;
				speed = speed * maxDistLower / newDist;
				newDist = maxDistLower;
			}
			newDist1 = newDist * (dist < 0 ? -1 : 1)+nowy;
			newTime = speed / deceleration;
			return { dist: newDist1, time: newTime};	
		},
		goscroll : function(x,y,time,v) {
		
			this.ing=false;
			var that = this,
			step = x,
			i, l;
			that.stop();
			if (!step.length) step = [{x:x, y: y, time: time}];
			for (i=0, l=step.length; i<l; i++) {
				that.steps.push({ x: step[i].x, y: step[i].y, time: step[i].time || 0 });
			}
			if(v){
				that.startAni(v);
			}else{
				that.startAni();
			}
			
		},
		startAni: function (v) {
			var	that = this;
			var	startY =that.y;
			var	startX =that.x;
			var startTime = Date.now();
			var step;
			var easeOut;
			var	animate;
			var speed = v || "easeOut";
			if (that.animating) return;
			if (!that.steps.length) {
				this.ing=true;
				that.resetPos(400);	
				if (this.onScrollEndEnd) this.onScrollEndEnd();//自定义函数
				return;
			}
			step = that.steps.shift();
			if (step.y == startY && step.x == startX) step.time = 0;
			that.animating = true;
			animate = function () {
				var now = Date.now();
				var newY;
				var newX;	
				if (now >= startTime +  step.time) {//现在的时间如果大于预计动画完成时间
					that.animating = false;
					that.startAni();
					return;
				};
				if(speed=="Linear"){  
					newY = startY+(now - startTime)/step.time*(step.y  - startY);
					newX = startX+(now - startTime)/step.time*(step.x  - startX);
				}else if(speed=="easeOut"){
					now = (now - startTime) / step.time - 1;
					easeOut = Math.sqrt(1 - now * now);
					newY = (step.y  - startY) * easeOut + startY;
					newX = (step.x  - startX) * easeOut + startX;
				}else if(speed=="easeInOut"){
					var progress =(now - startTime)/step.time*2;
					easeInOut = (progress<1?1-Math.sqrt(1-Math.pow(progress,2)):(Math.sqrt(1 - Math.pow(progress-2,2)) + 1));
					newY = easeInOut*(step.y - startY)/2  + startY;
					newX = easeInOut*(step.x - startX)/2  + startX;
				}
				that.pos(newX,newY);
				if (that.animating) that.aniTime=nextFrame(animate);
			};
			animate();
		},
		terseScroll: function(x,y,time){//简易滑动
			this.obj.style['webkitTransitionDuration'] = ''+time+'ms';
			this.obj.style['webkitTransform'] = 'translate3d('+x+'px, '+y+'px, 0px)';
		},
		resetPos: function (time,v) {
			var that = this,
			resetY = that.y >= that.minScrollY || that.maxScrollY > 0 ? that.minScrollY : that.y < that.maxScrollY ? that.maxScrollY : that.y;
			resetX = that.x >= that.minScrollX || that.maxScrollX > 0 ? that.minScrollX : that.x < that.maxScrollX ? that.maxScrollX : that.x;
			if (resetX == that.x && resetY == that.y) return;
			if(v){
				that.goscroll(resetX , resetY , time || 0,v);
			}else{
				that.goscroll(resetX , resetY , time || 0);
			}
			
		},
		refresh: function () {
			this.objOffsetWidth=this.obj.offsetWidth;
			this.objOffsetHeight=this.obj.offsetHeight;
			this.objPOffsetWidth=this.obj.parentNode.offsetWidth;
			this.objPOffsetHeight=this.obj.parentNode.offsetHeight;
			this.maxScrollY = this.objPOffsetHeight-this.objOffsetHeight+this.minScrollY+this.pullUpOffset;
			this.maxScrollX = this.objPOffsetWidth-this.objOffsetWidth+this.minScrollX+this.pullRiOffset;
			this.sizeBar();
		},
		stop: function () {
			cancelFrame(this.aniTime);
			this.steps = [];
			this.animating = false;
		},
		pos: function (x,y) {
			x = this.hScroll ? x : 0;
			y = this.vScroll ? y : 0;
			this.x=x;
			this.y=y;
			this.terseScroll(x,y,0);
			if(this.haveBar) this.scrollBarPos();
		},
		scrollBarPos: function () {
			var that = this,
				pos  = this.hScroll ? that.x : that.y,
				widthOrheight  = this.hScroll ? 'width' : 'height',
				size;
			pos = that.ScrollbarProp * pos;
			
			if (pos < 0) {//如果超过左极限则缩小
				size = that.scrollBarSize + Math.round(pos * 3);
				if (size < 8) size = 8;
				that.scrollBar.style[widthOrheight] = size + 'px';
				pos = 0;
			}else if (pos > that.scrollBarSizeMaxScroll) {//如果超过右极限则一边缩小一边右移
				size = that.scrollBarSize - Math.round((pos - that.scrollBarSizeMaxScroll) * 3);
				if (size < 8) size = 8;
				if(!that.scrollBarJitter){//如果不是第一屏出现，必须将此值设置为ture
					that.scrollBar.style[widthOrheight] = size + 'px';
				}
				pos = that.scrollBarSizeMaxScroll + (that.scrollBarSize - size);
			}
			if(this.hScroll){
				that.scrollBar.style['webkitTransform'] = 'translate3d( '+pos+'px, 0px, 0px)';
			}else{
				that.scrollBar.style['webkitTransform'] = 'translate3d( 0px, '+pos+'px, 0px)';
			}
		},
		recordXY: function(x,y){
			x = this.hScroll ? x : 0;
			y = this.vScroll ? y : 0;
			this.x=x;
			this.y=y;
		},
		bind: function (type, el, bubble) {
			(el || this.obj).addEventListener(type, this, !!bubble);
		},
		unbind: function (type, el, bubble) {
			(el || this.obj).removeEventListener(type, this, !!bubble);
		},
		automatic:function(){//下一屏
			this.obj.style['webkitTransitionDuration'] = '0ms';
			if(this.ing&&this.currPageX<this.screenNum){
				this.currPageX++;
				this.terseScroll(-(this.currPageX-1)*this.screenWidth,0,300);
			}else if(this.ing){
				this.currPageX=1;
				this.terseScroll(-(this.currPageX-1)*this.screenWidth,0,300);
			}
			if (this.onScrollEnd) this.onScrollEnd();
		},
		goNum:function(n){//跳到第几屏
			this.obj.style['webkitTransitionDuration'] = '0ms';
			this.currPageX=n+1;
			this.terseScroll(-(this.currPageX-1)*this.screenWidth,0,300);
			if (this.onScrollEnd) this.onScrollEnd();
		},
		sizeBar:function(){
			if(this.scrollBar&&this.hScroll){//模拟滚动条---横向滑动时
				this.scrollBarSize=Math.max(Math.round(this.objPOffsetWidth * this.objPOffsetWidth / this.objOffsetWidth), 8);//按比例计算滚动条的长度
				if(this.objPOffsetWidth-this.scrollBarSize<10){
					this.scrollBar.style.width='0px';
					this.haveBar=false;
				}else{
					this.scrollBar.style.width=''+this.scrollBarSize+'px';
					this.haveBar=true;
				}
				this.scrollBarSizeMaxScroll = this.objPOffsetWidth - this.scrollBarSize;//计算滚动条的最大滑动范围
				this.ScrollbarProp = this.scrollBarSizeMaxScroll / this.maxScrollX;//滚动条可以滚动的距离和主体可以滚动的距离的比例
			}else if(this.scrollBar&&this.vScroll){//模拟滚动条---纵向滑动时
				this.scrollBarSize=Math.max(Math.round(this.objPOffsetHeight * this.objPOffsetHeight / this.objOffsetHeight), 8);//按比例计算滚动条的长度
				this.scrollBarSizeMaxScroll = this.objPOffsetHeight - this.scrollBarSize;//计算滚动条的最大滑动范围
				this.ScrollbarProp = this.scrollBarSizeMaxScroll / this.maxScrollY;//滚动条可以滚动的距离和主体可以滚动的距离的比例
			}
		},
		
	};
	window.tScroll=tScroll;
})();