"use strict";

// *** General Variables *** //
var $window = $(window),
	$document = $(document),
	$this = $(this),
	$html = $("html"),
	$body = $("body");


// *** On ready *** //
$document.on("ready", function () {
	responsiveClasses();
	imageBG();
	fitVideos();
	lightboxImage();
	lightboxGallery();
	lightboxIframe();
	onePageNav();
	scrollToAnchor();
	stickyHeaderBar();
	sliderBanner();
	sliderServices2();
	sliderClients();
	sliderTestimonials();
	sliderProjects();
	sliderBlogPosts();
	menuMain();
	mobileMenuSidePanel();
	mobileMenu();
	sliderImageBG();
	optimizeSliderImageBG();	
	sectionParallaxImageBG();
});


// *** On load *** //
$window.on("load", function () {
	websiteLoading();

})

	// *** On resize *** //
	.on("resize", function () {
		responsiveClasses();
	})

	// *** On scroll *** //
	.on("scroll", function () {
		scrollTopIcon();
		stickyHeaderBar();
	});



// *** Responsive Classes *** //
function responsiveClasses() {
	var jRes = jRespond([
		{
			label: "smallest",
			enter: 0,
			exit: 479
		}, {
			label: "handheld",
			enter: 480,
			exit: 767
		}, {
			label: "tablet",
			enter: 768,
			exit: 991
		}, {
			label: "laptop",
			enter: 992,
			exit: 1199
		}, {
			label: "desktop",
			enter: 1200,
			exit: 10000
		}
	]);
	jRes.addFunc([
		{
			breakpoint: "desktop",
			enter: function () { $body.addClass("device-lg"); },
			exit: function () { $body.removeClass("device-lg"); }
		}, {
			breakpoint: "laptop",
			enter: function () { $body.addClass("device-md"); },
			exit: function () { $body.removeClass("device-md"); }
		}, {
			breakpoint: "tablet",
			enter: function () { $body.addClass("device-sm"); },
			exit: function () { $body.removeClass("device-sm"); }
		}, {
			breakpoint: "handheld",
			enter: function () { $body.addClass("device-xs"); },
			exit: function () { $body.removeClass("device-xs"); }
		}, {
			breakpoint: "smallest",
			enter: function () { $body.addClass("device-xxs"); },
			exit: function () { $body.removeClass("device-xxs"); }
		}
	]);
}


// *** RTL Case *** //
var HTMLDir = $("html").css("direction"),
	carouselRtl,
	selectRtl,
	slickDirection;

// If page is RTL
if (HTMLDir == "rtl") {
	$("body").addClass("direction-rtl");
	
	carouselRtl = true;
	selectRtl = "rtl";
	slickDirection = true;
} else {
	carouselRtl = false;
	selectRtl = false;
	slickDirection = false;
}


// *** Image Background *** //
function imageBG() {
	$(".img-bg").each(function () {
		var $this = $(this),
			imgSrc = $this.find("img").attr("src");

		if ($this.parent(".section-image").length) {
			$this.css("background-image", "url('" + imgSrc + "')");
		} else {
			$this.prepend("<div class='bg-element'></div>");
			var bgElement = $this.find(".bg-element");
			bgElement.css("background-image", "url('" + imgSrc + "')");
		}
		$this.find("img").css({ "opacity": 0, "visibility": "hidden" });
	});
}


// *** Fit Videos *** //
function fitVideos() {
	$("#full-container").fitVids();
}



// *** Lightbox Iframe *** //
function lightboxIframe() {
	$(".lightbox-iframe").magnificPopup({
		type: 'iframe',
		mainClass: 'mfp-fade',
		removalDelay: 160,
		preloader: false,
		fixedContentPos: false
	});
}


// *** Lightbox Image *** //
function lightboxImage() {
	$(".lightbox-img").magnificPopup({
		type: 'image',
		gallery: {
			enabled: false
		},
		mainClass: 'mfp-fade',
		removalDelay: 160,
		preloader: false,
		fixedContentPos: false
	});
}


// *** Lightbox Gallery *** //
function lightboxGallery() {
	$(".lightbox-gallery").magnificPopup({
		type: 'image',
		gallery: {
			enabled: true
		},
		mainClass: 'mfp-fade',
		removalDelay: 160,
		preloader: false,
		fixedContentPos: false
	});
}


// *** Scroll Top Icon *** //
function scrollTopIcon() {
	var windowScroll = $(window).scrollTop();
	if (windowScroll > 800) {
		$(".scroll-top-icon").addClass("show");
	} else {
		$(".scroll-top-icon").removeClass("show");
	}
}

$(".scroll-top").on("click", function (e) {
	e.preventDefault();
	$("html, body").animate({
		scrollTop: 0
	}, 1200); //1200 easeInOutExpo
});


