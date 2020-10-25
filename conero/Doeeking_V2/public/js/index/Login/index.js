$(function(){
    window.addEventListener("message",function(e){
        //alert(e.data);
    });
    // 变换验证码
    $('#change_img').click(function(){
        Cro.imageCreate();
    });
    // 验证码 输入框
    $('#iptimg').blur(function(){
        $('#img_tip').html('');
        var value = $(this).val();
        if(value){
            $.post('/conero/index/login/ajax',{'item':'index/testimg','code':value},function(data){
                if(data == 'Y') $('#img_tip').html('<b>√</b>');
                else $('#img_tip').html('<b>X</b>');
            });
        }
    });
    // submit
    $('#login_submit').click(function(){
        var value = $('#iptimg').val();
        var dom = $(this);
        if(value){
            $.post('/conero/index/login/ajax',{'item':'index/testimg','code':value},function(data){
                if(data == 'Y'){
                    //dom.submit();
                    $('#start_login_auth').submit();
                }
            });
        }
    });
    Cro.imageCreate();
});
var Cro = new Conero();
Cro.imageCreate = function(){
    /*
    var imgId = this.getJsVar('imgId');
    var src = '/conero/index/common/captcha?id='+imgId+'&emma='+Math.random();
    var img = $('#img_base_change');        
    img.attr("src",src);
    */
    $('#change_img').html('<img src="/conero/captcha.html?'+Math.random()+'" alt="captcha" />'); 
}
/*
Cro.uWin().response(function(event){
    console.log(event.data);
});
window.addEventListener("message",function(e){
    alert(e.data);
});
*/