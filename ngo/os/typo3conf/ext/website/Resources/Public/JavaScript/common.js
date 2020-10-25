(function ($) {
    var tempString = '';
    $(window).scroll(function () {
        if ($(window).scrollTop() > 300) {
            $(".go-to-top").fadeIn(500);
        } else {
            $(".go-to-top").fadeOut(500);
        }
    });
    $(document).ready(function ($) {
        App.init(); // init core

        //check mobile video
        if (/(iPad|iPhone|iPod|Android|iOS|micromessenger)/ig.test(window.navigator.userAgent)) {
            $('.sticky-bar').css('top', '90%');
            //$('.sticky-bar .qrcode, .sticky-bar .share').remove();
            $('.sticky-bar .share').remove();
            $('.sticky-bar .qrcode').remove();
            $('.slider-video').remove();
        } else {
            //page qr code
            $('#pageQrCode').attr('src', 'typo3conf/ext/website/Common/qrcode/qrcode.php?code=' + encodeURIComponent(window.location.href));
            $('.slider-video').each(function () {
                $(this).removeClass('hide').next('.slider-image').remove();
            });
        }

        $(".scroll").click(function (event) {
            event.preventDefault();
            $('html,body').animate({scrollTop: $(this.hash).offset().top}, 1000);
        });

        //table
        $('.csc-default table, .container table').each(function () {
            if (!$(this).hasClass('table')) {
                $(this).addClass('table');
            }
            if (!$(this).parent().hasClass('table-responsive')) {
                $(this).wrap('<div class="table-responsive"></div>');
            }
        });

        //date time picker
        $('.datepicker').datetimepicker({
            format: 'YYYY-MM-DD',
            locale: 'zh-cn'
        });

        $('.tx-indexedsearch-browsebox').each(function () {
            $(this).addClass('text-center');
            var currentPage = $(this).find('.tx-indexedsearch-browselist-currentPage');
            currentPage.addClass('active').html(currentPage.find('strong').html());
            $(this).find('.browsebox').addClass('pagination pagination-sm').find('li>a').each(function () {
                $(this).html($(this).html().replace(/(Page\ |Next\ |\ Previous)/g, ''));
            });
        });

        //share
        $('.sticky-bar .share .fa').click(function (e) {
            $(this).next().trigger('click');
            e.stopPropagation();
        });

        //Home banner
        var slider = $('.c-layout-revo-slider .tp-banner');
        var cont = $('.c-layout-revo-slider .tp-banner-container');
        var api = slider.show().revolution(
            {
                sliderType: "standard",
                sliderLayout: "fullscreen",
                dottedOverlay: "none",
                delay: 100000,
                navigation: {
                    keyboardNavigation: "off",
                    keyboard_direction: "horizontal",
                    mouseScrollNavigation: "off",
                    onHoverStop: "off",
                    arrows: {
                        style: "circle",
                        enable: true,
                        hide_onmobile: false,
                        hide_onleave: false,
                        tmp: '',
                        left: {
                            h_align: "left",
                            v_align: "center",
                            h_offset: 30,
                            v_offset: 0
                        },
                        right: {
                            h_align: "right",
                            v_align: "center",
                            h_offset: 30,
                            v_offset: 0
                        }
                    }
                },
                responsiveLevels: [2048, 1024, 778, 480],
                gridwidth: [1240, 1024, 778, 480],
                gridheight: [868, 768, 960, 720],
                //lazyType: "none",
                shadow: 0,
                spinner: "spinner2",
                stopLoop: "off",
                stopAfterLoops: -1,
                stopAtSlide: -1,
                shuffle: "off",
                autoHeight: "off",
                touchenabled: "on",
                disableProgressBar: "on",
                hideThumbsOnMobile: "off",
                hideSliderAtLimit: 0,
                hideCaptionAtLimit: 0,
                hideAllCaptionAtLilmit: 0,
                debugMode: false,
                fallbacks: {
                    simplifyAll: "off",
                    nextSlideOnWindowFocus: "off",
                    disableFocusListener: false
                }
            });

        // BEGIN: ISOTOPE GALLERY 4 INIT
        // init isotope gallery
        var $grid4 = $('.c-content-isotope-gallery.c-opt-4').imagesLoaded(function () {
            // init Isotope after all images have loaded
            $grid4.isotope({
                // options...
                itemSelector: '.c-content-isotope-item',
                layoutMode: 'packery',
                fitWidth: true,
                percentPosition: true
            });
        });
        // Filter buttons
        $('.c-content-isotope-filter-1').on('click', 'button', function () {
            var filterValue = $(this).attr('data-filter').substring(1);
            $grid4.isotope({filter: filterValue});
            $('.c-content-isotope-filter-1 .c-isotope-filter-btn').removeClass('c-active');
            $(this).addClass('c-active');

            // scroll to top of element on click
            $('html, body').stop();
            $('html, body').animate({
                scrollTop: $("#c-isotope-anchor-1").offset().top
            }, 500);
        });
        // END: ISOTOPE GALLERY 4

    });
})(jQuery);

$(".bds_weixin").bind("click",function(){
 	//$("#bdshare_weixin_qrcode_dialog").css({"width":"250px","height":"295px"});
	var interval = setInterval(function(){
		var width = $("#bdshare_weixin_qrcode_dialog table").css("width");
 		
 		if(width>'212px' || typeof(width)=="undefined"){
 			$("#bdshare_weixin_qrcode_dialog").css({"width":"290px","height":"345px"});
 		}else{
			$("#bdshare_weixin_qrcode_dialog").css({"width":"250px","height":"325px"});
			//$("#bdshare_weixin_qrcode_dialog table").css("width","212px");
		}
		if(typeof(width)!="undefined"){
			clearInterval(interval);
		}
	}, 300);
});
function videoheight(){
    var len = $("#c-video-card-3 video").height();
    var len_con = len+'px !important';
    $('.c-grid').css({
        'height': len_con,
        'min-height':len_con
    });
}
setTimeout(videoheight,1000);

$(".backnews .carousel-inner").children('.item').eq(0).addClass('active');

//iphone 手机响应式 变大问题
window.onload = function () {
    document.addEventListener('gesturestart', function (e) {
    	e.preventDefault();
    });
    document.addEventListener('dblclick', function (e) {
    	e.preventDefault();
    });
    document.addEventListener('touchstart', function (event) {
        // alert(1);
	    if (event.touches.length > 1) {
	    	event.preventDefault();
	    }
    });
    var lastTouchEnd = 0;
    document.addEventListener('touchend', function (event) {
	    var now = (new Date()).getTime();
	    if (now - lastTouchEnd <= 300) {
	    	event.preventDefault();
	    }
    	lastTouchEnd = now;
    }, false);
};