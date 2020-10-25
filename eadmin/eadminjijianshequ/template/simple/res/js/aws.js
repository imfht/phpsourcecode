var AWS =
{
	//全局loading
	loading: function (type)
	{
		if (!$('#aw-loading').length)
		{
			$('#aw-ajax-box').append(AW_TEMPLATE.loadingBox);
		}

		if (type == 'show')
		{
			if ($('#aw-loading').css('display') == 'block')
			{
				return false;
			}

			$('#aw-loading').fadeIn();

			AWS.G.loading_timer = setInterval(function ()
			{
				AWS.G.loading_bg_count -= 1;

				$('#aw-loading-box').css('background-position', '0px ' + AWS.G.loading_bg_count * 40 + 'px');

				if (AWS.G.loading_bg_count == 1)
				{
					AWS.G.loading_bg_count = 12;
				}
			}, 100);
		}
		else
		{
			$('#aw-loading').fadeOut();

			clearInterval(AWS.G.loading_timer);
		}
	},

	loading_mini: function (selector, type)
	{
		if (!selector.find('#aw-loading-mini-box').length)
		{
			selector.append(AW_TEMPLATE.loadingMiniBox);
		}

		if (type == 'show')
		{
			selector.find('#aw-loading-mini-box').fadeIn();

			AWS.G.loading_timer = setInterval(function ()
			{
				AWS.G.loading_mini_bg_count -= 1;

				$('#aw-loading-mini-box').css('background-position', '0px ' + AWS.G.loading_mini_bg_count * 16 + 'px');

				if (AWS.G.loading_mini_bg_count == 1)
				{
					AWS.G.loading_mini_bg_count = 9;
				}
			}, 100);
		}
		else
		{
			selector.find('#aw-loading-mini-box').fadeOut();

			clearInterval(AWS.G.loading_timer);
		}
	},

	ajax_request: function(url, params)
	{
		AWS.loading('show');

		if (params)
		{
			$.post(url, params + '&_post_type=ajax', function (result)
			{
				_callback(result);
			}, 'json').error(function (error)
			{
				_error(error);
			});
		}
		else
		{
			$.get(url, function (result)
			{
				_callback(result);
			}, 'json').error(function (error)
			{
				_error(error);
			});
		}

		function _callback (result)
		{
			AWS.loading('hide');

			if (!result)
			{
				return false;
			}

			if (result.err)
			{
				AWS.alert(result.err);
			}
			else if (result.rsm && result.rsm.url)
			{
				window.location = decodeURIComponent(result.rsm.url);
			}
			else if (result.errno == 1)
			{
				window.location.reload();
			}
		}

		function _error (error)
		{
			AWS.loading('hide');

			if ($.trim(error.responseText) != '')
			{
				alert(_t('发生错误, 返回的信息:') + ' ' + error.responseText);
			}
		}

		return false;
	},

	ajax_post: function(formEl, processer, type) // 表单对象，用 jQuery 获取，回调函数名
	{
		// 若有编辑器的话就更新编辑器内容再提交
		if (typeof CKEDITOR != 'undefined')
		{
			for ( instance in CKEDITOR.instances ) {
				CKEDITOR.instances[instance].updateElement();
			}
		}

		if (typeof (processer) != 'function')
		{
			var processer = AWS.ajax_processer;

			AWS.loading('show');
		}

		if (!type)
		{
			var type = 'default';
		}
		else if (type == 'reply_question')
		{
			AWS.loading('show');

			$('.btn-reply').addClass('disabled');

			// 删除草稿绑定事件
			EDITOR.removeListener('blur', EDITOR_CALLBACK);
		}

		var custom_data = {
			_post_type: 'ajax'
		};
$.post($(formEl).attr('action'),$(formEl).serialize(),function(result){processer(type, result)})

	},

	// ajax提交callback
	ajax_processer: function (type, result)
	{
		AWS.loading('hide');
		
		if (typeof (result.code) == 'undefined')
		{
			alert(result);
		}
		else if (result.code != 1)
		{
			
			switch (type)
			{
				case 'default':
				case 'comments_form':
				

				case 'ajax_post_alert':
				case 'ajax_post_modal':
				case 'error_message':
					if (!$('.error_message').length)
					{
						layer.msg(result.msg, {icon: 2, anim: 6, time: 1000});
					}
					else if ($('.error_message em').length)
					{
						$('.error_message em').html(result.msg);
					}
					else
					{
						 $('.error_message').html(result.msg);
					}

					if ($('.error_message').css('display') != 'none')
					{
						AWS.shake($('.error_message'));
					}
					else
					{
						$('.error_message').fadeIn();
					}

					
				break;
			}
		}
		else
		{
			if (type == 'comments_form')
			{
				AWS.reload_comments_list(result.msg.item_id, result.msg.item_id, result.msg.type_name);
				$('#aw-comment-box-' + result.msg.type_name + '-' + result.msg.item_id + ' form textarea').val('');
				$('.aw-comment-box-btn .btn-success').removeClass('disabled');
			}

			if (result.url)
			{
				// 判断返回url跟当前url是否相同
				if (window.location.href == result.url)
				{
					window.location.reload();
				}
				else
				{
					window.location = decodeURIComponent(result.url);
				}
			}
			else
			{
				switch (type)
				{
					case 'default':
					case 'ajax_post_alert':
					case 'error_message':
						window.location.reload();
					break;

					case 'ajax_post_modal':
						$('#aw-ajax-box div.modal').modal('hide');
					break;

				}
			}
		}
	},
	// 加载更多
	load_list_view_tem: function(url, selector, container, start_page, callback)
	{
		if (!selector.attr('id'))
		{
			return false;
		}

		if (!start_page)
		{
			start_page = 0
		}

		// 把页数绑定在元素上面
		if (selector.attr('data-page') == undefined)
		{
			selector.attr('data-page', start_page);
		}
		else
		{
			selector.attr('data-page', parseInt(selector.attr('data-page')) + 1);
		}

		selector.bind('click', function ()
		{
			var _this = this;

			$(this).addClass('loading');

			$.get(url + '&page=' + $(_this).attr('data-page'), function (result)
			{
				$(_this).removeClass('loading');

				if (result.code== 1)
				{
					var html=callback(result);
					
					
					
					
					if ($(_this).attr('data-page') == start_page && $(_this).attr('auto-load') != 'false')
					{
						
						
						container.html(html);
					}
					else
					{
						container.append(html);
					}

					// 页数增加1
					$(_this).attr('data-page', parseInt($(_this).attr('data-page')) + 1);
				}
				else
				{
					//没有内容
					if ($(_this).attr('data-page') == start_page && $(_this).attr('auto-load') != 'false')
					{
						container.html('<p style="padding: 15px 0" align="center">' + _t('没有内容') + '</p>');
					}

					$(_this).addClass('disabled').unbind('click').bind('click', function () { return false; });

					$(_this).find('span').html(_t('没有更多了'));
				}

				
			});

			return false;
		});

		// 自动加载
		if (selector.attr('auto-load') != 'false')
		{
			selector.click();
		}
	},
	// 加载更多
	load_list_view: function(url, selector, container, start_page, callback)
	{
		if (!selector.attr('id'))
		{
			return false;
		}

		if (!start_page)
		{
			start_page = 0
		}

		// 把页数绑定在元素上面
		if (selector.attr('data-page') == undefined)
		{
			selector.attr('data-page', start_page);
		}
		else
		{
			selector.attr('data-page', parseInt(selector.attr('data-page')) + 1);
		}

		selector.bind('click', function ()
		{
			var _this = this;

			$(this).addClass('loading');

			$.get(url + '&page=' + $(_this).attr('data-page'), function (result)
			{
				$(_this).removeClass('loading');

				if (result.code== 1)
				{
					
					if ($(_this).attr('data-page') == start_page && $(_this).attr('auto-load') != 'false')
					{
						container.html(result.data.data);
					}
					else
					{
						container.append(result.data.data);
					}

					// 页数增加1
					$(_this).attr('data-page', parseInt($(_this).attr('data-page')) + 1);
				}
				else
				{
					//没有内容
					if ($(_this).attr('data-page') == start_page && $(_this).attr('auto-load') != 'false')
					{
						container.html('<p style="padding: 15px 0" align="center">' + _t('没有内容') + '</p>');
					}

					$(_this).addClass('disabled').unbind('click').bind('click', function () { return false; });

					$(_this).find('span').html(_t('没有更多了'));
				}

				if (callback != null)
				{
					callback();
				}
			});

			return false;
		});

		// 自动加载
		if (selector.attr('auto-load') != 'false')
		{
			selector.click();
		}
	},
	
	// 重新加载评论列表
	reload_comments_list: function(item_id, element_id, type_name)
	{
		$('#aw-comment-box-' + type_name + '-' + element_id + ' .aw-comment-list').html('<p align="center" class="aw-padding10"><i class="aw-loading"></i></p>');

		$.get(G_BASE_URL + 'index.php?c=Ajaxinfo&a=getpinglun&id=' + item_id, function (result)
		{
			
			$('#aw-comment-box-' + type_name + '-' + element_id + ' .aw-comment-list').html(result.data.data);
		});
	},
	
	// 文章加载
	article_read: function(selector, article_id)
	{
		$.post(G_BASE_URL + '/article/ajax/article_read/', 'article_id=' + article_id, function (result) {
			if (result.errno != 1)
			{
				AWS.alert(result.err);
			}
			else if (result.errno == 1)
			{
				var a=result.rsm.ajax_html;
				var t=a.replace(/\n/g, "</p><p>");
				$(selector).html(t);				
			}
		}, 'json');
	},	

	// 警告弹窗
	alert: function (text)
	{
		if ($('.alert-box').length)
		{
			$('.alert-box').remove();
		}

		$('#aw-ajax-box').append(Hogan.compile(AW_TEMPLATE.alertBox).render(
		{
			message: text
		}));

		$(".alert-box").modal('show');
	},

	/**
	 *	公共弹窗
	 *	publish     : 发起
	 *	redirect    : 问题重定向
	 *	imageBox    : 插入图片
	 *	videoBox    : 插入视频
	 *  linkbox     : 插入链接
	 *	commentEdit : 评论编辑
	 *  favorite    : 评论添加收藏
	 *	inbox       : 私信
	 *  report      : 举报问题
	 */
	dialog: function (type, data, callback)
	{
		switch (type)
		{
			case 'alertImg':
				var template = Hogan.compile(AW_TEMPLATE.alertImg).render(
				{
					'hide': data.hide,
					'url': data.url,
					'message': data.message
				});
			break;

			case 'publish':
				var template = Hogan.compile(AW_TEMPLATE.publishBox).render(
				{
					'category_id': data.category_id,
					'ask_user_id': data.ask_user_id
				});
			break;

			case 'redirect':
				var template = Hogan.compile(AW_TEMPLATE.questionRedirect).render(
				{
					'data_id': data
				});
			break;

			case 'commentEdit':
				var template = Hogan.compile(AW_TEMPLATE.editCommentBox).render(
				{
					'answer_id': data.answer_id,
					'attach_access_key': data.attach_access_key
				});
			break;

			case 'favorite':
				var template = Hogan.compile(AW_TEMPLATE.favoriteBox).render(
				{
					 'item_id': data.item_id,
					 'item_type': data.item_type
				});
			break;

			case 'inbox':
				var template = Hogan.compile(AW_TEMPLATE.inbox).render(
				{
					'recipient': data
				});
			break;
			
			case 'foxreply':
				var template = Hogan.compile(AW_TEMPLATE.foxreply).render(
				{
					'foxid': data
				});
			break;

			case 'report':
				var template = Hogan.compile(AW_TEMPLATE.reportBox).render(
				{
					'item_type': data.item_type,
					'item_id': data.item_id
				});
			break;

			case 'topicEditHistory':
				var template = AW_TEMPLATE.ajaxData.replace('{{title}}', _t('编辑记录')).replace('{{data}}', data);
			break;

			case 'ajaxData':
				var template = AW_TEMPLATE.ajaxData.replace('{{title}}', data.title).replace('{{data}}', '<div id="aw_dialog_ajax_data"></div>');
			break;

			case 'imagePreview':
				var template = AW_TEMPLATE.ajaxData.replace('{{title}}', data.title).replace('{{data}}', '<p align="center"><img src="' + data.image + '" alt="" style="max-width:520px" /></p>');
			break;

			case 'confirm':
				var template = Hogan.compile(AW_TEMPLATE.confirmBox).render(
				{
					'message': data.message
				});
			break;

			case 'recommend':
				var template = Hogan.compile(AW_TEMPLATE.recommend).render();
			break;
			case 'projectEventForm':
				var template = Hogan.compile(AW_TEMPLATE.projectEventForm).render(
				{
					'project_id': data.project_id,
					'contact_name': data.contact_name,
					'contact_tel': data.contact_tel,
					'contact_email': data.contact_email
				});
			break;

			case 'projectStockForm':
				var template = Hogan.compile(AW_TEMPLATE.projectStockForm).render(
				{
					'project_id': data.project_id,
					'contact_name': data.contact_name,
					'contact_tel': data.contact_tel,
					'contact_email': data.contact_email
				});
			break;

			case 'activityBox':
				var template = Hogan.compile(AW_TEMPLATE.activityBox).render(
				{
					'contact_name': data.contact_name,
					'contact_tel': data.contact_tel,
					'contact_qq': data.contact_qq
				});

			break;
			
			case 'ajaxLogin':
				var template = Hogan.compile(AW_TEMPLATE.ajaxLogin).render(
		        {
					'return_url':encodeURIComponent(window.location.href),
		        });
			break;
		}

		if (template)
		{
			if ($('.alert-box').length)
			{
				$('.alert-box').remove();
			}

			$('#aw-ajax-box').html(template).show();

			switch (type)
			{
				case 'redirect' :
					AWS.Dropdown.bind_dropdown_list($('.aw-question-redirect-box #question-input'), 'redirect');
				break;

				case 'inbox' :
					AWS.Dropdown.bind_dropdown_list($('.aw-inbox #invite-input'), 'inbox');
					//私信用户下拉点击事件
					$(document).on('click','.aw-inbox .aw-dropdown-list li a',function() {
						$('.alert-box #quick_publish input.form-control').val($(this).text());
						$(this).parents('.aw-dropdown').hide();
					});
				break;

				case 'publish':
					AWS.Dropdown.bind_dropdown_list($('.aw-publish-box #quick_publish_question_content'), 'publish');
					AWS.Dropdown.bind_dropdown_list($('.aw-publish-box #aw_edit_topic_title'), 'topic');
					if (parseInt(data.category_enable) == 1)
					{
						$.get(G_BASE_URL + '/publish/ajax/fetch_question_category/', function (result)
						{
							AWS.Dropdown.set_dropdown_list('.aw-publish-box .dropdown', eval(result), data.category_id);

							$('.aw-publish-title .dropdown li a').click(function ()
							{
								$('.aw-publish-box #quick_publish_category_id').val($(this).attr('data-value'));
								$('.aw-publish-box #aw-topic-tags-select').html($(this).text());
							});
						});
					}
					else
					{
						$('.aw-publish-box .aw-publish-title').hide();
					}

					if (data.ask_user_id != '' && data.ask_user_id != undefined)
					{
						$('.aw-publish-box .modal-title').html('向 ' + data.ask_user_name + ' 提问');
					}

					if ($('#aw-search-query').val() && $('#aw-search-query').val() != $('#aw-search-query').attr('placeholder'))
					{
						$('#quick_publish_question_content').val($('#aw-search-query').val());
					}

					AWS.Init.init_topic_edit_box('#quick_publish .aw-edit-topic');

					$('#quick_publish .aw-edit-topic').click();

					$('#quick_publish .close-edit').hide();

					if (data.topic_title)
					{
						$('#quick_publish .aw-edit-topic').parents('.aw-topic-bar').prepend('<span class="topic-tag"><a class="text">' + data.topic_title + '</a><a class="close" onclick="$(this).parents(\'.topic-tag\').detach();"><i class="icon icon-delete"></i></a><input type="hidden" value="' + data.topic_title + '" name="topics[]" /></span>')
					}

					if (typeof(G_QUICK_PUBLISH_HUMAN_VALID) != 'undefined')
					{
						$('#quick_publish_captcha').show();
						$('#captcha').click();
					}
				break;

				case 'favorite':
					$.get(G_BASE_URL + '/favorite/ajax/get_favorite_tags/', function (result)
					{
						var html = ''

						$.each(result, function (i, e)
						{
							html += '<li><a data-value="' + e['title'] + '"><span class="title">' + e['title'] + '</span></a><i class="icon icon-followed"></i></li>';
						});

						$('.aw-favorite-tag-list ul').append(html);

						$.post(G_BASE_URL + '/favorite/ajax/get_item_tags/', {
							'item_id' : $('#favorite_form input[name="item_id"]').val(),
							'item_type' : $('#favorite_form input[name="item_type"]').val()
						}, function (result)
						{
							$.each(result, function (i, e)
							{
								var index = i;

								$.each($('.aw-favorite-tag-list ul li .title'), function (i, e)
								{
									if ($(this).text() == result[index])
									{
										$(this).parents('li').addClass('active');
									}
								});
							});
						}, 'json');

						$(document).on('click', '.aw-favorite-tag-list ul li a', function()
						{
							var _this = this,
								addClassFlag = true, url = G_BASE_URL + '/favorite/ajax/update_favorite_tag/';

							if ($(this).parents('li').hasClass('active'))
							{
								url = G_BASE_URL + '/favorite/ajax/remove_favorite_tag/';

								addClassFlag = false;
							}

							$.post(url,
							{
								'item_id' : $('#favorite_form input[name="item_id"]').val(),
								'item_type' : $('#favorite_form input[name="item_type"]').val(),
								'tags' : $(_this).attr('data-value')
							}, function (result)
							{
								if (result.errno == 1)
								{
									if (addClassFlag)
									{
										$(_this).parents('li').addClass('active');
									}
									else
									{
										$(_this).parents('li').removeClass('active');
									}
								}
							}, 'json');
						});

					}, 'json');
				break;

				case 'report':
					$('.aw-report-box select option').click(function ()
					{
						$('.aw-report-box textarea').text($(this).attr('value'));
					});
				break;

				case 'commentEdit':
					$.get(G_BASE_URL + '/question/ajax/fetch_answer_data/' + data.answer_id, function (result)
					{
						$('#editor_reply').html(result.answer_content.replace('&amp;', '&'));

						var editor = CKEDITOR.replace( 'editor_reply' );

						if (UPLOAD_ENABLE == 'Y')
						{
							var fileupload = new FileUpload('file', '.aw-edit-comment-box .aw-upload-box .btn', '.aw-edit-comment-box .aw-upload-box .upload-container', G_BASE_URL + '/publish/ajax/attach_upload/id-answer__attach_access_key-' + ATTACH_ACCESS_KEY, {'insertTextarea': '.aw-edit-comment-box #editor_reply', 'editor' : editor});

							$.post(G_BASE_URL + '/publish/ajax/answer_attach_edit_list/', 'answer_id=' + data.answer_id, function (data) {
								if (data['err']) {
									return false;
								} else {
									$.each(data['rsm']['attachs'], function (i, v) {
										fileupload.setFileList(v);
									});
								}
							}, 'json');
						}
						else
						{
							$('.aw-edit-comment-box .aw-file-upload-box').hide();
						}
					}, 'json');
				break;

				case 'ajaxData':
					$.get(data.url, function (result) {
						$('#aw_dialog_ajax_data').html(result);
					});
				break;

				case 'confirm':
					$('.aw-confirm-box .yes').click(function()
					{
						if (callback)
						{
							callback();
						}

						$(".alert-box").modal('hide');

						return false;
					});
				break;

				case 'recommend':
					$.get(G_BASE_URL + '/help/ajax/list/', function (result)
					{
						if (result && result != 0)
						{
							var html = '';

							$.each(result, function (i, e)
							{
								html += '<li class="aw-border-radius-5"><img class="aw-border-radius-5" src="' + e.icon + '"><a data-id="' + e.id + '" class="aw-hide-txt">' + e.title + '</a><i class="icon icon-followed"></i></li>'
							});

							$('.aw-recommend-box ul').append(html);

							$.each($('.aw-recommend-box ul li'), function (i, e)
							{
								if (data.focus_id == $(this).find('a').attr('data-id'))
								{
									$(this).addClass('active');
								}
							});

							$(document).on('click', '.aw-recommend-box ul li a', function()
							{
								var _this = $(this), url = G_BASE_URL + '/help/ajax/add_data/', removeClass = false;

								if ($(this).parents('li').hasClass('active'))
								{
									url =  G_BASE_URL + '/help/ajax/remove_data/';

									removeClass = true;
								}

								$.post(url,
								{
									'item_id' : data.item_id,
									'id' : _this.attr('data-id'),
									'title' : _this.text(),
									'type' : data.type
								}, function (result)
								{
									if (result.errno == 1)
									{
										if (removeClass)
										{
											_this.parents('li').removeClass('active');
										}
										else
										{
											$('.aw-recommend-box ul li').removeClass('active');

											_this.parents('li').addClass('active');
										}
									}
								}, 'json');
							});
						}
						else
						{
							$('.error_message').html(_t('请先去后台创建好章节'));

							if ($('.error_message').css('display') != 'none')
							{
								AWS.shake($('.error_message'));
							}
							else
							{
								$('.error_message').fadeIn();
							}
						}
					}, 'json');
				break;
			}

			$(".alert-box").modal('show');
		}
	},

	// 兼容placeholder
	check_placeholder: function(selector)
	{
		$.each(selector, function()
		{
			if (typeof ($(this).attr("placeholder")) != "undefined")
			{
				$(this).attr('data-placeholder', 'true');

				if ($(this).val() == '')
				{
					$(this).addClass('aw-placeholder').val($(this).attr("placeholder"));
				}

				$(this).focus(function () {
					if ($(this).val() == $(this).attr('placeholder'))
					{
						$(this).removeClass('aw-placeholder').val('');
					}
				});

				$(this).blur(function () {
					if ($(this).val() == '')
					{
						$(this).addClass('aw-placeholder').val($(this).attr('placeholder'));
					}
				});
			}
		});
	},

	// 回复背景高亮
	hightlight: function(selector, class_name)
	{
		if (selector.hasClass(class_name))
		{
			return true;
		}

		var hightlight_timer_front = setInterval(function ()
		{
			selector.addClass(class_name);
		}, 500);

		var hightlight_timer_background = setInterval(function ()
		{
			selector.removeClass(class_name);
		}, 600);

		setTimeout(function ()
		{
			clearInterval(hightlight_timer_front);
			clearInterval(hightlight_timer_background);

			selector.addClass(class_name);
		}, 1200);

		setTimeout(function ()
		{
			selector.removeClass(class_name);
		}, 6000);
	},

	nl2br: function(str)
	{
		return str.replace(new RegExp("\r\n|\n\r|\r|\n", "g"), "<br />");
	},

	content_switcher: function(hide_el, show_el)
	{
		hide_el.hide();
		show_el.fadeIn();
		AWS.shake(show_el);
		$('body').append('<div id="overDiv"></div>');		
		show_el.addClass('lightroom-item');
		$('body').animate({scrollTop: show_el.offset().top - 10}, 300);
	},
	
	content_switchers: function(hide_el, show_el, els)
	{
		hide_el.hide();
		els.html('');
		show_el.fadeIn();
		show_el.css({"backgroundColor":"#FFFFF0","border":"1px solid #FAF0E6"});
		AWS.shake(show_el);		
		$('#overDiv').remove();
		show_el.removeClass('lightroom-item');
		$('body').animate({scrollTop: show_el.offset().top - 70}, 300);
	},
	
	content_switcherst: function(e)
	{
		$('#article-list_'+e).hide();
		$('#article-list-box_'+e).html('');
		$('#oarticle-list_'+e).fadeIn();
		$('#oarticle-list_'+e).css({"backgroundColor":"#FFFFF0","border":"1px solid #FAF0E6"});
		AWS.shake($('#oarticle-list_'+e));		
		$('#overDiv').remove();
		$('#oarticle-list_'+e).removeClass('lightroom-item');
		$('body').animate({scrollTop: $('#oarticle-list_'+e).offset().top - 70}, 300);
	},
	
	htmlspecialchars: function(text)
	{
		return text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
	},

	/*
	 * 用户头像提示box效果
	 *  @params
	 *  type : user/topic
	 *	nTop    : 焦点到浏览器上边距
	 *	nRight  : 焦点到浏览器右边距
	 *	nBottom : 焦点到浏览器下边距
	 *	left    : 焦点距离文档左偏移量
	 *	top     : 焦点距离文档上偏移量
	 **
	 */
	show_card_box: function(selector, type, time) //selector -> .aw-user-name/.topic-tag
	{
		
		if (!time)
		{
			var time = 300;
		}

		$(document).on('mouseover', selector, function ()
		{
			
			clearTimeout(AWS.G.card_box_hide_timer);
			var _this = $(this);
			AWS.G.card_box_show_timer = setTimeout(function ()
			{
				//判断用户id or 话题id 是否存在
				if (_this.attr('data-id'))
				{
					 switch (type)
					{
						case 'user' :
							//检查是否有缓存
							if (AWS.G.cashUserData.length == 0)
							{
								_getdata('user', 'index.php?c=Ajaxinfo&a=user_info&uid=');
							}
							else
							{
								var flag = 0;
								//遍历缓存中是否含有此id的数据
								_checkcash('user');
								if (flag == 0)
								{
									_getdata('user', 'index.php?c=Ajaxinfo&a=user_info&uid=');
								}
							}
						break;

					}
				}

				//获取数据
				function _getdata(type, url)
				{
					if (type == 'user')
					{
						
						$.get(G_BASE_URL+url + _this.attr('data-id'), function(result)
						{
							
							var focus = result.data.focus, verified = result.data.verified, focusTxt;

							if (focus)
							{
								focus = 'active';
								focusTxt = '取消关注';
							}
							else
							{
								focus = '';
								focusTxt = '关注';
							}

							if(result.data.verified == 2)
							{
								verified_enterprise = 'icon-myvip i-ve';
								verified_title = '企业认证';
							}
							else if(result.data.verified == 1)
							{
								verified_enterprise = 'icon-myvip';
								verified_title = '个人认证';
							}
							else
							{
								verified_enterprise = verified_title = '';
							}

							//动态插入盒子
							$('#aw-ajax-box').html(Hogan.compile(AW_TEMPLATE.userCard).render(
							{
								'verified_enterprise' : verified_enterprise,
								'verified_title' : verified_title,
								'uid': result.data.id,
								'avatar_file': result.data.userhead,
								'user_name': result.data.nickname,
								'reputation': result.data.point,
								'agree_count': result.data.expoint1,
								'signature': result.data.description,
								'url' : result.data.url,
								'focus': focus,
								'focusTxt': focusTxt,
								'ask_name': "'" + result.data.nickname + "'",
								'fansCount': result.data.fscount
							}));

							//判断是否为游客or自己
							if (G_USER_ID == 0 || G_USER_ID == result.data.id || result.data.id < 0)
							{
								$('#aw-card-tips .mod-footer').hide();
							}
							_init();
							//缓存
							AWS.G.cashUserData.push($('#aw-ajax-box').html());
						}, 'json');
					}
					if (type == 'topic')
					{
						$.get(G_BASE_URL + url + _this.attr('data-id'), function(result)
						{
							var focus = result.focus,
								focusTxt;
								if (focus == false)
								{
									focus = '';
									focusTxt = _t('关注');
								}
								else
								{
									focus = 'active';
									focusTxt = _t('取消关注');
								}
								//动态插入盒子
								$('#aw-ajax-box').html(Hogan.compile(AW_TEMPLATE.topicCard).render(
								{
									'topic_id': result.topic_id,
									'topic_pic': result.userhead,
									'topic_title': result.topic_title,
									'topic_description': result.topic_description,
									'discuss_count': result.discuss_count,
									'focus_count': result.focus_count,
									'focus': focus,
									'focusTxt': focusTxt,
									'url' : result.url,
									'fansCount': result.fans_count
								}));
								//判断是否为游客
								if (G_USER_ID == '')
								{
									$('#aw-card-tips .mod-footer .follow').hide();
								}
								_init();
								//缓存
								AWS.G.cashTopicData.push($('#aw-ajax-box').html());
						}, 'json');
					}
				}

				//检测缓存
				function _checkcash(type)
				{
					if (type == 'user')
					{
						$.each(AWS.G.cashUserData, function (i, a)
						{
							if (a.match('data-id="' + _this.attr('data-id') + '"'))
							{
								$('#aw-ajax-box').html(a);
								$('#aw-card-tips').removeAttr('style');
								_init();
								flag = 1;
							}
						});
					}
					if (type == 'topic')
					{

						$.each(AWS.G.cashTopicData, function (i, a)
						{
							if (a.match('data-id="' + _this.attr('data-id') + '"'))
							{
								$('#aw-ajax-box').html(a);
								$('#aw-card-tips').removeAttr('style');
								_init();
								flag = 1;
							}
						});
					}
				}

				//初始化
				function _init()
				{
					var left = _this.offset().left,
						top = _this.offset().top + _this.height() + 5,
						nTop = _this.offset().top - $(window).scrollTop();

					//判断下边距离不足情况
					if (nTop + $('#aw-card-tips').innerHeight() > $(window).height())
					{
						top = _this.offset().top - ($('#aw-card-tips').innerHeight()) - 10;
					}

					//判断右边距离不足情况
					if (left + $('#aw-card-tips').innerWidth() > $(window).width())
					{
						left = _this.offset().left - $('#aw-card-tips').innerWidth() + _this.innerWidth();
					}

					$('#aw-card-tips').css(
					{
						left: left,
						top: top
					}).fadeIn();
				}
			}, time);
		});

		$(document).on('mouseout', selector, function ()
		{
			
			clearTimeout(AWS.G.card_box_show_timer);
			AWS.G.card_box_hide_timer = setTimeout(function ()
			{
				$('#aw-card-tips').fadeOut();
			}, 600);
		});
	},

	// @人功能
	at_user_lists: function(selector, limit) {
		$(selector).keyup(function (e) {
			var _this = $(this),
				flag = _getCursorPosition($(this)[0]).start;
			if ($(this).val().charAt(flag - 1) == '@')
			{
				_init();
				$('#aw-ajax-box .content_cursor').html($(this).val().substring(0, flag));
			} else
			{
				var lis = $('.aw-invite-dropdown li');
				switch (e.which)
				{
					case 38:
						var _index;
						if (!lis.hasClass('active'))
						{
							lis.eq(lis.length - 1).addClass('active');
						}
						else
						{
							$.each(lis, function (i, e)
							{
								if ($(this).hasClass('active'))
								{
									$(this).removeClass('active');
									if ($(this).index() == 0)
									{
										_index = lis.length - 1;
									}
									else
									{
										_index = $(this).index() - 1;
									}
								}
							});
							lis.eq(_index).addClass('active');
						}
						break;
					case 40:
						var _index;
						if (!lis.hasClass('active'))
						{
							lis.eq(0).addClass('active');
						}
						else
						{
							$.each(lis, function (i, e)
							{
								if ($(this).hasClass('active'))
								{
									$(this).removeClass('active');
									if ($(this).index() == lis.length - 1)
									{
										_index = 0;
									}
									else
									{
										_index = $(this).index() + 1;
									}
								}
							});
							lis.eq(_index).addClass('active');
						}
						break;
					case 13:
						$.each($('.aw-invite-dropdown li'), function (i, e)
						{
							if ($(this).hasClass('active'))
							{
								$(this).click();
							}
						});
						break;
					default:
						if ($('.aw-invite-dropdown')[0])
						{
							var ti = 0;
							for (var i = flag; i > 0; i--)
							{
								if ($(this).val().charAt(i) == "@")
								{
									ti = i;
									break;
								}
							}
							$.get(G_BASE_URL + '/search/ajax/search/?type=users&q=' + encodeURIComponent($(this).val().substring(flag, ti).replace('@', '')) + '&limit=' + limit, function (result)
							{
								if ($('.aw-invite-dropdown')[0])
								{
									if (result.length != 0)
									{
										var html = '';

										$('.aw-invite-dropdown').html('');

										$.each(result, function (i, a)
										{
											html += '<li><img src="' + a.detail.avatar_file + '"/><a>' + a.name + '</a></li>'
										});

										$('.aw-invite-dropdown').append(html);

										_display();

										$('.aw-invite-dropdown li').click(function ()
										{
											_this.val(_this.val().substring(0, ti) + '@' + $(this).find('a').html() + " ").focus();
											$('.aw-invite-dropdown').detach();
										});
									}
									else
									{
										$('.aw-invite-dropdown').hide();
									}
								}
								if (_this.val().length == 0)
								{
									$('.aw-invite-dropdown').hide();
								}
							}, 'json');
						}
				}
			}
		});

		$(selector).keydown(function (e) {
			var key = e.which;
			if ($('.aw-invite-dropdown').is(':visible')) {
				if (key == 38 || key == 40 || key == 13) {
					return false;
				}
			}
		});

		//初始化插入定位符
		function _init() {
			if (!$('.content_cursor')[0]) {
				$('#aw-ajax-box').append('<span class="content_cursor"></span>');
			}
			$('#aw-ajax-box').find('.content_cursor').css({
				'left': parseInt($(selector).offset().left + parseInt($(selector).css('padding-left')) + 2),
				'top': parseInt($(selector).offset().top + parseInt($(selector).css('padding-left')))
			});
			if (!$('.aw-invite-dropdown')[0])
			{
				$('#aw-ajax-box').append('<ul class="aw-invite-dropdown"></ul>');
			}
		};

		//初始化列表和三角型
		function _display() {
			$('.aw-invite-dropdown').css({
				'left': $('.content_cursor').offset().left + $('.content_cursor').innerWidth(),
				'top': $('.content_cursor').offset().top + 24
			}).show();
		};

		//获取当前textarea光标位置
		function _getCursorPosition(textarea)
		{
			var rangeData = {
				text: "",
				start: 0,
				end: 0
			};

			textarea.focus();

			if (textarea.setSelectionRange) { // W3C
				rangeData.start = textarea.selectionStart;
				rangeData.end = textarea.selectionEnd;
				rangeData.text = (rangeData.start != rangeData.end) ? textarea.value.substring(rangeData.start, rangeData.end) : "";
			} else if (document.selection) { // IE
				var i,
					oS = document.selection.createRange(),
					// Don't: oR = textarea.createTextRange()
					oR = document.body.createTextRange();
				oR.moveToElementText(textarea);

				rangeData.text = oS.text;
				rangeData.bookmark = oS.getBookmark();

				// object.moveStart(sUnit [, iCount])
				// Return Value: Integer that returns the number of units moved.
				for (i = 0; oR.compareEndPoints('StartToStart', oS) < 0 && oS.moveStart("character", -1) !== 0; i++) {
					// Why? You can alert(textarea.value.length)
					if (textarea.value.charAt(i) == '\n') {
						i++;
					}
				}
				rangeData.start = i;
				rangeData.end = rangeData.text.length + rangeData.start;
			}

			return rangeData;
		};
	},

	// 错误提示效果
	shake: function(selector)
	{
		var length = 6;
		selector.css('position', 'relative');
		for (var i = 1; i <= length; i++)
		{
			if (i % 2 == 0)
			{
				if (i == length)
				{
					selector.animate({ 'left': 0 }, 50);
				}
				else
				{
					selector.animate({ 'left': 10 }, 50);
				}
			}
			else
			{
				selector.animate({ 'left': -10 }, 50);
			}
		}
	}
}

