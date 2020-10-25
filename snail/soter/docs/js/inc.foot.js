if (parent == self) {
	document.write('<p> Powered By Soter &copy; ' + new Date().getFullYear() + '</p>');
	var goPage = window.location.hash.substr(1);
	if (goPage) {
		$('#mainBody').attr('src', $('.leftNav').find('a[href$="' + goPage + '.html"]').eq(0).attr('href'));
	}
	$('.leftNav').find('a').click(function () {
		$('#loadingInfo').show();
	});
} else {
	if (document.title) {
		top.document.title = document.title + "_Soter使用手册";
	}
}

$(function () {
	if ($('legend')[0]) {
		var lis = [];
		$('.title_h2').each(function (index) {
			var name = '_taget_' + index;
			$(this).after('<a name="' + name + '"></a>');
			lis.push('<li><a href="#' + name + '" class="anchor">' + $(this).text() + '</a></li>');
			$(this).append('<a href="#top" style="font-size:12px;margin-left:30px;" class="anchor" ">返回顶部</a>');
		});
		if (lis.length) {
			$('legend').after('<div><a name="top"></a><h2 class="title_h2">目录</h2><ol>' + lis.join('') + '</ol></div>');
		}
		$(".anchor").each(function () {
			var link = $(this);
			var href = link.attr("href");
			if (href && href[0] == "#") {
				var name = href.substring(1);
				$(this).click(function () {
					var nameElement = $("[name='" + name + "']");
					var idElement = $("#" + name);
					var element = null;
					if (nameElement.length > 0) {
						element = nameElement;
					} else if (idElement.length > 0) {
						element = idElement;
					}
					if (element) {
						var offset = element.offset();
						var top = offset.top + 150;
						window.parent.scrollTo(offset.left, top);
					}
					return false;
				});
			}
		});
	}
	;


});

var _hmt = _hmt || [];
(function () {
	var hm = document.createElement("script");
	hm.src = "https://hm.baidu.com/hm.js?171ef9efe6276634284825893be5ece2";
	var s = document.getElementsByTagName("script")[0];
	s.parentNode.insertBefore(hm, s);
})();
