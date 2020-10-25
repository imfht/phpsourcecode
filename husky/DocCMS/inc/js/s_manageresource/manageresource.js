function animation(id){
	$("#"+id).css("left",Math.ceil((document.body.clientWidth-$("#"+id).css("width").replace("px",""))/2)+"px");
	$(window).resize(function(){$("#"+id).css("left",Math.ceil((document.body.clientWidth-$("#"+id).css("width").replace("px",""))/2)+"px");});
	$("#"+id).css("top","50%");
	$("#"+id).fadeIn("normal");
}

	$(document).ready(function(){
		
/*
 * 加载等待动画 start
 * */ 
	$("#trigger_loading a, a.trigger_loading").click(function (){
		$(this).css({background:"#FFFFCC"});//efefef
		animation('loading');
	});
/*
 * 加载等待动画 end
 * */
/*
 * 滑动效果start
 *  */ 
	$("#tree li").mouseover(function (){
		$(this).css({background:"#FFFFCC"});//efefef
	});
	$("#tree li").mouseout(function (){
		$(this).css({background:"none"});
	});
	$("#aKey tr").mouseover(function (){
		$(this).css({background:"#FFFFCC"});//efefef
	});
	$("#aKey tr").mouseout(function (){
		$(this).css({background:"none"});
	});
/*
 * 滑动效果 end
 * */
/*
 * 弹出start
 * */
	$("#update").live('click', function()
	{
		var action="./?m=system&s=manageresource&a=updateResource";
		var UI=updateResourceUI( action );
		popup(this,UI);
		
	});
	$("#create").live('click', function()
	{
		var action="./?m=system&s=manageresource&a=createDir";
		var UI=createDirUI( action );
		popup(this,UI);
	});
	
	function popup(obj,UI){
		$("#createUI").empty();
		$("#createUI").append(UI);
		var objPos = mousePosition(obj);
		var tit =$("#tit").html();
		var wit =$("#wit").html();
		var messContent=$("#createUI").html();
		showMessageBox(tit,messContent,objPos,wit);
		$("#createUI").empty();
		var path=$(obj).children().attr("path");
		$("#dirPath").attr("value",path);
	}
	function createDirUI(action)
	{
		var UI;
		UI  = '<form name="createDir" id="createDir" method="POST" action="'+action +'" >';
		UI += '<ul>';
		UI += ' <li id="tit">创建文件夹</li>';
		UI += ' <li id="wit">600</li>';
		UI += ' <li><span>当前目录</span><input id="dirPath" name="dirPath" type="text" value=""/></li>';
		UI += ' <li><span>文件夹名</span><input id="newFolder" name="newFolder" type="text" value=""/></li>';
		UI += ' <li><input id="close" name="close" type="button" value="关闭" onclick="closeWindow();" /><input id="button" type="submit" value="提交" /></li>';
		UI += '</ul>';
		UI += '</form>';
		return UI;
	}
	function updateResourceUI( action )
	{
		var UI;
		UI  = '<form name="updateResource" id="updateResource" method="POST" enctype="multipart/form-data" action="'+action +'" >';
		UI += '<ul>';
		UI += ' <li id="tit">上传资源</li>';
		UI += ' <li id="wit">600</li>';
		UI += ' <li><span>当前目录</span><input id="dirPath" name="dirPath" type="text" value=""/></li>';
		UI += ' <li><span>资源</span><input id="newUpload" name="newUpload" type="file" value=""/></li>';
		UI += ' <li><input id="close" name="close" type="button" value="关闭" onclick="closeWindow();"/><input id="button" type="submit" value="提交" /></li>';
		UI += '</ul>';
		UI += '</form>';
		return UI;
	}
	
/*
 * 弹出end
 */
/*
 * 上传初始化
 */
$("#updateResource,#createDir").live('submit', function()
{
	var options = {
			beforeSubmit:  showRequest,
			success:       showResponse
			};
	$(this).ajaxSubmit(options);
	return false;
});
function showResponse(responseText, statusText)  {
	var mes=responseText.split('::');
	alert(mes[1]);
	if(mes[0]=='1'){
		closeWindow();
		window.location.reload(); 
	}
}
function showRequest(formData, jqForm, options) {
	var queryString = $.param(formData);
	var re=/^[^\[\]\{\}\+\*\|\^\$\?"'<>]*$/; 		 
	for (var i=0; i < formData.length; i++) {
		if (!formData[i].value) {
			alert('请填写信息！');
			return false;
		}
		if(!re.test(formData[i].value)){
			alert('信息含有非法字符！');
			return false;
		}
	}
		return true;
}

	});