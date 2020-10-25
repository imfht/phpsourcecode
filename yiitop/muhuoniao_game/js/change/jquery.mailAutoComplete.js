// by zhangxinxu 2010-06-17 welcome to visit my personal website http://www.zhangxinxu.com/
// mailAutoComplete.js v1.0 邮箱输入自动提示
// 2010-06-18 v2.0 使用CSS class类代替CSS对象，同时增强代码可读性
// 2010-06-18 v2.1 宽度自适应的添加
// 2010-06-18 v2.2 修复多个元素同时调用此方法的一些bug
// 2010-06-30 修改搜狐邮箱名的笔误
// 2010-06-30 修复中文回车内容为空的问题
// 2010-08-17 v3.0 重写邮件显示的核心方法，提高显示性能
// 2010-09-01 修复Firefox下点击列表无法取值的问题
// 2010-09-03 添加提示文字隐藏以及文字颜色变化
(function($){
	$.fn.mailAutoComplete = function(options){
		var defaults = {
			boxClass: "mailListBox", //外部box样式
			listClass: "mailListDefault", //默认的列表样式
			focusClass: "mailListFocus", //列表选样式中
			markCalss: "mailListHlignt", //高亮样式
			zIndex: 1,
			autoClass: true, //是否使用插件自带class样式
			mailArr: ["qq.com","gmail.com","126.com","163.com","hotmail.com","yahoo.com","yahoo.com.cn","live.com","sohu.com","sina.com"], //邮件数组
			textHint: false, //文字提示的自动显示与隐藏
			hintText: "",
			focusColor: "#333",
			blurColor: "#999"
		};
		var settings = $.extend({}, defaults, options || {});
		
		//页面装载CSS样式
		if(settings.autoClass && $("#mailListAppendCss").size() === 0){
			$('<style id="mailListAppendCss" type="text/css">.mailListBox{border:1px solid #369; background:#fff; font:12px/20px Arial;}.mailListDefault{padding:0 5px;cursor:pointer;white-space:nowrap;}.mailListFocus{padding:0 5px;cursor:pointer;white-space:nowrap;background:#369;color:white;}.mailListHlignt{color:red;}.mailListFocus .mailListHlignt{color:#fff;}</style>').appendTo($("head"));	
		}
		var cb = settings.boxClass, cl = settings.listClass, cf = settings.focusClass, cm = settings.markCalss; //插件的class变量
		var z = settings.zIndex, newArr = mailArr = settings.mailArr, hint = settings.textHint, text = settings.hintText, fc = settings.focusColor, bc = settings.blurColor;
		//创建邮件内部列表内容
		$.createHtml = function(str, arr, cur){
			var mailHtml = "";
			if($.isArray(arr)){
				$.each(arr, function(i, n){
					if(i === cur){
						mailHtml += '<div class="mailHover '+cf+'" id="mailList_'+i+'"><span class="'+cm+'">'+str+'</span>@'+arr[i]+'</div>';	
					}else{
						mailHtml += '<div class="mailHover '+cl+'" id="mailList_'+i+'"><span class="'+cm+'">'+str+'</span>@'+arr[i]+'</div>';	
					}
				});
			}
			return mailHtml;
		};
		//一些全局变量
		var index = -1, s;
		$(this).each(function(){
			var that = $(this), i = $(".justForJs").size();	
			if(i > 0){ //只绑定一个文本框
			 	return;	
			}
			var w = that.outerWidth(), h = that.outerHeight(); //获取当前对象（即文本框）的宽高
			//样式的初始化
			that.wrap('<span style="display:inline-block;position:relative;"></span>')
				.before('<div id="mailListBox_'+i+'" class="justForJs '+cb+'" style="min-width:'+w+'px;_width:'+w+'px;position:absolute;left:-6000px;top:'+h+'px;z-index:'+z+';"></div>');
			var x = $("#mailListBox_" + i), liveValue; //列表框对象
			that.focus(function(){
				//父标签的层级
				$(this).css("color", fc).parent().css("z-index", z);	
				//提示文字的显示与隐藏
				if(hint && text){
					var focus_v = $.trim($(this).val());
					if(focus_v === text){
						$(this).val("");
					}
				}
				//键盘事件
				$(this).keyup(function(e){
					s = v = $.trim($(this).val());	
					if(/@/.test(v)){
						s = v.replace(/@.*/, "");
					}
					if(v.length > 0){
						//如果按键是上下键
						if(e.keyCode === 38){
							//向上
							if(index <= 0){
								index = newArr.length;	
							}
							index--;
						}else if(e.keyCode === 40){
							//向下
							if(index >= newArr.length - 1){
								index = -1;
							}
							index++;
						}else if(e.keyCode === 13){
							//回车
							if(index > -1 && index < newArr.length){
								//如果当前有激活列表
								$(this).val($("#mailList_"+index).text());	
							}
						}else{
							if(/@/.test(v)){
								index = -1;
								//获得@后面的值
								//s = v.replace(/@.*/, "");
								//创建新匹配数组
								var site = v.replace(/.*@/, "");
								newArr = $.map(mailArr, function(n){
									var reg = new RegExp(site);	
									if(reg.test(n)){
										return n;	
									}
								});
							}else{
								newArr = mailArr;
							}
						}
						x.html($.createHtml(s, newArr, index)).css("left", 0);
						if(e.keyCode === 13){
							//回车
							if(index > -1 && index < newArr.length){
								//如果当前有激活列表
								x.css("left", "-6000px");	
							}
						}
					}else{
						x.css("left", "-6000px");	
					}
				}).blur(function(){
					if(hint && text){
						var blur_v = $.trim($(this).val());
						if(blur_v === ""){
							$(this).val(text);
						}
					}
					$(this).css("color", bc).unbind("keyup").parent().css("z-index",0);
					x.css("left", "-6000px");	
					
				});	
				//鼠标经过列表项事件
				//鼠标经过
				$(".mailHover").live("mouseover", function(){
					index = Number($(this).attr("id").split("_")[1]);	
					liveValue = $("#mailList_"+index).text();
					x.children("." + cf).removeClass(cf).addClass(cl);
					$(this).addClass(cf).removeClass(cl);
				});
			});

			x.bind("mousedown", function(){
				that.val(liveValue);		
			});
		});
	};
	
})(jQuery);