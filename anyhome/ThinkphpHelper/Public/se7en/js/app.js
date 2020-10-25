

$(function(){
	$(".navbar").mouseover(function() {
        $(".navbar").removeClass("closed");
        return setTimeout((function() {
            return $(".navbar").css({
                overflow: "visible"
            });
        }), 350);
    });

	$('.scrollbar').ClassyScroll({
        sliderOpacity: 1,
        wheelSpeed: 2,
        onscroll: function() {
            return $(this).prev().addClass("shadow");
        }
    });

	$("[fancybox]").fancybox({
        maxWidth: 700,
        height: 'auto',
        fitToView: false,
        autoSize: true,
        padding: 15,
    });

    $('.scrollbar').ClassyScroll({
        sliderOpacity: 1,
        wheelSpeed: 2,
        onscroll: function() {
            return $(this).prev().addClass("shadow");
        }
    });




    
})