$(function () {
    // 菜单栏
    $('.nav-item>a').on('click', function () {
        if (!$('.nav').hasClass('nav-mini')) {
            if ($(this).next().css('display') == "none") {
                $('.nav-item').children('ul').slideUp(300);
                $(this).next('ul').slideDown(300);
                $(this).parent('li').addClass('nav-show').siblings('li').removeClass('nav-show');
            } else {
                $(this).next('ul').slideUp(300);
                $('.nav-item.nav-show').removeClass('nav-show');
            }
        }
    });
});