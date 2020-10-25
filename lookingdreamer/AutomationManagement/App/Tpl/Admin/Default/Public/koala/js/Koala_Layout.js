/**
 * @author zjh
 */
+function ($) {
	"use strict";
	//构造函数
	/**
	 * 布局组件，目前暂时限于实现布局，至于CSS还没调好
	 * @class
	 */
	var Layout = function (element, options) {
		//4
		this.$element = $(element);
		this.options = $.extend({}, $.fn.layout.defaults, options);
		//事件列表
		this.render(options);
	};
	
	//原型定义，定义构造函数和其他方法
	Layout.prototype = {
		/**
		 * @constructor
		 */
		constructor:Layout,
		//渲染布局页面
		render:function(options){
			var $this = this.$element;
			var layout = options.layout;
			if(!layout){
				console.info("no layout!");
				return false;
			}
			
			var methods = {
				"default":"Default",
				"west_expanded":"WestExpanded",
				"west_to_bottom":"WestToBottom",
				"west_to_top":"WestToTop"
			};
			
			this["_render" + methods[layout]]($this);
		}
		
		//默认布局渲染
		,_renderDefault:function($this){
			
			var $north = $this.children("div[region='north']");
			var $west = $this.children("div[region='west']");
			var $center = $this.children("div[region='center']");
			var $east = $this.children("div[region='east']");
			var $south = $this.children("div[region='south']");
			var $row1 = $("<div></div>").addClass("row");
			var $row2 = $("<div></div>").addClass("row");
			var $row3 = $("<div></div>").addClass("row");
			//渲染north
			if($north.length){
				$north.addClass("col-xs-12").height("50");
				$row1.append($north);
			}
			//渲染west
			if($west.length){
				$west.addClass("col-xs-2").height(this.options.centerHeight);
			}
			//渲染center
			if($center.length){
				$center.addClass("col-xs-8").height(this.options.centerHeight);
			}
			//渲染east
			if($east.length){
				$east.addClass("col-xs-2").height(this.options.centerHeight);
			}
			$row2.append($west).append($center).append($east);
			//渲染south
			if($south.length){
				$south.addClass("col-xs-12").height("50");
				$row3.append($south);
			}
			
			$this.append($row1).append($row2).append($row3);
			
			//加空div条件判断
			if(!$west.length){
				$center.removeClass("col-xs-8").addClass("col-xs-10");
			}
			if(!$north.length){
				$row2.children("div").height($center.height()+50);
			}
			if(!$east.length){
				//如果右边为空，直接中间填充
				if($center.hasClass("col-xs-8")){
					$center.removeClass("col-xs-8").addClass("col-xs-10");
				}else if($center.hasClass("col-xs-10")){
					$center.removeClass("col-xs-10").addClass("col-xs-12");
				}
				
			}
			if(!$south.length){
				$row2.children("div").height($center.height()+50);
			}
		}
		
		//左边全扩展布局渲染
		,_renderWestExpanded:function($this){
			var $row1 = $("<div></div>").addClass("row");
			var $row2 = $("<div></div>").addClass("row");
			var $row3 = $("<div></div>").addClass("row");
			var $div = $("<div></div>").addClass("col-xs-10").height(this.options.maxHeight);
			var $north = $this.children("div[region='north']");
			var $west = $this.children("div[region='west']");
			var $center = $this.children("div[region='center']");
			var $east = $this.children("div[region='east']");
			var $south = $this.children("div[region='south']");
			//先清空，再逐个按照规定顺序插入
			//加入左边的
			$this.empty();
			if($west.length){
				$west.addClass("col-xs-2").height(this.options.maxHeight);
				$this.append($west);
			}
			//加入右边的
			if($north.length){
				$north.height("50");
				$row1.append($north);
				$div.append($row1);
			}
			if($center.length){
				$center.addClass("col-xs-10").height(this.options.centerHeight);
				$row2.append($center);
			}
			if($east.length){
				$east.addClass("col-xs-2").height(this.options.centerHeight);
				$row2.append($east);
			}
			
			$div.append($row2);
			if($south.length){
				$south.height("50");
				$row3.append($south);
				$div.append($row3);
			}
			//加入到上一级div
			$this.append($div);
			//加空div条件判断
			if(!$west.length){
				$div.removeClass("col-xs-10").addClass("col-xs-12");
			}
			if(!$north.length){
				$row2.children("div").height($center.height()+50);
			}
			if(!$east.length){
				$center.removeClass("col-xs-10").addClass("col-xs-12");
			}
			if(!$south.length){
				$row2.children("div").height($center.height()+50);
			}
		}
		
		//左边菜单延伸到底部
		,_renderWestToBottom:function($this){
			var $north = $this.children("div[region='north']");
			var $west = $this.children("div[region='west']");
			var $center = $this.children("div[region='center']");
			var $east = $this.children("div[region='east']");
			var $south = $this.children("div[region='south']");
			
			var $row1 = $("<div></div>").addClass("row");
			var $row2 = $("<div></div>").addClass("row");
			var $row3 = $("<div></div>").addClass("row");
			var $row4 = $("<div></div>").addClass("row");
			var $div = $("<div></div>").addClass("col-xs-10").height(this.options.centerHeight + 50);
			
			$this.empty();
			//渲染north
			if($north.length){
				$north.addClass("col-xs-12").height("50");
				$row1.append($north);
			}
			//1、row1包含west + div
			//2、div包含row2 + south
			//3、row2包含center + east
			//渲染west
			if($west.length){
				$west.addClass("col-xs-2").height(this.options.centerHeight + 50);
				$row2.append($west);
			}
			//渲染center
			if($center.length){
				$center.addClass("col-xs-10").height(this.options.centerHeight);
				$row3.append($center);
			}
			//渲染east
			if($east.length){
				$east.addClass("col-xs-2").height(this.options.centerHeight);
				$row3.append($east);
			}
			
			//渲染south
			if($south.length){
				$south.height("50");
				$row4.append($south);
			}
			
			//逐层由内到外追加
			$div.append($row3).append($row4);
			$row2.append($west).append($div);
			$this.append($row1).append($row2);
			
			//加空div条件判断
			if(!$west.length){
				$div.removeClass("col-xs-10").addClass("col-xs-12");
			}
			if(!$north.length){
				
			}
			if(!$east.length){
				//如果右边为空，直接中间填充
				$center.removeClass("col-xs-10").addClass("col-xs-12");
			}
			if(!$south.length){
				$row3.children("div").height($center.height()+50);
			}
		}
		
		//渲染左边延伸到顶部的布局
		,_renderWestToTop:function($this){
			var $north = $this.children("div[region='north']");
			var $west = $this.children("div[region='west']");
			var $center = $this.children("div[region='center']");
			var $east = $this.children("div[region='east']");
			var $south = $this.children("div[region='south']");
			var $row1=$("<div></div>").addClass("row");
			var $row2=$("<div></div>").addClass("row");
			var $row3=$("<div></div>").addClass("row");
			var $row4=$("<div></div>").addClass("row");
			var $div = $("<div></div>").addClass("col-xs-10");
			//先清空，再逐个按照规定顺序插入
			//加入左边的
			$this.empty();
			if($west.length){
				$west.addClass("col-xs-2").height(this.options.centerHeight);
				$row1.append($west);
			}
			//加入右边的
			if($north.length){
				$north.height("50");
				$row2.append($north);
				$div.append($row2);
			}
			if($center.length){
				$center.addClass("col-xs-10").height(this.options.centerHeight-50);
				$row3.append($center);
			}
			if($east.length){
				$east.addClass("col-xs-2").height(this.options.centerHeight-50);
				$row3.append($east);
			}
			$div.append($row3);
			$row1.append($div);
			
			$this.append($row1);
			//底部
			if($south.length){
				$south.addClass("col-xs-12").height("50");
				$row4.append($south);
				$this.append($row4);
			}
			
			//加空div条件判断
			if(!$west.length){
				$div.removeClass("col-xs-10").addClass("col-xs-12");
			}
			if(!$north.length){
				$row3.children("div").height($center.height()+50);
			}
			if(!$east.length){
				//如果右边为空，直接中间填充
				$center.removeClass("col-xs-10").addClass("col-xs-12");
			}
			if(!$south.length){
				
			}
			
		}
	};
	
	//加入到jQuery插件
	$.fn.layout = function(option,value){
		//3
		var methodReturn;

		var $set = this.each(function () {
			var $this = $(this);
			var data = $this.data('koala.layout');
			var options = typeof option === 'object' && option;

			if (!data) $this.data('koala.layout', (data = new Layout(this, options)));
			if (typeof option === 'string') methodReturn = data[option](value);
		});

		return (methodReturn === undefined) ? $set : methodReturn;
	};
	
	//默认属性列表
	$.fn.layout.defaults = {
		/**
		 * 顶部最小高度，单位px
		 * @property minHeigh
		 * @type {Integer}
		 * @default 50
		 */
		minHeigh:50,
		/**
		 * 布局方式
		 * @property layout
		 * @type {String}(default,west_expanded,west_to_bottom,west_to_top)
		 * @default "default"
		 */
		layout:"default",
		/**
		 * 中间区域的高度，单位px
		 * @property centerHeight
		 * @type {Integer}
		 * @default 500
		 */
		centerHeight:500,
		/**
		 * 总体布局的高度，单位px
		 * @property maxHeight
		 * @type {Integer}
		 * @default 600
		 */
		maxHeight:600,
		collapsible:true					//是否加上折合功能，此功能留到V2
	};
	
	//插件的构造对象赋值
	$.fn.layout.Constructor = Layout;
}(window.jQuery);