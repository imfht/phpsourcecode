var ADMIN_ENTRY = "index.php?file=administrator_controller&class=AdministratorController";
var DEFAULT_DESKTOP_URL = ADMIN_ENTRY + "&fun=getdefaultdesktop";
var SEARCH_SITE_URL = ADMIN_ENTRY + "&fun=searchsite";
var GET_SITES_URL = ADMIN_ENTRY + "&fun=getsitesbycategory";

var selectedSites = new Array();//被选中的网址
var selectedIcons = new Array();//被选中的桌面图标

$(document).ready(function(){
    //获取默认桌面内容
    reloadDesktop();
    disableBtn($("#btn-delete"));
    
    $("input[name=search_site]").keydown(searchSite);
    $("#select-category").change(getSitesByCategory);
    $("#btn-add").click(addToDefaultDesktop);
    $("#btn-delete").click(delIcon);
});

//搜索网址
function searchSite(event){
    if(event.keyCode != 13){
        return;
    }
    
    var keyword = $(this).val();
    $("#list-sites").load(SEARCH_SITE_URL, {keyword: keyword}, function(){
        selectedSites = [];
        $(".site-for-add").click(selectSite);
        setDefaultIcon();
    });
}

//选择网址
function selectSite(event){
    var site = $(this).children("input[name=site]").val();
    var index = array_index(selectedSites, site);
    if(index == -1){
        array_insert(selectedSites, site);
        $(this).children("img").css("box-shadow", "2px 2px 10px orange");
    }else{
        array_delete(selectedSites, site);
        $(this).children("img").css("box-shadow", "");
    }
}

//根据分类获取网址列表
function getSitesByCategory(event){
    var category = $(this).val();
    if(category == -1)
        return;
    
    $("#list-sites").load(GET_SITES_URL, {category: category}, function(){
        selectedSites = [];
        $(".site-for-add").click(selectSite);
        setDefaultIcon();
    });
}

//添加到默认桌面
function addToDefaultDesktop(event){
    if(selectedSites.length <= 0){
        showTips(['请选择需要添加的网址']);
        return;
    }
    
    var strSites = selectedSites.join(SEP_I);
    var command = new Command(
        "administrator_controller",
        "AdministratorController",
        "execAddToDefaultDesktop",
        {str_sites: strSites}
    );
    command.send(function(msg){
        if(msg.no == msg.MSG_SUCCESS){
            reloadDesktop();
        }
        showTips([msg.content]);
    });
}

//选择桌面图标
function selectIcon(event){
    var siteIndex = $(this).children("input[name=site_index]").val();
    
    var index = array_index(selectedIcons, siteIndex);
    if(index == -1){
        array_insert(selectedIcons, siteIndex);
        $(this).children("img").css("box-shadow", "2px 2px 10px orange");
    }else{
        array_delete(selectedIcons, siteIndex);
        $(this).children("img").css("box-shadow", "");
    }
    
    //启用按钮
    if(selectedIcons.length > 0){
        enableBtn($("#btn-delete"));
    }else{
        disableBtn($("#btn-delete"));
    }
}

//删除图标
function delIcon(event){
    if(selectedIcons.length < 0)
        return;
    
    var strIcons = selectedIcons.join(SEP_I);
    var command = new Command(
        "administrator_controller",
        "AdministratorController",
        "execDelIcon",
        {str_icons: strIcons}
    );
    command.send(function(msg){
        if(msg.no == msg.MSG_SUCCESS){
            reloadDesktop();
        }
        
        showTips([msg.content]);
    });
}

//重载桌面
function reloadDesktop(){
    $("#list-desktop").load(DEFAULT_DESKTOP_URL, {}, function(){
        selectedIcons = [];
        $(".desktop-icon").click(selectIcon);
        setDefaultIcon();
    });
}