/**
 * Created by dell on 2016/7/13.
 */
jQuery( document ).ready(function( $ ) {
    $('#auser').click(function () {
        $('#user').toggle();
    });

    $('#user_img').mouseover(function(){
        $('#user_img').addClass('animated rotateIn');
        setTimeout(function(){
            $('#user_img').removeClass('rotateIn');
        },3000);
    });
});
