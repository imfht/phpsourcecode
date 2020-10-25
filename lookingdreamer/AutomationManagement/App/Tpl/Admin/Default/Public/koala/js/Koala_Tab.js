/**
 * @author zjh
 */
+function($) {
	"use strict";

	/**
	 * KoalaUI的Tab组件，基于Bootstrap Tab改良，增加Close
	 * @class Tab
	 * @constructor
	 */
	var Tab = function(element) {
		this.element = $(element);
	};

	Tab.prototype = {
		constructor : Tab,
		
		/**
		 * 显示某一个Tab的方法
		 * @method show
		 * @example
		 * 	$('#myTab a').click(function (e) {
			  e.preventDefault()
			  $(this).tab('show')
			})
		 */
		show : function() {
			var $this = this.element;
			var $ul = $this.closest('ul:not(.dropdown-menu)');
			var selector = $this.attr('data-target');

			if (!selector) {
				selector = $this.attr('href');
				selector = selector && selector.replace(/.*(?=#[^\s]*$)/, ''); // strip
			}

			if ($this.parent('li').hasClass('active'))
				return

			var previous = $ul.find('.active:last a')[0];
			var e = $.Event('show.koala.tab', {
				relatedTarget : previous
			});

			$this.trigger(e);

			if (e.isDefaultPrevented())
				return
			var $target = $(selector);
			this.activate($this.parent('li'), $ul);
			this.activate($target, $target.parent(), function() {
				$this.trigger({
					type : 'shown.koala.tab',
					relatedTarget : previous
				});
			});
		},
		
		/**
		 * 新增的关闭方法，原先的Bootstrap没有关闭功能
		 * @method close
		 * @example
		 * 	$("#btnCloseRM").click(function(){
				$("[href='#role_manage']").tab("show").tab("close");
			})
		 */
		close:function(){
			var $this = this.element;
			var $thisLi = $this.parent();				//获得li
			//防止鼠标未移到对应的标签，让其未激活就去掉，必须要先激活状态才能去掉
			if(!$thisLi.hasClass("active")){
				return ;
			}
			var $thisPane = $($this.attr("href"));
			var $nextLi = $thisLi.next();
			//如果关掉的是最后一个，第一个li为激活
			if(!$nextLi.length){
				$nextLi = $thisLi.prev();
				if(!$nextLi.length){
					$nextLi = $thisLi.parent().find(":first-child");
				}
			}
			var $nextPane = $($nextLi.children("a").attr("href"));
			
			$thisLi.remove();
			$thisPane.remove();
			$nextLi.addClass("active");
			$nextPane.addClass("active").addClass("in");
//			console.info($this.html());
//			console.info($thisLi.html());
//			console.info($thisPane.html());
//			console.info($nextLi.html());
//			console.info($nextPane.html());
		},
	};

	Tab.prototype.activate = function(element, container, callback) {
		var $active = container.find('> .active');
		var transition = callback && $.support.transition
				&& $active.hasClass('fade');

		function next() {
			$active.removeClass('active').find('> .dropdown-menu > .active')
					.removeClass('active');

			element.addClass('active');

			if (transition) {
				element[0].offsetWidth;
				element.addClass('in');
			} else {
				element.removeClass('fade');
			}

			if (element.parent('.dropdown-menu')) {
				element.closest('li.dropdown').addClass('active');
			}

			callback && callback();
		}

		transition ? $active.one($.support.transition.end, next)
				.emulateTransitionEnd(150) : next();

		$active.removeClass('in');
	};

	// TAB PLUGIN DEFINITION
	// =====================

	var old = $.fn.tab;

	$.fn.tab = function(option) {
		return this.each(function() {
			var $this = $(this);
			var data = $this.data('koala.tab');

			if (!data)
				$this.data('koala.tab', (data = new Tab(this)));
			if (typeof option == 'string')
				data[option]();
		});
	};

	$.fn.tab.Constructor = Tab;

	// TAB NO CONFLICT
	// ===============

	$.fn.tab.noConflict = function() {
		$.fn.tab = old;
		return this;
	};

	// TAB DATA-API
	// ============

	$(document).on('click.koala.tab.data-api','[data-toggle="tab"], [data-toggle="pill"]', function(e) {
		e.preventDefault();
		$(this).tab('show');
	});

	$(document).on('click.koala.tab.data-api','[data-toggle="tab"] .tab-close', function(e) {
		e.preventDefault();
		$(this).parent().tab('close');
	});

}(window.jQuery);