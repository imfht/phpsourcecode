/*---------------------------------------------------/
/                 Plugins Setup
/---------------------------------------------------*/

var Plugins = function($) {

    'use strict';

    /*-----------------------------------------------------------
        Replace action action for the 'touchend' or 'mouseup' 
    ------------------------------------------------------------*/
    var isTouchDevice = 'ontouchstart' in document.documentElement;
    //set 'touchend' for touch devices - much faster response
    if (isTouchDevice) {
        var action = 'touchend';
    } else {
        var action = 'mouseup';
    }

    /*---------------------------------
        Back to top
    ----------------------------------*/
    function backToTop() {
        var offset = 220,
            duration = 500,
            selector = $('.back-to-top');
        $(window).scroll(function() {
            if ($(this).scrollTop() > offset) {
                selector.fadeIn(duration);
            } else {
                selector.fadeOut(duration);
            }
        });

        selector.on(action, function(event) {
            event.preventDefault();
            $('html, body').animate({
                scrollTop: 0
            }, duration);
            return false;
        });
    }

    /*---------------------------------
        Smooth scroll (from href to the ID)
    ----------------------------------*/
    function scrollTo() {
        $('a.scroll').on(action, function() {
            if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html,body').animate({
                        scrollTop: target.offset().top
                    }, 500);
                    return false;
                }
            }
        });
    }

    /*---------------------------------
        Revolution slider
    ----------------------------------*/
    function revolutionSlider() {
        $('html').removeClass('hideOveflow');
        $(window).resize(revolutionSlider);
    }


    /*---------------------------------
        Dialog Effects
    ----------------------------------*/
    function dialogEffects() {
        (function() {
            [].slice.call(document.querySelectorAll('[data-dialog]')).forEach(function(trigger) {
                var dlg = new DialogFx(document.getElementById(trigger.getAttribute('data-dialog')));
                trigger.addEventListener(action, dlg.toggle.bind(dlg));
            });
        })();

        /* if a dialog window is opened, navbar will be hidden */
        $('.btn.trigger').on(action, function() {
            $('.navbar').addClass('navbar-hide');
            $('html').addClass('hideOveflow');
        });

        $('.btn.action, .dialog__overlay').on(action, function() {
            $('.navbar').removeClass('navbar-hide');
            $('html').removeClass('hideOveflow');
        });
    }


    /*---------------------------------
         Modal Effects
    ----------------------------------*/
    function modalEffects() {
        
        /* if a dialog window is opened, navbar will be hidden */
        $('.md-trigger').on(action, function() {
            $('.navbar').addClass('navbar-hide');
            $('html').addClass('hideOveflow');
        });

        $('.md-close').on(action, function() {
            $('.navbar').removeClass('navbar-hide');
            $('html').removeClass('hideOveflow');
        });
    }

    function parallaxStellar() {
        var iOS = (navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false),
            Android = (navigator.userAgent.match(/(Android)/g) ? true : false),
            BlackBerry = (navigator.userAgent.match(/(BlackBerry)/g) ? true : false),
            Windows = (navigator.userAgent.match(/(IEMobile)/g) ? true : false),
            stellarImg = $('.bg-img').find('img');

        //parallax background is turned off for iOS, Android, BlackBerry, Windows Phone - the plugin doesn't work properly
        if (iOS || Android || BlackBerry || Windows) {
            stellarImg.attr("data-stellar-ratio", 1);
        } else {
            
        }
    }


    return {
        init: function() {
            backToTop();
            scrollTo();
        },
        revolutionSlider: function() {
            revolutionSlider();
        },        
        dialogEffects: function() {
            dialogEffects();
        },
       
        modalEffects: function() {
            modalEffects();
        },
        parallaxStellar: function() {
            parallaxStellar();
        }
    };

}(jQuery);
