/**
 * Created by dell on 2016/9/7.
 */
jQuery( document ).ready(function( $ ) {
    $('.upload').click(function () {
        $.ajax({
            success: function (data) {
                if(data.info){
                    alert(data.info);
                }
            }
        });
    });
});