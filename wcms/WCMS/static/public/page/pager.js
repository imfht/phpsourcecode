(function($) {
	$.fn.pager = function(options) {
		var opts = $.extend( {}, $.fn.pager.defaults, options);
		return this.each(function() {
				$(this).empty().append(
						renderpager(parseInt(options.pagenumber),
								parseInt(options.pagecount),
								options.buttonClickCallback));
				$('.pages li').mouseover(function() {
					document.body.style.cursor = "pointer";
				}).mouseout(function() {
					document.body.style.cursor = "auto";
				});
			});
	};
	function renderpager(pagenumber, pagecount, buttonClickCallback) {
		var $pager = $('<ul class="pages"></ul>');
		$pager
				.append(
						renderButton('第一页', pagenumber, pagecount,
								buttonClickCallback)).append(
						renderButton('上一页', pagenumber, pagecount,
								buttonClickCallback));
		var startPoint = 1;
		var endPoint = 5;
		if (pagenumber > 2) {
			startPoint = pagenumber - 2;
			endPoint = pagenumber + 2;
		}
		if (endPoint > pagecount) {
			startPoint = pagecount - 3;
			endPoint = pagecount;
		}
		if (startPoint < 1) {
			startPoint = 1;
		}
		for ( var page = startPoint; page <= endPoint; page++) {
			var currentButton = $('<li class="page-number">' + (page) + '</li>');
			page == pagenumber ? currentButton.addClass('pgCurrent')
					: currentButton.click(function() {
						buttonClickCallback(this.firstChild.data);
					});
			currentButton.appendTo($pager);
		}
		$pager
				.append(
						renderButton('下一页', pagenumber, pagecount,
								buttonClickCallback)).append(
						renderButton('尾页', pagenumber, pagecount,
								buttonClickCallback));
		return $pager;
	}
	function renderButton(buttonLabel, pagenumber, pagecount,
			buttonClickCallback) {
		var $Button = $('<li class="pgNext">' + buttonLabel + '</li>');
		var destPage = 1;
		switch (buttonLabel) {
		case "第一页":
			destPage = 1;
			break;
		case "上一页":
			destPage = pagenumber - 1;
			break;
		case "下一页":
			destPage = pagenumber + 1;
			break;
		case "尾页":
			destPage = pagecount;
			break;
		}
		if (buttonLabel == "第一页" || buttonLabel == "上一页") {
			pagenumber <= 1 ? $Button.addClass('pgEmpty') : $Button
					.click(function() {
						buttonClickCallback(destPage);
					});
		} else {
			pagenumber >= pagecount ? $Button.addClass('pgEmpty') : $Button
					.click(function() {
						buttonClickCallback(destPage);
					});
		}
		return $Button;
	}
	$.fn.pager.defaults = {
		pagenumber : 1,
		pagecount : 1
	};
})(jQuery);
