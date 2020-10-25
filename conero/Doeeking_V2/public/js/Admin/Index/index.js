$(function(){
    //  页面跳转 - 页面 tab
    $('.herf_link').click(function(){
        var href = $(this).attr("dataid");
        var url = $(this).attr("dataurl");
        var id = href+'_cro';
        var win = $('#frame_wins'),
            ol = $(this).parents('ol'),
            li = $(this).parents('li'),
            nav = $('#app_href_link'),
            app = $('#'+id);
        // 现实界面/切换
        win.find('[dataclass="win"]').hide();
        ol.find('li.active').removeClass('active');
        li.attr('class','active');
        // 首页不做跳转
        if(href != 'home'){
            // var url = '/conero/admin/'+href+'.html';
            // var url = href;
            var navBar = nav.find('a');
            
            // 导航栏变化
            navBar.text($(this).text());
            navBar.attr('href',url);
            navBar.attr("target","_blank");
            if(app.length == 0){
                var html = '<div class="embed-responsive embed-responsive-16by9" id="'+id+'" dataclass="win">'
                        + '<iframe class="embed-responsive-item" src="'+url+'"></iframe>'
                        + '</div>';
                win.append(html);
            }
            // iframe 加载以后
        }
        else{
            admin.clearNavBarActive();
        }
        app.show();
        location.href = location.pathname+"#"+id;
    });    
    // 重新载入当前的子页面
    $('#reload_chldwin_link').click(function(){admin.flushIframe();});
    // 有锚点时自动还原锚点对应的页面
    admin.autoAdjustWin();
});
var Cro = new Conero();
Cro.__APP = function(){
    var __APP__ = function(th){
        // 清除当前激活菜单栏
        this.clearNavBarActive = function(){
            var nav = $('#menu_list');
            nav.find('li.active').removeClass('active');
        }       
        // 获取当前激活的菜单栏
        this.getCurrentMenu = function(){
            var nav = $('#menu_list');
            var dataid = nav.find('li.active').find('a.herf_link').attr('dataid');
            return dataid;
        }
        // 刷新 iframe 框
        this.flushIframe = function(){
            var dataid  = this.getCurrentMenu();
            if(dataid){
                var win = $('#frame_wins');
                var frame = win.find('#'+dataid+'_cro').find('iframe');
                var app = frame.get(0).window || frame.get(0).contentWindow;
                app.location.reload(true);
            }
        }
        // 有锚点时自动还原锚点对应的页面
        this.autoAdjustWin = function(){
            var hash = location.hash;
            if(!th.empty(hash)){
                var wins = $('#frame_wins');
                var cWin = $(hash);
                var id = hash.replace('#','');
                var dataid = id.replace('_cro','');
                if(cWin.length == 0){
                    var menuA = $('#menu_list a[dataid="'+dataid+'"]');
                    var src = menuA.attr("dataurl");
                    this.clearNavBarActive();
                    menuA.parents("li").addClass('active');
                    var xhtml = '<div id="'+id+'" class="embed-responsive embed-responsive-16by9" dataclass="win"><iframe class="embed-responsive-item" src="'+src+'"></div></div>';
                    wins.find('[dataclass="win"]').hide();
                    wins.append(xhtml);
                    $(hash).show();

                    var nav = $('#app_href_link');
                    var navBar = nav.find('a');
                    // 导航栏变化
                    navBar.text(menuA.text());
                    navBar.attr('href',src);
                    navBar.attr("target","_blank");
                }
            }
        }
    };
    return new __APP__(this);
}
var admin = Cro.__APP();