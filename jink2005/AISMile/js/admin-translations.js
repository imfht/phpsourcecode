/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

var displayOnce = 0;
google.load("language", "1");
function translateAll() {
	if (!ggIsTranslatable(gg_translate['language_code']))
		alert('"'+gg_translate['language_code']+'" : '+gg_translate['not_available']);
	else
	{
		$.each($('input[type="text"]'), function() {
			var tdinput = $(this);
			if (tdinput.attr("value") == "" && tdinput.parent("td").prev().html()) {
				google.language.translate(tdinput.parent("td").prev().html(), "en", gg_translate['language_code'], function(result) {
					if (!result.error)
						tdinput.val(result.translation);
					else if (displayOnce == 0)
					{
						displayOnce = 1;
						alert(result.error.message);
					}
				});
			}
		});
		$.each($("textarea"), function() {
			var tdtextarea = $(this);
			if (tdtextarea.html() == "" && tdtextarea.parent("td").prev().html()) {
				google.language.translate(tdtextarea.parent("td").prev().html(), "en", gg_translate['language_code'], function(result) {
					if (!result.error)
						tdtextarea.html(result.translation);
					else if (displayOnce == 0)
					{
						displayOnce = 1;
						alert(result.error.message);
					}
				});
			}
		});
	}
}

$(document).ready(function(){$('#translate_all').bind('click', function(){
	translateAll();
})});
