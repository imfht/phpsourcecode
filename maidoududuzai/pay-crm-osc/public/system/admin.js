if(!window["befen"]) {
	window["befen"] = {};
}

befen.spinner = '<div class="befen-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';
befen.loading = '<div class="befen-loading"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';

befen.wd_init = function(input, keyword, callback) {
	var supportPlaceholder = "placeholder" in document.createElement("input");
	var input = input ? input : "#wd";
	if(supportPlaceholder) {
		$(input).attr("placeholder", keyword ? keyword : "输入关键字");
	}
};

$(function(){
	//befen();
});

$(document).ready(function(){
	$("div").on("scroll", function(){
		$(".layui-laydate").each(function(){
			var obj = $(this);
			if(!obj.hasClass("layui-laydate-static")) {
				obj.remove();
				obj.blur();
			}
		});
	});
});

function befen() {
	//console.log("test");
}

function reload() {
	window.location.reload();
}

function gotourl(the_url) {
	window.location = the_url;
}

function getEditor(id, html) {
	var id = id ? id : "content";
	var editor;
	KindEditor.ready(function(K){
		editor = K.create("textarea[name='" + id + "']", {
			resizeType: 1,
			shadowMode: false,
			filterMode: false,
			allowFileManager: true,
			dialogAlignType: "page",
			syncType: "form",
			afterBlur: function(){
				this.sync();
			},
			items: [
				'source', '|', 'undo', 'redo', '|',
				'fontname', 'fontsize', 'forecolor', 'hilitecolor', '|', 'bold', 'italic', 'underline', 'removeformat', '|',
				'image', 'multiimage', 'flash', 'insertfile', '|', 'link', 'unlink', '|',
				'table', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertunorderedlist', 'insertorderedlist', '|',
				'about'
			]
		});
		var val = html ? html : editor.html();
		if(val) {
			editor.html(val);
			editor.sync();
		}
	});
	return editor;
}

function isFunction(_Function) {
	if(Object.prototype.toString.call(_Function) === "[object Function]") {
		return true;
	} else {
		return false;
	}
}

var layer;
function _Layer(fun) {
	if(layer) {
		isFunction(fun) && fun();
	} else {
		layui.use("layer", function(){
			layer = layui.layer;
			layer.config({
				anim: -1,
				time: 0,
				shade: 0.5,
				isOutAnim: false
			});
			isFunction(fun) && fun();
		});
	}
}

_Layer();
function init_Layer(fun) {
	_Layer(fun);
}

var index_loading = 0;
function showLoader(msg) {
	$("*").blur();
	_Layer(function(){
		if(msg) {
			index_loading = layer.msg(msg, {time:0,anim:0,shade:0.5,isOutAnim:true});
		} else {
			index_loading = layer.open({
				type: 3,
				time: 0,
				anim: 0,
				shade: 0.5,
				isOutAnim: false,
				content: befen.loading
			});
		}
	});
}

function hideLoader() {
	_Layer(function(){
		layer.close(index_loading);
	});
}

var index_info;
function showInfo(msg, fun, time) {
	$("*").blur();
	var time = time ? time : 2000;
	_Layer(function(){
		index_info = layer.msg(msg, {time:time,anim:0,shade:0.5,isOutAnim:true}, function(index){
			layer.close(index);
			isFunction(fun) && fun();
		});
	});
}

var index_alert;
function showAlert(msg, fun) {
	$("*").blur();
	_Layer(function(){
		index_alert = layer.alert(msg, {anim:0,shade:0.5,isOutAnim:true}, function(index){
			layer.close(index);
			isFunction(fun) && fun();
		});
	});
}

var index_confirm;
function showConfirm(msg, fun, cancel) {
	$("*").blur();
	_Layer(function(){
		index_confirm = layer.confirm(msg, {anim:0,shade:0.5,isOutAnim:true}, function(index){
			layer.close(index);
			isFunction(fun) && fun();
		}, function(index){
			layer.close(index);
			isFunction(cancel) && cancel();
		});
	});
}

var index_window;
function showWindow(title, the_url, area, id, skin_class) {
	$("*").blur();
	var area = area ? area : "400px";
	if(/^\d+$/.test(area)) {
		area += "px";
	}
	var skin_class = skin_class ? skin_class : "layui-window";
	$.ajax({
		type: "GET",
		dataType: "html",
		timeout: 50000,
		url: the_url,
		data: {ajax:"html"},
		success: function(html){
			_Layer(function(){
				index_window = layer.open({
					type: 1,
					time: 0,
					anim: 0,
					shade: 0.5,
					isOutAnim: true,
					title: title,
					content: html,
					id: id,
					area: area,
					skin: skin_class
				});
			});
		},
		error: function(html){
			showAlert("读取数据失败");
		},
		complete: function(){
			hideLoader();
		},
		beforeSend: function(){
			showLoader();
		}
	});
}

