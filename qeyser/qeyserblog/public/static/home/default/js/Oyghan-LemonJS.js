
$(function(){
	//izdesh ramkisi unumi
	$('#search').focus(function(){
		$('#search').stop().animate({
			width: "200px",
		},"slow")
	})
	$('#search').blur(function(){
		$('#search').stop().animate({
			width: "160px",
		},"slow")
	})
	//yazma bolek
	$('.oyghan-notes').hover(function(){
		$(this).find('img').addClass('hover');
	},function(){
		$(this).find('img').removeClass('hover');
	})	


})

//bashbet siyrilma resim unum
$(function(){
    //1. optomatik mingish
    var i = 0;
    // funkisiye
    function run(){
    	// silishturma sanliq miqdar
    	i++;
    	if(i==4){
    		i=0;
    	}
    	// nowettiki li  ni yorutush
    	$("ul.oyghanSlideNum li").eq(i).addClass('on').siblings('li').removeClass('on');
    	// mas resimni korsitish
    	$(".slide-main a").eq(i).fadeIn(500).siblings("a").stop().fadeOut(500);
    }
    // waqit belgilesh
    timer = setInterval(run,5000);
    // 3.qolda almashturush
    $("ul.oyghanSlideNum li").mouseover(function(){
        i = $(this).index();
        // nowettiki li  ni yorutush
    	$("ul.oyghanSlideNum li").eq(i).addClass('on').siblings('li').removeClass('on');
    	// mas resimni korsitish
    	$(".slide-main a").eq(i).fadeIn(500).siblings("a").stop().fadeOut(500);         
    });
}) 

//ustige qaytish funkisiysi
	$(function(){
		showScroll();
		function showScroll(){
			$(window).scroll( function() { 
				var scrollValue=$(window).scrollTop();
				scrollValue > 100 ? $('div[class=scroll]').fadeIn():$('div[class=scroll]').fadeOut();
			} );	
			$('#scroll').click(function(){
				$("html,body").animate({scrollTop:0},800);	
			});	
		}
	})   