// 全局变量
AWS.G =
{
	cashUserData: [],
	cashTopicData: [],
	card_box_hide_timer: '',
	card_box_show_timer: '',
	dropdown_list_xhr: '',
	loading_timer: '',
	loading_bg_count: 12,
	loading_mini_bg_count: 9,
	notification_timer: ''
}

AWS.User =
{
	// 关注
	follow: function(selector, type, data_id)
	{
		
		if(G_USER_ID == 0){
			
			layer.msg('用户未登录', {icon: 2, anim: 6, time: 1000});
			return;
		}
		
		if (selector.html())
		{
			if (selector.hasClass('active'))
			{
				selector.find('span').html(_t('关注'));

				selector.find('b').html(parseInt(selector.find('b').html()) - 1);
			}
			else
			{
				selector.find('span').html(_t('取消关注'));

				selector.find('b').html(parseInt(selector.find('b').html()) + 1);
			}
		}
		else
		{
			if (selector.hasClass('active'))
			{
				selector.attr('data-original-title', _t('关注'));
			}
			else
			{
				selector.attr('data-original-title', _t('取消关注'));
			}
		}

		$(selector).addClass('disabled');

		switch (type)
		{
			case 'huati':
				var url = 'index.php?c=Ajaxinfo&a=focus&type=2&id=';
				break;

			case 'topic':
				var url = 'index.php?c=Ajaxinfo&a=focus&type=1&id=';
				break;

			case 'user':
				var url = 'index.php?c=Ajaxinfo&a=focus&type=3&id=';
			break;
		}

		$.get(G_BASE_URL + url + data_id, function (result)
		{
			
			if (result.code == 1)
			{
				
				if (result.data.qx == 2)
				{
					
					selector.addClass('active');
					
				}
				else
				{
					
					selector.removeClass('active');
					
				}
			}
			else
			{
				if (result.msg)
				{
					 layer.msg(result.msg, {icon: 2, anim: 6, time: 1000});
				}

				if (result.url)
				{
					window.location = decodeURIComponent(result.url);
				}
			}

			selector.removeClass('disabled');

		}, 'json');
	},

	share_out: function(webid, title, url)
	{
		var url = url || window.location.href;

		if (title)
		{
			var title = title + ' - ' + G_SITE_NAME;
		}
		else
		{
			var title = $('title').text();
		}

		shareURL = 'http://www.jiathis.com/send/?webid=' + webid + '&url=' + url + '&title=' + title + '';

		window.open(shareURL);
	},

	// 点赞帖子
	agree_topic: function(selector, answer_id)
	{
		$.post(G_BASE_URL + 'index.php?c=Ajaxinfo&a=agreet', 'cid=' + answer_id,function(result){
			
			if (result.code == 1)
			{
		

					$(selector).find('.dianzan').html(parseInt($(selector).find('.dianzan').html()) + 1);

				
				layer.msg(result.msg, {icon: 1, anim: 6, time: 1000});
				
				
			}else{
				layer.msg(result.msg, {icon: 2, anim: 6, time: 1000});
			}
			
			
		});

		
	},

	// 赞成投票
	agree_vote: function(selector, answer_id,user_name)
	{
		$.post(G_BASE_URL + 'index.php?c=Ajaxinfo&a=agreec', 'cid=' + answer_id,function(result){
			
			if (result.code == 1)
			{
				
					// 判断是否第一个投票
					if ($(selector).parents('.aw-item').find('.aw-agree-by .aw-user-name').length == 0)
					{
						$(selector).parents('.aw-item').find('.aw-agree-by').append('<a class="aw-user-name">' + user_name + '</a>');
					}
					else
					{
						$(selector).parents('.aw-item').find('.aw-agree-by').append('<em>、</em><a class="aw-user-name">' + user_name + '</a>');
					}

					$(selector).parents('.operate').find('.count').html(parseInt($(selector).parents('.operate').find('.count').html()) + 1);

					$(selector).parents('.aw-item').find('.aw-agree-by').show();

					$(selector).parents('.operate').find('a.active').removeClass('active');

					$(selector).addClass('active');
				
				layer.msg(result.msg, {icon: 1, anim: 6, time: 1000});
				
				
			}else{
				layer.msg(result.msg, {icon: 2, anim: 6, time: 1000});
			}
			
			
		});

		
	},

	// 反对投票
	disagree_vote: function(selector, answer_id,user_name)
	{
		$.post(G_BASE_URL + 'index.php?c=Ajaxinfo&a=disagreec', 'cid=' + answer_id,function(result){
			
			if (result.code == 1)
			{
				$(selector).parents('.operate').find('.count').html(parseInt($(selector).parents('.operate').find('.count').html()) + 1);

				$(selector).parents('.operate').find('.agree').removeClass('active');

				
				
				layer.msg(result.msg, {icon: 1, anim: 6, time: 1000});
			}else{
				layer.msg(result.msg, {icon: 2, anim: 6, time: 1000});
			}
		});

		
		
	},


	// 提交评论
	save_comment: function(selector)
	{
		selector.addClass('disabled');

		AWS.ajax_post(selector.parents('form'), AWS.ajax_processer, 'comments_form');
	},

	// 删除评论
	remove_comment: function(selector, comment_id)
	{
		$.get(G_BASE_URL + 'index.php?c=Ajaxinfo&a=delpinglun&id=' + comment_id);

		selector.parents('.aw-comment-box li').fadeOut();
	},
	// 删除评论
	remove_ding_comment: function(selector, comment_id)
	{
		$.get(G_BASE_URL + 'index.php?c=Ajaxinfo&a=delpinglun&id=' + comment_id);

		selector.closest('.aw-item').fadeOut();
	},
	

	// 创建收藏标签
	add_favorite_tag: function()
	{
		$.post(G_BASE_URL + '/favorite/ajax/update_favorite_tag/', {
			'item_id' : $('#favorite_form input[name="item_id"]').val(),
			'item_type' : $('#favorite_form input[name="item_type"]').val(),
			'tags' : $('#favorite_form .add-input').val()
		}, function (result)
		{
			if (result.errno == 1)
			{
				$('.aw-favorite-box .aw-favorite-tag-list').show();
				$('.aw-favorite-box .aw-favorite-tag-add').hide();

				$('.aw-favorite-tag-list ul').prepend('<li class="active"><a data-value="' + $('#favorite_form .add-input').val() + '"><span class="title">' + $('#favorite_form .add-input').val() + '</span></a><i class="icon icon-followed"></i></li>');
			}
		}, 'json');
	}
}

