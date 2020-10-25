var ADMIN_URL = "index.php?file=administrator_controller&class=AdministratorController";
var WIDGET_LIST_URL = ADMIN_URL + "&fun=widgetlist";

var selectedWidgets;

$(document).ready(function(){
	selectedWidgets = new Array();
	
	$("#btn-add").click(addWidget);
	$("#btn-delete").click(delWidget);
	reloadListWidgets();
});

//添加控件
function addWidget(){
	var tips = new Array();
	
	var widgetName = $("input[name=widget_name]").val();
	var widgetLink = $("input[name=widget_link]").val();
	var widgetHeight = $("input[name=widget_height]").val();
	
	widgetName = trim(widgetName);
	widgetLink = trim(widgetLink);
	
	if(widgetName == ""){
		tips.push("请填写控件名");
	}
	if(widgetLink == ""){
		tips.push("请填写页面链接");
	}
	if(widgetHeight != ""){
		widgetHeight = parseInt(widgetHeight);
		if(isNaN(widgetHeight)){
			tips.push("控件高度必须是数字");
		}
	}
	
	if(tips.length > 0){
		showTips(tips);
		return;
	}
	
	var command = new Command(
		"administrator_controller",
		"AdministratorController",
		"execAddWidget",
		{widget_name: widgetName, widget_link: widgetLink, widget_height: widgetHeight}
	);
	command.send(handleAddWidget);
}

//处理添加控件的结果
function handleAddWidget(msg){
	if(msg.no == msg.MSG_SUCCESS){
		reloadListWidgets();
	}
	
	showTips([msg.content]);
}

//重新加载控件列表
function reloadListWidgets(){
	$("#list-widgets").load(WIDGET_LIST_URL, null, function(){
		$(".line-widget").click(selectWidget);
	});
}

//选择控件
function selectWidget(event){
	var widget = $(this).children("input[name=widget]").val();
	var index = array_index(selectedWidgets, widget);
	if(index == -1){
		array_insert(selectedWidgets,widget);
		$(this).css("background-color", "lightblue");
	}else{
		array_delete(selectedWidgets, widget);
		$(this).css("background-color", "");
	}
}

//删除控件
function delWidget(){
	if(selectedWidgets.length <= 0){
		showTips(['请选择您要删除的控件']);
		return;
	}
	
	var result = confirm("您确定要删除这些控件？");
	if(!result)
		return;
		
	var strWidgets = selectedWidgets.join(SEP_I);
	var command = new Command(
		"administrator_controller",
		"AdministratorController",
		"execDelWidget",
		{str_widgets: strWidgets}
	);
	command.send(handleDelWidget);
}

//处理删除控件
function handleDelWidget(msg){
	if(msg.no == msg.MSG_SUCCESS){
		reloadListWidgets();
		selectedWidgets = [];
	}
	
	showTips([msg.content]);
}