function op(action, the_url, callback) {
	showConfirm(action, function(){
		$.ajax({
			type: "POST",
			dataType: "json",
			timeout: 50000,
			url: the_url,
			data: {ajax:"json"},
			success: function(data){
				if(data.status == "nologin") {
					showAlert(data.message, function(){
						window.location.reload();
					});
				} else if(data.status == 0) {
					showAlert(data.message);
				} else if(data.status == 1) {
					showInfo(data.message, function(){
						if(isFunction(callback)) {
							callback(data);
						} else {
							window.location.reload();
						}
					});
				}
			},
			error: function(data){
				showAlert("提交数据失败");
			},
			complete: function(){
				hideLoader();
			},
			beforeSend: function(){
				showLoader();
			}
		});
	});
}

function showDelete(the_url, callback) {
	op("确认要删除吗？", the_url, callback);
}

function submit(state, the_btn, the_form) {
	var the_btn = the_btn ? the_btn : "#submit";
	var the_form = the_form ? the_form : "#theform";
	if(state != 0) {
		showLoader();
		$(the_btn).attr("disabled", true);
	} else {
		hideLoader();
		$(the_btn).attr("disabled", false);
	}
}

function ajaxSubmit(the_btn, the_url, the_form, callback) {
	//var the_form = $(the_btn).closest("form");
	var the_btn = the_btn ? the_btn : "#submit";
	var the_form = the_form ? the_form : "#theform";
	$(the_form).ajaxForm({
		dataType: "json",
		timeout: 50000,
		data: {ajax:"json"},
		success: function(data){
			if(data.status == "nologin") {
				showAlert(data.message, function(){
					reload();
				});
			} else if(data.status == 0) {
				showAlert(data.message);
			} else if(data.status == 1) {
				showInfo(data.message, function(){
					if(isFunction(callback)) {
						callback(data);
					} else {
						if(!the_url){
							reload();
						} else {
							gotourl(the_url);
						}
					}
				});
			}
		},
		error: function(data){
			showAlert("提交数据失败");
		},
		complete: function(){
			submit(0, the_btn, the_form);
		},
		beforeSend: function(){
			submit(1, the_btn, the_form);
		}
	});
}

function ShowTr(obj, elem) {
	var elem = elem ? elem : "#elem";
	if($(elem).css("display") == "none") {
		$(elem).show();
	} else {
		$(elem).hide();
	}
}

function showTips(obj, msg, callback) {
	$(obj).addClass("input-warnning");
	layer.msg(msg, {anim:6,icon:2,time:2000}, function(){
		isFunction(callback) && callback();
	});
	$(obj).change(function(event){
		$(this).removeClass("input-warnning");
	});
}

function Logout(the_url, location) {
	showConfirm("确认要注销吗？", function(){
		$.ajax({
			type: "GET",
			dataType: "html",
			timeout: 50000,
			url: the_url,
			data: {ajax:"html"},
			success: function(data){
				showInfo("注销成功", function(){
					gotourl(location);
				});
			},
			error: function(data){
				showAlert("提交数据失败");
			},
			complete: function(){
				hideLoader();
			},
			beforeSend: function(){
				showLoader();
			}
		});
	});
}

function ClearCache(the_url) {
	$.ajax({
		type: "GET",
		dataType: "html",
		timeout: 50000,
		url: the_url,
		data: {ajax:"html"},
		success: function(data){
			showInfo("操作成功");
		},
		error: function(data){
			showAlert("提交数据失败");
		},
		complete: function(){
			hideLoader();
		},
		beforeSend: function(){
			showLoader();
		}
	});
}


function DrawImage(ImgD, ImgW, ImgH, ImgP) {
	var mmwidth = ImgW ? ImgW : 600;
	var mmheight = ImgH ? ImgH : 600;
	var image = new Image();
	image.src = ImgD.src;
	if(image.width > 0 && image.height > 0) {
		if(image.width / image.height >= mmwidth / mmheight) {
			if(image.width > mmwidth) {
				ImgD.width = mmwidth;
				ImgD.height = (image.height * mmwidth) / image.width;
			} else {
				ImgD.width = image.width;
				ImgD.height = image.height;
			}
		} else {
			if(image.height > mmheight) {
				ImgD.height = mmheight;
				ImgD.width = (image.width * mmheight) / image.height;
			} else {
				ImgD.width = image.width;
				ImgD.height = image.height;
			}
		}
		if(ImgP) {
			if(ImgD.width < ImgW) {
				Woffset = (ImgW - ImgD.width) / 2;
				ImgD.style.paddingLeft = Woffset + "px";
				ImgD.style.paddingRight = Woffset + "px";
			}
			if(ImgD.height < ImgH) {
				Hoffset = (ImgH - ImgD.height) / 2;
				ImgD.style.paddingTop = Hoffset + "px";
				ImgD.style.paddingBottom = Hoffset + "px";
			}
		}
	}
}

