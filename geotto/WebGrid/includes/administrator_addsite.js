var ICON_PAGE = "index.php?file=administrator_controller&class=AdministratorController&fun=geticons";
var selectedIcon = -1;

$(document).ready(function(){
    //获取图标列表
    $("#list-icons").load(ICON_PAGE, {}, function(){
        $(".grid").click(selectIcon);
    });
    $("input[name=search_icon]").keydown(searchIcon);
    $("#btn-add").click(addSite);
});

//选择图标
function selectIcon(event){
    var icon = $(this).children("input[name=icon]").val();
    
    $(".grid").css("border", "");
    if(selectedIcon == icon){
        selectedIcon = -1;
        $(this).css("border", "");
    }else{
        selectedIcon = icon;
        $(this).css("border", "1px solid red");
    }
}

//搜索图标
function searchIcon(event){
    if(event.keyCode != 13)
        return;
    
    var keyword = $(this).val();
    $("#list-icons").load(ICON_PAGE, {keyword: keyword}, function(){
        $(".grid").click(selectIcon);
    });
}

//添加网址
function addSite(event){
    var tips = new Array();
    
    var siteName = $("input[name=site_name]").val();
    var siteUrl = $("input[name=site_url]").val();
    var siteCategory = $("#select-category").val();
    var siteIcon = selectedIcon;
    
    siteName = trim(siteName);
    siteUrl = trim(siteUrl);
    if(siteName == ""){
        tips.push("请填写网址名称");
    }
    if(siteUrl == ""){
        tips.push("请填写网址链接");
    }
    
    var regexp = /^(http|https|ftp):\/\//;
    if(!siteUrl.match(regexp)){
        tips.push("请填写正确的网址链接");
    }
    
    //显示错误
    if(tips.length > 0){
        showTips(tips);
    }
    
    var command = new Command("administrator_controller", "AdministratorController", "execAddSite", {
        site_name: siteName,
        site_url: siteUrl,
        site_category: siteCategory,
        site_icon: siteIcon
    });
    command.send(msgHandler);
}