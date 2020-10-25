
(function($){
    $.fn.Slider = function(config){
            var defaults = {
            	speed:'slow',
            	easing:'swing',
            	stay:5000
            };
        var config = $.extend(defaults, config);
        return this.each(function(){
        	var slider = $(this),
	        	slider_w = slider.width(),
	    		item = slider.find('> div'),
	        	total = item.children().length,
	        	current=1;
	        	
	        slider.css({position: "relative", margin: "0 auto",overflow: "hidden"});
	        slider.find("div").css({position: 'absolute',width: '19999px',display: 'block'});
	    	item.children().eq(0).clone().appendTo(item);
	    	var tem = '<ul class="dot-nav">';
	    	for (var i = 1; i <= total; i++) {
	    		tem+='<li><a>'+i+'</a></li>';
	    	}
	    	slider.append(tem+'</ul>');
	    	slider.find(".dot-nav > li").removeClass('current').eq(0).addClass('current');
	    	//$(".dot-nav >li >  a",slider).each(function(i){
	    	slider.find(".dot-nav >li >  a").each(function(i){
	    		$(this).on("click", function(){
	    			run(i);
				});
				$(this).on("mouseover",function(){clearInterval(si);});
				$(this).on("mouseout",function(){
					si = setInterval(function(){
	           		run();}, config.stay);});
	    	});
	    	var run = function (index){
	    		if(index!=undefined) current=index;
	    		item.animate({left: -1 * slider_w * current},config.speed,config.easing,function(){
	    			current++;
	    			if(current>total){
		    			current=1;
		    			item.css({left:0});
		    		}
		    		slider.find(".dot-nav > li").removeClass('current').eq(current-1).addClass('current');
		    	});
	    	}
	    	//run(1);
	    	var si = setInterval(function(){
	           run();
	        }, config.stay);

	    });
    }
})(jQuery);