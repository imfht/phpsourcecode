/**
 * @author zjh
 */
+function ($) { "use strict";

  // COLLAPSE PUBLIC CLASS DEFINITION
  // ================================
	
	/**
	 * 原版的Bootstrap的Collapse
	 * @class Accordion
	 * @constructor
	 * @example
	 * 	<div id="accordion" class="panel-group">
	 *	  <div class="panel panel-default">
	 *	    <div class="panel-heading">
	 *	      <h4 class="panel-title">
	 *	        <a href="#collapseOne" data-parent="#accordion" data-toggle="collapse" class="collapsed">
	 *	          菜单1
	 *	        </a>
	 *	      </h4>
	 *	    </div>
	 *	    <div class="panel-collapse collapse" id="collapseOne" style="height: 0px;">
	 *	      <div class="panel-body">
	 *	        折叠内容1
	 *	      </div>
	 *	    </div>
	 *	  </div>
	 *	  <div class="panel panel-default">
	 *	    <div class="panel-heading">
	 *	      <h4 class="panel-title">
	 *	        <a href="#collapseTwo" data-parent="#accordion" data-toggle="collapse" class="collapsed">
	 *	          菜单2
	 *	        </a>
	 *	      </h4>
	 *	    </div>
	 *	    <div class="panel-collapse collapse" id="collapseTwo" style="height: 0px;">
	 *	      <div class="panel-body">
	 *	        折叠内容2
	 *	      </div>
	 *	    </div>
	 *	  </div>
	 *	  <div class="panel panel-default">
	 *	    <div class="panel-heading">
	 *	      <h4 class="panel-title">
	 *	        <a href="#collapseThree" data-parent="#accordion" data-toggle="collapse" class="">
	 *	          菜单3
	 *	        </a>
	 *	      </h4>
	 *	    </div>
	 *	    <div class="panel-collapse collapse in" id="collapseThree" style="height: auto;">
	 *	      <div class="panel-body">
	 *	        折叠内容3
	 *	      </div>
	 *	    </div>
	 *	  </div>
	 *	</div>
	 */
	var Accordion = function (element, options) {
	  this.$element      = $(element);
	  this.options       = $.extend({}, Accordion.DEFAULTS, options);
	  this.transitioning = null;

	  if (this.options.parent) this.$parent = $(this.options.parent);
	  if (this.options.toggle) this.toggle();
	};

	Accordion.DEFAULTS = {
		/**
		 * 点击其他时是否切换
		 * @property toggle
		 * @type Boolean
		 * @default true
		 */
		toggle: true						//是否切换
	};

  	Accordion.prototype = {
  		
  		/**
  		 * @private
  		 * @method dimemsion
  		 * @return {String} 'width' or 'height'
  		 */
  		dimension:function(){
  			var hasWidth = this.$element.hasClass('width');
  		    return hasWidth ? 'width' : 'height';
  		},
  		
  		/**
  		 * 显示
  		 * @method show
  		 */
  		show:function(){
  			if (this.transitioning || this.$element.hasClass('in')) return

  		    var startEvent = $.Event('show.koala.accordion');
  		    this.$element.trigger(startEvent);
  		    if (startEvent.isDefaultPrevented()) return;

  		    var actives = this.$parent && this.$parent.find('> .panel > .in');

  		    if (actives && actives.length) {
  		      var hasData = actives.data('koala.accordion');
  		      if (hasData && hasData.transitioning) return;
  		      actives.accordion('hide');
  		      hasData || actives.data('koala.accordion', null);
  		    }

  		    var dimension = this.dimension();

  		    this.$element
  		      .removeClass('accordion')
  		      .addClass('collapsing')
  		      [dimension](0);

  		    this.transitioning = 1;

  		    var complete = function () {
  		      this.$element
  		        .removeClass('collapsing')
  		        .addClass('in')
  		        [dimension]('auto');
  		      this.transitioning = 0;
  		      this.$element.trigger('shown.koala.accordion');
  		    };

  		    if (!$.support.transition) return complete.call(this);

  		    var scrollSize = $.camelCase(['scroll', dimension].join('-'));

  		    this.$element
  		      .one($.support.transition.end, $.proxy(complete, this))
  		      .emulateTransitionEnd(350)
  		      [dimension](this.$element[0][scrollSize]);
  		},
  		
  		/**
  		 * 隐藏
  		 * @method hide
  		 */
  		hide:function(){
  			if (this.transitioning || !this.$element.hasClass('in')) return

  		    var startEvent = $.Event('hide.koala.accordion');
  		    this.$element.trigger(startEvent);
  		    if (startEvent.isDefaultPrevented()) return;

  		    var dimension = this.dimension();

  		    this.$element
  		      [dimension](this.$element[dimension]())
  		      [0].offsetHeight;

  		    this.$element
  		      .addClass('collapsing')
  		      .removeClass('accordion')
  		      .removeClass('in');

  		    this.transitioning = 1;

  		    var complete = function () {
  		      this.transitioning = 0;
  		      this.$element
  		        .trigger('hidden.koala.accordion')
  		        .removeClass('collapsing')
  		        .addClass('accordion');
  		    };

  		    if (!$.support.transition) return complete.call(this);

  		    this.$element
  		      [dimension](0)
  		      .one($.support.transition.end, $.proxy(complete, this))
  		      .emulateTransitionEnd(350);
  		},
  		
  		/**
  		 * 根据class切换显示/隐藏
  		 * @method toggle
  		 */
  		toggle:function(){
  			this[this.$element.hasClass('in') ? 'hide' : 'show']();
  		}
  	};

  // COLLAPSE PLUGIN DEFINITION
  // ==========================

  	var old = $.fn.accordion;

  	$.fn.accordion = function (option) {
  		return this.each(function () {
			var $this   = $(this);
			var data    = $this.data('koala.accordion');
			var options = $.extend({}, Accordion.DEFAULTS, $this.data(), typeof option == 'object' && option);
	
			if (!data) $this.data('koala.accordion', (data = new Accordion(this, options)));
			if (typeof option == 'string') data[option]();
  		});
  	};

  	$.fn.accordion.Constructor = Accordion;


  // COLLAPSE NO CONFLICT
  // ====================

  	$.fn.accordion.noConflict = function () {
  		$.fn.accordion = old;
  		return this;
	};


  // COLLAPSE DATA-API
  // =================

	$(document).on('click.koala.accordion.data-api', '[data-toggle=accordion]', function (e) {
		var $this   = $(this), href;
		var target  = $this.attr('data-target')|| e.preventDefault()
        	|| (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, ''); //strip for ie7
    	var $target = $(target);
	    var data    = $target.data('koala.accordion');
	    var option  = data ? 'toggle' : $this.data();
	    var parent  = $this.attr('data-parent');
	    var $parent = parent && $(parent);

	    if (!data || !data.transitioning) {
	      if ($parent) $parent.find('[data-toggle=accordion][data-parent="' + parent + '"]').not($this).addClass('collapsed');
	      $this[$target.hasClass('in') ? 'addClass' : 'removeClass']('collapsed');
	    };

	    $target.accordion(option);
	});

}(window.jQuery);