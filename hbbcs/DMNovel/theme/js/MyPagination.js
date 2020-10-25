(function ($) {
    $.fn.extend({
        MyPagination: function (options) {

            var options = $.extend({
                width: 400,
                height: '400px',
                fadeSpeed: 200,
                fontSize: '20px',
                lineHeight: '30px',
                cookieid: '',
                cookieurl: '/'
            }, options || {});

            var $content = $(this);

            var lastPage;
            var cPage = (options.cookieid && $.cookie(options.cookieid)) ? parseInt($.cookie(options.cookieid)) : 1;

            setContent = function () {
                $content.addClass('mycontent');
                $content.css("font-size", options.fontSize);
                $content.css("line-height", options.lineHeight);

                if ($content.find('img').length>0) {
                    $content.find('img').each(function (e) {
                        var img=$(this);
                        if (img[0].offsetHeight > options.height) {
                            img.css('height', options.height)
                        } else if (img[0].offsetWidth > options.width) {
                            img.css('width', options.width-30)
                        }
                    });
                }
                lastPage = Math.ceil($content.outerHeight() / (options.height - 30));
                $content.height(options.height);
                $content.css("column-width", options.width + 'px');
                $content.css("-moz-column-width", options.width + 'px');
                $content.css("-webkit-column-width", options.width + 'px');

            };


            showPage = function (page) {
                if (page < 1) {
                    window.location.href = $('#prev_url').attr('href');
                    return;
                }
                if (page > lastPage) {
                    window.location.href = $('#next_url').attr('href');
                    return;
                }

                cPage = page;

                if (options.cookieid) {
                    $.cookie(options.cookieid, page, {expires: 7, path: options.cookieurl});
                }

                var scrollLeft = (page - 1) * (options.width + 20);

                $content.animate({
                    scrollLeft: scrollLeft
                }, options.fadeSpeed);

                $("#cPage").html(page + '/' + lastPage);
            };

            setContent();
            showPage(cPage);

            $('#prev').mousedown(function () {
                showPage(cPage - 1);
            });

            $(document).keyup(function (e) {
                var key = e.which;
                if (key === 37 || key === 38) {
                    showPage(cPage - 1);
                } else if (key === 39 || key === 40) {
                    showPage(cPage + 1);
                }
            });

            $('.chapter').bind("mousewheel DOMMouseScroll", function (event) {
                var delta = (event.wheelDelta) ? event.wheelDelta / 120 : -(event.detail || 0) / 3;
                showPage(cPage - delta);
            });

            // and Next
            $('#next').mousedown(function () {
                showPage(cPage + 1);
            });

        }
    });
})(jQuery);


