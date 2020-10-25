function del_current_node() {
	set_cookie("current_node", null);
}

function winprint() {
	window.print();
}

function freshVerify()
{
    $('#verifyImg').attr("src",$('#verifyImg').attr("src").split("?")[0]+"?"+Math.random());
}

function click_top_menu(node) {
	set_cookie("left_menu", "");
	url = $(node).attr("url");
	node = $(node).attr("node");
	set_cookie("top_menu", node);

	form = $("<form></form>");
	form.attr('action', url);
	form.attr('method', 'post');
	form.appendTo("body");
	form.css('display', 'none');
	form.submit();
}

function click_home_list(obj_node) {
	node = $(obj_node).attr("node");
	set_cookie("top_menu", node);

	return_url = $(obj_node).attr("return_url");
	set_return_url(return_url);

	url = $(obj_node).attr("url");

	form = $("<form></form>");
	form.attr('action', url);
	form.attr('method', 'post');
	form.appendTo("body");
	form.css('display', 'none');
	form.submit();
}

/* 填充时间*/
function fill_time(id) {
	for (var i = 5; i < 22; i++) {
		val = ("0" + i);
		val = val.substring(val.length - 2)
		$("#" + id).append("<option value='" + val + ":00'>" + val + ":00</option>");
		$("#" + id).append("<option value='" + val + ":30'>" + val + ":30</option>");
	}
}

/* 删除左右两端的空格*/
function trim(str) {
	return str.replace(/(^\s*)|(\s*$)/g, "");
}

/* 获取日历背景颜色*/
function schedule_bg(j) {
	var myArray = new Array(5);
	myArray[0] = "#CCCCCC";
	myArray[1] = "#99CCFF";
	myArray[2] = "#CCFFCC";
	myArray[3] = "#FFFFCC";
	myArray[4] = "#FFCCCC ";
	return myArray[j - 1];
}

function ui_info(msg) {
	$.gritter.add({
		position : 'bottom-right',
		text : '<h5>' + msg + '</h5>',
		sticky : false,
		time : 3000,
		class_name : 'gritter-light gritter-info'
	});
}

function ui_alert(msg, callback) {
	bootbox.dialog({
		message : "<h5>" + msg + "<h5>",
		buttons : {
			danger : {
				label : "确定",
				className : "btn-primary",
				callback : function() {
					callback();
				}
			}
		}
	});
}

function ui_confirm(msg, callback) {
	bootbox.dialog({
		message : "<h5>" + msg + "<h5>",
		buttons : {
			main : {
				label : "取消",
				className : "btn-default",
				callback : function() {
					//
				}
			},
			danger : {
				label : "确定",
				className : "btn-primary",
				callback : function() {
					callback();
				}
			}
		}
	});
}

function ui_error(msg) {
	$.gritter.add({
		text : '<h5>' + msg + '</h5>',
		sticky : false,
		time : 2000,
		class_name : 'gritter-light gritter-error gritter-center'
	});
}

/*联系人显示格式转换*/
function conv_address_item(id, name) {
	html = '<nobr><label>';
	html += '		<input class="ace" type="checkbox" name="addr_id" value="' + id + '"/>';
	html += '		<span class="lbl">' + name + '</span></label></nobr>';
	return html;
}

function conv_inputbox_item(id, name, title, data) {
	if (data !== undefined) {
		html = "<span data=\"" + data + "\" id=\"" + id + "\">";
	} else {
		html = "<span id=\"" + id + "\">";
	}
	html += "<nobr><b  title=\"" + title + "\">" + name + "</b>";
	html += "<a class=\"del\" title=\"删除\"><i class=\"fa fa-times\"></i></a></nobr></span>";
	return html;
}

/* 关闭弹出窗口*/
function myclose() {
	parent.winclose();
}

function winclose() {
	$("html,body").css("overflow", "auto");
	$("div.shade").hide();
	$("#dialog").html("");
	$("#dialog").remove();
}

/* 在iframe里显示textarea的内容*/
function show_content() {
	$(".content_wrap").each(function() {
		iframe = $(this).find(".content_iframe").get(0).contentWindow;
		var div = document.createElement("div");
		div.className = "height";
		div.innerHTML = $(this).find(".content").val();
		iframe.document.body.appendChild(div);
		height = $(iframe.document.body).find("div.height").height();
		if (height < 100) {
			height = 100;
		}
		iframe.height = height;
		$(this).height(height + 35);
		$(iframe).height(height + 35);
	})
}