// *** One Page Nav *** //
function onePageNav() {
	var stickyBar = $(".header-bar.sticky"),
		stickyBarHeight = stickyBar.height() - 20,
		offsetDifference = (!stickyBar) ? 0 : stickyBarHeight;

	$.scrollIt({
		upKey: false,
		downKey: false,
		scrollTime: 600,
		activeClass: 'current',
		onPageChange: null,
		topOffset: -offsetDifference
	});
}


// *** Scroll To Anchor *** //
function scrollToAnchor() {
	var stickyBar = $(".header-bar.sticky"),
		stickyBarHeight = stickyBar.height(),
		offsetDifference = (!stickyBar) ? 0 : stickyBarHeight;

	$(".scroll-to").on("click", function (e) {
		e.preventDefault();
		var $anchor = $(this);

		// scroll to specific anchor
		$("html, body").stop().animate({
			scrollTop: $($anchor.attr("href")).offset().top - offsetDifference
		}, 800 );
	});
}


// *** Slider Image BG *** //
function sliderImageBG() {
	$(".slider-img-bg .slick-slide").each(function () {
		var $this = $(this),
			imgSrc = $this.find(".slide").children("img").attr("src");
		$this.prepend("<div class='bg-element'></div>");
		var bgElement = $this.find("> .bg-element");
		bgElement.css("background-image", "url('" + imgSrc + "')");
	});
}


// *** Optimize Slider Image BG *** //
function optimizeSliderImageBG() {
	$(".slider-img-bg").each(function () {
		var imgHeight = $(this).closest("div").height();

		if ($(".banner-parallax").children(".banner-slider").length > 0) {
			// $( ".banner-parallax, .banner-parallax .row > [class*='col-']" ).height( $( ".banner-slider" ).height() );
		}

		$(this).find(".owl-item > li .slide").children("img").css({
			"display": "none",
			"height": imgHeight,
			"opacity": 0
		});
	});
}


// Custom banner height
$(".banner-parallax").each(function () {
	var customBannerHeight = $(this).data("banner-height"),
		boxContent = $(this).find(".row > [class*='col-']");
	$(this).css("min-height", customBannerHeight);
	$(boxContent).css("min-height", customBannerHeight);
});

// *** Section Parallax Image BG *** //
function sectionParallaxImageBG() {
	$(".section-parallax").each(function () {
		var parallaxSection = $(this),
			imgSrc = parallaxSection.children("img:first-child").attr("src");

		parallaxSection.prepend("<div class='bg-element'></div>");
		var bgElement = parallaxSection.find("> .bg-element");
		bgElement.css("background-image", "url('" + imgSrc + "')").attr("data-stellar-background-ratio", 0.2);
	});
}


// *** Slider Banner *** //
function sliderBanner() {
	var sliderBanner = $('.slider-banner > .slick-slider');
	sliderBanner.slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		dots: true,
		infinite: false,
		rtl: slickDirection,
		arrows: false,
		touchThreshold: 20
	});
}


// *** Slider Services 2 *** //
function sliderServices2() {
	var sliderServices2 = $('.slider-services-2 > .slick-slider');
	sliderServices2.slick({
		slidesToShow: 3,
		slidesToScroll: 1,
		dots: true,
		infinite: false,
		rtl: slickDirection,
		arrows: false,
		touchThreshold: 20,
		responsive: [
			{
				breakpoint: 1200,
				settings: {
					slidesToShow: 2
				}
			},
			{
				breakpoint: 600,
				settings: {
					slidesToShow: 1
				}
			}
		]
	});
}



// *** Slider Clients *** //
function sliderClients() {
	var sliderClients = $('.slider-clients > .slick-slider');
	sliderClients.slick({
		slidesToShow: 6,
		slidesToScroll: 1,
		dots: false,
		infinite: true,
		rtl: slickDirection,
		arrows: false,
		touchThreshold: 20,
		responsive: [
			{
				breakpoint: 1200,
				settings: {
					slidesToShow: 5
				}
			},
			{
				breakpoint: 992,
				settings: {
					slidesToShow: 4
				}
			},
			{
				breakpoint: 768,
				settings: {
					slidesToShow: 3
				}
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 2
				}
			}
		]
	});
}


// *** Slider Testimonials *** //
function sliderTestimonials() {
	var sliderTestimonials = $('.slider-testimonials > .slick-slider');
	sliderTestimonials.slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		dots: false,
		infinite: false,
		rtl: slickDirection,
		arrows: true,
		touchThreshold: 20,
		appendArrows: '.slick-arrows',
		prevArrow: '<a href="javascript:;" class="slick-prev"><i class="fas fa-arrow-down"></i></a>',
		nextArrow: '<a href="javascript:;" class="slick-next"><i class="fas fa-arrow-up"></i></a>'
	});
}


