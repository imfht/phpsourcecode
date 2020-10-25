$(function(){
    //Cro.loginUCheck();
    //
    Cro.pageInit();
    $('.app_btn').click(function(){
        var title = $(this).text();
        app.task($(this),title);        
    });
    // 变换主题
    $('#themeSelectOption').change(function(){
        var value = $('#themeSelectOption option:selected').val();
        var href
        if(value){
            href = location.pathname + '?theme='+value;
        }
        else href = location.pathname;
        location.href = href;
    });
});
var Cro = new Conero();
app.navDb = Cro.storage().table("conero_nav");
// 自动显示登录页面
Cro.loginUCheck = function(){
    var ulogin = Cro.getJsVar('ulogin');
    if(ulogin == 'N'){
        var openLogin = function(){
            app.task('login',{'url':'/conero/index/login','title':'您还没有登录系统，请注册或者登录'});
            Cro.uWin('app_login').post('bind_request');
            document.getElementById('app_login').contentWindow.postMessage('ssss','http://127.0.0.1/conero');
        };
        window.setTimeout(openLogin,300);
    }
}
Cro.pageInit = function(){
    // app 刷新自动恢复应用状态
    app.recover();
    app.autoWin();
}
// 动态事件绑定
$(document).on('click','.task_btn',function(){
    var dataid = app.dataid($(this));
    var id = 'cro_page_'+dataid;
    if($('#'+id).css('display') == 'none'){
        app.task_bar('reset');
    }
    else app.task_bar('reset',dataid);
    $('#'+id).toggle();
});