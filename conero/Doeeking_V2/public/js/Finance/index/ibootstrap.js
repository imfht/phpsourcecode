$(function(){
    // 首页
    $('#hometo_link').click(function(){
        App.pageToggle();
    });
    // 主菜单
    $('#mainto_link').find('li > a').click(function(){
        App.pageToggle('finance_main');  
        var dataid = $(this).attr('dataid');
        var href = $(this).attr("dataid");
        var id = href+'_cro';
        var win = $('#finance_main'),
            ul = $(this).parents('ul'),
            li = $(this).parents('li'),
            app = $('#'+id);
        // 现实界面/切换
        win.find('[dataclass="win"]').hide();
        ul.find('li.active').removeClass('active');
        li.attr('class','active');
        var url = $(this).attr("dataurl");
        // 导航页面生成先相应的跳转页面
        $('#mainto_thepart_nav').attr("href",url);
        $('#mainto_thepart_nav').attr('target','_blank');
        if(app.length == 0){
            var html = '<div class="embed-responsive embed-responsive-16by9" id="'+id+'" dataclass="win">'
                    + '<iframe class="embed-responsive-item" src="'+url+'"></iframe>'
                    + '</div>';
            win.append(html);
        }
        app.show();
        location.href = location.pathname+"#"+id;
        // <span class="glyphicon glyphicon-plane"></span>
    });
    // 页面跳转至新页面
    $('#mainto_thepart_nav').click(function(){       
        var win = App.getFrame();
        if(win){
            var href = win.location.href;
            window.open(href);
            return false;
        }
        return true;
    });
    // 子页面刷新
    $('#mainto_refresh_nav').click(function(){
        var win = App.getFrame();
        if(win){win.location.reload(true);}
    });
    App.autoAdjustWin();
});
var Cro = new Conero();
Cro.__APP = function(){
    var __APP = function(th){
        // 主页面切换
        this.pageToggle = function(name){
            name = name ? name:'finance_home';
            var tabs = ['finance_home','finance_main'];
            for(var i=0; tabs.length > i; i++){
                $('#'+tabs[i]).hide();
            }
            $('#'+name).show();
            if(name == 'finance_home') this.clearMainActive();
        }
        // 清除主菜单active
        this.clearMainActive = function(){
            var ul = $('#mainto_link');
            ul.find('li.active').removeClass('active');
        }
        // 获取页面的 href -> type: win/href
        this.getFrame = function(type){
            type = type? type:'win';
            var navBar = $('#mainto_link');
            var name = navBar.find('li.active').find('a').attr('dataid');
            name += '_cro';
            var frame = $('#'+name).find('iframe');
            if(frame.length > 0){
                var dom = frame.get(0);
                var win = dom.window || dom.contentWindow;
                if(type == 'win') return win;
                else if(type == 'href') return win.location.href;
                return href;
            }
            return null;
        }
        // 有锚点时自动还原锚点对应的页面
        this.autoAdjustWin = function(){
            var hash = location.hash;
            if(!th.empty(hash)){
                var id = hash.replace('#','');
                var dataid = id.replace('_cro','');

                var ul = $('#mainto_link');
                var aEl = $('#mainto_link a[dataid="'+dataid+'"]');
                var src = aEl.attr("dataurl");
                var win = $('#finance_main');
                var xhtml = '<div id="'+id+'" class="embed-responsive embed-responsive-16by9" dataclass="win"><iframe class="embed-responsive-item" src="'+src+'"></iframe></div>';
                win.append(xhtml);
                ul.find('[dataclass="win"]').hide();
                // $('#finance_home').hide();
                this.pageToggle('finance_main');  
                $(hash).show();
                // 导航
                this.clearMainActive();
                aEl.parents("li").addClass("active");
            }
        }
    }
    return new __APP(this);
}
var App = Cro.__APP();