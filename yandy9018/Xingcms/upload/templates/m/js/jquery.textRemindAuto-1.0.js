(function($){
	$.fn.textRemindAuto = function(options){
		options = options || {};
		var defaults = {
			blurColor: "#999",
			focusColor: "#666",
			auto: true,
			chgClass: ""
		};
		var settings = $.extend(defaults,options);
		$(this).each(function(){
			if(defaults.auto){
				$(this).css("color",settings.blurColor);
			}
			var v = $.trim($(this).val());
			if(v){
				$(this).focus(function(){
					if($.trim($(this).val()) === v){
						$(this).val("");
					}
					$(this).css("color",settings.focusColor);
					if(settings.chgClass){
						$(this).toggleClass(settings.chgClass);
					}
				}).blur(function(){
					if($.trim($(this).val()) === ""){
						$(this).val(v);
					}
					$(this).css("color",settings.blurColor);
					if(settings.chgClass){
						$(this).toggleClass(settings.chgClass);
					}
				});	
			}
		});
	};
})(jQuery);