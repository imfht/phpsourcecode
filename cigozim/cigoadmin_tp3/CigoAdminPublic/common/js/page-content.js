$(function () {
    $('.title_tool_bar').each(function () {
        $(this).prev('.title_tool_bar_bg').css('height', $(this).outerHeight(true));
    });
});