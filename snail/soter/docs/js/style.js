$(function(){
    $('.nav_manual a').on('click',function(){
        $(this).parent().parent().find('li').removeClass('active');
        $(this).parent().addClass('active');
    });
    var offset = 300;
    var duration = 500;
    $(window).scroll(function() {
        if ($(this).scrollTop() > offset) {
            $('#gotoTop').fadeIn(duration);
        } else {
            $('#gotoTop').fadeOut(duration);
        }
    });
    $('#gotoTop').click(function(event) {
        event.preventDefault();
        $('html,body').animate({
            scrollTop: 0
        },
        duration);
        return false;
    });
});

