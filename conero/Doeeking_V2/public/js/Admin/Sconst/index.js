$(function(){
    app.pageInit();     
});
var Cro = new Conero();
var auth = null;
var app = Cro.extends(function(th){
     this.pageInit = function(){
        // 父类加载时间
        th.uWin().pWin(function(win,req){
            var url = win.location.pathname;
            if(url == '/conero/admin.html'){ // iframe 模式
                //;
                $('.independence').hide();
            }
            // 非 iframe 引入模式
            else $('#sconst').attr('class','container');
        });
    }
});