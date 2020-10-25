/**
 * 分页JS
 */
(function($) {
	
	/**
	 * 注册jQuery分页插件
	 */
	$.fn.extend({
		
		/**
		 * 分页插件
		 */
		"pager" : function() {
			var $pager_box = $(this);
			var next_since_id = $pager_box.attr("next-since-id");
			var prev_since_id = $pager_box.attr("prev-since-id");
			var base_href = decodeURIComponent($pager_box.attr("base-href"));
			var last_page = $pager_box.attr("last-page");
			var pagerGo = function(number) {
				var query_data = {
					"next_since_id" : next_since_id,
					"prev_since_id" : prev_since_id,
					"last_page" : last_page,
					"p" : number
				};
				var href = base_href;
				href += (href ? "&" : "?") + $.param(query_data);
				location.href = href;
			};
			$pager_box.delegate("[action-type=pager-go]", "click", function() {
				pagerGo($(this).attr("number"));
			});
			
			$pager_box.find("[name=pager-selector]").change(function(){
				pagerGo($(this).val());
			});
		},
		
	
	});
})(jQuery);


$(function(){
	$("[node-type=pager]").pager();
});