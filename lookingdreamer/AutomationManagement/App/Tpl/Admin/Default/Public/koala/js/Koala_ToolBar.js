/**
 * @author zjh
 */
+function ($) {
	"use strict";
	
	var $toolbar = $("<div></div>").addClass("toolbar").addClass("btn-toolbar");
	
	/**
	 * 工具栏组件，样式基于Bootstrap的ButtonGroup
	 * @class ToolBar
	 * @constructor
	 */
	var ToolBar = function (element, options) {
		//4
		this.$element = $(element);
		this.options = $.extend({}, $.fn.toolbar.defaults, options);
		//事件列表
		this.render();
	};
	
	ToolBar.prototype={
			
		constructor:ToolBar,
		
		render:function(){
			var datas = this.options.datas;
			this.addTool(datas);
			$toolbar.appendTo(this.$element);
		},
		
		renderData:function(data){
			var id = data.id;
			var icon = data.icon;
			var style = data.style;
			var title = data.title;
			var $button = $("<button></button>").addClass("btn")
												.addClass("btn-lg");
			$button.addClass("btn-" + style);
			$button.attr("id",id);
			
			var $icon = $("<i></i>");
			$icon.text(title);
			$icon.addClass("icon-" + icon);
			$icon.appendTo($button);
			
			return $button;
		},
		
		/**
		 * 在原有的工具栏基础上再添加
		 * @method addTool
		 * @param {Array} datas
		 */
		addTool:function(datas){
			var $btnGroup = $("<div class='btn-group' data-toggle='buttons'></div>");
			//分组
			for(var key in datas){
//				console.info(datas[key]);
				var data = datas[key];
				if(data==null){
					$btnGroup.appendTo($toolbar);
					$btnGroup = $("<div class='btn-group' data-toggle='buttons'></div>");
				}else{
					var $button = this.renderData(data);
					$button.appendTo($btnGroup);
					//如果是最后的
					if(key==datas.length-1){
						$btnGroup.appendTo($toolbar);
					}
				}
			}
		}
	};
	
	//加入到jQuery插件
	$.fn.toolbar = function(option,value){
		//3
		var methodReturn;

		var $set = this.each(function () {
			var $this = $(this);
			var data = $this.data('koala.toolbar');
			var options = typeof option === 'object' && option;

			if (!data) $this.data('koala.toolbar', (data = new ToolBar(this, options)));
			if (typeof option === 'string') methodReturn = data[option](value);
		});

		return (methodReturn === undefined) ? $set : methodReturn;
	};
	
	//默认属性列表
	$.fn.toolbar.defaults = {
		/**
		 * 工具栏数据
		 * @property datas
		 * @type {Array}
		 * @default []
		 * @example
		 * 	var datas = [
				{"id":"btn-ok","icon":"ok","style":"success","title":"确认"},
				{"id":"btn-cancel","icon":"ban-circle","style":"success","title":"取消"},
				null,
				{"id":"btn-bold","icon":"bold","style":"info","title":"加粗"},
				{"id":"btn-italic","icon":"italic","style":"info","title":"倾斜"},
				{"id":"btn-font","icon":"font","style":"info","title":"字体"},
				null,
				{"id":"btn-left","icon":"align-left","style":"success","title":""},
				{"id":"btn-center","icon":"align-center","style":"success","title":""},
				{"id":"btn-right","icon":"align-right","style":"success","title":""},
			];
			
			$("#myToolBar").toolbar({
				datas:datas
			});
		 */
		datas:[],
		
	};
	
	//插件的构造对象赋值
	$.fn.toolbar.Constructor = ToolBar;
}(window.jQuery);