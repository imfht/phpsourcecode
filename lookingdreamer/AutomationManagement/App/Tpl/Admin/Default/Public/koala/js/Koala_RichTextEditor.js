/**
 * @author zjh
 */
+function ($) {
	//局部变量列表
	var fontNames = [];				//字体名称数组
	var fontSizes = [];				//字体大小数组
	var fontCtrls = [];				//字体控制数组
	var listCtrls = [];				//生成列表数组
	var justfyCtrls = [];			//生成对齐数组
	var redoCtrls = [];				//撤销重做控制
	var $toolBar = null;			//工具栏jQuery
	var that = null;				//this的替代
	/**
	 * 富文本编辑器
	 * @class RichTextEditor
	 * @constructor
	 * @example
	 * 	js代码
	 * 	$(function(){
			$("#editor").richtexteditor({
				showJustfyCtrls:false,
				showRedoCtrls:false,
			});
		});
	 
	 * @example
	 *	html代码
	 *	<div contenteditable="true" id="editor"></div>
	 */
	var RichTextEditor = function (element, options) {
		//4
		this.$element = $(element);
		this.options = $.extend({}, $.fn.richtexteditor.defaults, options);
		that = this;
		//初始化
		this.init(this.$element, this.options);
		//事件列表
		this.$element.parent().children("div[data-target]").on('click',"a[data-edit]", $.proxy( function(ev) { this.exec(ev.currentTarget); } ,this));
	};
	
	//原型定义，定义构造函数和其他方法
	RichTextEditor.prototype = {
		/**
		 * @constructor
		 */
		constructor:RichTextEditor,
		//初始化
		init:function($element,options){
			//初始化工具栏$
			$toolBar = $("<div data-target='#" + $element.attr("id") + "' data-role='editor-toolbar' class='btn-toolbar'></div>");
			
			//初始化工具栏各种数据
			if(that.options.showFontNames){
				fontNames = [{"name":"SimSun","title":"宋体"},
				             {"name":"FangSong_GB2312","title":"仿宋体"},
				             {"name":"SimHei","title":"黑体"},
				             {"name":"KaiTi_GB2312","title":"楷体"},
				             {"name":"Microsoft YaHei","title":"微软雅黑"},
				             {"name":"幼圆","title":"幼圆"},
				             {"name":"Times","title":"Times"},
				             {"name":"Times New Roman","title":"Times New Roman"},
				             {"name":"Verdana","title":"Verdana"}];
			}
			
			if(that.options.showFontSizes){
				fontSizes = [7,6,5,4,3,2,1];
			}
			
			if(that.options.showFontCtrls){
				fontCtrls = [{"name":"bold","title":"加粗"},
				             {"name":"italic","title":"倾斜"},
				             {"name":"strikethrough","title":"删除线"},
				             {"name":"underline","title":"下划线"}];
			}
			
			if(that.options.showListCtrls){
				listCtrls = [{"name":"insertunorderedlist","title":"插入无序列表","icon":"list-ul"},
				             {"name":"insertorderedlist","title":"插入有序列表","icon":"list-ol"},
				             {"name":"outdent","title":"减少缩进","icon":"indent-left"},
				             {"name":"indent","title":"增加缩进","icon":"indent-right"}];
			}
			
			if(that.options.showJustfyCtrls){
				justfyCtrls = [{"name":"justifyleft","title":"向左对齐","icon":"align-left"},
					             {"name":"justifycenter","title":"置中对齐","icon":"align-center"},
					             {"name":"justifyright","title":"向右对齐","icon":"align-right"},
					             {"name":"justifyfull","title":"全对齐","icon":"align-justify"}];
			}
			
			if(that.options.showRedoCtrls){
				redoCtrls = [{"name":"undo","title":"撤销","icon":"undo"},
				             {"name":"redo","title":"重做","icon":"repeat"}];
			}
			
			this._createFontNames();
			this._createFontSizes();
			this._createFontCtrls();
			this._createListCtrls();
			this._createJustifyCtrls();
			this._createRedoCtrls();
			
			//插入工具栏到富文本编辑框前面
			$toolBar.insertBefore($element);
		},
		//执行命令
		exec:function(el){
			var $el = $(el); 
			//这里需要定位出editor
			var value = $el.attr("data-edit");
			var spaceIdx = value.indexOf(" "); 
			var command = "";
			var params = "";
			if(spaceIdx>=0){
				command = value.substring(0,spaceIdx);
				params = value.substring(spaceIdx+1)||"";
			}else{
				command = value;
				params = "";
			}
			
			//console.info(value);
			document.execCommand(command,false,params);
		},
		//创建字体名称
		_createFontNames:function(){
			var $group = $("<div></div>").addClass("btn-group");
			var $icon = $('<a title="字体" data-toggle="dropdown" class="btn btn-default dropdown-toggle"><i class="icon-font"></i><b class="caret"></b></a>');
			var $ul = $("<ul></ul>").addClass("dropdown-menu");
			
			for ( var i=0;i<fontNames.length;i++) {
				var fontName = fontNames[i];
				var $li = $("<li></li>");
				var $a = $("<a data-edit='fontName " + fontName.name + "'></a>");
				$a.text(fontName.title);
				$a.css({
					"font-family":fontName.name
				});
				$li.append($a).appendTo($ul);
			}
			$group.append($icon).append($ul);
			//插入到工具栏里
			$group.appendTo($toolBar);
		},
		//创建字体大小
		_createFontSizes:function(){
			var $group = $("<div></div>").addClass("btn-group");
			var $icon = $('<a title="字体大小" data-toggle="dropdown" class="btn btn-default dropdown-toggle"><i class="icon-text-height"></i><b class="caret"></b></a>');
			var $ul = $("<ul></ul>").addClass("dropdown-menu");
			
			for(var i=0;i<fontSizes.length;i++){
				var fontSize = fontSizes[i];
				var $li = $("<li></li>");
				var $a = $("<a data-edit='fontSize " + fontSize + "'><font size='" + fontSize + "'>" + fontSize + "号字体</font></a>");
				$li.append($a).appendTo($ul);
			}
			$group.append($icon).append($ul);
			//插入到工具栏里
			$group.appendTo($toolBar);
		},
		//创建文字控制
		_createFontCtrls:function(){
			var $group = $("<div></div>").addClass("btn-group");
			for(var i=0;i<fontCtrls.length;i++){
				var fontCtrl = fontCtrls[i];
				var $a = $("<a title='" + fontCtrl.title + "' data-edit='" + fontCtrl.name + 
						"' class='btn btn-default'><i class='icon-" + fontCtrl.name + "'></i></a>");
				$group.append($a);
			}
			//插入到工具栏里
			$group.appendTo($toolBar);
		},
		//创建列表控制
		_createListCtrls:function(){
			this._createToolBar(listCtrls);
		},
		//创建对齐控制
		_createJustifyCtrls:function(){
			this._createToolBar(justfyCtrls);
		},
		//创建撤销/重做控制
		_createRedoCtrls:function(){
			this._createToolBar(redoCtrls);
		},
		//公共方法，基于工具栏生成dom，用数组作为参数
		_createToolBar:function(ctrls){
			var $group = $("<div></div>").addClass("btn-group");
			for(var i=0;i<ctrls.length;i++){
				var ctrl = ctrls[i];
				var $a = $("<a title='" + ctrl.title + "' data-edit='" + ctrl.name + 
						"' class='btn btn-default'><i class='icon-" + ctrl.icon + "'></i></a>");
				$group.append($a);
			}
			//插入到工具栏里
			$group.appendTo($toolBar);
		}
	};
	
	//加入到jQuery插件
	$.fn.RichTextEditor = function(option,value){
		//3
		var methodReturn;

		var $set = this.each(function () {
			var $this = $(this);
			var data = $this.data('richtexteditor');
			var options = typeof option === 'object' && option;

			if (!data) $this.data('richtexteditor', (data = new RichTextEditor(this, options)));
			if (typeof option === 'string') methodReturn = data[option](value);
		});

		return (methodReturn === undefined) ? $set : methodReturn;
	};
	
	//加入到jQuery插件
	$.fn.richtexteditor = function(option,value){
		//3
		var methodReturn;

		var $set = this.each(function () {
			var $this = $(this);
			var data = $this.data('richtexteditor');
			var options = typeof option === 'object' && option;

			if (!data) $this.data('richtexteditor', (data = new RichTextEditor(this, options)));
			if (typeof option === 'string') methodReturn = data[option](value);
		});

		return (methodReturn === undefined) ? $set : methodReturn;
	};
	
	//默认属性列表
	$.fn.richtexteditor.defaults = {
		/**
		 * 是否显示字体名称选择项
		 * @property showFontNames
		 * @type Boolean
		 * @default true
		 */
		showFontNames:true,
		/**
		 * 是否显示字体大小选择项
		 * @property showFontSizes
		 * @type Boolean
		 * @default true
		 */
		showFontSizes:true,
		/**
		 * 是否显示字体控制选择项
		 * @property showFontCtrls
		 * @type Boolean
		 * @default true
		 */
		showFontCtrls:true,
		/**
		 * 是否显示列表控制选择项
		 * @property showListCtrls
		 * @type Boolean
		 * @default true
		 */
		showListCtrls:true,
		/**
		 * 是否显示对齐选择项
		 * @property showJustfyCtrls
		 * @type Boolean
		 * @default true
		 */
		showJustfyCtrls:true,
		/**
		 * 是否显示撤销重做选择项
		 * @property showRedoCtrls
		 * @type Boolean
		 * @default true
		 */
		showRedoCtrls:true,
	};
	
	//插件的构造对象赋值
	$.fn.richtexteditor.Constructor = RichTextEditor;
}(window.jQuery);