AWS.Dropdown =
{
	// 下拉菜单功能绑定
	bind_dropdown_list: function(selector, type)
	{
		if (type == 'search')
		{
			$(selector).focus(function()
			{
				$(selector).parent().find('.aw-dropdown').show();
			});
		}
		$(selector).keyup(function(e)
		{
			if (type == 'search')
			{
				$(selector).parent().find('.search').show().children('a').text($(selector).val());
			}
			if ($(selector).val().length >= 1)
			{
				if (e.which != 38 && e.which != 40 && e.which != 188 && e.which != 13)
				{
					AWS.Dropdown.get_dropdown_list($(this), type, $(selector).val());
				}
			}
			else
			{
			   $(selector).parent().find('.aw-dropdown').hide();
			}

			
		});

		$(selector).blur(function()
		{
			$(selector).parent().find('.aw-dropdown').delay(500).fadeOut(300);
		});
	},

	// 插入下拉菜单
	set_dropdown_list: function(selector, data, selected)
	{
		$(selector).append(Hogan.compile(AW_TEMPLATE.dropdownList).render(
		{
			'items': data
		}));

		$(selector + ' .aw-dropdown-list li a').click(function ()
		{
			$('#aw-topic-tags-select').html($(this).text());
		});

		if (selected)
		{
			$(selector + " .dropdown-menu li a[data-value='" + selected + "']").click();
		}
	},

	/* 下拉菜单数据获取 */
	/*
	*    type : search, publish, redirect, invite, inbox, topic_question, topic
	*/
	get_dropdown_list: function(selector, type, data)
	{
		if (AWS.G.dropdown_list_xhr != '')
		{
			AWS.G.dropdown_list_xhr.abort(); // 中止上一次ajax请求
		}
		var url;
		switch (type)
		{
			case 'search' :
				url = G_BASE_URL + 'index.php?c=Ajaxinfo&a=searchajax?q=' + encodeURIComponent(data) + '&limit=5';
			break;


		}

		AWS.G.dropdown_list_xhr = $.get(url, function (result)
		{
			if (result.code == 1 && AWS.G.dropdown_list_xhr != undefined)
			{
				$(selector).parent().find('.aw-dropdown-list').html(''); // 清空内容
				switch (type)
				{
					case 'search' :
						$.each(result.data, function (i, a)
						{
							
							
							switch (a.type)
							{


								case 'articles':
									$(selector).parent().find('.aw-dropdown-list').append(Hogan.compile(AW_TEMPLATE.searchDropdownListArticles).render(
									{
										'url': a.url,
										'content': a.name,
										'comments': a.reply
									}));
								break;

								case 'topics':
									$(selector).parent().find('.aw-dropdown-list').append(Hogan.compile(AW_TEMPLATE.searchDropdownListTopics).render(
									{
										'url': a.url,
										'name': a.name,
										'discuss_count': a.topiccount,
										
									}));
								break;

								case 'users':
									if (a.signature == '')
									{
										var signature = _t('暂无介绍');
									}
									else
									{
										var signature = a.signature;
									}

									$(selector).parent().find('.aw-dropdown-list').append(Hogan.compile(AW_TEMPLATE.searchDropdownListUsers).render(
									{
										'url': a.url,
										'img': a.avatar_file,
										'name': a.name,
										'intro': signature
									}));
								break;
							}
						});
					break;


				}
				if (type == 'publish')
				{
					$(selector).parent().find('.aw-publish-suggest-question, .aw-publish-suggest-question .aw-dropdown-list').show();
				}
				else
				{
					$(selector).parent().find('.aw-dropdown, .aw-dropdown-list').show().children().show();
					$(selector).parent().find('.title').hide();
					// 关键词高亮
					$(selector).parent().find('.aw-dropdown-list li.question a').highText(data, 'b', 'active');
				}
			}else
			{
				$(selector).parent().find('.aw-dropdown').show().end().find('.title').html(_t('没有找到相关结果')).show();
				$(selector).parent().find('.aw-dropdown-list, .aw-publish-suggest-question').hide();
			}
		}, 'json');

	}
}


