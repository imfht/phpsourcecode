$(function(){    
    // 导航栏作用
    $('.blog-nav-item').click(function(){
        var dataid = $(this).attr('dataid');
        if(!Cro.empty(dataid)){
            var nav = $(this).parents('nav');
            nav.find('a.active').removeClass('active');
            $(this).addClass('active');
            $('div.main-top').addClass('hidden');
            $('#'+dataid).removeClass('hidden');
        }
    });
    app.pageInit();
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    this.pageInit = function(){        
        // 父类加载时间
        th.uWin().pWin(function(win,req){
            var url = win.location.pathname;
            if(url == '/conero/finance.html'){ // iframe 模式
                //;
                $('.independence').hide();
            }
            // 非 iframe 引入模式
            /*
            else{
                var div = $('div.container-flud');
                div.removeClass('container-flud');
                div.addClass('container');
            }
            */
        });
        // 通过锚点判断该显示的页面
        var hash = location.hash;
        if(hash){
            hash = hash.replace('#','');
            var nav = $('div.blog-masthead').find('nav.blog-nav');
            var aEl = nav.find('[dataid="'+hash+'"]');
            if(aEl.length > 0) aEl.click();
        }
    }
});