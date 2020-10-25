$(function($) {
	$('div#content-wrapper').css({'minHeight':$(window).height()-80});

	var storage, fail, uid;
	try {
		uid = new Date; (storage = window.localStorage).setItem(uid, uid);
		fail = storage.getItem(uid) != uid;
		storage.removeItem(uid);
		fail && (storage = false);
	} catch(e) {}
	if (storage) {
		try {
			var usedSkin = localStorage.getItem('config-skin');
			if (usedSkin != '') {
				$('#skin-colors .skin-changer').removeClass('active');
				$('#skin-colors .skin-changer[data-skin="' + usedSkin + '"]').addClass('active');
			}
			
			//固定头部、左侧菜单以及底部版权信息
			$('body').addClass('fixed-header');
			$('body').addClass('fixed-footer');
			$('body').addClass('fixed-leftmenu');
			if ($('#page-wrapper').hasClass('nav-small')) {
				$('#page-wrapper').removeClass('nav-small');
			}

			$('.fixed-leftmenu #col-left').nanoScroller({
				alwaysVisible: true,
				iOSNativeScrolling: false,
				preventPageScrolling: true,
				contentClass: 'col-left-nano-content'
			});

			var boxedLayout = localStorage.getItem('config-boxed-layout');
			if (boxedLayout == 'boxed-layout') {
				$('body').addClass(boxedLayout);
				$('#config-boxed-layout').prop('checked', true);
			}
			var sidebarSamll = localStorage.getItem('config-sidebar-samll');
			if (sidebarSamll == 'sidebar-samll') {
				$('#config-sidebar-samll').prop('checked', true);
				$('#page-wrapper').addClass('nav-small');
			} else {
				$('#page-wrapper').removeClass('nav-small');
			};
		} catch(e) {
			console.log(e);
		}
	}
	$('#config-tool-cog').on('click',
	function() {
		$('#config-tool').toggleClass('closed');
	});
	$('#config-boxed-layout').on('change',
	function() {
		var boxedLayout = '';
		if ($(this).is(':checked')) {
			$('body').addClass('boxed-layout');
			boxedLayout = 'boxed-layout';
		} else {
			$('body').removeClass('boxed-layout');
		}
		writeStorage(storage, 'config-boxed-layout', boxedLayout);
	});

	$('#config-sidebar-samll').on('change',function(){
		var sidebarSamll = '';
		if ($(this).is(':checked')) {
			$('#page-wrapper').addClass('nav-small');
			sidebarSamll = 'sidebar-samll';
			writeStorage(storage, 'config-sidebar-samll', sidebarSamll);
		} else {
			$('#page-wrapper').removeClass('nav-small');
			writeStorage(storage, 'config-sidebar-samll', sidebarSamll);
			location.reload();
		}
	})
	if (!storage) {
		$('#config-boxed-layout').prop('checked', false);
		$('#config-sidebar-samll').prop('checked', false);
	}
	$('#skin-colors .skin-changer').on('click',
	function() {
		$('body').removeClassPrefix('theme-');
		$('body').addClass($(this).data('skin'));
		$('#skin-colors .skin-changer').removeClass('active');
		$(this).addClass('active');
		writeStorage(storage, 'config-skin', $(this).data('skin'));
	});


	//合并自script.js
	setTimeout(function() {
		$('#content-wrapper > .row').css({
			opacity: 1
		});
	},
	200);
	$('#sidebar-nav,#nav-col-submenu').on('click', '.dropdown-toggle',
	function(e) {
		e.preventDefault();
		var $item = $(this).parent();
		if (!$item.hasClass('open')) {
			$item.parent().find('.open .submenu').slideUp('fast');
			$item.parent().find('.open').toggleClass('open');
		}
		$item.toggleClass('open');
		if ($item.hasClass('open')) {
			$item.children('.submenu').slideDown('fast');
		} else {
			$item.children('.submenu').slideUp('fast');
		}
	});
	$('body').on('mouseenter', '#page-wrapper.nav-small #sidebar-nav .dropdown-toggle',
	function(e) {
		if ($(document).width() >= 992) {
			var $item = $(this).parent();
			if ($('body').hasClass('fixed-leftmenu')) {
				var topPosition = $item.position().top;
				if ((topPosition + 4 * $(this).outerHeight()) >= $(window).height()) {
					topPosition -= 6 * $(this).outerHeight();
				}
				$('#nav-col-submenu').html($item.children('.submenu').clone());
				$('#nav-col-submenu > .submenu').css({
					'top': topPosition
				});
			}
			$item.addClass('open');
			$item.children('.submenu').slideDown('fast');
		}
	});
	$('body').on('mouseleave', '#page-wrapper.nav-small #sidebar-nav > .nav-pills > li',
	function(e) {
		if ($(document).width() >= 992) {
			var $item = $(this);
			if ($item.hasClass('open')) {
				$item.find('.open .submenu').slideUp('fast');
				$item.find('.open').removeClass('open');
				$item.children('.submenu').slideUp('fast');
			}
			$item.removeClass('open');
		}
	});
	$('body').on('mouseenter', '#page-wrapper.nav-small #sidebar-nav a:not(.dropdown-toggle)',
	function(e) {
		if ($('body').hasClass('fixed-leftmenu')) {
			$('#nav-col-submenu').html('');
		}
	});
	$('body').on('mouseleave', '#page-wrapper.nav-small #nav-col',
	function(e) {
		if ($('body').hasClass('fixed-leftmenu')) {
			$('#nav-col-submenu').html('');
		}
	});
	$('#make-small-nav').click(function(e) {
		$('#page-wrapper').toggleClass('nav-small');
	});
	$(window).smartresize(function() {
		if ($(document).width() <= 991) {
			$('#page-wrapper').removeClass('nav-small');
		}
	});
	$('.mobile-search').click(function(e) {
		e.preventDefault();
		$('.mobile-search').addClass('active');
		$('.mobile-search form input.form-control').focus();
	});
	$(document).mouseup(function(e) {
		var container = $('.mobile-search');
		if (!container.is(e.target) && container.has(e.target).length === 0) {
			container.removeClass('active');
		}
	});

	$('.fixed-leftmenu #col-left').nanoScroller({
		alwaysVisible: true,
		iOSNativeScrolling: false,
		preventPageScrolling: true,
		contentClass: 'col-left-nano-content'
	});
	
	$("[data-toggle='tooltip']").each(function(index, el) {
		$(el).tooltip({
			placement: $(this).data("placement") || 'top'
		});
	});
});
function writeStorage(storage, key, value) {
	if (storage) {
		try {
			localStorage.setItem(key, value);
		} catch(e) {
			console.log(e);
		}
	}
}

