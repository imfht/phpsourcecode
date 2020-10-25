/**
 * 先放到后面再升级，没CSS美工，而且目前暂时用YUI Doc
 * @param $
 */
+function ($) {
	"use strict";
	 
	var apiData = {};
	var that = null;
	
//	/**
//	 * API文档构造组件
//	 * @class ApiBuilder
//	 */
	//构造函数
	var ApiBuilder = function (element, options) {
		//4
		this.$element = $(element);
		this.options = $.extend({}, $.fn.apibuilder.defaults, options);
		that = this;
		//方法列表
		this.render(options);
	};
	
	ApiBuilder.prototype = {
		/**
		 * @constructor
		 */
		constructor:ApiBuilder,
		
		//渲染方法
		render:function(options){
			var $this = this.$element;
			apiData = this.options.data;
			var treeData = this.parse(this.options.data);
			var $left = $this.find("div[region='west']");
			
			this.buildTree($left, treeData);
		}
	
		/*左边建造一颗树*/
		,buildTree:function ($container,treeData){
			var dataSourceTree = {
				data: treeData,
				delay: 400
			};
			$container.tree({
				dataSource: dataSourceTree,
				loadingHTML: '<div class="static-loader">Loading...</div>',
				multiSelect: true,
				cacheItems: true,
				onSelect:this.showGrid
			});
		}
		
		/*左边的节点点击了之后，右边的内容部分清空并显示表格*/
		//that代表.tree本身，$el代表当前点击的节点
		,showGrid:function($el){
			//1、先获取原始的apiData对应的某个节点的数据
			var $center = $el.parents(".row").find("div[region='center']");
			var key = $el.attr("id");
			var pKey = key.split("_")[0];
			var cKey = key.split("_")[1];
			var gridDatas = apiData[pKey][cKey];
			var columns = that.buildGridColumns(gridDatas);
			
			that.buildGrid($center,columns,gridDatas);
		}
		
		/*建造树*/
		,buildGrid:function($el,columns,gridDatas){
			$el.empty();
			
			$el.grid({
				 identity: 'id',
	             columns: columns,
	             buttons: [],
	             querys: [],
	             isUserLocalData:true,			//如果为false，则发送ajax请求到url端，获取数据，否则，则视为获取静态数据
	             localData:gridDatas
	             ,isShowIndexCol:false
	             ,isShowPages:false
	             //,lockWidth: true
	        });
			
		}
		
		/*从原始的表格数据里获取到字段列表*/
		,buildGridColumns:function(gridDatas){
			if(!gridDatas.length) return [];
			
			var columns = [];
			for(var key in gridDatas[0]){
				var column = {};
				column.title = key;
				column.name = key;
				column.width = 250;
				
				columns.push(column);
			}
			
			return columns;
		}
		
		/*把原始的api data转换为树所需要的格式*/
		,parse:function(apiData){
			if(!apiData) return;
			
			var treeData = [];
			
			for(var apiKey in apiData){
				var apiValue = apiData[apiKey];
				var properties = apiValue["properties"];
				var methods = apiValue["properties"];
				var events = apiValue["events"];
				var children = {};
				var node = {};
				var menu = {};
				
				menu["id"] = apiKey;
				menu["title"] = apiKey;
				menu["href"] = "#";
				
				//这里应该要封装下方法
				children["properties"]={
					"parentId":apiKey,"menu":{
						"id":apiKey + "_properties",
						"title":"属性",
						"href":"#"
					},"children":{}
				};
				
				children["methods"]={
					"parentId":apiKey,"menu":{
						"id":apiKey + "_methods",
						"title":"方法",
						"href":"#"
					},"children":{}
				};
				
				children["events"]={
					"parentId":apiKey,"menu":{
						"id":apiKey + "_events",
						"title":"事件",
						"href":"#"
					},"children":{}
				};
				//*****************************
				
				node["parentId"]="root";
				node["menu"] = menu;
				node["children"] = children;
				
				treeData.push(node);
			}
			
			return treeData;
		}
	};
	
	//加入到jQuery插件
	$.fn.apibuilder = function(option,value){
		//3
		var methodReturn;

		var $set = this.each(function () {
			var $this = $(this);
			var data = $this.data('koala.apibuilder');
			var options = typeof option === 'object' && option;

			if (!data) $this.data('koala.apibuilder', (data = new ApiBuilder(this, options)));
			if (typeof option === 'string') methodReturn = data[option](value);
		});

		return (methodReturn === undefined) ? $set : methodReturn;
	};
	
	//默认属性列表
	$.fn.apibuilder.defaults = {
		//2
		/**
		 * API数据
		 * @property
		 * @type js对象
		 * @default {}
		 */
		data:{}					//API数据
	};
	
	//插件的构造对象赋值
	$.fn.apibuilder.Constructor = ApiBuilder;
}(window.jQuery);
