
$(function(){

    $('#app-tab-content').on('click','.layui-tab-close',function(){
        closeApp($(this).parent().parent());
        return false;
    })

    $('#app-tab-content').on('dblclick','li',function(){
        closeApp($(this));
        return false;
    })

    $('#app-tab-content').on('click','li',function(){
        openPage($(this).attr('app-url'), $(this).attr('app-id'), $(this).attr('app-name'));
        return false;
    })

    $('#refresh-btn').on('click',function(){
        var $curFrame = $('#frame_content iframe:visible');
        $curFrame.attr("src",$curFrame.attr("src"));
        return false;
    })

})

var $appframe_tpl = '<iframe src="" frameborder="0" class="appframe" style="width: 100%; height: 98%;"></iframe>';
var $apptab_tpl = '<li class="layui-nav-item"><a href="#"></a></li>';
var $closetab_tpl = '&nbsp;&nbsp;<i class="layui-icon layui-tab-close">ဆ</i>';

function closeApp($this){

    if(!$this.hasClass('noclose')){
        $this.prev().click();
        $this.remove();
        var $appid = $this.attr('app-id');
        $('#app-id-'+$appid).remove();
    }

}
function openPage(url, appId, appname, refresh){

    $('#app-tab-content .layui-this').removeClass('layui-this');

    var $tab = $("#app-tab-content li[app-id='"+appId+"']");
    $('.appframe').hide();

    if($tab.length == 0){ //没有tab

        $appframe = $($appframe_tpl).attr('src',url).attr('id',"app-id-"+appId);
        $appframe.appendTo('#frame_content');

        $appframe.load(function () {
            var srcLoaded = $appframe.get(0).contentWindow.location.href;
            if (srcLoaded.indexOf('admin/public/login') >= 0) {
                window.location.reload(true);
            }
        });

        addAppTab(url,appId,appname);
    }else{  //已存在tab
        $tab.addClass('layui-this');
        $appframe = $('#app-id-'+appId).show();
        if(refresh){
            $appframe.attr('src',url);
            $appframe.load(function () {
                var srcLoaded = $appframe.get(0).contentWindow.location.href;
                if (srcLoaded.indexOf('admin/public/login') >= 0) {
                    window.location.reload(true);
                }
            });
        }
    }
    //删除layui自行创建的bar
    $('.layui-nav-bar').remove();

}

function addAppTab(url, appId, appname){

    var tab = $($apptab_tpl).attr('app-id',appId).attr('app-url',url).attr('app-name',appname).addClass('layui-this');
    tab.find('a').html(appname+$closetab_tpl);
    tab.appendTo('#app-tab-content');

}
