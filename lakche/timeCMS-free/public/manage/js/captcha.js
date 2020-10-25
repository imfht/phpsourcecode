//验证码刷新
$('.captcha').on('click',function(){$.ajax({
        type: 'GET',
        url: "/auth/captcha",
        success: function (data) {
            if(data.error == 0){
                $('.captcha img').attr('src',data.captcha);
            }
        },
        error: function (data) {
            alert(data.message);
        }
    });
});