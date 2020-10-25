//////////////////////////////////////////////////////////////////////////////////
// Cloud Zoom V1.0.2.5
// (c) 2010 by R Cecco. <http://www.professorcloud.com>
// with enhancements by Philipp Andreas <https://github.com/smurfy/cloud-zoom>
//
// MIT License
//
// Please retain this copyright header in all versions of the software
//////////////////////////////////////////////////////////////////////////////////
(function ($) {

    $(document).ready(function () {
        $('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();
    });
	
	function getWidthBrowser() {
		var myWidth = 0;
	
		if( typeof( window.innerWidth ) == 'number' ) { 
			//Non-IE 
			myWidth = window.innerWidth;
			//myHeight = window.innerHeight; 
		} 
		else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) { 
			//IE 6+ in 'standards compliant mode' 
			myWidth = document.documentElement.clientWidth; 
			//myHeight = document.documentElement.clientHeight; 
		} 
		else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) { 
			//IE 4 compatible 
			myWidth = document.body.clientWidth; 
			//myHeight = document.body.clientHeight; 
		}
		
		return myWidth;
	}
	
	var window_width=getWidthBrowser();   
	
    function format(str) {
        for (var i = 1; i < arguments.length; i++) {
            str = str.replace('%' + (i - 1), arguments[i]);
        }
        return str;
    }

    function CloudZoom(jWin, opts) {
    	
    	 
    	//alert(window_width);
        var sImg = $('img', jWin);
        var	img1;
        var	img2;
        var zoomDiv = null;
        var	$mouseTrap = null;
        var	lens = null;
        var	$tint = null;
        var	softFocus = null;
        var	$ie6Fix = null;
        var	zoomImage;
        var controlTimer = 0;
        var cw, ch;
        var destU = 0;
        var	destV = 0;
        var currV = 0;
        var currU = 0;
        var filesLoaded = 0;
        var mx,
            my;
        var ctx = this, zw;
        // Display an image loading message. This message gets deleted when the images have loaded and the zoom init function is called.
        // We add a small delay before the message is displayed to avoid the message flicking on then off again virtually immediately if the
        // images load really fast, e.g. from the cache.
        //var	ctx = this;
        setTimeout(function () {
            //						 <img src="/images/loading.gif"/>
            if ($mouseTrap === null) {
                var w = jWin.width();
                jWin.parent().append(format('<div style="width:%0px;position:absolute;top:75%;left:%1px;text-align:center" class="cloud-zoom-loading" >Loading...</div>', w / 3, (w / 2) - (w / 6))).find(':last').css('opacity', 0.5);
            }
        }, 200);


        var ie6FixRemove = function () {

            if ($ie6Fix !== null) {
                $ie6Fix.remove();
                $ie6Fix = null;
            }
        };

        // Removes cursor, tint layer, blur layer etc.
        this.removeBits = function () {
            //$mouseTrap.unbind();
            if (lens) {
                lens.remove();
                lens = null;
            }
            if ($tint) {
                $tint.remove();
                $tint = null;
            }
            if (softFocus) {
                softFocus.remove();
                softFocus = null;
            }
            ie6FixRemove();

            $('.cloud-zoom-loading', jWin.parent()).remove();
        };


        this.destroy = function () {
            jWin.data('zoom', null);

            if ($mouseTrap) {
                $mouseTrap.unbind();
                $mouseTrap.remove();
                $mouseTrap = null;
            }
            if (zoomDiv) {
                zoomDiv.remove();
                zoomDiv = null;
            }
            //ie6FixRemove();
            this.removeBits();
            // DON'T FORGET TO REMOVE JQUERY 'DATA' VALUES
        };


        // This is called when the zoom window has faded out so it can be removed.
        this.fadedOut = function () {

            if (zoomDiv) {
                zoomDiv.remove();
                zoomDiv = null;
            }
            this.removeBits();
            //ie6FixRemove();
        };

        this.controlLoop = function () {
            if (lens) {
                var x = (mx - sImg.offset().left - (cw * 0.5)) >> 0;
                var y = (my - sImg.offset().top - (ch * 0.5)) >> 0;

                if (x < 0) {
                    x = 0;
                }
                else if (x > (sImg.outerWidth() - cw)) {
                    x = (sImg.outerWidth() - cw);
                }
                if (y < 0) {
                    y = 0;
                }
                else if (y > (sImg.outerHeight() - ch)) {
                    y = (sImg.outerHeight() - ch);
                }

                lens.css({
                    left: x,
                    top: y
                });
                lens.css('background-position', (-x) + 'px ' + (-y) + 'px');

                destU = (((x) / sImg.outerWidth()) * zoomImage.width) >> 0;
                destV = (((y) / sImg.outerHeight()) * zoomImage.height) >> 0;
                currU += (destU - currU) / opts.smoothMove;
                currV += (destV - currV) / opts.smoothMove;

                zoomDiv.css('background-position', (-(currU >> 0) + 'px ') + (-(currV >> 0) + 'px'));
            }
            controlTimer = setTimeout(function () {
                ctx.controlLoop();
            }, 30);
        };

        this.init2 = function (img, id) {

            filesLoaded++;
            //console.log(img.src + ' ' + id + ' ' + img.width);
            if (id === 1) {
                zoomImage = img;
            }
            //this.images[id] = img;
            if (filesLoaded === 2) {
                this.init();
            }
        };

        /* Init function start.  */
        this.init = function () {
            // Remove loading message (if present);
            $('.cloud-zoom-loading', jWin.parent()).remove();


            /* Add a box (mouseTrap) over the small image to trap mouse events.
             It has priority over zoom window to avoid issues with inner zoom.
             We need the dummy background image as IE does not trap mouse events on
             transparent parts of a div.
             */
            $mouseTrap = jWin.parent().append(format("<div class='mousetrap' style='background-image:url(\""+ opts.transparentImage +"\");z-index:999;position:absolute;width:%0px;height:%1px;left:%2px;top:%3px;\'></div>", sImg.outerWidth(), sImg.outerHeight(), 0, 0)).find(':last');

            //////////////////////////////////////////////////////////////////////
            /* Do as little as possible in mousemove event to prevent slowdown. */
            $mouseTrap.bind('mousemove', this, function (event) {
                // Just update the mouse position
                mx = event.pageX;
                my = event.pageY;
            });
            //////////////////////////////////////////////////////////////////////
            $mouseTrap.bind('mouseleave', this, function (event) {
                clearTimeout(controlTimer);
                //event.data.removeBits();
                if(lens) { lens.fadeOut(299); }
                if($tint) { $tint.fadeOut(299); }
                if(softFocus) { softFocus.fadeOut(299); }
                if(window_width>767){	
	                zoomDiv.fadeOut(300, function () {
	                    ctx.fadedOut();
	                });
                }
                return false;
            });
            //////////////////////////////////////////////////////////////////////
            $mouseTrap.bind('mouseenter', this, function (event) {
                mx = event.pageX;
                my = event.pageY;
                zw = event.data;
                if (zoomDiv) {
                    zoomDiv.stop(true, false);
                    zoomDiv.remove();
                }

                var xPos = opts.adjustX,
                    yPos = opts.adjustY;

                var siw = sImg.outerWidth();
                var sih = sImg.outerHeight();

                var w = opts.zoomWidth;
                var h = opts.zoomHeight;
                if (opts.zoomWidth == 'auto') {
                    w = siw;
                }
                if (opts.zoomHeight == 'auto') {
                    h = sih;
                }
                //$('#info').text( xPos + ' ' + yPos + ' ' + siw + ' ' + sih );
                var appendTo = jWin.parent(); // attach to the wrapper
                switch (opts.position) {
                    case 'top':
                        yPos -= h; // + opts.adjustY;
                        break;
                    case 'right':
                        xPos += siw; // + opts.adjustX;
                        break;
                    case 'bottom':
                        yPos += sih; // + opts.adjustY;
                        break;
                    case 'left':
                        xPos -= w; // + opts.adjustX;
                        break;
                    case 'inside':
                        w = siw;
                        h = sih;
                        break;
                    // All other values, try and find an id in the dom to attach to.
                    default:
                        appendTo = $('#' + opts.position);
                        // If dom element doesn't exit, just use 'right' position as default.
                        if (!appendTo.length) {
                            appendTo = jWin;
                            xPos += siw; //+ opts.adjustX;
                            yPos += sih; // + opts.adjustY;
                        } else {
                            w = appendTo.innerWidth();
                            h = appendTo.innerHeight();
                        }
                }
				if(window_width>767){			
                	zoomDiv = appendTo.append(format('<div id="cloud-zoom-big" class="cloud-zoom-big" style="display:none;position:absolute;left:%0px;top:%1px;width:%2px;height:%3px;background-image:url(\'%4\');z-index:99;"></div>', xPos, yPos, w, h, zoomImage.src)).find(':last');
				}
                // Add the title from title tag.
                if (sImg.attr('title') && opts.showTitle) {
                    zoomDiv.append(format('<div class="cloud-zoom-title">%0</div>', sImg.attr('title'))).find(':last').css('opacity', opts.titleOpacity);
                }

                // Fix ie6 select elements wrong z-index bug. Placing an iFrame over the select element solves the issue...
                var browserCheck = /(msie) ([\w.]+)/.exec( navigator.userAgent );
                if (browserCheck) {
                    if ((browserCheck[1] || "") == 'msie' && (browserCheck[2] || "0" ) < 7) {
                        $ie6Fix = $('<iframe frameborder="0" src="#"></iframe>').css({
                            position: "absolute",
                            left: xPos,
                            top: yPos,
                            zIndex: 99,
                            width: w,
                            height: h
                        }).insertBefore(zoomDiv);
                    }
                }
				if(window_width>767){	
                	zoomDiv.fadeIn(500);
				
                if (lens) {
                    lens.remove();
                    lens = null;
                } /* Work out size of cursor */
                cw = (sImg.outerWidth() / zoomImage.width) * zoomDiv.width();
                ch = (sImg.outerHeight() / zoomImage.height) * zoomDiv.height();

                // Attach mouse, initially invisible to prevent first frame glitch
                lens = jWin.append(format("<div class = 'cloud-zoom-lens' style='display:none;z-index:98;position:absolute;width:%0px;height:%1px;'></div>", cw, ch)).find(':last');

                $mouseTrap.css('cursor', lens.css('cursor'));

                var noTrans = false;

                // Init tint layer if needed. (Not relevant if using inside mode)
                if (opts.tint) {
                    lens.css('background', 'url("' + sImg.attr('src') + '")');
                    $tint = jWin.append(format('<div style="display:none;position:absolute; left:0px; top:0px; width:%0px; height:%1px; background-color:%2;" />', sImg.outerWidth(), sImg.outerHeight(), opts.tint)).find(':last');
                    $tint.css('opacity', opts.tintOpacity);
                    noTrans = true;
                    $tint.fadeIn(500);

                }
                if (opts.softFocus) {
                    lens.css('background', 'url("' + sImg.attr('src') + '")');
                    softFocus = jWin.append(format('<div style="position:absolute;display:none;top:2px; left:2px; width:%0px; height:%1px;" />', sImg.outerWidth() - 2, sImg.outerHeight() - 2, opts.tint)).find(':last');
                    softFocus.css('background', 'url("' + sImg.attr('src') + '")');
                    softFocus.css('opacity', 0.5);
                    noTrans = true;
                    softFocus.fadeIn(500);
                }

                if (!noTrans) {
                    lens.css('opacity', opts.lensOpacity);
                }
                if ( opts.position !== 'inside' ) { lens.fadeIn(500); }

                // Start processing.
                zw.controlLoop();
				}
                return; // Don't return false here otherwise opera will not detect change of the mouse pointer type.
            });
        };

        img1 = new Image();
        $(img1).load(function () {
            ctx.init2(this, 0);
        });
        img1.src = sImg.attr('src');

        img2 = new Image();
        $(img2).load(function () {
            ctx.init2(this, 1);
        });
        img2.src = jWin.attr('href');
    }

    $.fn.CloudZoom = function (options) {
        // IE6 background image flicker fix
        try {
            document.execCommand("BackgroundImageCache", false, true);
        } catch (e) {}
        this.each(function () {
            var	relOpts, opts;
            // Hmm...eval...slap on wrist.
            eval('var	a = {' + $(this).attr('rel') + '}');
            relOpts = a;
            if ($(this).is('.cloud-zoom')) {
                opts = $.extend({}, $.fn.CloudZoom.defaults, options);
                opts = $.extend({}, opts, relOpts);

                $(this).css({
                    'position': 'relative',
                    'display': 'block'
                });
                $('img', $(this)).css({
                    'display': 'block'
                });


                // Wrap an outer div around the link so we can attach things without them becoming part of the link.
                // But not if wrap already exists.
                if (!$(this).parent().hasClass('cloud-zoom-wrap') && opts.useWrapper) {
                    $(this).wrap('<div class="cloud-zoom-wrap"></div>');
                }
                $(this).data('zoom', new CloudZoom($(this), opts));

            } else if ($(this).is('.cloud-zoom-gallery')) {
                opts = $.extend({}, relOpts, options);
                $(this).data('relOpts', opts);
                $(this).bind('click', $(this), function (event) {
                    var data = event.data.data('relOpts');
                    // Destroy the previous zoom
                    $('#' + data.useZoom).data('zoom').destroy();
                    // Change the biglink to point to the new big image.
                    $('#' + data.useZoom).attr('href', event.data.attr('href'));
                    // Change the small image to point to the new small image.
                    $('#' + data.useZoom + ' img').attr('src', event.data.data('relOpts').smallImage);
                    // Init a new zoom with the new images.
                    $('#' + event.data.data('relOpts').useZoom).CloudZoom();
                    return false;
                });
            }
        });
        return this;
    };

    $.fn.CloudZoom.defaults = {
        zoomWidth: 'auto',
        zoomHeight: 'auto',
        position: 'right',
        transparentImage: '.',
        useWrapper: true,
        tint: false,
        tintOpacity: 0.5,
        lensOpacity: 0.5,
        softFocus: false,
        smoothMove: 3,
        showTitle: true,
        titleOpacity: 0.5,
        adjustX: 0,
        adjustY: 0
    };

})(jQuery);
jQuery(function ($) {
    "use strict";
    var $mainContainer = $(".container"),
        $section = $(".products-list"),
        $links = $(".quick-view:not(.fancybox)"),
        $view = $(".product-view-ajax"),
        $container = $(".product-view-container", $view),
        $loader = $(".ajax-loader", $view),
        $layar = $(".layar", $view),
        $slider;
    var initProductView = function ($productView) {
        var $slider = $(".flexslider-large", $productView),
            $nav = $(".flexslider-thumb", $productView),
            $navvertical = $(".flexslider-thumb-vertical", $productView),
            $close = $(".close-view", $productView);
        if ($productView && $productView.length) $.initSelect($productView.find(".btn-select"));
        $navvertical.each(function () {
            var jcarousetItemsNumber = $(this).find("ul li").size();
            if (jcarousetItemsNumber > 3) {
                $(this).flexVSlider({
                    animation: "slide",
                    direction: "vertical",
                    move: 3,
                    keyboard: false,
                    controlNav: false,
                    animationLoop: false,
                    slideshow: false,
                    prevText: "",
                    nextText: ""
                })
            }
        })
        $nav.each(function () {
            var jcarousetItemsNumber = $(this).find("ul li").size();
            if (jcarousetItemsNumber > 3) {
                $(this).flexslider({
                    animation: "slide",
                    keyboard: false,
                    controlNav: false,
                    animationLoop: false,
                    slideshow: false,
                    prevText: "",
                    nextText: "",
                    itemWidth: 72,
                    itemMargin: 7
                })
            }
        })
        $slider.flexslider({
            animation: "slide",
            keyboard: false,
            controlNav: true,
            directionNav: true,
            animationLoop: false,
            slideshow: false,
            prevText: "",
            nextText: ""
        });
        $close.click(function (e) {
            e.preventDefault();
            $container.slideUp(500, function () {
                $container.empty();
                $view.hide();
                $container.show()
            })
        })
    };
    $links.click(function (e) {
        if ($(".hidden-xs").is(":visible")) {
            e.preventDefault();
            var $this = $(this),
                url = $this.attr("href");
            if ($this.closest(".product-carousel").length > 0) $this.closest(".row").find(".product-view-ajax-container").first().append($view);
            else $this.parent().parent().nextAll(".product-view-ajax-container").first().append($view);
            $view.show();
            $layar.show();
            $loader.show();
            $.ajax({
                url: url,
                cache: false,
                success: function (data) {
                    var $data = $(data);
                    initProductView($data);
                    $loader.hide();
                    $layar.hide();
                    if (!$container.text()) {
                        $data.hide();
                        $container.empty().append($data);
                        $data.slideDown(500)
                    } else $container.empty().append($data)
                },
                complete: function () {
                    if ($(".various").length > 0) $(".various").fancybox({
                        maxWidth: 800,
                        maxHeight: 600,
                        fitToView: false,
                        width: "70%",
                        height: "70%",
                        autoSize: false,
                        closeClick: false,
                        openEffect: "none",
                        closeEffect: "none"
                    });
                    console.log("ajax complete");
                    CloudZoom.quickStart()
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $loader.hide();
                    $container.html(textStatus)
                }
            })
        }
    });
    initProductView();
    var productCarousel = $(".product-carousel"),
        container = $(".container");
    if (productCarousel.length > 0) productCarousel.each(function () {
        var items = 4,
            itemsDesktop = 4,
            itemsDesktopSmall = 3,
            itemsTablet = 2,
            itemsMobile = 1;

        if ($("body").hasClass("noresponsive")) {

            var items = 4,
                itemsDesktop = 4,
                itemsDesktopSmall = 4,
                itemsTablet = 4,
                itemsMobile = 4;
            if ($(this).closest("section.col-md-8.col-lg-9").length > 0) var items = 3,
                itemsDesktop = 3,
                itemsDesktopSmall = 3,
                itemsTablet = 3,
                itemsMobile = 3;
            else if ($(this).closest("section.col-lg-9").length > 0) var items = 3,
                itemsDesktop = 3,
                itemsDesktopSmall = 3,
                itemsTablet = 3,
                itemsMobile = 3;
            else if ($(this).closest("section.col-sm-12.col-lg-6").length > 0) var items = 2,
                itemsDesktop = 2,
                itemsDesktopSmall = 2,
                itemsTablet = 2,
                itemsMobile = 2;
            else if ($(this).closest("section.col-lg-6").length > 0) var items = 2,
                itemsDesktop = 2,
                itemsDesktopSmall = 2,
                itemsTablet = 2,
                itemsMobile = 12;
            else if ($(this).closest("section.col-sm-12.col-lg-3").length > 0) var items = 1,
                itemsDesktop = 1,
                itemsDesktopSmall = 1,
                itemsTablet = 1,
                itemsMobile = 1;
            else if ($(this).closest("section.col-lg-3").length > 0) var items = 1,
                itemsDesktop = 1,
                itemsDesktopSmall = 1,
                itemsTablet = 1,
                itemsMobile = 1;

        } else if ($(this).closest("section.col-md-8.col-lg-9").length > 0) var items = 3,
            itemsDesktop = 3,
            itemsDesktopSmall = 2,
            itemsTablet = 2,
            itemsMobile = 1;
        else if ($(this).closest("section.col-lg-9").length > 0) {var items = 3,
            itemsDesktop = 3,
            itemsDesktopSmall = 2,
            itemsTablet = 2,
            itemsMobile = 1;}
        else if ($(this).closest("section.col-sm-12.col-lg-6").length > 0) var items = 2,
            itemsDesktop = 2,
            itemsDesktopSmall = 3,
            itemsTablet = 2,
            itemsMobile = 1;
        else if ($(this).closest("section.col-lg-6").length > 0) var items = 2,
            itemsDesktop = 2,
            itemsDesktopSmall = 2,
            itemsTablet = 2,
            itemsMobile = 1;
        else if ($(this).closest("section.col-sm-12.col-lg-3").length > 0) var items = 1,
            itemsDesktop = 1,
            itemsDesktopSmall = 3,
            itemsTablet = 2,
            itemsMobile = 1;
        else if ($(this).closest("section.col-lg-3").length > 0) var items = 1,
            itemsDesktop = 1,
            itemsDesktopSmall = 2,
            itemsTablet = 2,
            itemsMobile = 1;
        $(this).owlCarousel({
            items: items,
            itemsDesktop: [1199, itemsDesktop],
            itemsDesktopSmall: [980, itemsDesktopSmall],
            itemsTablet: [768, itemsTablet],
            itemsTabletSmall: false,
            itemsMobile: [360, itemsMobile],
            navigation: true,
            pagination: false,
            rewindNav: false,
            navigationText: ["", ""],
            scrollPerPage: true,
            slideSpeed: 500,
            beforeInit: function rtlSwapItems(el) {
                if ($("body").hasClass("rtl")) el.children().each(function (i, e) {
                    $(e).parent().prepend($(e))
                })
            },
            afterInit: function afterInit(el) {
                if ($("body").hasClass("rtl")) this.jumpTo(1000)
            }
        })
    });
    var productsListSmall = $(".products-list-small .slides");
    if (productsListSmall.length > 0) {
        var items = 12,
            itemsDesktop = 12,
            itemsDesktopSmall = 8,
            itemsTablet = 6,
            itemsMobile = 3;
        if ($("body").hasClass("noresponsive")) var items = 12,
            itemsDesktop = 12,
            itemsDesktopSmall = 12,
            itemsTablet = 12,
            itemsMobile = 12;
        productsListSmall.owlCarousel({
            items: items,
            itemsDesktop: [1199, itemsDesktop],
            itemsDesktopSmall: [980, itemsDesktopSmall],
            itemsTablet: [768, itemsTablet],
            itemsTabletSmall: false,
            itemsMobile: [360, itemsMobile],
            navigation: true,
            pagination: false,
            rewindNav: false,
            navigationText: ["", ""],
            scrollPerPage: true,
            slideSpeed: 500,
            beforeInit: function rtlSwapItems(el) {
                if ($("body").hasClass("rtl")) el.children().each(function (i, e) {
                    $(e).parent().prepend($(e))
                })
            },
            afterInit: function afterInit(el) {
                if ($("body").hasClass("rtl")) this.jumpTo(1000)
            }
        })
    }

    var brandsCarousel = $(".brands-carousel ul");
	var brandsCarouselMax = 6;
	if ($(".content-center .brands-carousel ul").length > 0) {  brandsCarouselMax = 4 }
	
	if (brandsCarousel.length > 0){
	        brandsCarousel.carouFredSel({
					responsive: true,
					width: '100%',
					scroll: 1,
					prev: '#brands-carousel-prev',
					next: '#brands-carousel-next',
					items: {
						width: 170,
						height: '30%',	//	optionally resize item-height
						visible: {
							min: 1,
							max: brandsCarouselMax
						}
					}
				});

	}
    var productWidgets = $(".product-widgets");
    if (productWidgets.length > 0) productWidgets.owlCarousel({
        items: 1,
        navigation: true,
        pagination: false,
        rewindNav: false,
        navigationText: ["", ""],
        scrollPerPage: true,
        slideSpeed: 300
    });
    var $contentcenter = $(".content-center"),
        $contentaside = $(".content-aside");
    if ($(".visible-xs").is(":visible")) $contentcenter.insertBefore($contentaside);
    $(window).resize(function () {
        var $contentcenter = $(".content-center"),
            $contentaside = $(".content-aside");
        if ($(".visible-xs").is(":visible")) $contentcenter.insertBefore($contentaside);
        else $contentaside.insertBefore($contentcenter)
    })
});
