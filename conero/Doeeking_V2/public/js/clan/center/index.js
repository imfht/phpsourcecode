$(function(){app.pageInit();});
var Cro = new Conero();
var app = Cro.extends(function(th){
    var selfObj = this;
    this.pageInit = function(){
        // 菜单点击
        $('#menu_list > li > a').click(function(){
            // th.modal_alert('sssss');
            var dom = $(this);
            var dataid = dom.attr("dataid");
            var id = dataid+'_cro';
            var wins = $('#frame_wins');
            var href = dom.attr('dataurl');
            if($('#'+id).length > 0){
                wins.find('[dataclass="win"]').hide();
                $('#'+id).show();
            }
            else{                
                var xhtml = '<div class="embed-responsive embed-responsive-16by9" dataclass="win" id="'+id+'">'
                    + ' <iframe class="embed-responsive-item" src="'+href+'"></iframe>'
                    + ' </div>'
                    ;
                $('#frame_wins').append(xhtml);
                wins.find('[dataclass="win"]').hide();
                $('#'+id).show();
            }
            var appHrefLink = $('#app_href_link > a');
            appHrefLink.attr("href",href);
            appHrefLink.attr("target","_blank");
            appHrefLink.text(dom.text());
            $('#menu_list li').removeClass('active');
            dom.parents('li').addClass('active');
            location.href = location.pathname + '#'+id;
        });
        // 首页点击
        $('a.herf_link').click(function(){
            var wins = $('#frame_wins');
            wins.find('[dataclass="win"]').hide();
            var id = $(this).attr("dataid");
            id = id + '_cro';
            $('#'+id).show();
            location.href = location.pathname + '#'+id;
            $('#menu_list > li.active').removeClass('active');
            var aEl = $('#app_href_link > a');
            var href = $('#home_cro > iframe').attr("src");
            aEl.attr("href",href);
            aEl.attr("target",'_blank');
            aEl.text('首页');
        });
        // 子页面刷新
        $('#reload_chldwin_link').click(function(){
            var id = selfObj.getCurrentMenu();
            id = id + '_cro';
            // th.modal_alert(id);
            if(!th.empty(id)){
                var win = $('#'+id);
                var frame = win.find('iframe');
                var app = frame.get(0).window || frame.get(0).contentWindow;
                app.location.reload(true);                
            }
        });
        this.autoMenuByHash();
    }
    // 获取当前的菜单名称
    this.getCurrentMenu = function(){
        var nav = $('#menu_list');
        var dataid = nav.find('li.active > a').attr('dataid');
        dataid = th.empty(dataid)? 'home':dataid;
        return dataid;
    }
    // 根据锚点自适应 - 页面
    this.autoMenuByHash = function(){
        var hash = location.hash;
        if(!th.empty(hash) && hash != 'home_cro'){
            var winEl = $(hash);
            if(winEl.length == 0){
                var id = hash.replace('#','');
                var dataid = id.replace('_cro','');
                var aEl = $('#menu_list > li > a[dataid="'+dataid+'"]');
                var url = aEl.attr("dataurl");
                var xhtml = '<div class="embed-responsive embed-responsive-16by9" dataclass="win" id="'+id+'">'
                    + ' <iframe class="embed-responsive-item" src="'+url+'"></iframe>'
                    + ' </div>'
                    ;
                $('#frame_wins').append(xhtml);
                aEl.parents('li').addClass('active');
                $('#frame_wins > div[dataclass="win"]').hide();
                $(hash).show();
                var navAEl = $('#app_href_link > a');
                navAEl.attr("href",url);
                navAEl.attr("target",'_blank');
                navAEl.text(aEl.text());
            }
        }
    }
});