$.fn.removeClassPrefix = function(prefix) {
	this.each(function(i, el) {
		var classes = el.className.split(" ").filter(function(c) {
			return c.lastIndexOf(prefix, 0) !== 0;
		});
		el.className = classes.join(" ");
	});
	return this;
}; (function($, sr) {
	var debounce = function(func, threshold, execAsap) {
		var timeout;
		return function debounced() {
			var obj = this,
			args = arguments;
			function delayed() {
				if (!execAsap) func.apply(obj, args);
				timeout = null;
			};
			if (timeout) clearTimeout(timeout);
			else if (execAsap) func.apply(obj, args);
			timeout = setTimeout(delayed, threshold || 100);
		};
	}
	jQuery.fn[sr] = function(fn) {
		return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr);
	};
})(jQuery, 'smartresize');

function helpIntro(){
	var placementRight = 'right';
	var placementLeft = 'left';
	
	if ($('body').hasClass('rtl')) {
		placementRight = 'left';
		placementLeft = 'right';
	}
	
	// Define the tour!
	var tour = {
		id: "Cube-intro",
		steps: [
			{
				target: 'make-small-nav',
				title: "设置小菜单按钮",
				content: "点击小菜单可以把左侧菜单变成小菜单，增大右侧操作区域！",
				placement: "bottom",
				zindex: 999,
				xOffset: -8
			},
			{
				target: 'config-tool-options',
				title: "后台配置工具",
				content: "配置后台主题色彩，定制头部、左侧菜单以及底部信息",
				placement: placementLeft,
				zindex: 999,
				fixedElement: true,
				xOffset: -55
			},
			{
				target: 'sidebar-nav',
				title: "左侧导航区域",
				content: "左侧功能导航区域。",
				placement: placementRight
			}
		],
		showPrevButton: true
	};

	// Start the tour!
	hopscotch.startTour(tour);
}