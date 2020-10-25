/**
 * 微调器
 * @author zjh
 */
+function ($) {
	"use strict";
	
	var Spinner = function(element,options) {
		this.$element = $(element);
		this.options = $.extend({}, $.fn.spinner.defaults, options);
		
		this.render();
		this.$element.on("click",'button[data-type]',$.proxy(function(ev){this.update(ev.currentTarget,ev);},this));
	};
	
	Spinner.prototype = {
		constructor:Spinner,
		
		render:function(){
			var $div = $("<div></div>").addClass("input-group");
			
			this.renderButton($div, "minus");
			this.renderText($div);
			this.renderButton($div, "plus");
			
			$div.appendTo(this.$element);
		},
		
		renderButton:function($div,type){
			var $btnGroup = $("<span></span>").addClass("input-group-btn");
			var btnClass = type=="plus"?"success":"danger";
			var $button = $("<button>").addClass("btn").addClass("btn-" + btnClass);
			$button.attr("data-type",type);
			var $span = $("<span></span>").addClass("icon").addClass("icon-" + type);
			
			$span.appendTo($button);
			$button.appendTo($btnGroup);
			$btnGroup.appendTo($div);
		},
		
		renderText:function($div){
			var value = this.options.value;
			var name = this.options.name;
			var $text = $("<input>").addClass("form-control").addClass("input-number");
			$text.attr("type","text");
			$text.attr("value",value);
			if(name){
				$text.attr("name",name);
			}
			
			$text.appendTo($div);
		},
		
		update:function(el,ev){
			var $el = $(el);
			var type = $el.attr("data-type");
			var $text = $el.parent().siblings(":text");
			var val = parseInt($text.attr("value"));
			var action = this[type];
			if(!action){
				return;
			}
			$text.attr("value",action(this,val));
		},
		
		plus:function(that,val){
			var max = that.options.max;
			if(val + that.options.step >= max){
				return val;
			}
			return val+that.options.step;
		},
		
		minus:function(that,val){
			var min = that.options.min;
			if(val - that.options.step <= min){
				return val;
			}
			return val - that.options.step;
		}
	};
	
	//加入到jQuery插件
	$.fn.spinner = function(option,value){
		//3
		var methodReturn;

		var $set = this.each(function () {
			var $this = $(this);
			var data = $this.data('koala.spinner');
			var options = typeof option === 'object' && option;

			if (!data) $this.data('koala.spinner', (data = new Spinner(this, options)));
			if (typeof option === 'string') methodReturn = data[option](value);
		});

		return (methodReturn === undefined) ? $set : methodReturn;
	};
	
	//默认属性列表
	$.fn.spinner.defaults = {
		/**
		 * 微调器最小值
		 * @property min
		 * @type {Integer}
		 * @default 0
		 */
		min:0,
		/**
		 * 微调器最大值
		 * @property max
		 * @type {Integer}
		 * @default 100
		 */
		max:100,
		/**
		 * 微调器当前值
		 * @property value
		 * @type {Integer}
		 * @default 10
		 */
		value:10,
		/**
		 * 数值调节大小
		 * @property step
		 * @type {Integer}
		 * @default 1
		 */
		step:1,
		/**
		 * 微调器输入框的name属性
		 * @property name
		 * @type {String}
		 * @default ""
		 */
		name:"",
	};
	
	//插件的构造对象赋值
	$.fn.spinner.Constructor = Spinner;
}(window.jQuery);