/*
*选择框组件
*/
+function($){

	"use strict" ;
	
	/**
	 * 选择框组件
	 * @class Select
	 * @constructor 
	 */
	var Select = function(element, options){
		this.$element = $(element);
		this.options = options;
		this.$element.html(Select.DEFAULTS.TEMPLATE);
		this.$element.addClass("input-group").addClass("combobox");
        this.$button = this.$element.find('[data-toggle="button"]');
		this.$items = this.$element.find('.dropdown-menu');
		this.$item = this.$element.find('[data-toggle="item"]');
		this.$value = this.$element.find('[data-toggle="value"]');
		this.init();
	};
	Select.DEFAULTS = {
		/**
		 * 下拉框内容
		 * @property contents
		 * @type {Array}
		 * @default []
		 * @example
		 * 	var contents = [
				{value:1,title:"第一个",selected:true},
				{value:2,title:"第二个",selected:false},
				{value:3,title:"第三个",selected:false},
			];
			$(function(){
				$("#mySelect").select({
					contents:contents
				});
			})
		 */
		contents:[]
	};
	Select.prototype = {
		Constructor: Select,
		init: function(){
			var self = this;
            this.$button.on('click', function(){
                self.$element.toggleClass('open');
            });
            this.$item.on('click', function(){
                self.$element.toggleClass('open');
            });
			this.$item.html(self.options.title);
			var contents = self.options.contents;
			if(contents && contents.length){
				self.setItems(contents);
			}
			this.setDefaultSelection();
			self.$items.on('mouseleave', function(){
				self.$element.removeClass('open');
			});
		},
		setItems: function(contents){
			var self = this;
			var items = new Array();
			for(var i = 0, j = contents.length; i < j; i++){
				var content =  contents[i];
				var li = "";
				if(content.selected){
					li = '<li data-value="' + content.value + '" class="selected"><a href="#">' + content.title + '</a></li>';
				}else{
					li = '<li data-value="' + content.value + '"><a href="#">' + content.title + '</a></li>';
				}
				items.push(li);
			}
			self.$items.html(items.join(' '));
			if(self.$items.find('li').length > 5){
				self.$items.css({'height': '130px', 'overflow-y': 'auto'});
			}
			self.$items.find('li').on('click', function(e){
				e.preventDefault();
				self.clickItem($(this));
			});
			return self.$element;
		},
		clickItem: function($item){
            this.$element.removeClass('open');
            this.$button.removeClass('active');
			var value = $item.data('value');
			if(this.$value.val() == value){
				return this.$element;
			}
			this.$item.html($item.find('a:first').html());
			this.$value.val(value);
			this.$element.trigger('change').popover('destroy').parent().removeClass('has-error');
			return  this.$element;
		},
		getValue: function(){
			return this.$value.val();
		},
		getItem: function(){
			return this.$item.html();
		},
		selectByValue: function(value){
			return this.selectBySelector('li[data-value="'+value+'"]');
		},
		selectByIndex: function(index){
			return this.selectBySelector('li:eq('+index+')');
		},
		selectBySelector: function(selector){
			return this.clickItem(this.$items.find(selector));
		},
		setDefaultSelection: function(){
			return this.selectBySelector('li.selected');
		}
	};
	Select.DEFAULTS.TEMPLATE = '<button type="button" class="btn btn-default" data-toggle="item">' +
		'</button><button type="button" class="btn btn-default dropdown-toggle" data-toggle="button">' +
		'<span class="caret"></span>' +
		'</button>' +
		'<input type="hidden" data-toggle="value"/>' +
		'<ul class="dropdown-menu" role="menu"></ul>';
	$.fn.getValue = function(){
		if($(this).data('koala.select')){
			return $(this).data('koala.select').getValue();
		}
	};
	$.fn.getItem = function(){
		if($(this).data('koala.select')){
			return $(this).data('koala.select').getItem();
		}
	};
	$.fn.setValue = function(value){
		if($(this).data('koala.select')){
			return $(this).data('koala.select').selectByValue(value);
		}
	};
	$.fn.setSelectItems = function(contents){
		if($(this).data('koala.select')){
			return $(this).data('koala.select').setItems(contents);
		}
	};
	$.fn.appendItems = function(contents){
		return $(this).data('koala.select').setItems(contents);
	};
	$.fn.resetItems = function(contents){
		$(this).data('koala.select').$item.empty();
		return $(this).data('koala.select').setItems(contents);
	};
	var old = $.fn.select;
	$.fn.select = function(option){
		return this.each(function(){
			var $this = $(this);
			var data = $this.data('koala.select');
			var options = $.extend({}, Select.DEFAULTS, $this.data(), typeof option == 'object' && option);
			if(!data){
				$this.data('koala.select', (data = new Select(this, options)));
			}
			if(typeof option == 'string'){
				data[option]();
			}
		});
	};
	$.fn.select.Constructor = Select;
	$.fn.select.noConflict = function(){
		$.fn.select = old;
		return this;
	};
}(window.jQuery);