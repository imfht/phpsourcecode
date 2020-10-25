+(function($){
	$(window).scroll(function() {

		//$('.ecology .muu').each();

		$('.spanele').each(function(){
		var imagePos = $(this).offset().top;
		
		var topOfWindow = $(window).scrollTop();
			if (imagePos < topOfWindow+700) {
				if($(this).hasClass('muu')){
					$(this).addClass("fadeInDown");
					$(this).removeClass("animate-box");
				}
				if($(this).hasClass('deve')){
					$(this).addClass("fadeInLeft");
					$(this).removeClass("animate-box");
				}
				if($(this).hasClass('store')){
					$(this).addClass("fadeInRight");
					$(this).removeClass("animate-box");
				}
				if($(this).hasClass('need')){
					$(this).addClass("fadeInUp");
					$(this).removeClass("animate-box");
				}
			}
		});	
        
        $('.services').each(function(){
			var imagePos = $(this).offset().top;
		
			var topOfWindow = $(window).scrollTop();
			if (imagePos < topOfWindow+500) {
				$(this).addClass("bounceIn");
			}
		});	

		$('.scheme .ts-d_ul li').each(function(){
			var imagePos = $(this).offset().top;
			
			var topOfWindow = $(window).scrollTop();
				if (imagePos < topOfWindow+500) {
					$(this).addClass("flipInY");
					$(this).removeClass("animate-box");
				}
		});	
        
        $('.developer .fh5co-box').each(function(){
			var imagePos = $(this).offset().top;
			
			var topOfWindow = $(window).scrollTop();
				if (imagePos < topOfWindow+500) {
					$(this).addClass("fadeInUp");
				}
		});	
				
	});

})(jQuery);
		