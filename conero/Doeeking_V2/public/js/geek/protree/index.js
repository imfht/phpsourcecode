$(function(){
    app.pageInit();
    // 项目内容
    $('.log_detail_link').click(function(){
        $.post('/conero/geek/protree/ajax.html',{item:'logs_detail',dataid:$(this).attr('dataid')},function(html){
            $('#log_detail_div').html(html);
            $('#log_detail_div').removeClass('hidden');
        });
    });    
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    this.pageInit = function(){
        th.uWin().pWin(function(win,req){
            var url = win.location.pathname;
            if(url == '/conero/geek/project.html'){ // iframe 模式
                //;
                $('#geek_public_navbar').hide();
            }
            // 非 iframe 引入模式
            else $('#projecttree').attr('class','container');
        });
    }
});