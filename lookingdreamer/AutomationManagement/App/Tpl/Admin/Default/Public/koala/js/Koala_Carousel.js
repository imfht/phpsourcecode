/**
 * @author zjh
 */
+function ($) {

	"use strict"; 
	
	/**
	 * 图片轮播组件
	 * @class Carousel
	 * @constructor
	 * @example
	 *  js代码
	 * 	var images = ["../../images/gnome1.png","../../images/gnome2.png","../../images/KoalaUI.png"];
		$(function(){
			$("#mypic").carousel({
				images:images
			});
		});
		
		html代码
		<div class="container">
			<div id="mypic"></div>
		</div>	
	 */
	var Carousel = function (element, options) {
		//4
		this.$element = $(element);
		this.options = $.extend({}, $.fn.carousel.defaults, options);
		//事件列表
		this.render();
		this.$element.on(this.options.toggleEvent,'.carousel-nav-classical li',$.proxy(function(ev){this.toggle(ev.currentTarget,ev);},this));
//		this.$element.on('mouseover','.carousel-nav-classical li',$.proxy(function(ev){this.toggle(ev.currentTarget,ev);},this));
		//判断是否自动开启轮播
		if(this.options.autoToggle&&this.options.interval){
			this.startToggle(this.$element);
		}
	};
	
	Carousel.prototype={
			
		constructor:Carousel,
		
		render:function(){
			var $ulContent = $("<ul></ul>").addClass("carousel-content");
			var $ulNav = $("<ul></ul>").addClass("carousel-nav-classical");
			var images = this.options.images;
			var length = images.length;
			if(!length){
				return;
			}
			this.renderContent($ulContent,images,length);
			this.renderNav($ulNav, length);
			this.$element.addClass("carousel");
			
			$(".carousel .carousel-content li:first").show();
		},
		
		renderContent:function($ulContent,images,length){
			for(var i=0;i<length;i++){
				var $li = $("<li></li>").addClass("carousel-item");
				var $img = $("<img>").attr("src",images[i]);
				$img.appendTo($li);
				$li.appendTo($ulContent);
			}
			$ulContent.appendTo(this.$element);
			
		},
		
		renderNav:function($ulNav,length){
			for(var i=0;i<length;i++){
				var $li = $("<li></li>");
				$li.text(i+1);
				if(i==0){
					$li.addClass("nav-selected");
				}
				$li.appendTo($ulNav);
			}
			$ulNav.appendTo(this.$element);
		},
		
		toggle:function(el,ev){
			
			var action = this.options.toggleAction;
			var callback = this["toggle_" + action];
			if(!action&&!callback){
				this.toggle_default(el, ev);
			}else{
				callback(el,ev);
			}
			this.toggle_nav(el, ev);
		},
		
		toggle_nav:function(el,ev){
			var $el = $(el);
			var index = parseInt($el.text());
			var $li = $(".carousel .carousel-nav-classical li:nth-child(" + index + ")");
			$li.siblings("li").removeClass("nav-selected");
			$li.addClass("nav-selected");
		},
		
		toggle_default:function(el,ev){
			var $el = $(el);
			var index = parseInt($el.text());
			var $li = $(".carousel .carousel-content li:nth-child(" + index + ")");
			var $liPrev = $(".carousel .carousel-content li:visible");
			$liPrev.hide("slow");
			$li.show("fast");
		},
		
		toggle_horizontal:function(el,ev){
			
		},
		
		toggle_vertical:function(el,ev){
			
		},
		
		toggle_fadein:function(el,ev){
			
		},
		
		toggle_random:function(el,ev){
			
		},
		
		startToggle:function($el){
			var that = this;
			setInterval(function(){
				that.activateNext($el);
			}, this.options.interval);
		},
		
		/**
		 * 激活下一个
		 * @param $el
		 */
		activateNext:function($el){
			var $li = $(".carousel .carousel-nav-classical li.nav-selected");
			var length = $(".carousel .carousel-nav-classical li").length;
			var index = parseInt($li.text());
			index=index%length+1;
			$(".carousel .carousel-nav-classical li:nth-child(" + index + ")").trigger(this.options.toggleEvent);
		}
	};
	
	//加入到jQuery插件
	$.fn.carousel = function(option,value){
		//3
		var methodReturn;

		var $set = this.each(function () {
			var $this = $(this);
			var data = $this.data('koala.carousel');
			var options = typeof option === 'object' && option;

			if (!data) $this.data('koala.carousel', (data = new Carousel(this, options)));
			if (typeof option === 'string') methodReturn = data[option](value);
		});

		return (methodReturn === undefined) ? $set : methodReturn;
	};
	
	//默认属性列表
	$.fn.carousel.defaults = {
		/**
		 * 图片URL列表
		 * @property images
		 * @type {Array}
		 * @default []
		 */
		images:[],
		/**
		 * 图片切换jQuery事件，
		 * @property toggleEvent
		 * @type {String}
		 * @default "click"
		 */
		toggleEvent:"click",
		/**
		 * 切换动作，目前暂时不实现其他的动作，留到V2升级
		 * @property toggleAction
		 * @type {String}
		 * @default "default"
		 */
		toggleAction:"default",
		/**
		 * 是否自动切换?
		 */
		autoToggle:false,
		/**
		 * 切换图片的时间间隔，只有在autoToggle为true时才有效
		 */
		interval:1000,
	};
	
	//插件的构造对象赋值
	$.fn.carousel.Constructor = Carousel;
	
	
}(window.jQuery);