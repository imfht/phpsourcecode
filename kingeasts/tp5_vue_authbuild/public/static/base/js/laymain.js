// 管理内页脚本
/*====================================
 *基于JQuery 1.10.2以上主框架
 *DTcms管理界面
 *作者：一些事情
====================================*/
//页面加载完成时执行
$(function () {
    initContentTab(); //初始化TAB
    // $(".toolbar").ruleLayoutToolbar();
    // $(".imglist").ruleLayoutImgList();
    // $(".content-tab").ruleLayoutTab();
    // $(".tab-content").ruleLayoutContent();
    // $(".table-container").ruleLayoutTable();
    // $(".page-footer").ruleLayoutFooter();
    //窗口尺寸改变时
    $(window).resize(function () {
        //延迟执行,防止多次触发
        setTimeout(function () {
            // $("#floatHead").children("div").width($("#floatHead").width());
            //$(".toolbar").ruleLayoutToolbar();
            $("#floatHead").height($("#floatHead").children("div").outerHeight());
            //$(".imglist").ruleLayoutImgList();
            //$(".content-tab").ruleLayoutTab();
            //$(".tab-content").ruleLayoutContent();
            //$(".table-container").ruleLayoutTable();
            //$(".page-footer").ruleLayoutFooter();
        }, 200);
    });
});



//初始化Tab事件
function initContentTab() {
    var parentObj = $(".content-tab");
    var tabObj = $('<div class="tab-title"><span>' + parentObj.find("ul li a.selected").text() + '</span><i></i></div>');
    parentObj.children().children("ul").before(tabObj);
    parentObj.find("ul li a").click(function () {
        var tabNum = $(this).parent().index("li")
        //设置点击后的切换样式
        $(this).parent().parent().find("li a").removeClass("selected");
        $(this).addClass("selected");
        tabObj.children("span").text($(this).text());
        //根据参数决定显示内容
        $(".tab-content").hide();
        $(".tab-content").eq(tabNum).show();
        //$(".page-footer").ruleLayoutFooter();
    });
}