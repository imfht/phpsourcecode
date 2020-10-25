(function( window, undefined ) {
	var aiWeb = function(selector) {
		return new aiWeb.fn.init(selector);
	},
	doc = window.document;
	aiWeb.fn = aiWeb.prototype = {
		init:function(selector){
			if ( !selector ) {
				return this;
			}else{
				return document.getElementById(selector);
			}
		},
		action:function(obj,css,time,speed,Delay){//动画函数
			for (var prop in css){
				obj.style['webkitTransform'] = ''+prop+'('+css[prop]+')';	
			}
			obj.style['webkitTransitionDuration'] = ''+time+'ms';
			obj.style['webkitTransitionTimingFunction'] = speed?speed:'linear';
			obj.style['webkitTransitionDelay'] = Delay?''+Delay+'ms':'0ms';
		},
	    assignment:function (Class,$this,args) {//参数说明：Class：指定的类，$this:this,args：传递进来的对象
			$this.originalArgs=args;
			for (var i in args) {
				$this[i]=args[i];
			}
			if (Class.defaultArgs) {
				for (i in Class.defaultArgs) {
					if (args[i]===undefined) $this[i]=Class.defaultArgs[i].valueOf($this);
				}
			}
		},
		requestInterval:function(fn, delay) {//固定时间循环函数
			if( !window.requestAnimationFrame       && 
				!window.webkitRequestAnimationFrame && 
				!window.mozRequestAnimationFrame    && 
				!window.oRequestAnimationFrame      && 
				!window.msRequestAnimationFrame)
				return window.setInterval(fn, delay);
				var start = new Date().getTime(),
				handle = new Object();
			function loop() {
				var current = new Date().getTime(),
				delta = current - start;
				if(delta >= delay) {
					fn.call();
					start = new Date().getTime();
				}
				handle.value = requestAnimFrame(loop);
			};
			handle.value = requestAnimFrame(loop);
			return handle;
		},
		clearRequestInterval:function(handle) {
			window.cancelAnimationFrame ? window.cancelAnimationFrame(handle.value) :
			window.webkitCancelRequestAnimationFrame ? window.webkitCancelRequestAnimationFrame(handle.value)   :
			window.mozCancelRequestAnimationFrame ? window.mozCancelRequestAnimationFrame(handle.value) :
			window.oCancelRequestAnimationFrame ? window.oCancelRequestAnimationFrame(handle.value) :
			window.msCancelRequestAnimationFrame ? msCancelRequestAnimationFrame(handle.value) :
			clearInterval(handle);
		},
	    requestTimeout:function(fn, delay) {//固定时间执行函数
		if( !window.requestAnimationFrame       &&
			!window.webkitRequestAnimationFrame &&
			!window.mozRequestAnimationFrame    &&
			!window.oRequestAnimationFrame      &&
			!window.msRequestAnimationFrame)
				return window.setTimeout(fn, delay);
			var start = new Date().getTime(),
				handle = new Object();
			function loop(){
				var current = new Date().getTime(),
				delta = current - start;
				delta >= delay ? fn.call() : handle.value = requestAnimFrame(loop);
			};
			handle.value = requestAnimFrame(loop);
			return handle;
		},
		clearRequestTimeout : function(handle) {
			window.cancelAnimationFrame ? window.cancelAnimationFrame(handle.value) :
			window.webkitCancelRequestAnimationFrame ? window.webkitCancelRequestAnimationFrame(handle.value)   :
			window.mozCancelRequestAnimationFrame ? window.mozCancelRequestAnimationFrame(handle.value) :
			window.oCancelRequestAnimationFrame ? window.oCancelRequestAnimationFrame(handle.value) :
			window.msCancelRequestAnimationFrame ? msCancelRequestAnimationFrame(handle.value) :
			clearTimeout(handle);
		}
	}
	aiWeb.fn.init.prototype = aiWeb.fn;
	window.aiWeb = aiWeb;
})( window );

var common={};	
new function(){
	common.dom = [];
	common.dom.isReady = false;
	common.dom.isFunction = function(obj){
	  return Object.prototype.toString.call(obj) === "[object Function]";
	}
	common.dom.Ready = function(fn){
	  common.dom.initReady();//如果没有建成DOM树，则走第二步，存储起来一起杀
	  if(common.dom.isFunction(fn)){
		if(common.dom.isReady){
		  fn();//如果已经建成DOM，则来一个杀一个
		}else{
		  common.dom.push(fn);//存储加载事件
		}
	  }
	}
	common.dom.fireReady =function(){
	  if (common.dom.isReady)  return;
	  common.dom.isReady = true;
	  for(var i=0,n=common.dom.length;i<n;i++){
		var fn = common.dom[i];
		fn();
	  }
	  common.dom.length = 0;//清空事件
	}
	common.dom.initReady = function(){
	  if (document.addEventListener) {
		document.addEventListener( "DOMContentLoaded", function(){
		  document.removeEventListener( "DOMContentLoaded", arguments.callee, false );//清除加载函数
		  common.dom.fireReady();
		}, false );
	  }else{
		if (document.getElementById) {
		  document.write("<script id=\"ie-domReady\" defer='defer'src=\"//:\"><\/script>");
		  document.getElementById("ie-domReady").onreadystatechange = function() {
			if (this.readyState === "complete") {
			  common.dom.fireReady();
			  this.onreadystatechange = null;
			  this.parentNode.removeChild(this)
			}
		  };
		}
	  }
	}
}