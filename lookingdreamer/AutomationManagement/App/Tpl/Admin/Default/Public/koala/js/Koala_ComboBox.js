/**
 * @author zjh
 */
+function ($) {
	"use strict";
	/**
	 * 下拉组合框
	 * @class Combobox
	 * @constructor
	 * @example
	 * 		js代码
	 *		$(function(){
	 *			$('#combobox-selectByValue').on('click', function () {
	 *				$('#MyCombobox1').combobox('selectByValue', '3');
	 *			});
	 *		});
	 * 
	 * 		html代码
	 * 		<div class="container">	
	 * 			 <div class="col-lg-2">
	 *			    <div id="MyCombobox1" class="input-group combobox">
	 *			      <input type="text" class="form-control">
	 *			      <div class="input-group-btn">
	 *			        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
	 *			        	<span class="caret"></span>
	 *			        </button>
	 *			        <ul class="dropdown-menu pull-right">
	 *			          <li data-value="1"><a href="#">广州</a></li>
	 *			          <li data-value="2"><a href="#">深圳</a></li>
	 *			          <li data-fizz="buzz" data-value="3"><a href="#">北京</a></li>
	 *			          <li class="divider"></li>
	 *			          <li data-value="4"><a href="#">国外</a></li>
	 *			        </ul>
	 *			      </div>
	 *			    </div>
	 *			  </div>
	 *			  <input type="button" value="select by value" id="combobox-selectByValue" class="btn btn-default">
	 *		</div>
	 */
	var Combobox = function (element, options) {
		this.$element = $(element);
		this.options = $.extend({}, $.fn.combobox.defaults, options);
		this.$element.on('click', 'a', $.proxy(this.itemclicked, this));
		this.$element.on('change', 'input', $.proxy(this.inputchanged, this));
		this.$input = this.$element.find('input');
		this.$button = this.$element.find('.btn');

		// set default selection
		this.setDefaultSelection();
	};

	Combobox.prototype = {
		
		constructor: Combobox,
		
		/**
		 * 获取选择的值，并生成js对象
		 * @method selectedItem
		 * @returns {text:文本值}
		 */
		selectedItem: function () {
			var item = this.$selectedItem;
			var data = {};

			if (item) {
				var txt = this.$selectedItem.text();
				data = $.extend({ text: txt }, this.$selectedItem.data());
			}
			else {
				data = { text: this.$input.val()};
			}

			return data;
		},

		/**
		 * 根据内容选择
		 * @method selectByText
		 * @param text
		 */
		selectByText: function (text) {
			var selector = 'li:fuelTextExactCI(' + text + ')';
			this.selectBySelector(selector);
		},

		/**
		 * 根据data-value属性值选择
		 * @method selectByValue
		 * @param value
		 */
		selectByValue: function (value) {
			var selector = 'li[data-value="' + value + '"]';
			this.selectBySelector(selector);
		},

		/**
		 * 根据li索引选择
		 * @method selectByIndex
		 * @param index
		 */
		selectByIndex: function (index) {
			// zero-based index
			var selector = 'li:eq(' + index + ')';
			this.selectBySelector(selector);
		},

		/**
		 * 根据jQuery选择器选中
		 * @method selectBySelector
		 * @param selector
		 */
		selectBySelector: function (selector) {
			var $item = this.$element.find(selector);

			if (typeof $item[0] !== 'undefined') {
				this.$selectedItem = $item;
				this.$input.val(this.$selectedItem.text());
			}
			else {
				this.$selectedItem = null;
			}
		},

		/**
		 * 设置第一条选中
		 * @method setDefaultSelection
		 */
		setDefaultSelection: function () {
			var selector = 'li[data-selected=true]:first';
			var item = this.$element.find(selector);

			if (item.length > 0) {
				// select by data-attribute
				this.selectBySelector(selector);
				item.removeData('selected');
				item.removeAttr('data-selected');
			}
		},

		/**
		 * 设置输入框可用
		 * @method enable
		 */
		enable: function () {
			this.$input.removeAttr('disabled');
			this.$button.removeClass('disabled');
		},

		/**
		 * 设置输入框不可用
		 * @method disable
		 */
		disable: function () {
			this.$input.attr('disabled', true);
			this.$button.addClass('disabled');
		},

		itemclicked: function (e) {
			this.$selectedItem = $(e.target).parent();

			// set input text and trigger input change event marked as synthetic
			this.$input.val(this.$selectedItem.text()).trigger('change', { synthetic: true });

			// pass object including text and any data-attributes
			// to onchange event
			var data = this.selectedItem();

			// trigger changed event
			this.$element.trigger('changed', data);

			e.preventDefault();
		},

		inputchanged: function (e, extra) {

			// skip processing for internally-generated synthetic event
			// to avoid double processing
			if (extra && extra.synthetic) return;

			var val = $(e.target).val();
			this.selectByText(val);

			// find match based on input
			// if no match, pass the input value
			var data = this.selectedItem();
			if (data.text.length === 0) {
				data = { text: val };
			}

			// trigger changed event
			this.$element.trigger('changed', data);

		}

	};


	// COMBOBOX PLUGIN DEFINITION

	$.fn.combobox = function (option, value) {
		var methodReturn;

		var $set = this.each(function () {
			var $this = $(this);
			var data = $this.data('koala.combobox');
			var options = typeof option === 'object' && option;

			if (!data) $this.data('koala.combobox', (data = new Combobox(this, options)));
			if (typeof option === 'string') methodReturn = data[option](value);
		});

		return (methodReturn === undefined) ? $set : methodReturn;
	};

	//默认属性列表
	$.fn.combobox.defaults = {
		//以下新增属性暂时不实现，下期再来升级
		mutiSelect:false,				//支持多选
		dataGrid:null,					//使用哪个表格，如果是null，就表示不用
		ajaxUrl:null					//异步加载URL，如果有，则不用data
	};

	$.fn.combobox.Constructor = Combobox;


	// COMBOBOX DATA-API

	$(function () {

		$(window).on('load', function () {
			$('.combobox').each(function () {
				var $this = $(this);
				if ($this.data('koala.combobox')) return;
				$this.combobox($this.data());
			});
		});

		$('body').on('mousedown.combobox.data-api', '.combobox', function (e) {
			var $this = $(this);
			if ($this.data('koala.combobox')) return;
			$this.combobox($this.data());
		});
	});

}(window.jQuery);