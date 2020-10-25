var USER_URL = "index.php";
var DESKTOP_URL = USER_URL + "?fun=getDesktopContent";

$(document).ready(function(){
    //获取推荐内容
    getPopSites();
    
	$(".item-category").click(getSitesByCategory);
	$("#button-close").click(closeViewSitesDialog);
	$("#button-add").click(addSites);
    $("#textbox-search").keydown(searchSite);
});

//根据分类浏览网址
function getSitesByCategory(event){	
	var category = $(this).children("input[name=category]").val();
	var command = new Command("user_controller", "UserController", "getSitesByCategory", {category: category});
	command.send(getSitesByCategoryHandler);
}

//处理根据分类获取网址的返回结果
function getSitesByCategoryHandler(msg){
		if(msg.no == msg.MSG_SUCCESS){
			//清空列表
			$("#list-sites").html('');
			selected_sites.length = 0;
			
			var rows = msg.content.split(SEP_I);
			for(var i=0;i<rows.length;i++){
				var cols = rows[i].split(SEP_II);
				var site =  createSiteDOMObject(cols[0], cols[1], cols[2], cols[3], cols[4], cols[5]);
				site.className += " grid";
				$("#list-sites").append(site);
			}
			
		}
		else{
			showTips([msg.content]);
		}
}

//生成 site DOM 对象
function createSiteDOMObject(id, url, name, icon, population, category){
		var img = document.createElement("img");
		img.src = (icon == "")?url + "/favicon.ico":ICON_PATH + "/" + icon;
		img.onerror = function(){
			img.src = ICON_PATH + "/default.png";
		};
		img.title = "点击选中";
		img.onclick = selectSite;
		
		var label = document.createElement("div");
		var labelLink = document.createElement("a");
		labelLink.href = url;
		labelLink.target = "_blank";
		var labelText = document.createTextNode(name);
		labelLink.appendChild(labelText);
		label.appendChild(labelLink);
		label.className += " icon-name";
		
		var inputId = document.createElement("input");
		inputId.type = "hidden";
		inputId.name = "id";
		inputId.value = id;
		var inputCategory = document.createElement("input");
		inputCategory.type = "hidden";
		inputCategory.name = "category";
		inputCategory.value = category;
		var inputPopulation = document.createElement("input");
		inputPopulation.type = "hidden";
		inputPopulation.name = "population";
		inputPopulation.value = population;
		
		var site = document.createElement("div");
		site.appendChild(img);
		site.appendChild(label);
		site.appendChild(inputId);
		site.appendChild(inputCategory);
		site.appendChild(inputPopulation);
		
		return site;
}

//选中网址
function selectSite(event){
		var site = $(this).siblings("input[name=id]").val();
		var index = array_index(selected_sites, site);
		if(index == -1){
			array_insert(selected_sites, site);
			//$(this).parent(".grid").css("background-color", "#ADD8E6");
			$(this).css("box-shadow", "1px 1px 10px orange");
		}
		else{
			array_delete(selected_sites, site);
			//$(this).parent(".grid").css("background-color", "");
			$(this).css("box-shadow", "");
		}
}

//关闭浏览网址窗口
function closeViewSitesDialog(event){
	$("#dialog-view-sites").hide();
}

//处理添加网址的结果
function handleAddSites(msg){    
    if(msg.no == msg.MSG_SUCCESS){
        var desktop = currentDesktop;
        reloadDesktop(desktop);
    }
    
    showTips([msg.content]);
}

//添加网址到桌面
function addSites(event){
    var desktop = currentDesktop;
    var strSites = selected_sites.join(SEP_I);
    
    if(desktop == -1){
        showTips(['您尚未登录，请登录后再试']);
        return;
    }
    
    var command = new Command(
        "user_controller",
        "UserController",
        "addSites",
        {desktop: desktop, str_sites: strSites}
    );
    command.send(handleAddSites);
}

//搜索网址
function searchSite(event){
    if(event.keyCode != 13)
        return;
    
    var keyword = $(this).val();
    keyword = trim(keyword);
    if(keyword == "")
        return;
    
    var command = new Command(
        "user_controller",
        "UserController",
        "execSearchSite",
        {keyword: keyword}
    );
    command.send(getSitesByCategoryHandler);
}

//获取推荐内容
function getPopSites(){
    var command = new Command(
        "user_controller",
        "UserController",
        "execGetPopSites",
        null
    );
    command.send(getSitesByCategoryHandler);
}
