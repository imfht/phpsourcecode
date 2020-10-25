/**
 * @author zjh
 */
+function($) {

	"use strict";

	// Wizard PUBLIC CLASS DEFINITION
	// ==============================

	/**
	 * 向导组件
	 * @class Wizard
	 * @constructor
	 * @use Koala_Tab
	 * @example
	 * 	js代码
	 * 	$(function(){
			$(".wizard").wizard();
		});
	 * @example
	 * 	html代码
	 * 	<div id="main">
			<form action="#" method="post">
				<div class="wizard">
					<ul class="nav nav-tabs">
					  <li class="active"><a href="#">第一步</a></li>
					  <li><a href="#" >第二步</a></li>
					  <li><a href="#" >第三步</a></li>
					</ul>
			
					<div class="items">
						<div class="page">
			               <div>第一步内容</div>
			               <br/><br/><br/><br/><br/><br/><br/><br/>
			               <div class="btn_nav">
			                 	<button data-toggle="next" class="btn right btn-default">下一步&raquo;</button>
			                 	<button data-toggle="last" class="btn right btn-default">最后一步&raquo;</button>
			                 	<button data-toggle="goPage" class="btn right btn-success" id="gofirst" pageNo="10" pageId="second-step">第十页</button>
			               </div>
			            </div>
						<div class="page">
			               <div>这个是第二页</div>
			               <br/><br/><br/><br/><br/><br/><br/><br/>
			               <div class="btn_nav">
			                   <button data-toggle="prev" class="right btn btn-default" >&laquo;上一步</button>
			               	   <button data-toggle="next" class="right btn btn-default" >下一步&raquo;</button>
			               	   <button data-toggle="goPage" class="right btn btn-success" id="gofirst" pageNo="10" pageId="second-step">第十页</button>
			               </div>
			            </div>
						<div class="page">
			               <div>最后一页</div>
			               <br/><br/><br/><br/><br/><br/><br/><br/>
			               <div class="btn_nav">
			               	  <button data-toggle="prev" class="right btn btn-default" >&laquo;上一步</button>
			               	  <button data-toggle="next" class="right btn btn-default" >&laquo;下一步</button>
			               	  <button data-toggle="first" class="right btn btn-default" >&laquo;首页</button>
			               	  
			               </div>
			            </div>
					</div>
				</div>
			</form>
		</div>
	 */
	var Wizard = function(element, options) {
		this.init(element, options);
	};

	Wizard.DEFAULTS = {
		/**
		 * 向导页签TAB的选择器
		 * @property ul
		 * @type String
		 * @default '.nav-tabs'
		 */
		ul : '.nav-tabs',
		/**
		 * 向导内容区域所在的选择器
		 * @property items
		 * @type String
		 * @default '.items'
		 */
		items : '.items',
		/**
		 * items下每一个内容div的选择器
		 * @property itempage
		 * @type String
		 * @default '.page'
		 */
		itempage : '.page',
		/**
		 * 动画切换方式
		 * @property swing
		 * @type String
		 * @default 'swing'
		 */
		swing : 'swing',
		/**
		 * 下一步的选择器
		 * @property next
		 * @type String
		 * @default '[data-toggle="next"]'
		 */
		next : '[data-toggle="next"]',
		/**
		 * 上一步的选择器
		 * @property prev
		 * @type String
		 * @default '[data-toggle="prev"]'
		 */
		prev : '[data-toggle="prev"]',
		/**
		 * 跳转页面的选择器
		 * @property goPage
		 * @type String
		 * @default '[data-toggle="goPage"]'
		 */
		goPage : '[data-toggle="goPage"]',
		/**
		 * 直接跳到最后
		 * @property first
		 * @type String
		 * @default '[data-toggle="first"]'
		 */
		first : '[data-toggle="first"]',
		/**
		 * 直接跳到最后
		 * @property last
		 * @type String
		 * @default '[data-toggle="last"]'
		 */
		last : '[data-toggle="last"]',
		/**
		 * 动画显示的速度
		 * @property speed
		 * @type Integer
		 * @default 400（毫秒）
		 */
		speed : 400
	};

	Wizard.prototype = {
		constructor:Wizard,
		init : function(element, options) {
			this.$element = $(element);
			this.options = $.extend({}, Wizard.DEFAULTS, options);
			this.ul = this.$element.find(this.options.ul);
			this.items = this.$element.find(this.options.items);
			this.items.find(this.options.next).on('click.bs.wizard',$.proxy(function(e) {
				e.preventDefault();
				this.next(e);
			}, this));
			this.items.find(this.options.prev).on('click.bs.wizard',$.proxy(function(e) {
				e.preventDefault();
				this.prev(e);
			}, this));
			this.items.find(this.options.goPage).on('click.bs.wizard',$.proxy(function(e) {
				e.preventDefault();
				this.goPage(e);
			}, this));
			this.items.find(this.options.first).on('click.bs.wizard',$.proxy(function(e) {
				e && e.preventDefault();
				this.first(e);
			}, this));
			this.items.find(this.options.last).on('click.bs.wizard',$.proxy(function(e) {
				e && e.preventDefault();
				this.last(e);
			}, this));
		},
		
		/**
		 * 下一步
		 * @method next
		 * @param event
		 */
		next : function(e) {
			var that = this;
			var target = $(e.currentTarget);
			var page = target.parent().parent();
			var n = {
				left : -(page.position().left + page.outerWidth())
			};
			that.items.animate(n, that.options.speed, that.options.swing,function() {
				that.ul.find('li').eq(page.index() + 1).find('a').tab('show');
			});
		},
		
		/**
		 * 上一步
		 * @method prev
		 * @param event
		 */
		prev : function(e) {
			var that = this;
			var target = $(e.currentTarget);
			var page = target.parent().parent();
			var n = {
				left : page.outerWidth() - page.position().left
			};
			that.items.animate(n, that.options.speed, that.options.swing,function() {
				that.ul.find('li').eq(page.index() - 1).find('a').tab('show');
			});
		},
		
		/**
		 * 跳到第几步
		 * @method goPage
		 * @param event
		 * @param {Integer} value
		 */
		goPage : function(e,value) {
			var that = this;
			var total = $(that.ul).children("li").length;
			var $target = $(e.currentTarget);
			var currentPageNo = that._getPageIndex($target);
			var pageNo = $target.attr("pageNo")||value;
			var pageId = $target.attr("pageId");
			// 获取相对位置
			var pos = 0;

			var page = $target.parent().parent();
			if (pageNo) {
				if(pageNo > total){
					pageNo = total;
				}
				pos = pageNo -1;
			} else if (pageId) {
				pos = that.ul.find('li').index($('li[id=' + pageId + ']'));
			}
			//这条计算逻辑真TMD复杂，最终搞定
			var n = {left : (page.outerWidth()) * (currentPageNo-pos)-page.position().left};
			that.items.animate(n, that.options.speed, that.options.swing,function() {
				if (pageNo) {
					that.ul.find('li').eq(pageNo - 1).find('a').tab('show');
				} else if (pageId) {
					that.ul.find('li[id=' + pageId + ']').find('a').tab('show');
				}
			});
		},
		
		/**
		 * 第一步
		 * @method first
		 * @param event
		 */
		first:function(e){
			this.goPage(e,1);
		},
		
		/**
		 * 最后一步
		 * @method last
		 * @param event
		 */
		last:function(e){
			this.goPage(e, this._getPageCount());
		},
		
		_getPageIndex:function($target){
			var $page = $target.parents(this.options.itempage);
			var index = $(this.options.itempage).index($page);
			return index;
		},
		
		_getPageCount:function(){
			var $item = $(this.options.items);
			var count = $item.children(this.options.itempage).length;
			return count;
		}
	};

	// Wizard PLUGIN DEFINITION
	// ========================

	var old = $.fn.wizard;

	$.fn.wizard = function(option) {
		return this.each(function() {
			var $this = $(this);
			var data = $this.data('bs.wizard');
			var options = typeof option == 'object' && option;

			if (!data)
				$this.data('bs.wizard', (data = new Wizard(this, options)));
		});
	};

	$.fn.wizard.Constructor = Wizard;

	// BUTTON NO CONFLICT
	// ==================

	$.fn.wizard.noConflict = function() {
		$.fn.wizard = old;
		return this;
	};

}(window.jQuery);
