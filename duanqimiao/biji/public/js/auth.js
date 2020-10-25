/**
 * Created by Administrator on 2016/11/29 0029.
 */
jQuery( document ).ready(function( $ ) {
    $(document).on('blur','#email-input', function () {
        $.ajax({
            type:"GET",
            url:"thumb",
            data:{
               'email':$('input[name=email]').val()
            },
            success:function(data){
                $.each(data.thumb,function(index,values){
                    if(values == ""){
                        $('.thumb').attr('src','../images/photo.jpg');
                    }else{
                        $('.thumb').attr('src',values);
                    }
                });
            }
        });
    });
});
