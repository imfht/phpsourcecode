/**
 * 用于切换显示/隐藏的组件
 * @param $
 */
+function($){
	"use strict";
	var that = null;
	
	//4
	var Toggle = function(element,options){
		this.$element = $(element);
		this.options = $.extend({}, $.fn.toggle.defaults, options);
		that = this;
		this.render(this.$element);
		
		this.$element.on('click', "i", $.proxy( function(ev) { this.toggle(ev.currentTarget); } ,this));
	};
	
	Toggle.prototype = {
		//1
		constructor:Toggle,
		
		render:function($el){
			var $content = $el.find("div[data-role='toggle-content']");
			if(!$content.length){
				return;
			}
			var $iToggle = $("<i></i>").addClass("glyphicon").addClass("glyphicon-minus");
			
			$iToggle.prependTo($el);
		}
		
		,toggle:function(el){
			var $el = $(el);
			var $content = $el.siblings("div[data-role='toggle-content']");
			$content.is(":hidden")?that.show($el,$content):that.hide($el,$content);
		}
		
		,show:function($toggle,$content){
			$content.slideDown("normal",function(){
				$toggle.removeClass("glyphicon-plus").addClass("glyphicon-minus");
			});
		}
		
		,hide:function($toggle,$content){
			$content.slideUp("normal",function(){
				$toggle.removeClass("glyphicon-minus").addClass("glyphicon-plus");
			});
			
		}
	};
	
	//no conflict
  	var old = $.fn.toggle;
  	
  	$.fn.toggle.noConflict = function () {
  		$.fn.toggle = old;
  		return this;
	};
	//********************
  	
	$.fn.toggle = function(option,value){
		//3
		var methodReturn;

		var $set = this.each(function () {
			var $this = $(this);
			var data = $this.data('koala.toggle');
			var options = typeof option === 'object' && option;

			if (!data) $this.data('koala.toggle', (data = new Toggle(this, options)));
			if (typeof option === 'string') methodReturn = data[option](value);
		});

		return (methodReturn === undefined) ? $set : methodReturn;
	};
	
	//默认属性列表
	$.fn.toggle.defaults = {
		//2
		showTip:false				//鼠标移动到内容框上面时，是否要显示提示
	};
	
	//页面装载后
	$(function(){
		var $container = $("div[data-role='toggle-container']");
		if(!$container.length){
			return;
		}
		$container.each(function(){
			var $this = $(this);
			if($this.data("koala.toggle")){
				return;
			}
			$this.toggle($this.data());
		});
	});
	
	//插件的构造对象赋值
	$.fn.toggle.Constructor = Toggle;
}(window.jQuery);