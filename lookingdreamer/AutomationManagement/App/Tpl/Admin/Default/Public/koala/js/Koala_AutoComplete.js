/**
 * @author zjh
 */
+function ($) {

	  "use strict"; // jshint ;_;


	 /* TYPEAHEAD PUBLIC CLASS DEFINITION
	  * ================================= */
	  
	  /**
	   * 输入完成组件，对原生的Bootstrap2 TypeHead稍作修改
	   * @class AutoComplete
	   * @constructor
	   * @example		
	   * 	js代码
	   *	var data = ["zoo","yui","x-men","apple","asia","boy","baby","cisco","disco","easy","fire","google","go"];
	   * 	$("#username").autocomplete({
	   * 		source:data
	   *	});
	   * 	html代码
	   * 	<input type="text" id="username">	
	   */
	  var AutoComplete = function (element, options) {
	    this.$element = $(element);
	    this.options = $.extend({}, $.fn.autocomplete.defaults, options);
	    this.matcher = this.options.matcher || this.matcher;
	    this.sorter = this.options.sorter || this.sorter;
	    this.highlighter = this.options.highlighter || this.highlighter;
	    this.updater = this.options.updater || this.updater;
	    this.source = this.options.source;
	    this.$menu = $(this.options.menu);
	    this.shown = false;
	    this.listen();
	  };

	  AutoComplete.prototype = {
	    
	    constructor: AutoComplete, 
	    
	    
	    select: function () {
	      var val = this.$menu.find('.active').attr('data-value');
	      this.$element
	        .val(this.updater(val))
	        .change();
	      return this.hide();
	    }

	  , updater: function (item) {
	      return item;
	    }

	  , show: function () {
	      var pos = $.extend({}, this.$element.position(), {
	        height: this.$element[0].offsetHeight
	      });

	      this.$menu
	        .insertAfter(this.$element)
	        .css({
	          top: pos.top + pos.height
	        , left: pos.left
	        })
	        .show();

	      this.shown = true;
	      return this;
	    }

	  , hide: function () {
	      this.$menu.hide();
	      this.shown = false;
	      return this;
	    }

	  , lookup: function (event) {
	      var items;

	      this.query = this.$element.val();

	      if (!this.query || this.query.length < this.options.minLength) {
	        return this.shown ? this.hide() : this;
	      }

	      items = $.isFunction(this.source) ? this.source(this.query, $.proxy(this.process, this)) : this.source;

	      return items ? this.process(items) : this;
	    }

	  , process: function (items) {
	      var that = this;

	      items = $.grep(items, function (item) {
	        return that.matcher(item);
	      });

	      items = this.sorter(items);

	      if (!items.length) {
	        return this.shown ? this.hide() : this;
	      }

	      return this.render(items.slice(0, this.options.items)).show();
	    }

	  , matcher: function (item) {
	      return ~item.toLowerCase().indexOf(this.query.toLowerCase());
	    }

	  , sorter: function (items) {
	      var beginswith = []
	        , caseSensitive = []
	        , caseInsensitive = []
	        , item;

	      while (item = items.shift()) {
	        if (!item.toLowerCase().indexOf(this.query.toLowerCase())) beginswith.push(item);
	        else if (~item.indexOf(this.query)) caseSensitive.push(item);
	        else caseInsensitive.push(item);
	      }

	      return beginswith.concat(caseSensitive, caseInsensitive);
	    }

	  , highlighter: function (item) {
	      var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&');
	      return item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
	        return '<strong>' + match + '</strong>';
	      });
	    }

	  , render: function (items) {
	      var that = this;

	      items = $(items).map(function (i, item) {
	        i = $(that.options.item).attr('data-value', item);
	        i.find('a').html(that.highlighter(item));
	        return i[0];
	      });

	      items.first().addClass('active');
	      this.$menu.html(items);
	      return this;
	    }

	  , next: function (event) {
	      var active = this.$menu.find('.active').removeClass('active')
	        , next = active.next();

	      if (!next.length) {
	        next = $(this.$menu.find('li')[0]);
	      }

	      next.addClass('active');
	    }

	  , prev: function (event) {
	      var active = this.$menu.find('.active').removeClass('active')
	        , prev = active.prev();

	      if (!prev.length) {
	        prev = this.$menu.find('li').last();
	      }

	      prev.addClass('active');
	    }

	  , listen: function () {
	      this.$element
	        .on('focus',    $.proxy(this.focus, this))
	        .on('blur',     $.proxy(this.blur, this))
	        .on('keypress', $.proxy(this.keypress, this))
	        .on('keyup',    $.proxy(this.keyup, this));

	      if (this.eventSupported('keydown')) {
	        this.$element.on('keydown', $.proxy(this.keydown, this));
	      }

	      this.$menu
	        .on('click', $.proxy(this.click, this))
	        .on('mouseenter', 'li', $.proxy(this.mouseenter, this))
	        .on('mouseleave', 'li', $.proxy(this.mouseleave, this));
	    }

	  , eventSupported: function(eventName) {
	      var isSupported = eventName in this.$element;
	      if (!isSupported) {
	        this.$element.setAttribute(eventName, 'return;');
	        isSupported = typeof this.$element[eventName] === 'function';
	      }
	      return isSupported;
	    }

	  , move: function (e) {
	      if (!this.shown) return;

	      switch(e.keyCode) {
	        case 9: // tab
	        case 13: // enter
	        case 27: // escape
	          e.preventDefault();
	          break;

	        case 38: // up arrow
	          e.preventDefault();
	          this.prev();
	          break;

	        case 40: // down arrow
	          e.preventDefault();
	          this.next();
	          break;
	      }

	      e.stopPropagation();
	    }

	  , keydown: function (e) {
	      this.suppressKeyPressRepeat = ~$.inArray(e.keyCode, [40,38,9,13,27]);
	      this.move(e);
	    }

	  , keypress: function (e) {
	      if (this.suppressKeyPressRepeat) return;
	      this.move(e);
	    }

	  , keyup: function (e) {
	      switch(e.keyCode) {
	        case 40: // down arrow
	        case 38: // up arrow
	        case 16: // shift
	        case 17: // ctrl
	        case 18: // alt
	          break;

	        case 9: // tab
	        case 13: // enter
	          if (!this.shown) return
	          this.select();
	          break;

	        case 27: // escape
	          if (!this.shown) return
	          this.hide();
	          break;

	        default:
	          this.lookup();
	      }

	      e.stopPropagation();
	      e.preventDefault();
	  }

	  , focus: function (e) {
	      this.focused = true;
	    }

	  , blur: function (e) {
	      this.focused = false;
	      if (!this.mousedover && this.shown) this.hide();
	    }

	  , click: function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      this.select();
	      this.$element.focus();
	    }

	  , mouseenter: function (e) {
	      this.mousedover = true;
	      this.$menu.find('.active').removeClass('active');
	      $(e.currentTarget).addClass('active');
	    }

	  , mouseleave: function (e) {
	      this.mousedover = false;
	      if (!this.focused && this.shown) this.hide();
	    }

	  };


	  /* TYPEAHEAD PLUGIN DEFINITION
	   * =========================== */

	  var old = $.fn.autocomplete;

	  $.fn.autocomplete = function (option) {
	    return this.each(function () {
	      var $this = $(this)
	        , data = $this.data('koala.autocomplete')
	        , options = typeof option == 'object' && option;
	      if (!data) $this.data('koala.autocomplete', (data = new AutoComplete(this, options)));
	      if (typeof option == 'string') data[option]();
	    });
	  };

	  $.fn.autocomplete.defaults = {
		/**
		 * 数据来源， 数组的形式
		 * @property source
		 * @type Array
		 * @default []
		 */
	    source: [],
	    /**
		 * 下拉菜单中显示的最大的条目数
		 * @property items
		 * @type Integer
		 * @default 8
		 */
	    items: 8, 
	    /**
		 * 下拉框菜单的默认html，不建议更改
		 * @property menu
		 * @type String
		 * @default '<ul class="autocomplete dropdown-menu"></ul>',
		 */
	    menu: '<ul class="autocomplete dropdown-menu"></ul>', 
	    /**
		 * 菜单menu子项，不建议更改
		 * @property item
		 * @type String
		 * @default '<li><a href="#"></a></li>'
		 */
	    item: '<li><a href="#"></a></li>', 
	    /**
		 * 触发提示所需的最小字符个数
		 * @property minLength
		 * @type Integer
		 * @default 1
		 */
	    minLength: 1
	  };

	  $.fn.autocomplete.Constructor = AutoComplete;


	 /* TYPEAHEAD NO CONFLICT
	  * =================== */

	  $.fn.autocomplete.noConflict = function () {
	    $.fn.autocomplete = old;
	    return this;
	  };


	 /* TYPEAHEAD DATA-API
	  * ================== */

	  $(document).on('focus.autocomplete.data-api', '[data-provide="autocomplete"]', function (e) {
	    var $this = $(this);
	    if ($this.data('koala.autocomplete')) return
	    console.info("$this.data() = " + $this.data());
	    $this.autocomplete($this.data());
	  });

}(window.jQuery);