function toggle_adv_search() {
	if ($("#adv_search").attr("class").indexOf("display-none") < 0) {
		$("#adv_search").addClass("display-none");
		$("#toggle_adv_search_icon").addClass("fa-chevron-down");
		$("#toggle_adv_search_icon").removeClass("fa-chevron-up");
	} else {
		$("#adv_search").removeClass("display-none");
		$("#toggle_adv_search_icon").addClass("fa-chevron-up");
		$("#toggle_adv_search_icon").removeClass("fa-chevron-down");
	}
}

function toggle_left_menu() {
	if ($("#left_menu").css("display") == "none") {
		$("#left_menu").show();
	} else {
		$("#left_menu").hide();
	}
}

function submit_search() {
	$("#form_search").submit();
}

function submit_adv_search() {
	$("#form_adv_search").submit();
}

function close_adv_search() {
	$("#adv_search").addClass("display-none");
	$("#toggle_adv_search_icon").addClass("fa-chevron-down");
	$("#toggle_adv_search_icon").removeClass("fa-chevron-up");
}

var ul_table = {
	//displays a toolbar according to the number of selected messages
	display_bar : function(count) {
		if (count == 0) {
			$('#id-toggle-all').removeAttr('checked');
			$('#id-message-list-navbar .message-toolbar').addClass('hide');
			$('#id-message-list-navbar .message-infobar').removeClass('hide');
		} else {
			$('#id-message-list-navbar .message-infobar').addClass('hide');
			$('#id-message-list-navbar .message-toolbar').removeClass('hide');
		}
	},
	select_all : function() {
		var count = 0;
		$('.tbody input[type=checkbox]').each(function() {
			this.checked = true;
			$(this).closest('.tbody').addClass('selected');
			count++;
		});

		$('#id-toggle-all').get(0).checked = true;

		ul_table.display_bar(count);
	},
	select_none : function() {
		$('.tbody input[type=checkbox]').removeAttr('checked').closest('.tbody').removeClass('selected');
		$('#id-toggle-all').get(0).checked = false;

		ul_table.display_bar(0);
	},
	select_read : function() {
		$('.message-unread input[type=checkbox]').removeAttr('checked').closest('.tbody').removeClass('selected');

		var count = 0;
		$('.tbody:not(.message-unread) input[type=checkbox]').each(function() {
			this.checked = true;
			$(this).closest('.tbody').addClass('selected');
			count++;
		});
		ul_table.display_bar(count);
	},
	select_unread : function() {
		$('.tbody:not(.message-unread) input[type=checkbox]').removeAttr('checked').closest('.tbody').removeClass('selected');

		var count = 0;
		$('.message-unread input[type=checkbox]').each(function() {
			this.checked = true;
			$(this).closest('.tbody').addClass('selected');
			count++;
		});

		ul_table.display_bar(count);
	}
}

var Inputbox = {
	//displays a toolbar according to the number of selected messages
	display_bar : function(count) {
		if (count == 0) {
			$('#id-toggle-all').removeAttr('checked');
			$('#id-message-list-navbar .message-toolbar').addClass('hide');
			$('#id-message-list-navbar .message-infobar').removeClass('hide');
		} else {
			$('#id-message-list-navbar .message-infobar').addClass('hide');
			$('#id-message-list-navbar .message-toolbar').removeClass('hide');
		}
	},
	select_all : function() {
		var count = 0;
		$('.tbody input[type=checkbox]').each(function() {
			this.checked = true;
			$(this).closest('.tbody').addClass('selected');
			count++;
		});

		$('#id-toggle-all').get(0).checked = true;

		ul_table.display_bar(count);
	},
	select_none : function() {
		$('.tbody input[type=checkbox]').removeAttr('checked').closest('.tbody').removeClass('selected');
		$('#id-toggle-all').get(0).checked = false;

		ul_table.display_bar(0);
	},
	select_read : function() {
		$('.message-unread input[type=checkbox]').removeAttr('checked').closest('.tbody').removeClass('selected');

		var count = 0;
		$('.tbody:not(.message-unread) input[type=checkbox]').each(function() {
			this.checked = true;
			$(this).closest('.tbody').addClass('selected');
			count++;
		});
		ul_table.display_bar(count);
	},
	select_unread : function() {
		$('.tbody:not(.message-unread) input[type=checkbox]').removeAttr('checked').closest('.tbody').removeClass('selected');

		var count = 0;
		$('.message-unread input[type=checkbox]').each(function() {
			this.checked = true;
			$(this).closest('.tbody').addClass('selected');
			count++;
		});
		ul_table.display_bar(count);
	}
}

/*赋值*/

