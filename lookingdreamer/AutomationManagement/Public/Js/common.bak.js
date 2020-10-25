/**
 +----------------------------------------------------------
 * 内容轮播
 +----------------------------------------------------------
 */
jQuery.ThinkAutoSlide = function (slideID, speed) {
    var $slide = $(slideID),
        $list = $slide.children('.slide_box'),
        count = $list.length,
        _html = '',
        index = 0,
        cTime, $finger;

    // 只有内容列表有两个或以上时才会产生轮播
    if (count >= 2) {
        $list.css('display', 'none').eq(0).show();

        for (var i = 0; i < count; i++) {
            _html += '<a href="javascript:;" title="">' + i + '</a>';
        }
        $slide.append('<div id="slide_finger">' + _html + '</div>');

        $finger = $('#slide_finger').children('a');
        $finger.eq(0).addClass('on').end()
            .each(function (n) {
                $(this).mouseover(function () {
                    $(this).addClass('on').siblings().removeClass('on');
                    //$list.hide('slow').eq(n).show('slow'); // 动画效果一
                    $list.fadeOut(600).eq(n).fadeIn(600); // 动画效果二
                    //$list.hide().eq(n).show(); // 动画效果三
                    index = n;
                });
            });

        $list.hover(
            function () {
                clearInterval(cTime);
            },
            function () {
                clearInterval(cTime);
                cTime = setInterval(function () {
                    index = index + 1;
                    if (index > count) {
                        index = 0;
                    }
                    $finger.eq(index).mouseover();
                }, speed);
            }).trigger("mouseleave");
    }
};

/**
 +----------------------------------------------------------
 * 友情链接滚动
 * 只有超过8个才会动
 +----------------------------------------------------------
 */
jQuery.ThinkAutoScroll = function (ID, speed) {
    var $ul = $('.scroll_list>ul', ID),
        cTime;

    if ($ul.children('li').length > 8) {
        cTime = setInterval(toScroll, speed);

        $(ID).hover(
            function () {
                clearInterval(cTime);
            }, function () {
                cTime = setInterval(toScroll, speed);
            }
        );

        $('.scroll_left', ID).bind('click', function () {
            toScroll(1);
        });
        $('.scroll_right', ID).bind('click', function () {
            toScroll();
        });
    }

    function toScroll(isLeft) {
        if (isLeft) {
            $ul.animate({left: '-98px'}, {duration: 200, complete: function () {
                    $ul.append($ul.find('li:first'));
                    $ul.css('left', '0');
                }}
            );
        } else {
            $ul.animate({left: 0}, {duration: 200, complete: function () {
                    $ul.prepend($ul.find('li:last'));
                    $ul.css("left", '-98px');
                }}
            );
        }
    }
};


$(document).ready(function () {

    /* 背景渐变修正 如果其它页面会有可能整体高度会很小时请去掉下面两行注释 */
    //var $content = $('#tp_content');
    //if ($content.height()< 700){$content.height(700);}

    // 内容轮播
    $.ThinkAutoSlide('#tp_slide', 3000);

    // 特性，学习，案例，新闻等链接动画效果
    //$('#tp_index_box').children('div.index_box_div').each(function() {
    //$(this).hover(
    //function(){$(this).children('p').fadeIn(1000);},
    //function(){$(this).children('p').hide(300);}
    //);
    //});

    // 友情链接滚动
    $.ThinkAutoScroll('#tp_scroll_box', 2000);
});