// *** Slider Projects *** //
function sliderProjects() {
	var sliderProjects = $('.slider-projects > .slick-slider');
	sliderProjects.slick({
		slidesToShow: 4,
		slidesToScroll: 1,
		dots: true,
		infinite: false,
		rtl: slickDirection,
		arrows: false,
		touchThreshold: 20,
		responsive: [
			{
				breakpoint: 1400,
				settings: {
					slidesToShow: 3
				}
			},
			{
				breakpoint: 1200,
				settings: {
					slidesToShow: 2
				}
			},
			{
				breakpoint: 992,
				settings: {
					slidesToShow: 2
				}
			},
			{
				breakpoint: 768,
				settings: {
					slidesToShow: 1
				}
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 1
				}
			}
		]
	});
}


// *** Slider Blog Posts *** //
function sliderBlogPosts() {
	var sliderBlogPosts = $('.slider-blog-posts > .slick-slider');
	sliderBlogPosts.slick({
		slidesToShow: 3,
		slidesToScroll: 1,
		dots: false,
		infinite: false,
		rtl: slickDirection,
		arrows: false,
		touchThreshold: 20,
		responsive: [
			{
				breakpoint: 1400,
				settings: {
					slidesToShow: 3
				}
			},
			{
				breakpoint: 1200,
				settings: {
					slidesToShow: 2
				}
			},
			{
				breakpoint: 992,
				settings: {
					slidesToShow: 2
				}
			},
			{
				breakpoint: 768,
				settings: {
					slidesToShow: 1
				}
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 1
				}
			}
		]
	});
}


// *** Menu Main *** //
function menuMain() {
	// Firing Superfish plugin
	$(".menu-main").superfish({
		popUpSelector: "ul",
		cssArrows: true,
		delay: 0,
		speed: 150,
		speedOut: 150,
		animation: {
			opacity: "show",
			marginTop: 0
		}, //  , height : "show"
		animationOut: {
			opacity: "hide",
			marginTop: 20
		}
	});
}


// *** Mobile Menu Side Panel *** //
function mobileMenuSidePanel() {
	$("body").append("<div class='popup-preview-overlay'>");

	$(".popup-preview-overlay").on("click", function (e) {
		e.preventDefault();
		$(".popup-preview-overlay").toggleClass("viewed");
		$(".side-panel-menu").removeClass("viewed");
		$(".menu-mobile-btn").find(".hamburger").toggleClass("is-active");
		$("html").toggleClass("scroll-lock");
	});
}


// *** Mobile Menu *** //
function mobileMenu() {
	// Cloning Main Menu to Mobile Menu
	$("#menu-main").children().clone().appendTo("#menu-mobile");

	// console.log( $( "#menu-mobile-wrap" ).outerHeight() );

	$(".menu-mobile a").each(function (e) {
		if ($(this).next(".sub-menu").length) {
			// $( this ).addClass( "ddddddd" );
			$(this).closest("li").addClass("has-ul");
		}
	})

	$(".menu-mobile a").on("click", function (e) {
		var $this = $(this);
		if ($this.next(".sub-menu").length) {
			e.preventDefault();
			if ($this.next().hasClass("viewed")) {
				$this.next().removeClass("viewed");
				$this.parent().find(".active").removeClass("active")
				$this.next().slideUp(250);
			} else {
				$this.parent().parent().find(".active").removeClass("active");
				$this.parent("ul").find(".active").removeClass("active")
				$this.parent().parent().find("li .sub-menu").removeClass("viewed");
				$this.parent().parent().find("li .sub-menu").slideUp(250);
				$this.toggleClass("active");
				$this.next().toggleClass("viewed");
				$this.next().slideToggle(250);
			}
		}
	});

	// Toggle Mobile Menu
	$(".menu-mobile-btn").on("click", function (e) {
		e.preventDefault();
		$(this).find(".hamburger").toggleClass("is-active");
		$("#menu-mobile-wrap").stop().slideToggle(200);
	});

	$(".menu-mobile-btn").on("click", function (e) {
		e.preventDefault();
		$(".side-panel-menu").addClass("viewed");
		$(".popup-preview-overlay").addClass("viewed");
		$html.addClass("scroll-lock");
	});
}


// *** Sticky Nav *** //
function stickyHeaderBar() {
	var windowScroll = $(window).scrollTop(),
		headerBar = $(".header-bar");

	headerBar.each(function () {
		var $this = $(this);

		if ($this.hasClass("sticky")) {
			if (windowScroll > $this.offset().top) {
				$this.addClass("is-sticky");
				// logo.attr( "src" , logoSrc );
			} else {
				$this.removeClass("is-sticky");
			}
		}
	});
}


// *** Scroll To *** //
$(".scroll-to").on("click", function (e) {
	e.preventDefault();
	var $anchor = $(this);

	// scroll to specific anchor
	$("html, body").stop().animate({
		scrollTop: $($anchor.attr("href")).offset().top
	}, 1200);
});


// *** Website Loading *** //
function websiteLoading() {
	$("#website-loading").find(".loader, .logo-loader").delay(1500).fadeOut(250);
	$("#website-loading").delay(2000).fadeOut(300);
}




