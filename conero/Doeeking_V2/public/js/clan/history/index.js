$(function(){
    app.pageInit();
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    var _self = this;
    var _pid;
    this.pageInit = function()
    {
        // 父类加载时间
        th.uWin().pWin(function(win,req){
            var url = win.location.pathname;
            // iframe 模式
            if(url != location.pathname){
                $('.independence').hide();
            }
            // 非 iframe 引入模式
            else $('#page_main').attr('class','container');
        });
    }
});