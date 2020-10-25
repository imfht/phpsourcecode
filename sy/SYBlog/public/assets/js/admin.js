(function($) {
	if (typeof(window.sy) !== 'undefined') {
		var sy = window.sy;
	} else {
		var sy = {};
	}
	var isLoading = false;
	var nowPage = 0;

	/*--------------------------------------------------------------------------*/
	
	/**
	 * 显示、隐藏加载
	 */
	function hideLoading() {
		$('#loading').addClass('hide');
		$('#loading_bg').hide();
	}
	function showLoading() {
		$('#loading').removeClass('hide');
		$('#loading_bg').show();
	}
	/**
	 * 处理Ajax结果
	 * @param mixed response Ajax结果
	 * @param object callback 回调
	 * @param function callback[success] 验证成功回调
	 * @param function callback[fail] 验证失败回调（可选）
	 */
	function ajaxResult(response, callback) {
		if (typeof(response) !== 'object' && response.substr(0, 1) === '{') { //JSON
			response = $.parseJSON(response);
		}
		if (typeof(response) === 'object') {
			if (response.success == 1) {
				callback.success(response);
			} else {
				Materialize.toast(response.message, 5000);
				if (typeof(callback.fail) !== 'undefined') {
					callback.fail();
				}
			}
		} else {
			callback.success(response);
		}
	}
	/**
	 * 获取日期和时间
	 * @param int time 时间戳
	 * @return object
	 */
	function dateGet(time) {
		var d = new Date();
		if (typeof(time) !== 'undefined') {
			time = time.toString();
			if (time.length === 10) {
				time += '000';
			}
			d.setTime(time);
		}
		var Month = (d.getMonth() + 1).toString(),
		Day = d.getDate().toString(),
		Hour = d.getHours().toString(),
		Minute = d.getMinutes().toString(),
		Second = d.getSeconds().toString();
		if (Month.length === 1) {
			Month = '0' + Month;
		}
		if (Day.length === 1) {
			Day = '0' + Day;
		}
		if (Hour.length === 1) {
			Hour = '0' + Hour;
		}
		if (Minute.length === 1) {
			Minute = '0' + Minute;
		}
		if (Second.length === 1) {
			Second = '0' + Second;
		}
		return {
			'date': d.getFullYear() + '-' + Month + '-' + Day,
			'time': Hour + ':' + Minute + ':' + Second,
			'Y': d.getFullYear(),
			'm': Month,
			'd': Day,
			'h': Hour,
			'i': Minute,
			's': Second
		}
	}
	/**
	 * 生成页码导航
	 * @param object box 放置页码的总元素
	 * @param object param 参数
	 * @param int param[now] 当前页码
	 * @param int param[max] 最大页码
	 * @param string param[itembox] 单个元素的box类型
	 * @param string param[itemclass] 单个元素的class
	 * @param string param[current] 当前元素的class
	 * @param string param[disabled] 禁用元素的class
	 * @param string param[prev] “上一页”的class
	 * @param string param[next] “下一页”的class
	 * @param string param[spaces] “...”的class
	 * @return object
	 */
	function pagination(box, param) {
		var now = param.now,
		max = param.max;
		box = $(box);
		if (max <= 0) {
			max = 1;
		}
		var getElement = function() {
			if (typeof(param.itembox) === 'undefined') {
				var itembox = 'li';
			} else {
				var itembox = param.itembox;
			}
			var element = document.createElement(itembox);
			var child = document.createElement('a');
			//单个item的class
			if (typeof(param.itemclass) !== 'undefined') {
				$(element).addClass(param.itemclass);
			} else {
				$(element).addClass('waves-effect');
			}
			$(element).append(child);
			return element;
		};
		var setHtml = function(element, content) {
			$(element).find('a').html(content);
		}
		//current
		if (typeof(param.current) === 'undefined') {
			var current = 'active';
		} else {
			var current = param.current;
		}
		//disabled
		if (typeof(param.disabled) === 'undefined') {
			var disabled = 'disabled';
		} else {
			var disabled = param.disabled;
		}
		//prev
		if (typeof(param.prev) === 'undefined') {
			var prev = 'prev';
		} else {
			var prev = param.prev;
		}
		//next
		if (typeof(param.next) === 'undefined') {
			var next = 'next';
		} else {
			var next = param.next;
		}
		//spaces
		if (typeof(param.spaces) === 'undefined') {
			var spaces = 'spaces';
		} else {
			var spaces = param.spaces;
		}
		var r = '';
		var e;
		//上一页
		e = getElement();
		$(e).addClass(prev);
		setHtml(e, '<i class="mdi-arrow-back"></i>');
		if (now === 1) {
			$(e).addClass(disabled);
		}
		box.append(e);
		//页码
		if (max > 5) {
			if (now < 3) {
				var end = 5;
			} else {
				var end = now + 2;
			}
			var start = end - 4;
		} else {
			var start = 1;
			var end = max;
		}
		//第一页
		if (start > 1) {
			e = getElement();
			setHtml(e, '1');
			box.append(e);
			if (start !== 2) {
				e = getElement();
				$(e).addClass(spaces);
				setHtml(e, '...');
				box.append(e);
			}
		}
		for (var i = start; i <= end; i++) {
			e = getElement();
			if (i === now) {
				$(e).addClass(current);
			}
			setHtml(e, i);
			box.append(e);
		}
		//最后一页
		if (end !== max) {
			if ((max - 1) !== end) {
				e = getElement();
				$(e).addClass(spaces);
				setHtml(e, '...');
				box.append(e);
			}
			e = getElement();
			setHtml(e, max);
			box.append(e);
		}
		//下一页
		e = getElement();
		$(e).addClass(next);
		setHtml(e, '<i class="mdi-arrow-forward"></i>');
		if (now === max) {
			$(e).addClass(disabled);
		}
		box.append(e);
		return box;
	}
	/**
	 * Tag：输入框To字符串
	 * @param object el 输入框对象
	 * @return string
	 */
	function getTag(el) {
		var tags = $(el).find('.selection-item'),
		r = '';
		$(tags).each(function(i, n) {
			r += $(n).attr('title') + ',';
		});
		r = r.replace(/,$/, '');
		return r;
	}
	/**
	 * Tag：字符串To输入框
	 * @param object el 输入框对象
	 * @param string tag Tag
	 */
	function setTag(el, tag) {
		$(el).find('.selection-item').remove();
		addTag(el, tag);
	}
	/**
	 * Tag：增加一个或多个Tag
	 * @param object el 输入框对象
	 * @param string tag Tag
	 */
	function addTag(el, tag) {
		el = $(el);
		var addEl = el.find('.selection-input');
		var v = tag.split(',');
		for (var i = 0; i < v.length; i++) {
			if (v[i] !== '') {
				//判断是否存在
				if ($(el).find('.selection-item[title=\'' + v[i] + '\']').length === 0) {
					$(addEl).before('<li title="' + v[i] + '" class="chip selection-item">' + v[i] + '<i class="mdi-close"></i></li>');
				}
			}
		}
	}
	/**
	 * Tag输入
	 * 绑定输入框事件
	 * 绑定选择框事件
	 * 绑定删除按钮事件
	 */
	$('.selection-input-field').bind('keyup paste',
	function() {
		var v = $(this).val(),
		selection = $(this).parents('.selection'),
		selectionList = $(selection).find('.selection-list');
		$(selectionList).css({
			"display": 'none',
			"opacity": 0
		});
		$(selectionList).html('');
		if (v === '') {
			return;
		}
		if (v.indexOf(',') >= 0) {
			addTag(selection, v);
			$(this).val('');
			return;
		}
		$.ajax({
			'url': sy.url.meta,
			'type': 'POST',
			'data': {
				'ajax': 1,
				'title': v
			},
			'success': function(response) {
				ajaxResult(response, {
					'success': function(r) {
						if (r.list.length === 0) {
							return;
						}
						$(r.list).each(function(i, n) {
							$(selectionList).append('<li><a>' + n.title + '</a></li>');
						});
						$(selectionList).css({
							"display": 'block',
							"opacity": 1
						});
					}
				});
			}
		});
	});
	$('.selection-list').on('click', 'a',
	function() { //点击选项
		var t = $(this).html(),
		input = $(this).parents('.selection').find('.selection-input-field');
		$(this).parents('.selection-list').css({
			"display": 'none',
			"opacity": 0
		});
		$(input).val('');
		addTag($(this).parents('.selection'), t);
		$(input).focus();
	});
	$('.selection').on('click', '.selection-item',
	function() { //移除
		var input = $(this).parents('.selection').find('.selection-input-field');
		$(this).remove();
		$(input).focus();
	});
	$('.selection-input-field').trigger('keyup');
	//附件上传组件
	var uploader = {
		"files": [],
		"uploading": false,
		"nowFileId": 0,
		"getExt": function(name) {
			if (name.indexOf('.') === -1) {
				return null;
			}
			while (name.indexOf('.') > -1) {
				name = name.substr(name.indexOf('.') + 1);
			}
			return name;
		},
		"addFile": function(file, el) {
			this.files.push(file);
			var newEl = $('<li/>', {
				"class": "collection-item progress"
			});
			$(newEl).append($('<p/>', {
				"text": file.name,
				"class": "name"
			}));
			$(newEl).append($('<button/>', {
				"class": "waves-effect waves-light btn edit",
				"disabled": "true"
			}).append($('<i/>', {
				"class": "mdi-edit"
			})));
			$(newEl).append($('<div/>', {
				"class": "determinate"
			}));
			this.files[this.files.length - 1].element = newEl;
			$(newEl).data('fileId', this.files.length - 1);
			$(el).append(newEl);
			this.upload();
		},
		"setPercent": function(fileId, percent) {
			$(this.files[fileId].element).find('.determinate').css({
				"width": percent.toString() + '%'
			});
		},
		"setError": function(fileId) {
			this.setPercent(fileId, 100);
			$(this.files[fileId].element).addClass('fail');
			this.nowFileId++;
			this.uploading = false;
			this.upload();
		},
		"setSuccess": function(fileId, id, url, type) {
			this.setPercent(fileId, 100);
			$(this.files[fileId].element).addClass('success');
			$(this.files[fileId].element).data('url', url);
			$(this.files[fileId].element).data('type', type);
			$(this.files[fileId].element).data('id', id);
			$(this.files[fileId].element).find('.edit').removeAttr('disabled');
			this.nowFileId++;
			this.uploading = false;
			this.upload();
		},
		"upload": function() {
			if (this.uploading) {
				return;
			}
			if (typeof(this.files[this.nowFileId]) === 'undefined') {
				return;
			}
			var formData = new FormData();
			formData.append('file', this.files[this.nowFileId]);
			this.setPercent(this.nowFileId, 0);
			this.uploading = true;
			var _this = this;
			var xhr = new XMLHttpRequest();
			xhr.upload.onprogress = function(evt) {
				var Percent = Math.round(evt.loaded / evt.total) * 100;
				_this.setPercent(_this.nowFileId, Percent);
			};
			xhr.upload.onerror = function() {
				_this.setError(_this.nowFileId);
				_this.uploading = false;
				_this.upload();
			};
			xhr.onreadystatechange = function() {
				if (xhr.readyState === 4 && xhr.status === 200) {
					var result = xhr.responseText;
					console.log(result);
					try {
						result = $.parseJSON(result);
					} catch(e) {
						_this.setError(_this.nowFileId);
						Materialize.toast(result, 5000);
					}
					if (typeof(result.message) !== 'undefined') {
						_this.setError(_this.nowFileId);
						Materialize.toast(result.message, 5000);
					} else {
						_this.setSuccess(_this.nowFileId, result.id, result.url, result.type);
					}
				}
				if (xhr.status >= 400) {
					_this.setError(_this.nowFileId);
					Materialize.toast('HTTP ' + xhr.status.toString() + ' 错误', 5000);
				}
			};
			xhr.open('POST', sy.url.upload, true);
			xhr.setRequestHeader("Cache-Control", "no-cache");
			xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			xhr.send(formData);
		}
	};
	$('#upload_button').bind('click',
	function() {
		if (typeof(FormData) === "undefined" || typeof(FormData) === 'unknow') {
			alert('浏览器不支持');
			return;
		}
		$('#upload_input').trigger('click');
	});
	$('#upload_input').bind('change',
	function() {
		var ext = $(this).attr('data-ext');
		//扩展名检查
		if (typeof(ext) !== 'undefined' && ext !== null) {
			ext = ext.split(':');
			var exttype = ext[0];
			var fileext = uploader.getExt(this.files[0].name.toLowerCase());
			ext = ext[1].split(',');
			if (fileext !== null) {
				if (exttype === 'allow' && ext.indexOf(fileext) === -1) {
					Materialize.toast('此类型文件禁止上传', 5000);
					return;
				}
				if (exttype === 'refuse' && ext.indexOf(fileext) !== -1) {
					Materialize.toast('此类型文件禁止上传', 5000);
					return;
				}
			}
		}
		//上传大小检查
		var maxSize = $(this).attr('data-maxsize');
		var fileSize = this.files[0].size;
		if (typeof(maxSize) !== 'undefined' && maxSize !== null && maxSize !== '') {
			maxSize = parseInt(maxSize);
			if (maxSize < fileSize) {
				Materialize.toast('文件太大了', 5000);
				return;
			}
		}

		uploader.addFile(this.files[0], $(this).parents('#attachment').children('.collection')[0]);
	});
	//
	function size2bytes(size) {
		var units = ['K', 'M', 'G', 'T'];
		size = size.toUpperCase();
		var unit = size.substr(size.length - 1, 1);
		var num = parseFloat(size.substr(0, size.length - 1));
		if (units.indexOf(unit) < 0) {
			return null;
		}
		switch (unit) {
		case 'K':
			return Math.ceil(num * 1024);
		case 'M':
			return Math.ceil(num * 1048576);
		case 'G':
			return Math.ceil(num * 1073741824);
		case 'T':
			return Math.ceil(num * 1099511627776);
		}
	};
	function bytes2size(bytes) {
		bytes *= 100;
		if (bytes < 102400) {
			return (bytes / 100).toString() + 'B';
		}
		if (bytes >= 102400 && bytes < 104857600) {
			//KB-MB
			return (Math.ceil(bytes / 1024) / 100).toString() + 'K';
		}
		if (bytes >= 104857600 && bytes < 107374182400) {
			//MB-GB
			return (Math.ceil(bytes / 1048576) / 100).toString() + 'M';
		}
		if (bytes >= 107374182400 && bytes < 109951162777600) {
			//GB-TB
			return (Math.ceil(bytes / 1073741824) / 100).toString() + 'G';
		}
		if (bytes >= 109951162777600) {
			//TB
			return (Math.ceil(bytes / 1099511627776) / 100).toString() + 'T';
		}
	}

	/*--------------------------------------------------------------------------*/

	//编辑器
	if ($('textarea[data-provide=\'markdown\']').length) {
		$('textarea[data-provide=\'markdown\']').markdown({
			language: 'zh',
			fullscreen: {
				enable: true
			},
			autofocus: true,
			resize: 'vertical',
			base64url: sy.url.base64Upload
		});
		if (typeof(window.localStorage) !== 'undefined' && typeof(window.localStorage) !== 'unknow') {
			if (sy.page === 'ArticleEdit') {
				var cacheContent = window.localStorage.getItem('syblog_content_' + sy.article.id);
				var cacheTime = parseInt(window.localStorage.getItem('syblog_time_' + sy.article.id));
				if (parseInt(sy.article.modify) > cacheTime || typeof(cacheContent) !== 'string' || cacheContent.trim() === '') {
					//在上次缓存后文章有更新
					$('#body').data('markdown').setContent(toMarkdown(sy.article.body));
				} else {
					//从缓存取出内容
					$('#body').data('markdown').setContent(cacheContent);
				}
				//自动保存
				setInterval(function() {
					window.localStorage.setItem('syblog_time_' + sy.article.id, new Date().getTime().toString().substr(0, 10));
					window.localStorage.setItem('syblog_content_' + sy.article.id, $('#body').val());
				},
				5000);
				//Tag
				setTag($('#tag_box'), sy.article.tag);
			} else {
				if (window.localStorage.getItem('syblog_add') !== null) {
					$('#body').data('markdown').setContent(window.localStorage.getItem('syblog_add'));
				}
				//自动保存
				setInterval(function() {
					window.localStorage.setItem('syblog_add', $('#body').val());
				},
				5000);
			}
		} else {
			if (sy.page === 'ArticleEdit') {
				$('#body').data('markdown').setContent(toMarkdown(sy.article.body));
			}
		}
	}
	//文章提交
	$('#article_submit').bind('click',
	function() {
		var data = {};
		if (sy.page === 'ArticleEdit') {
			data.id = sy.article.id;
		}
		data.title = $('#title').val();
		if (data.title === '') {
			Materialize.toast('标题不能为空', 5000);
			return;
		}
		//是否为快速编辑
		if (sy.page === 'ArticleList') {
			data.id = $('#aid').val();
			data.time = $('#datetime').val();
			url = sy.url.ajaxEdit;
			$('#list_edit').hide();
		} else {
			//自动转为HTML
			data.body = $('#body').data('markdown').parseContent();
			data.time = $('#date').val() + ' ' + $('#time').val();
			url = sy.url.submit;
		}
		if (data.time.match(/^(\d){4}\-(\d){2}\-(\d){2} (\d){2}:(\d){2}:(\d){2}$/) === null) {
			Materialize.toast('时间格式不正确', 5000);
			return;
		}
		//Tag相关
		if ($('#tag_input').val() !== '') {
			$('#tag_input').val($('#tag_input').val() + ',');
			$('#tag_input').trigger('keyup');
		}
		data.tags = getTag($('#tag_box'));
		showLoading();
		$.ajax({
			'url': url,
			'type': 'POST',
			'data': data,
			'success': function(response) {
				hideLoading();
				ajaxResult(response, {
					'success': function(r) {
						if (sy.page === 'ArticleList') {
							var el = $('#list').find('tr[data-id=\'' + data.id + '\']');
							el.attr('data-tags', data.tags);
							el.attr('data-time', r.time);
							el.find('.sub-header').html(data.title);
							Materialize.toast('已保存', 5000);
						} else {
							//移除自动保存内容
							if (typeof(window.localStorage) !== 'undefined') {
								if (sy.page === 'ArticleEdit') {
									window.localStorage.removeItem('syblog_time_' + sy.article.id);
									window.localStorage.removeItem('syblog_content_' + sy.article.id);
								} else {
									window.localStorage.removeItem('syblog_add');
								}
							}
							window.location.href = sy.url.list;
						}
					}
				});
			}
		});
	});

	//列表加载通用函数
	function listLoader(page, url, callback, data) {
		nowPage = page;
		showLoading();
		if (typeof(data) === 'undefined') {
			data = {};
		}
		data.ajax = 1;
		data.page = page;
		$.ajax({
			'url': url,
			'type': 'POST',
			'data': data,
			'success': function(response) {
				hideLoading();
				ajaxResult(response, {
					'success': function(r) {
						hideLoading();
						callback(r.list);
						//页码
						$('#page').html('');
						var maxPage = Math.ceil(r.count / 30);
						pagination($('#page'), {
							'max': maxPage,
							'now': nowPage,
							'itembox': 'li',
							'itemclass': 'waves-effect',
							'prev': 'prev',
							'next': 'next',
							'disabled': 'disabled',
							'current': 'active teal'
						});
					}
				});
			}
		});
	}
	function MyListLoader(p, eachCallback) {
		listLoader(p, sy.url.list,
		function(l) {
			$('#list').html('');
			//列表
			if (l.length) {
				$(l).each(function(i, n) {
					eachCallback(i, n);
				});
			} else {
				$('#list').append('<tr><td colspan="2" style="text-align:center"><p class="header">没有咯 ~ </p></td></tr>');
			}
			if (window.scrollY !== 0) {
				window.scrollTo(0, $('#list').offset().top);
			}
		});
	}
	
	//将Ajax获取到的信息处理为附件HTML
	function parseAttachment(n) {
		var r='';
		var date =  dateGet(n.time),
		imgurl = sy.url.img + '/icon/' + n.type.toString() + '.png';;
		r += '<div class="card" data-id="' + n.id + '"><div class="card-image">';
		if (n.type == 1) { //图片
			imgurl = n.url;
		}
		r += '<img src="' + imgurl + '"><span class="card-title">' + n.name + '</span></div>';
		r += '<div class="card-content"><p>大小:&nbsp;' + bytes2size(n.size) + '</p><p>日期:&nbsp;' + date.m + '月' + date.d + '日 ' + date.time + '</p><p>URL:&nbsp;<span class="url">' + n.url + '</span></div>';
		r += '<div class="card-action"><a class="edit">编辑</a><a class="del">删除</a></div>';
		r += '</div>';
		return r;
	}

	/*--------------------------------------------------------------------------*/

	//不同页面，不同操作
	var pageAction = {
		//登录页
		"login": function() {
			$('#form').bind('submit',
			function() {
				showLoading();
				$.ajax({
					'url': sy.url.submit,
					'type': 'POST',
					'data': {
						'password': $('#password').val()
					},
					'success': function(response) {
						hideLoading();
						if (response == 1) {
							window.location.href = sy.url.to;
						} else {
							Materialize.toast('密码错误，登录失败', 5000);
						}
					}
				});
			});
		},
		//文章列表
		"ArticleList": function() {
			var loaderCallback = function(i, n) {
				//地址
				var u = sy.url.edit.replace('__id__', n.id);
				var v = sy.url.view.replace('__id__', n.id);
				$('#list').append('<tr data-id="' + n.id + '" data-tags="' + n.tags + '" data-time="' + n.modify + '"><td><a href="' + u + '" target="_self" class="title">' + n.title + '</a></td><td class="action"><a href="' + v + '" target="_blank" class="waves-effect waves-light btn"><i class="mdi-open-in-new"></i></a><button class="waves-effect waves-light btn ajaxEdit"><i class="mdi-edit"></i></button><button class="waves-effect waves-light btn ajaxDel"><i class="mdi-delete"></i></button></td></tr>');
			};
			$('#list').on('click', '.ajaxEdit',
			function() {
				var p = $(this).parents('tr'),
				id = p.attr('data-id'),
				tags = p.attr('data-tags'),
				title = p.find('.title').html(),
				time = p.attr('data-time'),
				datetime = dateGet(time);
				$('#aid').val(id);
				$('#title').val(title);
				$('#datetime').val(datetime.date + ' ' + datetime.time);
				//Tag
				setTag($('#tag_box'), tags);
				//编辑URL
				$('#edit_full_mode').attr('href', sy.url.edit.replace('__id__', id));
				$('#list_edit').openModal();
			});
			$('#list').on('click', '.ajaxDel',
			function() {
				if (!confirm('删除后不可恢复，继续吗?')) {
					return;
				}
				var id = $(this).parents('tr').attr('data-id');
				showLoading();
				$.ajax({
					"url": sy.url.ajaxDel,
					"type": 'POST',
					"data": {
						"ajax": 1,
						"id": id
					},
					"success": function(response) {
						hideLoading();
						ajaxResult(response, {
							"success": function(r) {
								Materialize.toast('已删除', 5000);
								MyListLoader(nowPage, loaderCallback);
							}
						});
					}
				});
			});
			//页码相关
			isLoading = true;
			MyListLoader(1, loaderCallback);
			$('#page').on('click', 'li',
			function() {
				var p;
				if ($(this).hasClass('disabled') || $(this).hasClass('active')) {
					return;
				}
				if ($(this).hasClass('prev')) {
					p = nowPage - 1;
				} else if ($(this).hasClass('next')) {
					p = nowPage + 1;
				} else {
					p = parseInt($(this).find('a').html());
				}
				MyListLoader(p, loaderCallback);
			});
		},
		//链接
		"Link": function() {
			var loaderCallback = function(i, n) {
				$('#list').append('<tr data-id="' + n.id + '" data-url="' + n.url + '" data-rel="' + n.rel + '"><td class="sub-header">' + n.title + '</td><td class="action"><button class="waves-effect waves-light btn ajaxEdit"><i class="mdi-edit"></i></button><button class="waves-effect waves-light btn ajaxDel"><i class="mdi-delete"></i></button></td></tr>');
			};
			$('#list').on('click', '.ajaxEdit',
			function() {
				var p = $(this).parents('tr'),
				id = p.attr('data-id'),
				url = p.attr('data-url'),
				rel = p.attr('data-rel'),
				title = p.find('.sub-header').html();
				$('#link_form_title').html('编辑');
				$('#link_form').openModal();
				$('#link_id').val(id);
				$('#title').val(title).focus();
				$('#url').val(url).find('label').addClass('active');
				$('#rel').val(rel).find('label').addClass('active');
			});
			$('#link_add').bind('click',
			function() {
				$('#link_form_title').html('增加');
				$('#link_id').val('');
				$('#link_form').find('input[type=\'text\']').val('');
				$('#link_form').find('label.active').removeClass('active');;
				$('#link_form').openModal();
				$('#title').focus();
			});
			$('#list').on('click', '.ajaxDel',
			function() {
				if (!confirm('删除后不可恢复，继续吗?')) {
					return;
				}
				var id = $(this).parents('tr').attr('data-id');
				showLoading();
				$.ajax({
					"url": sy.url.ajaxDel,
					"type": 'POST',
					"data": {
						"ajax": 1,
						"id": id
					},
					"success": function(response) {
						hideLoading();
						ajaxResult(response, {
							"success": function(r) {
								Materialize.toast('已删除', 5000);
								MyListLoader(nowPage, loaderCallback);
							}
						});
					}
				});
			});
			//添加和编辑的提交
			$('#link_submit').bind('click',
			function() {
				var id = $('#link_id').val(),
				data = {},
				url = '';
				if (id === '') {
					url = sy.url.ajaxAdd;
				} else {
					data.id = id;
					url = sy.url.ajaxEdit;
				}
				data.title = $('#title').val();
				data.rel = $('#rel').val();
				data.url = $('#url').val();
				$('#link_form').closeModal();
				showLoading();
				$.ajax({
					"url": url,
					"type": 'POST',
					"data": data,
					"success": function(response) {
						hideLoading();
						ajaxResult(response, {
							"success": function(r) {
								MyListLoader(nowPage, loaderCallback);
							}
						});
					}
				});
			});
			//页码相关
			isLoading = true;
			MyListLoader(1, loaderCallback);
			$('#page').on('click', 'li',
			function() {
				var p;
				if ($(this).hasClass('disabled') || $(this).hasClass('active')) {
					return;
				}
				if ($(this).hasClass('prev')) {
					p = nowPage - 1;
				} else if ($(this).hasClass('next')) {
					p = nowPage + 1;
				} else {
					p = parseInt($(this).find('a').html());
				}
				MyListLoader(p, loaderCallback);
			});
		},
		//Meta
		"Meta": function() {
			var loaderCallback = function(i, n) {
				$('#list').append('<tr data-id="' + n.id + '" data-type="' + n.type + '"><td class="title">' + n.title + '</td><td>' + n.num + '</td><td class="action"><button class="waves-effect waves-light btn ajaxEdit"><i class="mdi-edit"></i></button></td></tr>');
			};
			//编辑
			$('#list').on('click', '.ajaxEdit',
			function() {
				var p = $(this).parents('tr'),
				id = p.attr('data-id'),
				type = parseInt(p.attr('data-type')),
				title = p.find('.title').html();
				$('#meta_form').openModal();
				$('#title').val(title).focus();
				$('#mid').val(id);
				$('#type').find('option').each(function(i, n) {
					n.selected = false;
				});
				$('#type').find('option[value=\'' + type.toString() + '\']')[0].selected = true;
			});
			$('#meta_submit').bind('click',
			function() {
				var title = $('#title').val(),
				id = $('#mid').val(),
				type = $('#type').val();
				$('#meta_form').closeModal();
				showLoading();
				$.ajax({
					"url": sy.url.ajaxEdit,
					"type": "POST",
					"data": {
						"id": id,
						"title": title,
						"type": type
					},
					"success": function(response) {
						ajaxResult(response, {
							"success": function(r) {
								var el = $('#list').find('tr[data-id=\'' + id + '\']');
								el.attr('data-type', type);
								el.find('.title').html(title);
								hideLoading();
							}
						});
					}
				});
			});
			//页码相关
			isLoading = true;
			MyListLoader(1, loaderCallback);
			$('#page').on('click', 'li',
			function() {
				var p;
				if ($(this).hasClass('disabled') || $(this).hasClass('active')) {
					return;
				}
				if ($(this).hasClass('prev')) {
					p = nowPage - 1;
				} else if ($(this).hasClass('next')) {
					p = nowPage + 1;
				} else {
					p = parseInt($(this).find('a').html());
				}
				MyListLoader(p, loaderCallback);
			});
		},
		//修改密码
		"OptionPassword": function() {
			$('#submit').bind('click',
			function() {
				var o = $('#old').val(),
				n = $('#new1').val();
				if (n.length < 5) {
					Materialize.toast('修改失败，密码必须不小于5个字符', 5000);
					return;
				}
				if (n !== $('#new2').val()) {
					Materialize.toast('修改失败，两次输入的密码不一致', 5000);
					return;
				}
				showLoading();
				$.ajax({
					'url': sy.url.submit,
					'type': 'POST',
					'data': {
						'ajax': 1,
						'old': o,
						'new': n
					},
					'success': function(response) {
						hideLoading();
						ajaxResult(response, {
							'success': function(r) {
								$('.page-content').find('input[type="password"]').val('');
								Materialize.toast('密码修改成功', 5000);
							}
						});
					}
				});
			});
		},
		//基本设置
		"Option": function() {
			$('#opt_bas_submit').bind('click',
			function() {
				var c = $('.option_input[data-changed=\'1\']');
				if (c.length === 0) {
					Materialize.toast('您没有做任何修改', 5000);
					return;
				}
				data = {};
				$(c).each(function(i, n) {
					n = $(n);
					data['op_' + n.attr('name')] = n.val();
				});
				showLoading();
				$.ajax({
					"url": sy.url.submit,
					"type": "POST",
					"data": data,
					"success": function(response) {
						hideLoading();
						ajaxResult(response, {
							"success": function(r) {
								Materialize.toast('已保存', 5000);
							}
						});
					}
				})
			});
			$('.option_input').bind('keyup paste change',
			function() {
				$(this).attr('data-changed', '1');
			});
		},
		//附件设置
		"OptionAttachment": function() {
			//远程开关
			$('#remoteCheck').bind('change',
			function() {
				if (this.checked) {
					$('#remote').show();
					$('#remoteType').trigger('change');
				} else {
					$('.remote_setting').hide();
					$('#remote').hide();
				}
			});
			//本地备份开关
			$('#backup').bind('change',
			function() {
				if (this.checked) {
					$('#backup_name').show();
				} else {
					$('#backup_name').hide();
				}
			});
			//远程类型
			$('#remoteType').bind('change',
			function() {
				var t = $(this).val();
				$('.remote_setting').hide();
				$('#setting_' + t).show();
			});
			//大小
			$('#upload_max_filesize').html(sy.phpMaxSize);
			sy.phpMaxSize = size2bytes(sy.phpMaxSize);
			$('#size').val(bytes2size(Math.min(parseInt(sy.optionMaxSize), sy.phpMaxSize)));
			$('#submit').bind('click',
			function() {
				var isRemote = $('#remoteCheck')[0].checked,
				data = {};
				data.format = $('input[name=\'format\']').val();
				data.size = size2bytes($('#size').val());
				if (data.size === null) {
					Materialize.toast('附件大小不能为空', 5000);
					return;
				}
				if (data.size > parseInt(sy.phpMaxSize)) {
					Materialize.toast('附件大小不能大于服务器限制', 5000);
					return;
				}
				if (!isRemote) {
					data.type = 'local';
				} else {
					data.url = $('input[name=\'remote_url\']').val();
					if (data.url.match(/\/$/) === null) {
						data.url += '/';
					}
					data.type = $('#remoteType').val();
					if ($('#backup')[0].checked) {
						data.backup = 1;
						data.backupFormat = $('input[name=\'backup_format\']').val();
						if (data.backupFormat === '') {
							Materialize.toast('备份格式不能为空', 5000);
							return;
						}
					} else {
						data.backup = 0;
					}
					$('.' + data.type).each(function(i, n) {
						n = $(n);
						data[n.attr('name')] = n.val();
					});
				}
				showLoading();
				$.ajax({
					"url": sy.url.submit,
					"type": "POST",
					"data": data,
					"success": function(response) {
						hideLoading();
						ajaxResult(response, {
							"success": function(r) {
								Materialize.toast('已保存', 5000);
							}
						});
					}
				})
			});
		},
		//SEO设置
		"OptionSEO": function() {
			$('.seosave').bind('click',
			function() {
				var type = $(this).attr('data-type');
				var form = $(this).parents('.form');
				var data = {
					"_type": type
				};
				$(form).find('.getme').each(function(i, n) {
					if ($(n).attr('type') === 'checkbox') {
						if (n.checked) {
							data[$(n).attr('name')] = 1;
						} else {
							data[$(n).attr('name')] = 0;
						}
					} else {
						data[$(n).attr('name')] = $(n).val();
					}
				});
				showLoading();
				$.ajax({
					'url': sy.url.submit,
					'type': 'POST',
					'data': data,
					'success': function(response) {
						hideLoading();
						ajaxResult(response, {
							'success': function(r) {
								Materialize.toast('已保存', 5000);
							}
						});
					}
				});
			});
			$('.seobox').bind('change',
			function() {
				var form = $(this).parents('.form');
				if (this.checked) {
					$(form).find('.input-field').show();
				} else {
					$(form).find('.input-field').hide();
				}
			});
			$('#updateSitemap').bind('click',
			function() {
				showLoading();
				$.ajax({
					'url': sy.url.sitemap,
					'type': 'POST',
					'data': {
						'password': sy.cronPassword,
						'action': 'make'
					},
					'success': function(response) {
						hideLoading();
						Materialize.toast('已生成', 5000);
					}
				})
			})
		},
		//附件管理
		"AttachmentManage": function(){
			var loading = false;
			var load = function(){
				if ($('#attachmentList').data('end') === true || loading === true) {
					return;
				}
				var lastId = $('#attachmentList').data('lastId');
				$('#loading_bottom').show();
				loading = true;
				$.ajax({
					"url": sy.url.list,
					"type": 'POST',
					"data": {
						"id": lastId
					},
					"success": function(response){
						ajaxResult(response, {
							'success': function(r) {
								$('#loading_bottom').hide();
								loading = false;
								if (r.list.length > 0) {
									$(r.list).each(function(i, n){
										var d = dateGet(n.time),
										ym = d.Y + d.m;
										if (ym !== $('#attachmentList').data('lastDate')) {
											$('#attachmentList').append('<h4>' + d.Y + '年' + d.m + '月</h4>');
											 $('#attachmentList').data('lastDate', ym);
										}
										$('#attachmentList').append(parseAttachment(n));
									});
									$('#attachmentList').data('lastId', r.lastId);
								} else {
									$('#none').show();
									$('#attachmentList').data('end', true);
								}
							}
						});
					}
				});
			}
			$('#attachmentList').data('lastId', 999999);
			$('#none').hide();
			load();
			$(window).bind('scroll',
			function(){
				var scrollTop = $(this).scrollTop();
				var scrollHeight = $(document).height();
				var windowHeight = $(this).height();
				if (Math.abs(scrollTop + windowHeight - scrollHeight) <= 100) {
					load();
				}
			});
			//删除按钮
			$('#attachmentList').on('click', '.del',
			function() {
				if (!confirm('删除后不可恢复，继续吗?')) {
					return;
				}
				var p = $(this).parents('.card');
				var id = p.attr('data-id');
				showLoading();
				$.ajax({
					'url': sy.url.attachmentDel,
					'type': 'POST',
					'data': {
						'id': id
					},
					'success': function(response) {
						hideLoading();
						ajaxResult(response, {
							'success': function(r) {
								p.hide();
								Materialize.toast('已删除', 5000);
							},
							'fail': function(r) {
								Materialize.toast('删除失败', 5000);
							}
						});
					}
				});
			});
		}
	}

	//添加附件到编辑器
	$('#attachment').children('.collection').on('click', '.name',
	function() {
		var p = $(this).parents('.collection-item');
		var name = $(this).html();
		var type = $(p).data('type');
		if (typeof(type) === 'undefined' || type === null) {
			return;
		}
		var url = $(p).data('url');
		if (type == 1) {
			//图片
			$('#body').data('markdown').setImageLink(url);
		} else {
			//其他附件
			$('#body').data('markdown').addLink(url, true, name);
		}
		$('#body').focus();
	});
	//编辑附件
	function openAttachmentEdit(id, name, url, el, callback) {
		var modal = $('#attachmentEdit');
		modal.data('el', el);
		modal.data('callback', callback);
		modal.find('input[name="id"]').val(id);
		modal.find('input[name="name"]').val(name);
		modal.find('input[name="url"]').val(url);
		modal.openModal();
	}
	$('#attachment').children('.collection').on('click', '.edit',
	function() {
		var p = $(this).parents('.collection-item');
		var name = $(p).find('.name').html();
		var url = $(p).data('url');
		var id = $(p).data('id');
		openAttachmentEdit(id, name, url, p, function(data){
			var el = data.el;
			el.data('type', data.type);
			el.find('.name').html(data.name);
		});
	});
	$('#attachmentList').on('click', '.edit',
	function() {
		var p = $(this).parents('.card');
		var name = $(p).find('.card-title').html();
		var url = $(p).find('.url').html();
		var id = $(p).attr('data-id');
		openAttachmentEdit(id, name, url, p, function(data){
			var el = data.el,
			imgurl = sy.url.img + '/icon/' + data.type.toString() + '.png';
			if (data.type == 1) { //图片
				imgurl = url;
			}
			el.find('img').attr('src', imgurl);
			el.find('.card-title').html(data.name);
		});
	});
	//编辑框
	$('#attachmentEdit').find('input[name="url"]').bind('focus',
	function() {
		$(this).select();
	});
	$('#attachment_submit').bind('click',
	function() {
		var modal = $('#attachmentEdit');
		var data = {
			"id": modal.find('input[name="id"]').val(),
			"name": modal.find('input[name="name"]').val()
		};
		if (data.name === '') {
			Materialize.toast('名称不能为空', 5000);
			return;
		}
		modal.closeModal();
		showLoading();
		$.ajax({
			'url': sy.url.attachmentEdit,
			'type': 'POST',
			'data': data,
			'success': function(response) {
				hideLoading();
				ajaxResult(response, {
					'success': function(r) {
						var callback = modal.data('callback');
						data.type = r.type;
						data.el = modal.data('el');
						callback(data);
					}
				});
			}
		});
	});
	if (sy.page === 'ArticleEdit') {
		$('#title').val(sy.article.title);
		//Tag
		setTag($('#tag_box'), sy.article.tag);
		//时间和日期
		var d = dateGet(sy.article.modify);
		$('#date').val(d.date);
		$('#time').val(d.time);
	}

	/*--------------------------------------------------------------------------*/

	//页面初始化
	//执行页面函数
	if (typeof(pageAction[sy.page]) !== 'undefined') {
		pageAction[sy.page]();
	}
	$('a[type="submit"]').bind('click',
	function() {
		$(this).parents('form').submit();
	});
	//日期选择器
	if ($('#date').length > 0) {
		$('#date').pickadate({
			format: "yyyy-mm-dd"
		});
	}
	//设置为当前时间
	$('#setToNow').bind('click',
	function() {
		var d = dateGet();
		$('#date').val(d.date);
		$('#time').val(d.time);
	});
	if (sy.page === 'ArticleNew') {
		$('#setToNow').trigger('click');
	}
	//触发checkbox
	$('input[type="checkbox"]').trigger('change');
	if (!isLoading) {
		hideLoading();
	}
})(jQuery);