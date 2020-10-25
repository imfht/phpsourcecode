var addTabs = function (options) {
    var url = window.location.protocol + '//' + window.location.host;
    options.url = url + options.url;
    var id = "tab_" + options.id;
    var active_flag = false;
    if($("#" + id).length){
        active_flag = $("#" + id).hasClass('active');
    }
    $("div[id^='tab_'],li[id^='tab_']").removeClass("active");
    //如果TAB不存在，创建一个新的TAB
    if (!$("#" + id).length) {
        //固定TAB中IFRAME高度
        var mainHeight = $(document.body).height() - 90;
        //创建新TAB的title
        var title = '<li role="presentation" id="tab_' + id + '"><a href="#' + id + '" aria-controls="' + id + '" role="tab" data-toggle="tab"><i class="'+options.icon+'"></i>' + options.title;
        //是否允许关闭
        if (options.close) {
            title += ' <i class="fa fa-close" tabclose="' + id + '"></i>';
        }
        title += '</a></li>';
        //是否指定TAB内容
        if (options.content) {
            content = '<div role="tabpanel" class="tab-pane" id="' + id + '">' + options.content + '</div>';
        } else {//没有内容，使用IFRAME打开链接
            content = '<div role="tabpanel" class="tab-pane" id="' + id + '"><iframe src="' + options.url + '" width="100%" height="' + mainHeight +
                '" frameborder="no" border="0" onload="changeFrameHeight(this)" style="margin-top: -20px" marginwidth="0" marginheight="0" allowtransparency="yes"></iframe></div>';
        }
        //加入TABS
        $(".content-nav-tabs").append(title);
        $(".content-tab-content").append(content);
        //调整middletab的ul的left
        var tabsWidth = $(".nav-my-tab .middletab").width();
        var totalWidth=0;
        $.each($(".content-nav-tabs li"),function(key, item){
            totalWidth+= $(item).width();
        });
        //如果总宽度>tab宽度,则向左移动
        if(totalWidth>tabsWidth){
            $(".middletab .content-nav-tabs").css('left',(tabsWidth-totalWidth))
        }
    }else{
        if(active_flag){
            $("#iframe_" + id).attr('src', $("#iframe_" + id).attr('src'));
        }
        //调整middletab的ul的left
        tabsWidth = $(".nav-my-tab .middletab").width();
        totalWidth=0;
        $.each($(".content-nav-tabs li"),function(key, item){
            totalWidth+= $(item).width();
            //跳出
            if($(item).attr('id')=='tab_'+id){
                return false;
            }
        });
        if(totalWidth>tabsWidth){
            $(".middletab .content-nav-tabs").css('left',(tabsWidth-totalWidth))
        }else{
            $(".middletab .content-nav-tabs").css('left',0)
        }
    }
    //激活TAB
    $("#tab_" + id).addClass('active');
    $("#" + id).addClass("active");
    //nav-left变更class
    var new_active = $("#"+options.id);
    $('.nav-list li.active').removeClass('active');
    new_active.addClass('active').parents('.nav-list li').addClass('active');
    //console.log(new_active);
};
var changeFrameHeight = function (that) {
    //console.log(document.documentElement.clientHeight);
    $(that).height(document.documentElement.clientHeight - 215);
    $(that).parent(".tab-pane").height(document.documentElement.clientHeight - 215);
};
var closeTab = function (id) {
    //如果关闭的是当前激活的TAB，激活他的前一个TAB
    if ($(".content-nav-tabs li.active").attr('id') == "tab_" + id) {
        $("#tab_" + id).prev().addClass('active');
        $("#" + id).prev().addClass('active');
    }
    //关闭TAB
    $("#tab_" + id).remove();
    $("#" + id).remove();
    //调整middletab的ul的left
    var tabsWidth = $(".nav-my-tab .middletab").width();
    var totalWidth=0;
    $.each($(".content-nav-tabs li"),function(key, item){
        totalWidth+= $(item).width();
        //跳出
        if($(item).attr('id')=='tab_'+$(".content-nav-tabs li.active").attr('id')){
            return false;
        }
    });
    if(totalWidth>tabsWidth){
        $(".middletab .content-nav-tabs").css('left',(tabsWidth-totalWidth))
    }else{
        $(".middletab .content-nav-tabs").css('left',0)
    }
};
window.onresize = function () {
    var target = $(".content-tab-content .active iframe");
    changeFrameHeight(target);
};
$(function () {
    var mainHeight = $(document.body).height() - 45;
    $('.main-left,.main-right').height(mainHeight);
    $("[addtabs]").click(function () {
        addTabs({ id: $(this).attr("id"), title: $(this).attr('title'), close: true });
    });

    $(".content-nav-tabs").on("click", "[tabclose]", function (e) {
        var id = $(this).attr("tabclose");
        closeTab(id);
        return false;
    }).on("click", "li[id^='tab_']", function (e) {
        if(!$(this).hasClass('active')){
            var id=$(this).attr('id').substr(4);
            $('#'+id).addClass('active').siblings().removeClass('active');
            $(this).addClass('active').siblings().removeClass('active');
            return false;
        }
    });
    $(function () {
        //tab页向左向右移动
        var middletab=$('.nav-my-tab .middletab .content-nav-tabs');
        $('.nav-my-tab .leftbackward').click(function(){
            var strLeft=middletab.css('left');
            var iLeft = parseInt(strLeft.replace('px', ''));
            if(iLeft>=0){
                return;
            }
            else{
                //debugger;
                //console.log(iLeft);
                var totalWidth=0;
                var lis = $(".content-nav-tabs li");
                for(var i=0;i<lis.length;i++){
                    var item = lis[i];
                    totalWidth-= $(item).width();
                    if(iLeft>totalWidth){
                        iLeft+=$(item).width();
                        break;
                    }
                }
                if(iLeft>0){
                    iLeft=0;
                }
                $(".nav-my-tab .middletab .content-nav-tabs").animate({left:iLeft + 'px'});
            }
        });
        $('.nav-my-tab .rightforward').click(function(){
            var strLeft=middletab.css('left');
            var iLeft = parseInt(strLeft.replace('px', ''));
            var totalWidth=0;
            $.each($(".content-nav-tabs li"),function(key, item){
                totalWidth+= $(item).width();
            });
            var tabsWidth = $(".nav-my-tab .middletab").width();
            if(totalWidth>tabsWidth){
                //debugger;
                if(totalWidth-tabsWidth<=Math.abs(iLeft)){
                    return;
                }
                var lis = $(".content-nav-tabs li");
                totalWidth=0;
                for(var i=0;i<lis.length;i++){
                    var item = lis[i];
                    totalWidth-= $(item).width();
                    if(iLeft>totalWidth){
                        iLeft-=$(item).width();
                        break;
                    }
                }
                $(".nav-my-tab .middletab .content-nav-tabs").animate({left:iLeft + 'px'});
            }

        });
    });
});