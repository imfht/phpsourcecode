jQuery( document ).ready(function( $ ) {
    $('.sign').click(function () {
        $.ajax({
            type: "GET",
            url: "/sign/",
            success: function (data) {

                $('.sign').val("已签到");
                if(data.sign){
                }else{
                    /*alert("不能重复签到！");*/
                    var d = dialog({
                     title: '提示',
                     content: '不能重复签到！',
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