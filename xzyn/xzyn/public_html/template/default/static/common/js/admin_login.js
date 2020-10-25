/**
 * login
 */
$(function(){
    //登陆
    $('body').off('click', '.login');
    $('body').on("click", '.login', function(event){
        var _this = $(this);
        _this.button('loading');
        var form = _this.closest('form');
        if(form.length){
            var ajax_option={
                dataType:'json',
                success:function(data){
                    if(data.status == '1'){
                        layer.msg(data.info,{offset:'100px'});
                        var url = data.url;
                        window.location.href=url;
                    }else{
                        layer.msg(data.info,{offset:'100px'});
                        $('#code').click();
                        _this.button('reset');
                    }
                }
            }
            form.ajaxSubmit(ajax_option);
        }
    });

})