AWS.Init =
{
	// 初始化问题评论框
	init_comment_box: function(selector)
	{
		$(document).on('click', selector, function ()
		{
			
			

			if (!$(this).attr('data-type') || !$(this).attr('data-id'))
			{
				return true;
			}

			var comment_box_id = '#aw-comment-box-' + $(this).attr('data-type') + '-' + 　$(this).attr('data-id');

			if ($(comment_box_id).length)
			{
				if ($(comment_box_id).css('display') == 'none')
				{
					$(this).addClass('active');

					$(comment_box_id).fadeIn();
				}
				else
				{
					$(this).removeClass('active');
					$(comment_box_id).fadeOut();
				}
			}
			else
			{
				// 动态插入commentBox
				switch ($(this).attr('data-type'))
				{
					case 'question':
						var comment_form_action = G_BASE_URL + '/question/ajax/save_question_comment/question_id-' + $(this).attr('data-id');
						var comment_data_url = G_BASE_URL + '/question/ajax/get_question_comments/question_id-' + $(this).attr('data-id');
						break;

					case 'answer':
						var comment_form_action = G_BASE_URL + 'index.php?c=Ajaxinfo&a=addpinglun&id=' + $(this).attr('data-id');
						var comment_data_url = G_BASE_URL + 'index.php?c=Ajaxinfo&a=getpinglun&id=' + $(this).attr('data-id');
						break;
				}
				
				if (G_USER_ID)
				{
					
					$(this).parents('.aw-item').find('.mod-footer').append(Hogan.compile(AW_TEMPLATE.commentBox).render(
					{
						'comment_form_id': comment_box_id.replace('#', ''),
						'comment_form_action': comment_form_action
					}));

					$(comment_box_id).find('.aw-comment-txt').bind(
					{
						focus: function ()
						{
							$(comment_box_id).find('.aw-comment-box-btn').show();
						},

						blur: function ()
						{
							if ($(this).val() == '')
							{
								$(comment_box_id).find('.aw-comment-box-btn').hide();
							}
						}
					});

					$(comment_box_id).find('.close-comment-box').click(function ()
					{
						$(comment_box_id).fadeOut();
						$(comment_box_id).find('.aw-comment-txt').css('height', $(this).css('line-height'));
					});
				}
				else
				{
					$(this).parents('.aw-item').find('.mod-footer').append(Hogan.compile(AW_TEMPLATE.commentBoxClose).render(
					{
						'comment_form_id': comment_box_id.replace('#', ''),
						'comment_form_action': comment_form_action
					}));
				}

				// 判断是否有评论数据
				$.get(comment_data_url, function (result)
				{
					if (result.code==0)
					{
						result = '<div align="center" class="aw-padding10">' + _t('暂无评论') + '</div>';
					}else{
						result = result.data.data;
					}

					$(comment_box_id).find('.aw-comment-list').html(result);
				});

				// textarae自动增高
				$(comment_box_id).find('.aw-comment-txt').autosize();

				$(this).addClass('active');

				
			}

			
		});
	},

	// 初始化文章评论框
	init_article_comment_box: function(selector)
	{
		$(document).on('click', selector, function ()
		{
			var _editor_box = $(this).parents('.aw-item').find('.aw-article-replay-box');
			if (_editor_box.length)
			{
				if (_editor_box.css('display') == 'block')
				{
				   _editor_box.fadeOut();
				}
				else
				{
					_editor_box.fadeIn();
				}
			}
			else
			{
				$(this).parents('.mod-footer').append(Hogan.compile(AW_TEMPLATE.articleCommentBox).render(
				{
					'at_uid' : $(this).attr('data-id'),
					'article_id' : $('.aw-topic-bar').attr('data-id')
				}));
			}
		});
	},

	
}

