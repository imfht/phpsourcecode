'use strict';
var registerTimeout = 0;

jQuery(function($) {
    var app = $('#APP').val();
    $('#dxrz').bind('click', function() {
        $('#hideForm').addClass('bounceInUp').show();
        return false;
    });
    

    $('#getCode').bind('click', function() {
        $('#msg').html('请输入手机号码获取验证码').css('color', '#000');
        var phone = $('#phone').val();
        var reg = /^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/;
        if(!reg.test($.trim(phone))){
            $('#msg').html('您输入的手机号码不正确').css('color', 'red');
            return false;
        }
        
        var url = app+'/Index/send_sms';
        var is_mobile_verify = $('#is_mobile_verify').val();
        if (is_mobile_verify == 0){
            url = app+'/Index/virtual_send_sms';
        }
        $(this).button('loading');
        $.ajax({ 
            url: url,  
            data:{'phonenumber':phone},
            dataType:'json',
            type:'POST',
            success: function(data){
                $('#getCode').button('reset');
                $('.am-modal-bd').html();
                $('#your-modal').modal();
                if (data.ret == 1){
                    $('#msg').html(data.msg).css('color', '#000');
                    registerTimeout = 60;
                    $(this).attr('disabled', true);
                    doClick();
                }else{
                    $('#msg').html(data.msg).css('color', 'red');
                }
            }
        });
        
    });
    $('#check_verify').bind('click', function(){
        $('#msg').html('请输入手机号码获取验证码').css('color', '#000');
        var verify = $('#verify').val();
        var phone = $('#phone').val();
        var is_mobile_verify = $('#is_mobile_verify').val();
        var reg = /^1[3|4|5|8][0-9]\d{4,8}$/;

        if(!reg.test($.trim(phone))){
            $('#msg').html('您输入的手机号码不正确').css('color', 'red');
            return false;
        }
        if (verify == ''){
            $('#msg').html('验证码不能为空').css('color', 'red');
            return false;
        }
        $(this).button('loading');
        $.ajax({ 
            url: app+'/Index/mobile_verify',  
            data:{'phonenumber':phone, 'verifycode':verify, 'type':is_mobile_verify},
            dataType:'json',
            type:'POST',
            success: function(data){
                $('#check_verify').button('reset');
                if (data.ret == 1){
                    location.href = data.url;
                }else{
                    $('#msg').html(data.msg).css('color', 'red');
                }
            }
        });
    })
    //滑下效果
    $('#top').animate({top:'0px'}, 1000);
    $('#main').animate({bottom:'0px'}, 1000);
});

//倒计时
function doClick(){
    if(registerTimeout == 0){
        $('#getCode').attr('disabled', false).text('获取验证码');
    }else{
        registerTimeout--;
        $('#getCode').text(registerTimeout + '秒之后重新获取');
        setTimeout("doClick()",1000);
    }
}