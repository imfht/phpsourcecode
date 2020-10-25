var jp = {
	/**
	 * 上传组件
	 * @returns {undefined}
	 */
	uploader: function (configs) {
		//默认参数
		var options = {
			formObject: {}, //表单对象
			image: '', //图像默认值
			imageSrc: '10%', //图片预览值
			type: 1, //上传类型, 1为城市图片
			hiddenField: '.image',
			uploadField: '.image_file',
			fieldname: 'image_file'
		};
		//合并
		$.extend(options, configs);

		//对象
		var form = options.formObject, hiddenFieldObject = form.find(options.hiddenField), uploadFieldObject = form.find(options.uploadField);
		hiddenFieldObject.val(options.image);
		uploadFieldObject.fileinput({
			uploadUrl: '/api/commons/upload',
			uploadExtraData: {type: options.type, fieldname: options.fieldname},
			showPreview: true,
			initialPreview: options.imageSrc == '' ? '' : [
				"<img src='" + options.imageSrc + "' class='file-preview-image' alt='Desert' title='Desert'>"
			],
			initialPreviewConfig: [
			],
			showCaption: false,
			showRemove: false,
			showCancel: false,
			dropZoneEnabled: false,
			maxFileCount: 1,
			browseClass: "btn btn-success",
			browseLabel: '选择图片',
			removeLabel: '取消',
			uploadLabel: '上传图片',
			uploadClass: "btn btn-primary",
			allowedFileExtensions: ['jpg', 'png', 'gif'],
			layoutTemplates: {
				main1: '<div class="kv-upload-progress hide"></div>\n' +
						'<div class="input-group {class}">\n' +
						'   {caption}\n' +
						'   <div class="input-group-btn">\n' +
						'       {remove}\n' +
						'       {cancel}\n' +
						'       {browse}\n' +
						'       {upload}\n' +
						'   </div>\n' +
						'</div>\n' +
						'<div class="with-padding"></div>\n' +
						'{preview}',
				main2: '<div class="kv-upload-progress hide"></div>\n{remove}\n{cancel}\n{browse}\n{upload}\n<div class="with-padding"></div>\n{preview}'
			},
		});
		uploadFieldObject.on('fileuploaded', function (event, data, previewId, index) {
			var form = data.form, files = data.files, extra = data.extra,
					response = data.response, reader = data.reader;
			hiddenFieldObject.val(response.filename);
		});
	},
	/**
	 * 跳转到前台的方法
	 * @returns {undefined}
	 */
	redirect: function (auth_key, url) {
		var url = url ? '&url=' + encodeURIComponent(url) : '';
		this.redirectUrl(site_url + 'api/users/login-by-auth-key?auth_key=' + auth_key + url);
	},
	/**
	 * 跳转到指定URL
	 * @returns {undefined}
	 */
	redirectUrl: function (url, new_window) {
		var new_window = typeof (arguments[1]) != 'undefined' ? arguments[1] : true;
		if (new_window) {
			window.open(url);
		} else {
			window.location.href = url;
		}

	},
	/**
	 * 加载速度条
	 * @type type
	 */
	progress: {
		timer: null,
		template: function () {
			return '<div class="progress navbar-fixed-top" style="height: 2px; top: 0px;">\n\
					<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">\n\
					</div>\n\
				</div>'
		},
		loading: function () {
			var width = $(".progress .progress-bar").width() / $(document).width() * 100;
			if (width == 0) {
				width = 8;
			} else {
				var random = parseInt(Math.random() * (5 - 2 + 1) + 2);
				var width = parseInt(random + width);
			}
			$(".progress .progress-bar").animate({
				width: width + '%'
			}, 100);
			if (width < 10) {
				var interval = 300;
			} else {
				var interval = parseInt(Math.random() * (2000 - 500 + 1) + 500);
			}
			if (width <= 95) {
				jp.progress.timer = setTimeout(jp.progress.loading, interval);
			}
		},
		start: function () {
			if ($(".progress .progress-bar").length == 0) {
				$('body').append(this.template());
			}
			$(".progress .progress-bar").css({width: '0px'})
			$(".progress").fadeIn();
			this.loading();
		},
		done: function () {
			$(".progress .progress-bar").animate({
				width: '100%'
			}, 100, function () {
				$(".progress").delay(300).fadeOut();
			}).finish();
			clearInterval(jp.progress.timer);
		}
	},
	/**
	 * 分页组件
	 */
	pager: {
		/**
		 * 渲染分页数据
		 * @param int id 容器
		 * @param int total 记录数量
		 * @param int page 页码
		 * @param int page_size 分页大小
		 * @param function callback 页码回调函数
		 */
		render: function (id, total, page, page_size, callback) {
			var pagerObj = $(id);
			if (total <= page_size) {
				pagerObj.empty();
				return false;
			}
			var page_count = Math.ceil(total / page_size);
			var data = {
				page: parseInt(page),
				page_count: page_count,
				page_previous: parseInt(page) - 1,
				page_next: parseInt(page) + 1,
				page_last: page_count,
			};
			var pagerTpl = '		<ul class="pager pager-loose">\n\
			<!-- 显示上一页按钮 -->\n\
			<%if(page > 1){%>\n\
			<li class="previous" data-page="<%=page_previous%>"><a href="javascript:void(0);">«</a></li>\n\
			<%}else{%>\n\
				<li class="previous disabled"><a href="javascript:void(0);">«</a></li>\n\
			<%}%>\n\
			\n\
			<!-- 不足10个全部显示 -->\n\
			<%if(page_count < 10){%>\n\
				<%for(var i = 1; i <= page_count; i++){%>\n\
					<%if(page == i){%>\n\
					<li class="active"><a href="javascript:void(0);"><%=i%></a></li>\n\
					<%}else{%>\n\
					<li data-page="<%=i%>"><a href="javascript:void(0);"><%=i%></a></li>\n\
					<%}%>\n\
				<%}%>\n\
			<%}else{%>\n\
			\n\
				<!-- 显示第一页 -->\n\
				<%if(page > 4){%>\n\
				<li data-page="1"><a href="javascript:void(0);">1</a></li>\n\
				<li><a href="javascript:void(0); " data-toggle="pager" data-placement="top">...</a></li>\n\
				<%}%>\n\
				\n\
				\n\
				<%for(var i = (page-3 < 1 ? 1 : page-3); i <= (page+3 > page_last ? page_last : page+3); i++){%>\n\
					<%if(page == i){%>\n\
					<li class="active"><a href="javascript:void(0);"><%=i%></a></li>\n\
					<%}else{%>\n\
					<li data-page="<%=i%>"><a href="javascript:void(0);"><%=i%></a></li>\n\
					<%}%>\n\
				<%}%>\n\
				\n\
				<!-- 显示最后页 -->\n\
				<%if(page_last-page > 4){%>\n\
				<li><a href="javascript:void(0);" data-toggle="pager" data-placement="top">...</a></li>\n\
				<li data-page="<%=page_last%>"><a href="javascript:void(0);"><%=page_last%></a></li>\n\
				<%}%>\n\
			<%}%>\n\
			\n\
			<!-- 显示下一页按钮 -->\n\
			<%if(page < page_count){%>\n\
			<li class="next" data-page="<%=page_next%>"><a href="javascript:void(0);">»</a></li>\n\
			<%}else{%>\n\
			<li class="next disabled"><a href="javascript:void(0);">»</a></li>\n\
			<%}%>\n\
		</ul>';
			pagerTpl = baidu.template(pagerTpl, data);
			pagerObj.html(pagerTpl);
			pagerObj.find('li[data-page]').on('click', callback);
		}
	},
	/**
	 * 消息组件
	 * @type type
	 */
	messager: {
		/**
		 * 显示提示信息
		 * @param {string} msg 消息
		 * @param {string} type 类型
		 * @param {string} placement 位置
		 * @param {number} time 自动关闭时间
		 * @returns {undefined}
		 */
		show: function (msg, type, placement, time) {
			var type = arguments[1] || 'default';
			var placement = arguments[2] || 'center';
			var time = arguments[3] || 2000;
			$.messager.show(msg, {placement: placement, time: time, type: type});
		},
		/**
		 * 失败信息
		 * @param {string} msg 消息
		 * @returns {undefined}
		 */
		failure: function (msg) {
			this.show(msg, 'warning');
		},
		/**
		 * 成功信息
		 * @param {string} msg 消息
		 * @returns {undefined}
		 */
		success: function (msg) {
			this.show(msg, 'success');
		},
		/**
		 * 
		 * @param {int} uid 接收消息的用户
		 * @returns {undefined}
		 */
		send: function (uid, mobile_number) {
			var modal = new ModalTrigger({custom: $('#messager-send').html()});
			modal.show({
				title: '发送消息',
				shown: function (event)
				{
					var form = $(event.target).find('.modal-content');
					form.find('.uid').val(uid);
					form.find('.mobile_number').val(mobile_number);
					//绑定保存按钮事件
					var button = form.find('.send');
					button.click(function () {
						//设置按钮状态
						button.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
						//设置参数
						var params = {
							type: form.find("input[type='radio']:checked").val(),
							uid: form.find('.uid').val(),
							mobile_number: form.find('.mobile_number').val(),
							msg: form.find('.msg').val(),
						};
						//修改
						$.post('/api/commons/send', params, function (data) {
							if (data.errcode) {
								jp.messager.failure(data.errmsg);
							} else {
								modal.close();
								jp.messager.success('发送成功');
							}
							button.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
						}, 'json');
					});
				}
			});
		}
	},
	/**
	 * grid组件
	 * @param {type} configs
	 * @returns {undefined}
	 */
	grider: function (configs) {

		/**
		 * 自身对象
		 * @type app
		 */
		var self = this;
		/**
		 * 配置参数
		 */
		var options = {
			//列数据
			columns: [],
			//数据表格id
			datatableId: '.datatable',
			//分页容器id
			pagerId: '#pager',
			//表格数据读取url
			readUrl: '',
			//搜索表单ID
			searchFromId: '#search-from',
			//默认页码
			page: 1,
			//默认分页数量
			page_size: 10,
			//grid组件 左侧固定的所有列的宽度，可以指定为百分比
			fixedLeftWidth: '50%',
			//grid组件 右侧固定的所有列的宽度，可以指定为百分比
			fixedRightWidth: '10%',
		};
		/**
		 * 请求参数
		 */
		this.params = {}

		/**
		 * 表格数据
		 */
		this.data = [];
		/**
		 * 初始化
		 */
		var _init = function () {
			//合并参数
			$.extend(options, configs);
			self.data = {
				cols: options.columns,
				rows: []
			};
			//默认请求参数
			self.params = {
				page: options.page,
				page_size: options.page_size
			}

			//绑定搜索事件
			$('#search').on('click', self.onSearch);
			//绑定清除条件事件
			$('#remove').on('click', self.onRemove);
			//绑定按键提交搜索
			$(options.searchFromId).find('input').bind('keydown', 'return', self.onSearch);
			//渲染表格
			var datatableConfig = {
				sortable: true,
				storage: false,
				fixedLeftWidth: options.fixedLeftWidth,
				fixedRightWidth: options.fixedRightWidth,
				sort: self.onSort,
				data: self.data
			};
			$(options.datatableId).datatable(datatableConfig);
			//加载表格数据
			self.loadData();
		}

		/**
		 * 操作按钮组件封装
		 */
		var actioner = {
			/**
			 * 显示按钮组件
			 * @param {type} buttonItems 按钮配置
			 * @param {type} rowData 当前行数据
			 * @param {type} key 当前行索引
			 * @returns {unresolved}
			 */
			show: function (buttonItems, rowData, key) {
				//渲染操作按钮模板

				var buttonTpl = '\n\
			<div class="action-buttons btn-group">\n\
				<%for(var i = 0; i < showItems.length; i++){%>\n\
					<button type="button" class="action-btn btn btn-default">\n\
						<i class="<%=showItems[i].iconClass;%>"> </i>\n\
						<%=showItems[i].text%>\n\
					</button>\n\
				<%}%>\n\
				\n\
				<%if(moreItems.length > 0){%>\n\
					<div class="btn-group">\n\
						<button id="btnGroupDrop1" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">\n\
							<span class="caret"></span>\n\
						</button>\n\
						<ul class="dropdown-menu pull-right" role="menu" aria-labelledby="btnGroupDrop1">\n\
							<%for(var i = 0; i < moreItems.length; i++){%>\n\
							<li class="action-btn"><a href="javascript:void(0); "><%=moreItems[i].text%></a></li>\n\
							<%}%>\n\
						</ul>\n\
					</div>\n\
				<%}%>\n\
			</div>\n\
			';
				var showItems = [];
				var moreItems = [];
				$.each(buttonItems, function (key, item) {
					var show = typeof (item.show) == 'undefined' ? true : item.show;
					if (show) {
						if (item.more == true) {
							moreItems.push(item);
						} else {
							showItems.push(item)
						}
					}
				});
				var buttons = baidu.template(buttonTpl, {showItems: showItems, moreItems: moreItems});
				return buttons;
			},
			/**
			 * 绑定按钮事件
			 */
			bind: function () {
				$(self.data.cols).each(function (colKey, col) {
					if (col.type == 'action') {
						$(self.data.rows).each(function (rowKey, row) {
							var buttonItems = col.render(null, row.rawData);
							var showItems = [];
							var moreItems = [];
							$.each(buttonItems, function (key, item) {
								var show = typeof (item.show) == 'undefined' ? true : item.show;
								if (show) {
									if (item.more == true) {
										moreItems.push(item);
									} else {
										showItems.push(item)
									}
								}
							});
							//循环操作按钮
							$(showItems).each(function (buttonKey, button) {
								var buttonObj = $(options.datatableId).find('.action-buttons').eq(rowKey).find('button.action-btn').eq(buttonKey);
								buttonObj.click(function (event) {
									button.handler(row.rawData, event.target);
								});
							});
							$(moreItems).each(function (buttonKey, button) {
								var buttonObj = $(options.datatableId).find('.action-buttons').eq(rowKey).find('li.action-btn').eq(buttonKey);
								buttonObj.click(function (event) {
									button.handler(row.rawData, event.target);
								});
							});
						});
					}

				});
			}
		}

		/**
		 * 加载表格数据
		 */
		this.loadData = function () {
			jp.progress.start();
			$.get(options.readUrl, self.params, function (data) {
				//渲染表格数据
				self.data.rows = [];
				$(data.items).each(function (key, rawData) {
					var showData = [];
					$(self.data.cols).each(function (key, col) {
						if (col.type == 'action') {
							//如果是操作列, 追加操作按钮
							var buttonItems = col.render(rawData[col.name], rawData);
							showData.push(actioner.show(buttonItems, rawData, key));
						} else {

							if (col.render) {
								//如果有render回调函数，则调用处理数据再追加s
								showData.push(col.render(rawData[col.name], rawData));
							} else {
								//否则直接追加值
								showData.push(rawData[col.name]);
							}
						}
					});
					var row = {checked: false, data: showData, rawData: rawData};
					self.data.rows.push(row);
				});
				//重新load表格数据
				$(options.datatableId).datatable('load');
				//绑定操作列行为事件
				actioner.bind();

				//渲染分页数据
				jp.pager.render(options.pagerId, data.total, self.params.page, self.params.page_size, function () {
					self.params.page = $(this).attr('data-page');
					self.loadData();
				});
				jp.progress.done();
			});
		}

		/**
		 * 重新查询参数
		 */
		this.resetParams = function () {
			self.params = {
				page: options.page,
				page_size: options.page_size
			}
		};
		/**
		 * 搜索事件
		 * @returns {undefined}
		 */
		this.onSearch = function () {
			self.resetParams();
			$(options.searchFromId).find('[name]').each(function (key, field) {
				self.params[field.name] = $(this).val();
			});
			self.loadData();
		}

		/**
		 * 清除搜索条件
		 * @returns {undefined}
		 */
		this.onRemove = function () {
			$(options.searchFromId).find('[name]').each(function (key, field) {
				$(this).val('');
				self.params[field.name] = $(this).val();
			});
			self.loadData();
		}

		/**
		 * 排序
		 */
		this.onSort = function (event) {
			self.params.sort_field = self.data.cols[event.sorter.index].name;
			self.params.sort_type = event.sorter.type == 'up' ? 'asc' : 'desc';
			self.loadData();
		}
		_init();
	}
};


//固定导航菜单
var width, scrollTop, menu = $('.menu');
var offsetTop = menu.offset().top;
var handleScroll = function ()
{
	width = menu.width();
	scrollTop = $(window).scrollTop();
	menu.toggleClass('affix', scrollTop > offsetTop && $(window).width() >= 992);
	if (menu.hasClass('affix'))
	{
		menu.css({
			width: width,
			top: '0px'
		});
	}
	else
	{
		menu.attr('style', '');
	}
};
$(window).scroll(handleScroll);