function _t(string, replace)
{
	if (typeof (aws_lang) != 'undefined')
	{
		if (typeof (aws_lang[string]) != 'undefined')
		{
			string = aws_lang[string];
		}
	}

	if (replace)
	{
		string = string.replace('%s', replace);
	}

	return string;
};
function getplusername(obj,name){
	
	$(obj).parents(".aw-comment-box").find("form textarea").insertAtCaret("@"+name+":");
	$(obj).parents(".aw-comment-box").find("form").show().find("textarea").focus();
	$.scrollTo($(obj).parents(".aw-comment-box").find("form"), 300, {queue:true});
	
	
}
// jQuery扩展
(function ($)
{
	$.fn.extend(
	{
		insertAtCaret: function (textFeildValue)
		{
			var textObj = $(this).get(0);
			if (document.all && textObj.createTextRange && textObj.caretPos)
			{
				var caretPos = textObj.caretPos;
				caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == '' ?
					textFeildValue + '' : textFeildValue;
			}
			else if (textObj.setSelectionRange)
			{
				var rangeStart = textObj.selectionStart,
					rangeEnd = textObj.selectionEnd,
					tempStr1 = textObj.value.substring(0, rangeStart),
					tempStr2 = textObj.value.substring(rangeEnd);
				textObj.value = tempStr1 + textFeildValue + tempStr2;
				textObj.focus();
				var len = textFeildValue.length;
				textObj.setSelectionRange(rangeStart + len, rangeStart + len);
				textObj.blur();
			}
			else
			{
				textObj.value += textFeildValue;
			}
		},

		highText: function (searchWords, htmlTag, tagClass)
		{
			return this.each(function ()
			{
				$(this).html(function high(replaced, search, htmlTag, tagClass)
				{
					var pattarn = search.replace(/\b(\w+)\b/g, "($1)").replace(/\s+/g, "|");

					return replaced.replace(new RegExp(pattarn, "ig"), function (keyword)
					{
						return $("<" + htmlTag + " class=" + tagClass + ">" + keyword + "</" + htmlTag + ">").outerHTML();
					});
				}($(this).text(), searchWords, htmlTag, tagClass));
			});
		},

		outerHTML: function (s)
		{
			return (s) ? this.before(s).remove() : jQuery("<p>").append(this.eq(0).clone()).html();
		}
	});

	$.extend(
	{
		// 滚动到指定位置
		scrollTo : function (type, duration, options)
		{
			if (typeof type == 'object')
			{
				var type = $(type).offset().top
			}

			$('html, body').animate({
				scrollTop: type
			}, {
				duration: duration,
				queue: options.queue
			});
		}
	})

})(jQuery);
