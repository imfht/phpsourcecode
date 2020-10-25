/**
 * Created by Administrator on 2016/11/30 0030.
 */
jQuery( document ).ready(function( $ ) {
    $(document).on('click','.yes',function(){
        $.ajax({
            type: "GET",
            url:"/guide/yes/"+$('input[name = articleId]').val(),
            success:function(data){
               if(data.help){
                   var d = dialog({
                       title: '提示',
                       content: '已提交',
                       width: 220
                   });
                   d.show();
                   setTimeout(function () {
                       d.close().remove();
                   }, 3000);
                   var html = parseInt($("#count").text()) +1;
                   $("#count").html(html);
               }else{
                   var d = dialog({
                       title: '提示',
                       content: '不能重复提交',
                       width: 220
                   });
                   d.show();
                   setTimeout(function () {
                       d.close().remove();
                   }, 3000);
               }
            }
        });
    });

    $(document).on('click','.no',function(){
        $.ajax({
            type: "GET",
            url:"/guide/no/"+$('input[name = articleId]').val(),
            success:function(data) {
                if (data.help) {
                    var d = dialog({
                        title: '提示',
                        content: '已提交',
                        width: 220
                    });
                    d.show();
                    setTimeout(function () {
                        d.close().remove();
                    }, 3000);
                }else{
                    var d = dialog({
                        title: '提示',
                        content: '不能重复提交',
                        width: 220
                    });
                    d.show();
                    setTimeout(function () {
                        d.close().remove();
                    }, 3000);
                }

            }
        });
    });
});
