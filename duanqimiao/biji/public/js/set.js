/**
 * Created by dell on 2016/9/4.
 */
jQuery( document ).ready(function( $ ) {
    $('.modify_a').click(function(){
        $('.modify_div').toggle();
    }) ;
    $('.modify_pass').click(function(){
        $.ajax({
            type: "POST",
            url: "/secure/password",
            data: {
                '_token':$('input[name=_token]').val(),
                '_method':$('input[name=_method]').val(),
                'old_pass':$('input[name=old_pass]').val(),
                'new_pass':$('input[name=new_pass]').val(),
                'confirm_new_pass':$('input[name=confirm_new_pass]').val()
            },
            success:function(data){
                if(data){
                    alert(data.info)
                }
            }
        }) ;
    });

});