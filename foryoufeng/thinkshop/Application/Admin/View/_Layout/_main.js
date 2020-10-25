$(function() {
    //一次性初始化所有弹出框
    $('[data-toggle="popover"]').popover();

    //切换左侧菜单
    var result = window.matchMedia("(min-width: 768px)");
    if (result.matches) {
        $('.full-container').addClass($.cookie('sidebar_title_hide'));
        $('body').on('click', '#sidebar-toggle', function() {
            if($.cookie('sidebar_title_hide') == 'title-hide'){
                $('.full-container').removeClass('title-hide');
                $.cookie('sidebar_title_hide', null, {path: '/'});
            }else{
                $('.full-container').addClass('title-hide');
                $.cookie('sidebar_title_hide', 'title-hide', {path: '/'});
            }
        });
    }
});
