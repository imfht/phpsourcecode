$(function(){
    // 菜单
    $('.app_nav').click(function(){
        /*
        $('nav.main_nav').find('a.active').removeClass('active');
        $(this).addClass('active');
        var dataid = Cro.dataid($(this));
        if(Cro.empty(dataid)) return;
        var moduleId = Cro.strLastValue(dataid);
        // 锚点区分保存不同的页面，点击后会生成
        var newHref = '/conero/index/appsh/app/finance#'+moduleId;
        Cro.log(moduleId);
        dataid = dataid+(dataid.indexOf('?') == -1? '?mode=IFR':'&mode=IFR');
        var win = '<iframe src="'+dataid+'"></iframe>'; 
        $('#app_win').html(win);      
        location.href = newHref;
        */
        Cro.appCreate($(this));  
    });
    Cro.pageInit();
});
var Cro = new Conero();
// 自动撑高
Cro.pageInit = function(){
    var height = document.documentElement.clientHeight - 10;
    $('#app_win').css({'height':height});
}
// app 生成器
Cro.appCreate = function(dom){
    // tab 变换
    $('nav.main_nav').find('a.active').removeClass('active');
    dom.addClass('active');

    var dataid = Cro.dataid(dom);
    if(Cro.empty(dataid)) return;
    var iframeId = 'sh_'+Cro.strLastValue(dataid);
    iframeId = iframeId.toLowerCase();
    iframeId = iframeId.replace('.html','');
    // 存在时
    var pageDance = $('#'+iframeId);
    $('#app_win').find('iframe.show').removeClass('show');
    if(pageDance.length>0){        
        pageDance.addClass('show');
        return;
    }
    // 锚点区分保存不同的页面，点击后会生成
    var newHref = '/conero/index/appsh/app/'+this.getJsVar('_app_')+'#'+iframeId;
    // 页面模式-iframe/app
    dataid = dataid+(dataid.indexOf('?') == -1? '?mode=IFR':'&mode=IFR');
    var win = '<iframe src="'+dataid+'" id="'+iframeId+'" class="pagedance show"></iframe>'; 
    $('#app_win').append(win);      
    location.href = newHref;  
}