/* SideShow */

(function(jQuery){
	jQuery.sideShow = function(title, the_url){
		jQuery("*").blur();
		jQuery.ajax({
			type: "GET",
			dataType: "html",
			timeout: 50000,
			url: the_url,
			data: {ajax:"html"},
			success: function(html){
				var content = '';
				content += '<div class="side-show">';
				content += '<div class="side-head">';
				content += '<h3>' + title + '</h3>';
				content += '<a href="javascript:;" class="layui-icon layui-icon-close side-head-close"></a>';
				content += '</div>';
				content += '<div class="side-body">';
				content += html;
				content += '</div>';
				content += '</div>';
				jQuery("body").append(content);
				SideShowHeight();
				jQuery(".side-head-close").click(function(event){
					jQuery(".side-show").remove();
				});
			},
			error: function(html){
				showTips(null, "读取数据失败");
			},
			complete: function(){
				jQuery(".side-loader").remove();
			},
			beforeSend: function(){
				jQuery("body").append('<div class="side-loader">' + befen.spinner + '</div>');
			}
		});
	}
})(jQuery);

//AutoHeight
$(function(){
	SideShowHeight();
});

//AutoHeight
$(window).resize(function(){
	SideShowHeight();
});

//AutoHeight
function SideShowHeight() {
	var _height = $(window).height() - $("#admin-header").height();
	$(".side-show .side-body").css("height", (_height + 40) + "px");
}

/* SideShow */

layui.config({
	base: '/public/layui/modules/'
});

/* laydate */

var laydate;

(function(jQuery){
	jQuery.fn.calendar = function(type, value, callback, done_callback, change_callback){
		var elem = this.get(0);
		var type = type ? type : "date";
		if(laydate) {
			laydate.render({
				type: type,
				elem: elem,
				trigger: "click",
				ready: function(date){
					isFunction(callback) && callback(date);
				},
				done: function(value, date, endDate){
					isFunction(done_callback) && done_callback(value, date, endDate);
				},
				change: function(value, date, endDate){
					isFunction(change_callback) && change_callback(value, date, endDate);
				}
			});
		} else {
			layui.use("laydate", function(){
				laydate = layui.laydate;
				jQuery(elem).calendar(type, value, callback, done_callback, change_callback);
			});
		}
	}
})(jQuery);

/* laydate */

var form;
function init_Form(object, filter) {
	if(form) {
		form.render(object, filter);
	} else {
		layui.use(["form"], function(){
			form = layui.form;
			form.render(object, filter);
		});
	}
}

var layarea;
function init_Layarea(object, config = {}) {
	if(Object.prototype.toString.call(config.data) !== "[object Object]") {
		config.data = {};
	}
	layui.use(['layer', 'form', 'layarea'], function(){
		var layer = layui.layer;
		var form = layui.form;
		var layarea = layui.layarea;
		layarea.render({
			elem: object,
			data: config.data,
			ready: function(res) {
				//console.log('ready', res);
				isFunction(config.ready) && config.ready(res);
			},
			change: function(res) {
				//console.log('change', res);
				isFunction(config.change) && config.change(res);
			}
		});
	});
}

/* layui-upload */

var option_upload = {
	elem: '',
	field: '',
	url: '',
	size: '',
	exts: '',
	acceptMime: '',
	progress: function(n){

	},
	callback: function(item, data, index, upload){

	}
};

var upload;

function init_upload(option) {
	if(!option.field) {
		option.field = 'file';
	}
	if(!option.size) {
		option.size = 2048;
	}
	layui.use(['upload'], function(){
		upload = layui.upload;
		upload.render({
			elem: option.elem,
			field: option.field,
			url: option.url,
			size: option.size,
			exts: option.exts,
			accept: 'file',
			acceptMime: option.acceptMime,
			progress: function(n){
				isFunction(option.progress) && option.progress(n);
			},
			done: function(data, index, upload){
				item = this.item;
				hideLoader();
				isFunction(option.callback) && option.callback(item, data, index, upload);
			},
			error: function(){
				hideLoader();
			},
			before: function(){
				showLoader();
			}
		});
	});
}

/* layui-upload */

