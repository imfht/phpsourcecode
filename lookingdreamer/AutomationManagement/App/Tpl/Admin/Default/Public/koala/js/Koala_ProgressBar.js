/**
 * @author zjh
 */
+function ($) {
	"use strict";
	/**
	 * 进度条组件，强化了Bootstrap原生的progressbar(只有CSS而没有js的)
	 * @class ProgressBar
	 * @constructor
	 */
	var ProgressBar = function (element, options) {
		//4
		this.$element = $(element);
		this.options = $.extend({}, $.fn.progressbar.defaults, options);
		//事件列表
		this.render();
	};
	
	//原型定义，定义构造函数和其他方法
	ProgressBar.prototype = {
		
		constructor:ProgressBar,
		
		render:function(){
			var css = this.options.css || "success";
			var percent =  this.options.value / this.options.max * 100 + "%";
			var $outer = $("<div></div>").addClass("progress").addClass("progress-striped");
			if(this.options.isActive){
				$outer.addClass("active");
			}
			var $bar = $("<div></div>").addClass("progress-bar").addClass("progress-bar-" + css);
//			var $span = $("<span></span>").addClass("sr-only").html(this.options.value).appendTo($bar);
			$bar.css({
				width:percent
			}).appendTo($outer);
			
			$outer.appendTo(this.$element);
			console.info(this.$element.html());
		},
		
		/**
		 * 动画显示进度
		 * @method progressing
		 * @param action:动画显示方式（"fast","normal","slow")，跟jQuery的animate参数一样
		 */
		progressing:function(action){
			this.$element.animate({},action,"swing",function(){
				$(this).find(".progress-bar").css({
					width:"100%"
				});
				
			});
			
		},
		
		/**
		 * 重设进度为开始的0状态
		 * @method reset
		 */
		reset:function(){
			var percent =  this.options.value / this.options.max * 100 + "%";
			this.$element.find(".progress-bar").css({
				width:percent
			});
		},
		
		activate:function(){
			
		},
		
		deactivate:function(){
			
		}
		
	};
	
	//加入到jQuery插件
	$.fn.progressbar = function(option,value){
		//3
		var methodReturn;

		var $set = this.each(function () {
			var $this = $(this);
			var data = $this.data('koala.progressbar');
			var options = typeof option === 'object' && option;

			if (!data) $this.data('koala.progressbar', (data = new ProgressBar(this, options)));
			if (typeof option === 'string') methodReturn = data[option](value);
		});

		return (methodReturn === undefined) ? $set : methodReturn;
	};
	
	//默认属性列表
	$.fn.progressbar.defaults = {
		/**
		 * 最小值
		 * @property min
		 * @type {Integer}
		 * @default 0
		 */
		min:0,
		/**
		 * 最大值
		 * @property max
		 * @type {Integer}
		 * @default 100
		 */
		max:100,
		/**
		 * 默认初始值
		 * @property value
		 * @type {Integer}
		 * @default 0
		 */
		value:0,
		/**
		 * 是否带有活动样式
		 * @property isActive
		 * @type {Boolean}
		 * @default true
		 */
		isActive:true,
		/**
		 * 进度条的样式
		 * @property css
		 * @type {String}("success"/"info"/"warning"/"danger")
		 * @default "success"
		 */
		css:"success",
		
		onComplete:function(ev){},
	};
	
	//插件的构造对象赋值
	$.fn.progressbar.Constructor = ProgressBar;
}(window.jQuery);