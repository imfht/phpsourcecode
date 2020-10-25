/*
 *************************************** ajax 库 *************************************
 *
 *   @author      lensic [mhy]
 *   @link        http://www.lensic.cn/
 *   @copyright   Copyright (c) 2013 - , lensic [mhy].
 *
 *	功能：异步更新，无刷新技术
 *
 *	参数：
 *		url 	  : 异步传输的处理页
 *		back_fun  : 需绑定的回调函数
 *		send_data : 需要发送的数据
 *		json 	  : 其他参数
 *
 *	调用：
 *		ajax(url, back_fun, send_data, json)
 *
 *	例如：
 *		ajax('testajax.php?pid=' + 3, back_ajax, '', {pid:3})   ---   get 方式
 *
 *		ajax('testajax.php', back_ajax, 'pid = 3', {pid:3})     ---   post 方式
 *
 *	返回值：
 *		return null
 *
 *************************************************************************************
 */

// 该函数用于创建 ajax 对象
function create_ajax() {
	var ajax;
	if (window.XMLHttpRequest) {
		// 非 IE 浏览器实例化 XMLHttpRequest 对象
		ajax = new XMLHttpRequest();
	} else {
		// IE 各版本浏览器实例化 XMLHttpRequest 对象
		if (window.ActiveXObject) {
			try {
				ajax = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try {
					ajax = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {

				}
			}
		}
	}
	if (!ajax) {
		alert('无法创建 XMLHttpRequest 对象实例！');
		return false;
	}
	return ajax;
}

// 该函数用于建立异步传输
function ajax(url, back_fun, send_data, json) {
	// 默认值的处理
	send_data = send_data ? send_data : null;
	method = send_data ? 'post' : 'get';
	// 第一步：调用函数创建 ajax 对象
	var ajax = create_ajax();
	// 第二步：绑定回调函数
	ajax.onreadystatechange = function() {
		back_ajax(ajax, back_fun, json);
	};
	// 第三步：建立连接，实现异步传输
	url += (url.indexOf('?') != -1 ? '&' : '?') + Math.random();
	ajax.open(method, url, true);
	// post 方式设置传输编码
	if (method == 'post') {
		ajax.setRequestHeader("Content-Type",
				"application/x-www-form-urlencoded");
	}
	// 第四步：发送数据
	ajax.send(send_data);
}

// 该函数用于回调数据的处理
function back_ajax(ajax, back_fun, json) {
	// 回调数据验证
	if (ajax.readyState == 4 && ajax.status == 200) {
		// 调用回调函数，实现回调数据处理
		back_fun(ajax, json);
	}
}

/*
 * @author lensic [mhy] @link http://www.lensic.cn/ @copyright Copyright (c)
 * 2013 - , lensic [mhy].
 * 
 * 功能：实现无级别分类下拉列表
 * 
 * 调用： <script type="text/javascript" src="lib_ajax_cate.js"></script> <script
 * type="text/javascript"> // 从顶级分类 0 开始，显示无限级别分类
 * stepless_classification('show_list_one', 'pid[]', 'index.php?pid=', 0); // 从
 * id=11 数据开始，不显示 id=11 数据项，但显示它的同类和父类 stepless_classification('show_list_two',
 * 'pid[]', 'index.php?pid=', 3, '&id=11'); // 从 id=11 数据开始，显示 id=11
 * 数据项并选中，同时显示它的同类和父类，但不显示子类 stepless_classification('show_list_three', 'pid[]',
 * 'index.php?pid=', 3, '&id=11', '&type=1'); // 从 id=11 数据开始，显示 id=11
 * 数据项并选中，同时显示它的同类、父类和子类 stepless_classification('show_list_four', 'pid[]',
 * 'index.php?pid=', 3, '&id=11', '&type=2'); </script>
 * 
 */
// 无级分类下拉列表封装
function stepless_classification(obj_id, select_name, ajax_url, pid, id_str,
		display_type, option_default, select_level) {
	select_level = select_level ? select_level : 0;
	if (isNaN(parseInt(pid))) {
		remove_stepless_classification(select_level, obj_id);
	} else {
		ajax(ajax_url + pid
				+ (id_str ? id_str : (option_default ? option_default : ''))
				+ (display_type ? display_type : ''),
				back_stepless_classification, '', {
					obj_id : obj_id,
					select_name : select_name,
					ajax_url : ajax_url,
					pid : pid,
					id_str : id_str,
					display_type : display_type,
					option_default : option_default,
					select_level : select_level
				});
	}
}

// 无级分类回调函数
function back_stepless_classification(ajax, json) {
	if (json.pid != 0) {
		remove_stepless_classification(json.select_level, json.obj_id);
	}
	var rt = ajax.responseText;
	// alert(rt); return false;
	var sel = document.createElement('select');
	var obj = document.getElementById(json.obj_id);
	if (json.id_str) {
		obj.insertBefore(sel, obj.firstChild);
		if (json.display_type && !json.option_default) {
			// 去掉注释为显示但不选中
			/*
			 * if(isNaN(parseInt(json.display_type))) { var type =
			 * json.display_type.split('='); var type_have = type[1]; } else {
			 * var type_have = parseInt(json.display_type); } if(type_have == 2) {
			 */
			var last_id = json.id_str.split('=');
			json.option_default = typeof (last_id[1]) == 'undefined' ? last_id[0]
					.replace(/\//g, '')
					: last_id[1];
			/* } */
		}
	} else {
		obj.appendChild(sel);
	}
	sel.name = json.select_name;
	sel.setAttribute("style", "width:100px");
	sel.options[0] = new Option('请选择', '');
	var back_data = eval(rt);
	eval(back_data.sels);
	if (json.id_str) {
		default_stepless_classification(sel, json.option_default);
		if (back_data.ppid) {
			stepless_classification(json.obj_id, json.select_name,
					json.ajax_url, back_data.ppid, json.id_str,
					json.display_type, back_data.pid, json.select_level + 1);
		} else {
			var sels = obj.getElementsByTagName('select');
			var len = sels.length;
			for ( var i = 0; i < len; i++) {
				sels[i].setAttribute('level', i);
				sels[i].onchange = function() {
					stepless_classification(json.obj_id, json.select_name,
							json.ajax_url, parseInt(this.value), '',
							json.display_type, json.id_str, parseInt(this
									.getAttribute('level')) + 1);
				};
			}
			if (json.display_type) {
				if (isNaN(parseInt(json.display_type))) {
					var type = json.display_type.split('=');
					var type_have = type[1];
				} else {
					var type_have = parseInt(json.display_type);
					json.display_type = '/' + json.display_type;
				}
				if (type_have == 2) {
					var last_id = json.id_str.split('=');
					stepless_classification(json.obj_id, json.select_name,
							json.ajax_url,
							typeof (last_id[1]) == 'undefined' ? last_id[0]
									.replace(/\//g, '') : last_id[1], '',
							json.display_type, '', json.select_level + 1);
				}
			}
		}
	} else {
		sel.onchange = function() {
			stepless_classification(json.obj_id, json.select_name,
					json.ajax_url, parseInt(this.value), json.id_str,
					json.display_type, json.option_default,
					json.select_level + 1);
		};
	}
}

// 下拉列表值改变时，移除后续下拉列表
function remove_stepless_classification(select_level, obj_id) {
	var sel_list = document.getElementById(obj_id);
	var sels = sel_list.getElementsByTagName('select');
	var len = sels.length;
	for ( var i = len - 1; i >= select_level; i--) {
		sel_list.removeChild(sels[i]);
	}
}

// 形成的下拉列表默认选中值
function default_stepless_classification(obj_id, id_value) {
	obj_id = typeof (obj_id) == 'object' ? obj_id : document
			.getElementById(obj_id);
	var opts = obj_id.options;
	var len = opts.length;
	for ( var i = 0; i < len; i++) {
		if (opts[i].value == id_value) {
			opts[i].selected = true;
			break;
		}
	}
}

// ------------------------------------------------------------------------

// 单选默认选中
function radio_select(name, value) {
	$('input[name="' + name + '"]').each(function() {
		if ($(this).val() == value) {
			$(this).attr('checked', true);
			return false;
		}
	});
}

// 权限组中权限的点击
function power_click(obj_this, n) {
	if ($(obj_this).attr('checked')) {
		if (n == 1) {
			$('#dj_' + n).find('div').find('div:eq(0)').find('span:eq(0)')
					.find('input:eq(0)').removeAttr('disabled');
			$('#dj_' + n).find('div').find('span.eq(0)').find('div').find(
					'span:eq(0)').find('input:eq(0)').attr('disabled', true);
		} else {
			$('#dj_' + n).find('div').find('span').find('input').removeAttr(
					'disabled');
		}
	} else {
		if (n == 1) {
			$('#dj_' + n).find('div').find('div:eq(0)').find('span:eq(0)')
					.find('input:eq(0)').attr('disabled', true).removeAttr(
							'checked').click();
		} else {
			$('#dj_' + n).find('div').find('span').find('input').attr(
					'disabled', true).removeAttr('checked').click();
		}
	}
}

// 权限组默认选中权限
function default_sel(ids_str) {
	var power_sel = ids_str.split(',');
	$(power_sel).each(
			function() {
				var sel_val = this;
				$('input[name="powers[]"]').each(
						function() {
							if ($(this).val() == sel_val) {
								$(this).attr('checked', true);
								$('#dj_' + $(this).val() + ' input')
										.removeAttr('disabled');
								$('#dj_' + $(this).val() + ' span input').attr(
										'disabled', true);
								return false;
							}
						});
			});
}
