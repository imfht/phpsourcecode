
//跳转页面
function hrefUrl(url){
	$('#iframeMain').load(url);
}

//数据全选
function dataSelectAll(seleName,seleNameZi){
	if ($("#selectAll").attr("checked")){
		$("input[name=infoID]").each(function(){$(this).attr("checked", true);});
	}else{
		$("input[name=infoID]").each(function(){$(this).attr("checked", false);});
	}
}

