$(function(){
	
	roll($('div.pro-rol'), 4, 1140);

	roll($('div.tea-rol'), 4, 1140);
	
	function roll($scroll, srollNum, srollWidth){

		var $lis = $scroll.find('div.roll li'),

			curNum = 0;

		$scroll.find('span.btn-left').click(function(){

			scroll(0);
		
		});

		$scroll.find('span.btn-right').click(function(){

			scroll(1);
		
		});

		function scroll(flag){

			var length = $lis.length,
				
				max = Math.ceil(length / srollNum) - 1;

			if(flag){

				if(curNum == max) curNum = 0;

				else curNum += 1;

			}else{

				if(curNum == 0) curNum = max;

				else curNum -= 1;

			}

			$scroll.find('ul').stop().animate({'left' : '-' + (curNum * srollWidth) + 'px'}, 1500);
		
		}

	}
	
    var $img = [],

    cur, intval, lock = false,

    $layer = $('#banner'),
    
    $handlers = $('<div class="btns"></div>').appendTo($layer);

	$layer.find('div.imgs a').each(function(i){
	
	    $img.push($(this));
	    
	    $handlers.append('<span></span>');
	
	});
	
	cur = $img.length - 1;
	
	cur = cur < 0 ? 0 : cur;
	
	$layer.children('div.imgs').on({
	
	    mouseenter : function(){ lock = true;},
	
	    mouseleave : function(){ lock = false;}
	
	});
	
	$handlers.find('span').each(function(i){
	
	    $(this).click(function(){

	        clearTimeout(intval);
	
	        show(i);
	
	    });
	
	});
	
	$layer.find('span.prev').click(function(){
		
		show(cur - 1);
	
	});
	
	$layer.find('span.next').click(function(){
		
		show(cur + 1);
	
	});		
	
	function show(n){
	
	    if(cur == n || lock){ loop(); return}
	
	    else if(n < 0) n = $img.length - 1;
	
	    else if(n > $img.length - 1) n = 0;

	    $img[cur].stop().fadeOut('slow');

	    $handlers.find('span:eq(' + cur + ')').removeClass('checked');
	
	    $img[n].stop().fadeIn('slow');
	
	    $handlers.find('span:eq(' + n + ')').addClass('checked');
	
	    cur = n;
	
	    loop();
	
	}
	
	function loop(){
	
	    clearInterval(intval);
	
	    intval = setTimeout(function(){ show(cur + 1)}, 5000);
	
	}
	
	show(0);

});