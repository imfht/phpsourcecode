;(function ($) {
	var leftMenu = function (ele, opt) {
		var instance = this;
		instance.$element = ele;
		instance.defaults = {
			'menu_container': '.left-menu'
		};
		instance.options = $.extend({}, instance.defaults, opt);

		instance.init = function () {
			instance.requestMenuData();
			instance.bindMenuItemClickEvent();
			instance.bindMenuExpandEvent();
			instance.bindMenuHoverEvent();
		};

		instance.requestMenuData = function () {
            var target = $(instance.options.menu_container).attr('href') || $(instance.options.menu_container).attr('url');
            if (target !== undefined && target !== '' && target !== '#') {
				$.post(target, {}, function (data) {
					if (data.status === 1) {
						if (data.info.length < 1) {
							$(instance.options.menu_container).html('');
							return;
						}

						var ulContent = new Array();
						instance.loadSubMenu(ulContent, data.info, 0);
						$(instance.options.menu_container).html(ulContent.join(''));
					} else {
						cigoLayer.msg(data.info, {icon: 5});
					}
				});
			}
		};

		instance.loadSubMenu = function (ulContent, pList, level) {
			if (level == 0) {
				ulContent.push('<ul class="menu-list-left-sider" style="z-index: ' + (level + 1) + ';">');
			} else {
				ulContent.push('<ul style="z-index: ' + (level + 1) + ';">');
			}
			$.each(pList, function (key, dataItem) {
				if ('group_flag' in dataItem) {
					ulContent.push('<li class="group">');
					ulContent.push(dataItem['title']);
					ulContent.push('</li>');

					return;
				}

				if ('subList' in dataItem) {
					ulContent.push('<li id="menu_' + dataItem['id'] + '" data-ids="' + dataItem['path'] + dataItem['id'] + '" class="has-sub">');
				} else {
					ulContent.push('<li id="menu_' + dataItem['id'] + '"  data-ids="' + dataItem['path'] + dataItem['id'] + '">');
				}
				ulContent.push(//菜单项存在链接则不影响其跳转，即跳转和展开子项同时进行
					'' == dataItem['url'] ? '<a href="#" onclick="return false;" title="' + dataItem['title'] + '">' :
						'<a href="' + dataItem['url'] + '" target="' + dataItem['target'] + '" title="' + dataItem['title'] + '">'
				);
				ulContent.push('<i class="cigo-iconfont ' + dataItem['icon'] + '"></i>');
				ulContent.push('<span>&nbsp;' + dataItem['title'] + '</span>');

				if (level == 0 && ('subList' in dataItem)) {
					ulContent.push('<i class="cigo-iconfont cigo-icon-left pull-right more"></i>');
				}
				ulContent.push('<span class="label pull-right ' + dataItem['label_class'] + '"></span>');

				ulContent.push('</a>');
				if ('subList' in dataItem) {
					instance.loadSubMenu(ulContent, dataItem['subList'], level + 1);
				}
				ulContent.push('</li>');
			});
			ulContent.push('</ul>');
		};

		instance.bindMenuItemClickEvent = function () {
			var target = $(instance.options.menu_container).attr('url-add-opt-rate');
			if ($(instance.options.menu_container).hasClass('count-opt-rate') && target != '' && target != undefined) {
				$(instance.options.menu_container).on('click', ' ul>li', function (e) {
					var argsData = {};
					argsData['ids'] = $(this).attr('data-ids');
					$.post(target, argsData, function (data) {
					});
					//菜单项存在链接则不影响其跳转
					// return false;
				});
			}
		};

		instance.bindMenuExpandEvent = function () {
			$(instance.options.menu_container).on('click', '> ul>li.has-sub>a', function (e) {
				if ($(instance.options.menu_container).parent().hasClass("closed")) {
					return;
				}

				var liNode = $(this).parent();
				if (liNode.hasClass('active')) {
					liNode.find('>ul').slideUp(300, function () {
						liNode.find('>ul').removeClass('open');
						liNode.removeClass('active');
						liNode.find('>ul').css('display', '');
					});
				} else {
					liNode.parent().find('>li.active>ul.open').slideUp(300, function () {
						liNode.parent().find('>li.active>ul').removeClass('open');
						liNode.parent().find('>li.active').removeClass('active');
					});

					liNode.find('>ul').slideDown(300, function () {
						liNode.addClass('active');
						liNode.find('>ul').addClass('open');
						liNode.find('>ul').css('display', '');
					});
				}
			});
		};

		instance.bindMenuHoverEvent = function () {
			$(instance.options.menu_container).on('mouseenter mouseleave', '>ul>li>ul>li.has-sub', function (e) {
				if ($(instance.options.menu_container).parent().hasClass("closed")) {
					return;
				}

				var currLiNode = $(this);
				instance.liHover(currLiNode, e);
			});
			$(instance.options.menu_container).on('mouseenter mouseleave', '>ul>li.has-sub', function (e) {
				if (!$(instance.options.menu_container).parent().hasClass("closed")) {
					return;
				}

				var currLiNode = $(this);
				instance.liHover(currLiNode, e);
			});
		};

		instance.liHover = function (currLiNode, e) {
			switch (e.type) {
				case 'mouseenter':
					currLiNode.find('>ul').css('right', -195);
					currLiNode.find('>ul').css('top', currLiNode.offset().top - $(instance.options.menu_container).offset().top);
					break;
				case 'mouseleave':
					currLiNode.find('>ul').css('left', '');
					currLiNode.find('>ul').css('top', '');
					break;
			}
		};

		instance.smallLayout = function () {
			$(instance.options.menu_container).find('.menu-list-left-sider>li.active>ul.open').removeClass('open');
			$(instance.options.menu_container).find('.menu-list-left-sider>li.active').removeClass('active');
		};
	};

//定义插件
	$.fn.leftMenu = function (options) {
		var menuInstance = new leftMenu(this, options);
		//进行初始化操作
		menuInstance.init();

		return menuInstance;
	};
})
(jQuery);
