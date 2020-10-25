$(function(){
    app.pageInit();
});
var Cro = new Conero();
app = Cro.extends(function(th){
    this.pageInit = function(){
        // 父类加载时间
        th.uWin().pWin(function(win,req){
            var url = win.location.pathname;
            // iframe 模式
            if(url != location.pathname){
                $('.independence').hide();
            }
            // 非 iframe 引入模式
            else $('#genNode').attr('class','container');
        });
        // 删除确认
        $('.dellink').click(function(){
            var url = $(this).attr("href");
            th.confirm('您确定要删除数据吗?',function(){
                location.href = url;
            });
            return false;
        });
    }
});

