/**
 * @author zjh
 */
+function ($) {
	"use strict";
	
	var $listview = $("<div></div>").addClass("listview").addClass("btn-toolbar");
	var $iconList = $("<ul></ul>").addClass("iconList");
	
	/**
	 * 列表视图组件
	 * @class ListView
	 * @constructor
	 */
	var ListView = function (element, options) {
		//4
		this.$element = $(element);
		this.options = $.extend({}, $.fn.listview.defaults, options);
		//事件列表
		this.render();
	};
	
	ListView.prototype={
			
		constructor:ListView,
		
		render:function(){
			var datas = this.options.datas;
			if(!datas.length){
				return;
			}
			this.addView(datas);
			$iconList.appendTo($listview);
			$listview.appendTo(this.$element);
		},
		
		renderData:function(data){
			var id = data.id;
			var title = data.title;
			var icon = data.icon;
			var $li = $("<li id='" + id + "'></li>").addClass("desktop_icon");
			var $span = $("<span></span>").addClass("icon");
			var $img = $("<img>").attr("src",icon);
			$img.appendTo($span);
			$span.appendTo($li);
			
			var $text = $("<div></div>").addClass("text").text(title);
			$text.append($("<div class='right_cron'></div>"));
			$text.appendTo($li);
			
			$iconList.append($li);
		},
		
		/**
		 * 在原有的列表基础上再添加
		 * @method addView
		 * @param {Array} datas
		 */
		addView:function(datas){
			for(var key in datas){
				var data = datas[key];
				this.renderData(data);
			}
		},
		
		/**
		 * 清空ListView
		 * @method clear
		 */
		clear:function(){
			this.$element.empty();
		},
		
		/**
		 * 更新某一个View
		 * @method update
		 * @param {Object} data
		 */
		update:function(data){
			var id = data.id;
			var title = data.title;
			var icon = data.icon;
			
			var $li = $("#" + id);
			if(!$li.length){
				return;
			}
			
			$li.find("img").attr("src",icon);
			$li.children(".text").text(title)
				.append($("<div class='right_cron'></div>"));
		},
		
		/**
		 * 除掉某一个View
		 * @method remove
		 * @param {String} id
		 */
		remove:function(id){
			var $li = $("#" + id);
			$li.remove();
		}
	};
	
	//加入到jQuery插件
	$.fn.listview = function(option,value){
		//3
		var methodReturn;

		var $set = this.each(function () {
			var $this = $(this);
			var data = $this.data('koala.listview');
			var options = typeof option === 'object' && option;

			if (!data) $this.data('koala.listview', (data = new ListView(this, options)));
			if (typeof option === 'string') methodReturn = data[option](value);
		});

		return (methodReturn === undefined) ? $set : methodReturn;
	};
	
	//默认属性列表
	$.fn.listview.defaults = {
		/**
		 * 列表栏数据
		 * @property datas
		 * @type {Array}
		 * @default []
		 */
		datas:[],
		
	};
	
	//插件的构造对象赋值
	$.fn.listview.Constructor = ListView;
}(window.jQuery);