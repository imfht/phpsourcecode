//Ê×Ò³»ÃµÆÆ¬
$(function() { (function($) {
        $.fn.Slide = function(options) {
            var defaults = {
                item: "slide-item",
                nav: "slide-nav",
                nowClass: "nownav",
                loading: "slide-loading"
            },
            options = options || {};
            options = $.extend(defaults, options);
            var cont = $(this),
            item = cont.find("." + options.item),
            nav = cont.find("." + options.nav),
            curr = options.nowClass,
            len = item.length,
            width = item.width(),
            html = "",
            index = order = 0,
            timer = null,
            lw = "-" + width + "px",
            rw = width + "px",
            newtimer,
            ld = cont.find("." + options.loading);
            item.each(function(i) {
                $(this).css({
                    left: i === index ? 0 : (i > index ? width + 'px': '-' + width + 'px')
                });
                html += '<a href="javascript:">' + (i + 1) + '</a>';
            });
            $("#slide").hover(function() {
                $('#next').fadeIn();
                $('#prev').fadeIn();
            },
            function() {
                $('#next').fadeOut();
                $('#prev').fadeOut();
            });
            nav.html(html);
            var navitem = nav.find("a");
            navitem.eq(index).addClass(curr);
            function anim(index, dir) {
                loading();
                if (order === len - 1 && dir === 'next') {
                    item.eq(order).stop(true, false).animate({
                        left: lw
                    });
                    item.eq(index).css({
                        left: rw
                    }).stop(true, false).animate({
                        left: 0
                    });
                } else if (order === 0 && dir === 'prev') {
                    item.eq(0).stop(true, false).animate({
                        left: rw
                    });
                    item.eq(index).css({
                        left: lw
                    }).stop(true, false).animate({
                        left: 0
                    });
                } else {
                    item.eq(order).stop(true, false).animate({
                        left: index > order ? lw: rw
                    });
                    item.eq(index).stop(true, false).css({
                        left: index > order ? rw: lw
                    }).animate({
                        left: 0
                    });
                }
                order = index;
                navitem.removeClass(curr).eq(index).addClass(curr);
            }
            function next() {
                index = order >= len - 1 ? 0 : order + 1;
                _stop();
                ld.stop(true, true).animate({
                    "width": 0
                },
                0);
                anim(index, 'next');
                timer = setInterval(next, 5000);
            }
            function prev() {
                index = order <= 0 ? len - 1 : order - 1;
                _stop();
                ld.stop(true, true).animate({
                    "width": 0
                },
                0);
                anim(index, 'prev');
                timer = setInterval(next, 5000);
            }
            function auto() {
                loading();
                timer = setInterval(next, 5000);
            }
            function _stop() {
                clearInterval(timer);
            }
            function loading() {
                ld.css({
                    "height": "0",
                    "height": "5px",
                    "position": "absolute",
                    "left": "0",
                    "bottom": "0",
                    "background": "#ffe825",
                    "z-index": "10"
                });
                ld.animate({
                    "width": "100%"
                },
                5000).animate({
                    "width": 0
                },
                0);
            }
            return this.each(function() {
                auto();
                navitem.hover(function() {
                    _stop();
                    var i = navitem.index(this);
                    if (/nownav/.test($(this).attr('class'))) {
                        return false;
                    }
                    if (newtimer) clearTimeout(newtimer);
                    newtimer = setTimeout(function() {
                        _stop();
                        ld.stop(true, true).animate({
                            "width": 0
                        },
                        0);
                        anim(i, this);
                    },
                    250);
                },
                auto);
                $('#next').on('click', next);
                $('#prev').on('click', prev);
            });
        };
    })(jQuery);
    $("#slide").Slide();
});