function set_val(name, val) {
	if ($("#" + name + " option").length > 0) {
		$("#" + name + " option[value='" + val + "']").attr("selected", "selected");
		return;
	}

	if (($("#" + name).attr("type")) === "checkbox") {
		if (val == 1) {
			$("#" + name).attr("checked", true);
			return;
		}
	}
	if ($("." + name).length > 0) {
		if (($("." + name).first().attr("type")) === "checkbox") {
			var arr_val = val.split(",")
			for (var s in arr_val) {
				$("input." + name + "[value=" + arr_val[s] + "]").attr("checked", true);
			}
		}
	}

	if (($("#" + name).attr("type")) === "text") {
		$("#" + name).val(val);
		return;
	}
	if (($("#" + name).attr("type")) === "hidden") {
		$("#" + name).val(val);
		return;
	}
	if (($("#" + name).attr("rows")) > 0) {
		$("#" + name).text(val);
		return;
	}
}

/*设置要返回的URL*/
function set_return_url(url, level) {
	if (url != undefined) {
		if (level != undefined) {
			set_cookie("return_url_" + level, url);
		} else {
			set_cookie("return_url", url);
		}
	} else {
		set_cookie("return_url", document.location);
	}
}

/*返回到上一页*/
function go_return_url(level) {
	if (level != undefined) {
		return_url = get_cookie('return_url_' + level);
		window.open(return_url, "_self");
	} else {
		return_url = get_cookie('return_url');
		window.open(return_url, "_self");
	}
	return false;
}

/*打开弹出窗口*/
function winopen(url, w, h) {
	url = fix_url(url);
	$("html,body").css("overflow","hidden");
	$("div.shade").show();
	var _body = $("body").eq(0);
	if ($("#dialog").length == 0){
		if (!is_mobile()) {
			_body.append("<div id=\"dialog\" ><iframe class=\"myFrame\" src='" + url + "' style='width:" + w + "px;height:100%' scrolling='auto' ></iframe></div>");
			$("#dialog").css({
				"width" : w,
				"height" : h,				
				"position" : "fixed",
				"z-index" : "2000",
				"top" : ($(window).height() / 2 - h / 2),
				"left" : (_body.width() / 2 - w / 2),
				"background-color" : "#ffffff"
			});
		} else {
			$("div.shade").css("width", _body.width());
			_body.append("<div id=\"dialog\" ><iframe class=\"myFrame\" src='" + url + "' style='width:100%;height:100%' scrolling='auto' ></iframe></div>");
			$("#dialog").css({				
				"width" : _body.width(),
				"height" : h,
				"position" : "fixed",
				"z-index" : "2000",
				"top":0,
				"left":0,
				"background-color" : "#ffffff"
			});
		}
	} else {
		$("#dialog").show();
	}
}

var toScrollFrame = function(iFrame, mask) {
	if (!navigator.userAgent.match(/iPad|iPhone/i))
		return false;
	//do nothing if not iOS devie

	var mouseY = 0;
	var mouseX = 0;
	jQuery(iFrame).ready(function() {
		jQuery(iFrame).contents()[0].body.addEventListener('touchstart', function(e) {
			mouseY = e.targetTouches[0].pageY;
			mouseX = e.targetTouches[0].pageX;
		});

		jQuery(iFrame).contents()[0].body.addEventListener('touchmove', function(e) {
			e.preventDefault();

			var box = jQuery(mask);
			box.scrollLeft(box.scrollLeft() + mouseX - e.targetTouches[0].pageX);
			box.scrollTop(box.scrollTop() + mouseY - e.targetTouches[0].pageY);
		});
	});

	return true;
};

/*联系人显示格式转换*/
function contact_conv(val) {
	var arr_temp = val.split(";");
	var html = "";
	for (key in arr_temp) {
		if (arr_temp[key] != '') {
			data = arr_temp[key].split("|")[1];
			id = arr_temp[key].split("|")[1];
			name = arr_temp[key].split("|")[0];
			title = arr_temp[key].split("|")[0];
			html += conv_inputbox_item(id, name, title, data)
			//html +=  '<span data="' + arr_temp[key].split("|")[1] + '" onmousedown="return false"><nobr>' + arr_temp[key].split("|")[0] + '<a class=\"del\" title=\"删除\"><i class=\"fa fa-times\"></i></a></nobr></span>';
		}
	}
	return html;
}

/* 判断是否是移动设备 */
function is_mobile() {
	return navigator.userAgent.match(/mobile/i);
}

/*联系人显示格式转换*/
function fix_url(url) {
	var ss = url.split('?');
	url = ss[0] + "?";
	for (var i = 1; i < ss.length; i++) {
		url += ss[i] + "&";
	}
	if (ss.length > 0) {
		url = url.substring(0, url.length - 1);
	}
	return url;
}

