/**
 * @author zjh
 */
+function ($) {
	"use strict";
	var that = null;
	
	/**
	 * 左则菜单组件，只限于两级菜单，另一版的Accordion，暂不支持数据驱动
	 * @class SideBar
	 * @constructor
	 * @example
	 *	<div class="sidebar">
	 *  	<ul data-role="nav">
 	 *			<li class="has_sub"><a href="#"><i class="icon-edit"></i>基础组件<span class="pull-right"><i class="icon-chevron-right"></i></span></a> 
 	 *              <ul>
 	 *                	<li><a href="javascript:void(0)" load-html="pages/accordion/basic.html">手风琴</a></li>
 	 *					<li><a href="javascript:void(0)" load-html="checkBox.html">复选框</a></li>
 	 *					<li><a href="javascript:void(0)" load-html="pages/dropdown/basic.html">下拉菜单</a></li>
 	 *					<li><a href="javascript:void(0)" load-html="radio.html">单选框</a></li>
 	 *              </ul> 
 	 *    		</li>
 	 *     		<li class="has_sub"><a href="#"><i class="icon-list-alt"></i>组合框 <span class="pull-right"><i class="icon-chevron-right"></i></span></a>
 	 *              <ul>
 	 *                <li><a href="javascript:void(0)" load-html="accordion.html">基本功能</a></li>
 	 *                <li><a href="javascript:void(0)" load-html="accordion.html">多选支持</a></li>
 	 *                <li><a href="javascript:void(0)" load-html="accordion.html">异步加载</a></li>
 	 *                <li><a href="javascript:void(0)" load-html="accordion.html">下拉表格</a></li>
 	 *              </ul> 
 	 *	      	</li>
 	 *	      	<li class="has_sub"><a href="#"> <i class="icon-list-alt"></i>树形组件 <span class="pull-right"><i class="icon-chevron-right"></i></span></a>
 	 *              <ul>
 	 *                <li><a href="javascript:void(0)" load-html="accordion.html">基本功能</a></li>
 	 *                <li><a href="javascript:void(0)" load-html="accordion.html">带复选框</a></li>
 	 *                <li><a href="javascript:void(0)" load-html="accordion.html">异步加载</a></li>
 	 *                <li><a href="javascript:void(0)" load-html="accordion.html">可拖拽节点</a></li>
 	 *                <li><a href="javascript:void(0)" load-html="accordion.html">自定义节点图标</a></li>
 	 *                <li><a href="javascript:void(0)" load-html="accordion.html">可编辑节点</a></li>
 	 *                <li><a href="javascript:void(0)" load-html="accordion.html">节点事件</a></li>
 	 *                <li><a href="javascript:void(0)" load-html="accordion.html">节点右键菜单</a></li>
 	 *              </ul> 
 	 *	      	</li>
 	 *		</ul>
 	 *	</div>
	 */
	var SideBar = function(element,options) {
		this.$element = $(element);
		this.options = $.extend({}, $.fn.sidebar.defaults, options);
		that = this;
//		this._init();
//		this.$element.on('click','ul > li > a',$.proxy(function(ev){this.itemClick(ev.currentTarget);},this));
	};
	
	SideBar.prototype = {
			
		constructor:SideBar,
		
		/**
		 * 切换方法
		 * @method toggle
		 */
		toggle:function(el,ev){
			var $this = $(el);
			if($this.parent().hasClass("has_sub")) {
		        ev.preventDefault();
			}   
			
			if(!$this.hasClass("subdrop")) {
				// hide any open menus and remove all other classes
				$("[data-role=nav] li ul").slideUp(350);
				$("[data-role=nav] li a").removeClass("subdrop");
				
				// open our new menu and add the open class
				$this.next("ul").slideDown(350);
				$this.addClass("subdrop");
			}else if($this.hasClass("subdrop")) {
				$this.removeClass("subdrop");
				$this.next("ul").slideUp(350);
			} 
		},

	};
	
	SideBar.DEFAULTS = {
//		/**
//		 * 点击其他时是否切换
//		 * @property isToggle
//		 * @type Boolean
//		 * @default true
//		 */
//		isToggle: true,
//		/**
//		 * 切换时，显示的动画动作
//		 * @property animate
//		 * @type String("fast","normal","slow","close")
//		 * @default "close"
//		 */
//		animate: "close",
//		/**
//		 * 菜单数据
//		 * @property data
//		 * @type json对象
//		 * @default null
//		 */
//		data:null,
	};
	

  	var old = $.fn.sidebar;

  	$.fn.sidebar = function (option,target,event) {
  		return this.each(function () {
			var $this   = $(this);
			var data    = $this.data('koala.sidebar');
			var options = $.extend({}, SideBar.DEFAULTS, $this.data(), typeof option == 'object' && option);
	
			if (!data) $this.data('koala.sidebar', (data = new SideBar(this, options)));
			if (typeof option == 'string') data[option](target,event);
  		});
  	};

  	$.fn.sidebar.Constructor = SideBar;


  // COLLAPSE NO CONFLICT
  // ====================

  	$.fn.sidebar.noConflict = function () {
  		$.fn.sidebar = old;
  		return this;
	};

	//Click
	$(document).on('click.koala.sidebar.data-api', '[data-role=nav] > li > a', function (e) {
	    var $this = $(this);
	    var data = $this.data('koala.sidebar');
//	    var option  = data ? 'toggle' : $this.data();
	    $this.sidebar('toggle',this,e);
	});
	
	
}(window.jQuery);