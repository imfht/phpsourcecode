/**
 * @author zjh
 */
+function ($) {

	  "use strict"; 
	  
	  /**
	   * 按钮组件,基于原生的Boostrap_Button
	   * @class Button
	   * @constructor
	   */
	  var Button = function (element, options) {
		  this.$element = $(element);
		  this.options = $.extend({}, $.fn.button.defaults, options);
	  };

	  Button.prototype = {
		  
		  constructor:Button,
		  
		  /**
		   * 设置按钮状态
		   * @method setState
		   * @param state:可设置为loading（载入中状态)，reset(重设状态)
		   * @return
		   */
		  setState : function (state) {
		    var d = 'disabled'
		      , $el = this.$element
		      , data = $el.data()
		      , val = $el.is('input') ? 'val' : 'html';

		    state = state + 'Text';
		    data.resetText || $el.data('resetText', $el[val]());

		    $el[val](data[state] || this.options[state]);

		    // push to event loop to allow forms to submit
		    setTimeout(function () {
		      state == 'loadingText' ?
		        $el.addClass(d).attr(d, d) :
		        $el.removeClass(d).removeAttr(d);
		    }, 0);
		  },
		  
		  /**
		   * 切换激活和非激活状态
		   * @method toggle
		   * @param
		   * @return
		   */
		  toggle : function () {
		    var $parent = this.$element.closest('[data-toggle="buttons"]');

		    $parent && $parent
		      .find('.active')
		      .removeClass('active');

		    this.$element.toggleClass('active');
		  }
	  };



	 /* BUTTON PLUGIN DEFINITION
	  * ======================== */

	  var old = $.fn.button;

	  $.fn.button = function (option) {
	    return this.each(function () {
	      var $this = $(this)
	        , data = $this.data('koala.button')
	        , options = typeof option == 'object' && option;
	      if (!data) $this.data('koala.button', (data = new Button(this, options)));
	      if (option == 'toggle') data.toggle();
	      else if (option) data.setState(option);
	    });
	  };

	  $.fn.button.defaults = {
	    loadingText: 'loading...'
	  };

	  $.fn.button.Constructor = Button;


	 /* BUTTON NO CONFLICT
	  * ================== */

	  $.fn.button.noConflict = function () {
	    $.fn.button = old;
	    return this;
	  };


	 /* BUTTON DATA-API
	  * =============== */

	  $(document).on('click.koala.button.data-api', '[data-toggle^=button]', function (e) {
	    var $btn = $(e.target);
	    if (!$btn.hasClass('btn')) $btn = $btn.closest('.btn');
	    var data    = $btn.data('koala.button');
	    var option  = data ? 'toggle' : $btn.data();
	    $btn.button(option);
	  });
}(window.jQuery);