function check_form(form_id) {
	var check_flag = true;
	$("#" + form_id + " :input").each(function(i) {
		if ($(this).attr("check")) {
			if (!validate($(this).val(), $(this).attr("check"))) {
				ui_error($(this).attr("msg"));
				$(this).focus();
				check_flag = false;
				return check_flag;
			}
		}
	})
	return check_flag;
}

/* 验证数据类型*/
function validate(data, datatype) {
	if (datatype.indexOf("|")) {
		tmp = datatype.split("|");
		datatype = tmp[0];
		data2 = tmp[1];
	}
	switch (datatype) {
		case "require":
			if (data == "") {
				return false;
			} else {
				return true;
			}
			break;
		case "email":
			var reg = /^([0-9A-Za-z\-_\.]+)@([0-9a-z]+\.[a-z]{2,3}(\.[a-z]{2})?)$/g;
			return reg.test(data);
			break;
		case "number":
			var reg = /^[0-9]+\.{0,1}[0-9]{0,3}$/;
			return reg.test(data);
			break;
		case "html":
			var reg = /<...>/;
			return reg.test(data);
			break;
		case "eqt":
			data2 = $("#" + data2).val();
			return data >= data2
			break;
	}
}

/* ajax提交*/
function sendAjax(url, vars, callback) {
	return $.ajax({
		type : "POST",
		url : url,
		data : vars + "&ajax=1",
		dataType : "json",
		success : callback
	});
}

/*提交表单*/
function sendForm(formId, post_url, return_url) {
	if ($("#ajax").val() == 1) {
		var vars = $("#" + formId).serialize();
		$.ajax({
			type : "POST",
			url : post_url,
			data : vars,
			dataType : "json",
			success : function(data) {
				if (data.status) {
					ui_alert(data.info, function() {
						if (return_url) {
							location.href = return_url;
						}
					});
				} else {
					ui_error(data.info);
				}
			}
		});
	} else {
		$("#" + formId).attr("action", post_url);
		if (return_url) {
			set_cookie('return_url', return_url);
		}
		$("#" + formId).submit();
	}
}

function click_nav_menu(obj_node) {
	url = $(obj_node).attr("href");
	if (url.length > 0 && (url != "#")) {
		node = $(obj_node).attr("node");
		set_cookie("current_node", node);
	} else {
		//node = $(obj_node).parent().find("ul li a:first").attr("node");
		//set_cookie("current_node", node);
		//url = $(obj_node).parent().find("ul li a:first").attr("href");
		//if (url !== undefined) {
		//	location.href = url;
		//}
		//return false;
	}
}

/*设置 cookie*/
function set_cookie(key, value, exp, path, domain, secure) {
	path = "/";
	var cookie_string = key + "=" + escape(value);
	if (exp) {
		cookie_string += "; expires=" + exp.toGMTString();
	}
	if (path)
		cookie_string += "; path=" + escape(path);
	if (domain)
		cookie_string += "; domain=" + escape(domain);
	if (secure)
		cookie_string += "; secure";
	document.cookie = cookie_string;
}

/*读取 cookie*/
function get_cookie(cookie_name) {
	var results = document.cookie.match('(^|;) ?' + cookie_name + '=([^;]*)(;|$)');
	if (results)
		return (unescape(results[2]));
	else
		return null;
}

/*删除 cookie*/
function del_cookie(cookie_name) {
	var cookie_date = new Date();
	//current date & time
	cookie_date.setTime(cookie_date.getTime() - 1);
	document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString();
}


$(document).ready(function() {
	$(".sidebar .nav a").click(function() {
		click_nav_menu($(this));
	})
	$('.ul_table .tbody input[type=checkbox]').removeAttr('checked');
	$('.ul_table').delegate('.tbody input[type=checkbox]', 'click', function() {
		$(this).closest('.tbody').toggleClass('selected');
		if (this.checked)
			ul_table.display_bar(1);
		//display action toolbar when a message is selected
		else {
			ul_table.display_bar($('.ul_table input[type=checkbox]:checked').length);
			//determine number of selected messages and display/hide action toolbar accordingly
		}
	});
	$('#id-toggle-all').removeAttr('checked').on('click', function() {
		if (this.checked) {
			ul_table.select_all();
		} else
			ul_table.select_none();
	});
	breadcrumb = "";
	current_node = get_cookie("current_node");
	$(".sidebar .nav a[node='" + current_node + "']").parents("li").each(function() {
		$(this).addClass("active open");
		breadcrumb = '<li>' + $(this).find("a:first").text() + '</li>' + breadcrumb;
	});
	$(".breadcrumb").append(breadcrumb);
	top_menu = get_cookie("top_menu");
	$(".navbar-nav a.nav-app[node=" + top_menu + "]").addClass("active");
});
