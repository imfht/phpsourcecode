$('.stayShape img').jqthumb({
		width: 200,
		height: 200,
		after: function(imgObj){
			imgObj.css('opacity', 0).animate({opacity: 1}, 2000);
		}
	});