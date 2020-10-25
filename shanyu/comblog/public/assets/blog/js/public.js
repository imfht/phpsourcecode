//栏目高亮
$(function(){
    function getNavName()
    {
        var navUrl = window.location.pathname;
        var reg=new RegExp("(/[a-z]*)[-|\/]{0,1}[0-9]{0,}[\.html]{0,1}",'g');
        var res=reg.exec(navUrl);
        return res[1];
    }

    var navName = getNavName();
    var $navA = $('#navList a');
    if(navName == '/' || navName == '/index'){
        $navA.first().addClass('current');
    }else{
        $.each($navA,function(i,n){
            var href=$(this).attr('href');
            if(href.indexOf(navName) != -1){
                $(this).addClass('current');
            }
        });
    }
});
/*
i   执行对大小写不敏感的匹配。
g   执行全局匹配（查找所有匹配而非在找到第一个匹配后停止）。
m   执行多行匹配。
*/

//返回顶部
$(window).scroll(function() {
    if ($(window).scrollTop() >= 200) {
        $("#top").fadeIn(500)
    } else {
        $("#top").fadeOut(500)
    }
});
$("#rocket").click(function() {
    $("#rocket").addClass("launch");
    $("html, body").animate({
        scrollTop: 0
    },1500,
    function() {
        $("#rocket").removeClass("launch")
    });
    return false
});