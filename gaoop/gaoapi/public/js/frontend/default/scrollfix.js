/*! https://github.com/kujian/scrollfix
//http://caibaojian.com/scrollfix
*/
(function ($) {
        $.fn.scrollFix = function (options) {
            return this.each(function () {
                var opts = $.extend({}, $.fn.scrollFix.defaultOptions, options);
                var obj = $(this), base = this, selfTop = 0, selfLeft = 0, toTop = 0, parentOffsetLeft = 0,
                    parentOffsetTop = 0, outerHeight, outerWidth, objWidth = 0, placeholder = jQuery('<div>'),
                    optsTop = opts.distanceTop, endfix = 0;
                var originalPosition;
                var originalOffsetTop;
                var originalZIndex;
                var lastOffsetLeft = -1;
                var isUnfixed = true;
                if (obj.length <= 0) {
                    return;
                }
                if (lastOffsetLeft == -1) {
                    originalZIndex = obj.css('z-index');
                    position = obj.css('position');
                    originalPosition = obj.css('position');
                    originalOffsetTop = obj.css('top');
                }
                var zIndex = obj.css('zIndex');
                if (opts.zIndex != 0) {
                    zIndex = opts.zIndex;
                }
                var parents = obj.parent();
                var Position = parents.css('position');
                while (!/^relative|absolute$/i.test(Position)) {
                    parents = parents.parent();
                    Position = parents.css('position');
                    if (/^body|html$/i.test(parents[0].tagName))
                        break;
                }
                var ie6 = !-[1,] && !window.XMLHttpRequest;
                var resizeWindow = false;

                function resetScroll() {
                    setUnfixed();
                    selfTop = obj.offset().top;
                    selfLeft = obj.offset().left;
                    outerHeight = obj.outerHeight();
                    outerHeight = parseFloat(outerHeight) + parseFloat(obj.css('marginBottom').replace(/auto/, 0));
                    outerWidth = obj.outerWidth();
                    objWidth = obj.width();
                    var documentHeight = $(document).height();
                    var startTop = $(opts.startTop), startBottom = $(opts.startBottom), toBottom, ScrollHeight;
                    if (/^body|html$/i.test(parents[0].tagName)) {
                        parentOffsetTop = 0,
                            parentOffsetLeft = 0;
                    } else {
                        parentOffsetLeft = parents.offset().left,
                            parentOffsetTop = parents.offset().top;
                    }
                    var bodyToTop = parseInt(jQuery('body').css('top'), 10);
                    if (!isNaN(bodyToTop)) {
                        optsTop += bodyToTop;
                    }
                    if (!isNaN(opts.endPos)) {
                        toBottom = opts.endPos;
                    } else {
                        toBottom = parseFloat(documentHeight - $(opts.endPos).offset().top);
                    }
                    ScrollHeight = parseFloat(documentHeight - toBottom - optsTop),
                        endfix = parseFloat(ScrollHeight - outerHeight);
                    if (startTop[0]) {
                        var startTopOffset = startTop.offset()
                            , startTopPos = startTopOffset.top;
                        selfTop = startTopPos;
                    }
                    if (startBottom[0]) {
                        var startBottomOffset = startBottom.offset()
                            , startBottomPos = startBottomOffset.top
                            , startBottomHeight = startBottom.outerHeight();
                        selfTop = parseFloat(startBottomPos + startBottomHeight);
                    }
                    toTop = selfTop - optsTop;
                    toTop = (toTop > 0) ? toTop : 0;
                    var selfBottom = documentHeight - selfTop - outerHeight;
                    if ((toBottom != 0) && (selfBottom <= toBottom)) {
                        return;
                    }
                }

                function setUnfixed() {
                    if (!isUnfixed) {
                        lastOffsetLeft = -1;
                        placeholder.css("display", "none");
                        obj.css({
                            'z-index': originalZIndex,
                            'width': '',
                            'position': originalPosition,
                            'left': '',
                            'top': originalOffsetTop,
                            'margin-left': ''
                        });
                        obj.removeClass('scrollfixed');
                        isUnfixed = true;
                    }
                }

                function onScroll() {
                    lastOffsetLeft = 1;
                    var ScrollTop = $(window).scrollTop();
                    if (opts.bottom != -1) {
                        ScrollTop = ScrollTop + $(window).height() - outerHeight - opts.bottom;
                    }
                    if (ScrollTop > toTop && (ScrollTop < endfix)) {
                        if (ie6) {
                            obj.addClass(opts.baseClassName).css({
                                "z-index": zIndex,
                                "position": "absolute",
                                "top": opts.bottom == -1 ? ScrollTop + optsTop - parentOffsetTop : ScrollTop - parentOffsetTop,
                                "bottom": 'auto',
                                "left": selfLeft - parentOffsetLeft,
                                'width': objWidth
                            })
                        } else {
                            obj.addClass(opts.baseClassName).css({
                                "z-index": zIndex,
                                "position": "fixed",
                                "top": opts.bottom == -1 ? optsTop : '',
                                "bottom": opts.bottom == -1 ? '' : opts.bottom,
                                "left": selfLeft,
                                "width": objWidth
                            });
                        }
                        placeholder.css({
                            'height': outerHeight,
                            'width': outerWidth,
                            'display': 'block'
                        }).insertBefore(obj);
                    } else if (ScrollTop >= endfix) {
                        obj.addClass(opts.baseClassName).css({
                            "z-index": zIndex,
                            "position": "fixed",
                            "top": parentOffsetTop + optsTop,
                            'bottom': optsTop,
                            "left": selfLeft - parentOffsetLeft,
                            "width": objWidth
                        });
                        placeholder.css({
                            'height': outerHeight,
                            'width': outerWidth,
                            'display': 'block'
                        }).insertBefore(obj)
                    } else {
                        obj.removeClass(opts.baseClassName).css({
                            "z-index": originalZIndex,
                            "position": "static",
                            "top": "",
                            "bottom": "",
                            "left": ""
                        });
                        placeholder.remove()
                    }
                }

                var Timer = 0;
                resetScroll();
                $(window).on("scroll", function () {
                    if (Timer) {
                        clearTimeout(Timer);
                    }
                    Timer = setTimeout(onScroll, 0);
                });
                $(window).on("resize", function () {
                    if (Timer) {
                        clearTimeout(Timer);
                    }
                    Timer = setTimeout(function () {
                        isUnfixed = false;
                        resetScroll();
                        onScroll();
                    }, 0);
                });
            })
        }
        $.fn.scrollFix.defaultOptions = {
            startTop: null,
            startBottom: null,
            distanceTop: 0,
            endPos: 0,
            bottom: -1,
            zIndex: 0,
            baseClassName: 'scrollfixed'
        };
    }
)(jQuery);
