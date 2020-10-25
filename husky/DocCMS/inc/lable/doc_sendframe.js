/*!
 * Frame JavaScript 
 * http://www.doccms.com/
 * Date: 狗头巫师(grysoft) 2012/11/11
 * QQ:767912290
 */
var val = new Array();
var style ='';
var __type  ='';
var gry = jQuery.noConflict();

function doc_send_window(ev, type, value, num) {
	var objPos = mousePosition(ev);
	value  = value.replace(/\'/g, "");
	val    = value.split(",");
	__type = type;
	for (i = 0; i < 10; i++) {
		val[i] = val[i] == null ? 0 : val[i];
	}
	switch (type) {
	case 'article':
		setlabelview(val,type,num);
		break;
	case 'download':
		setlabelview(val,type,num)
		break;
	case 'focus':
		setlabelview(val,type,num)
		break;
	case 'guestbook':
		setlabelview(val,type,num)
		break;
	case 'jobs':
		setlabelview(val,type,num)
		break;
	case 'linkers':
		setlabelview(val,type,num)
		break;
	case 'mapshow':
		setlabelview(val,type,num)
		break;
	case 'picture':
		setlabelview(val,type,num)
		break;
	case 'poll':
		setlabelview(val,type,num)
		break;
	case 'product':
		setlabelview(val,type,num)
		break;
	case 'video':
		setlabelview(val,type,num)
		break;
	case 'nav_sub':
		setlabelview(val,type,num)
		break;
	default:
		setlabelview(val,type,num)
		break;
	}
	showMessageBox(type+'模块 标签调用设置', messContent, objPos, 350);
	showMenu(type, value);
	showStyle(type, style);
	showStyleTxt(type, style)
}
function setlabelview(val,type,num)
{
	switch (type) {
	case 'article':
		style = val[2];		
		messContent = "<div class='setbox'><ul>";
		messContent += "<form action='lable_edit.php?m=" + type + "&num=" + num + "' method='post' name='myform'>";
		messContent += '<input type="hidden" name="labelId" id="showId"  value="' + val[0] + '">';
		messContent += "<li><div class='checkId'>可选择调用栏目（双击鼠标选择）：<br/><select name='list1' id='sele1' multiple='multiple' ondblclick='moveOption(document.myform.list1, document.myform.list2)'></select></div>";
		messContent += "<div class='checkId'>已选择调用栏目：<br/><select name='list2' id='sele2' multiple='multiple' ondblclick='moveOption(document.myform.list2, document.myform.list1)'></select></div></li>";

		messContent += "<li><div class='checkId'><span>调用页数：</span><br/><input class='setbipt' type='text' name='labelNum' value='" + val[1] + "'></div>";
		
		messContent += '<div class="checkId"><span>选择样式：</span><br/><select class="setselct" name="labelStyle" id="sele3" onchange="showStyleTxt(\'' + type + '\',this.value)"></select></div></li>';
		
		messContent += "<li><div class='checkId'>标题截取字数(填写0 则为不限制)：<br/><input class='setbipt' type='text' name='labelCountT' value='" + val[3] + "'></div>";
		messContent += "<div class='checkId'>描述截取字数(填写0 则为不限制)：<br/><input class='setbipt' type='text' name='labelCountD' value='" + val[4] + "'></div></li>";
		messContent += "<li><span>内容截取字数：</span><br/><input style=' width:520px;' class='setbipt' type='text' name='labelCountC' value='" + val[5] + "'></li>";
		
		messContent += "<li><div class='checkId'><span>字串结尾是否加省略号：</span><br/><input type='radio' name='labelIsellipsis' value='true' " + ischecked(val[6], 'true') + ischecked(val[6], 0) + ">是 <input type='radio' name='labelIsellipsis' value='false' " + ischecked(val[6], 'false') + "> 否</div>";
		
		messContent += "<div class='checkId'><span>是否保留内容HTML代码：</span><br/><input type='radio' name='labelHastag' value='true' " + ischecked(val[7], 'true') + ">是 <input type='radio' name='labelHastag' value='false' " + ischecked(val[7], 'false') + ischecked(val[7], 0) + "> 否</div></li>";
		
		messContent += "<li><span>排序方式（正序）：</span><br/>按照 <select class='setselct' name='labelOrder' id='labelOrder'><option value='id' " + isselected(val[8], 'id') + ">id</option><option value='dtTime' " + isselected(val[7], 'dtTime') + ">添加日期</option><option value='counts' " + isselected(val[8], 'counts') + ">点击次数</option><option value='pageId' " + isselected(val[8], 'pageId') + ">手动排序</option></select> 排序。从第 <input class='setbipt' type='text' name='labelFrom' id='labelFrom' value='" + val[9] + "'> 条数据开始调用</li>";
		
		messContent += "<li><span>样式内容：</span><br/><textarea class='editbq' name='styleContent' id='styleContent'></textarea></li>";
		messContent += "<li style='text-align:center;'><input class='savebt' type='submit' value='保存'></li>";
		messContent += "</form></ul></div>";
		break;
	case 'focus':
		style = val[2];		
		messContent = "<div class='setbox'><ul>";
		messContent += "<form action='lable_edit.php?m=" + type + "&num=" + num + "' method='post' name='myform'>";
		messContent += '<input type="hidden" name="labelId" id="showId"  value="' + val[0] + '">';
		messContent += "<li><div class='checkId'>可选择调用栏目（双击鼠标选择）：<br/><select name='list1' id='sele1' multiple='multiple' ondblclick='moveOption(document.myform.list1, document.myform.list2)'></select></div>";
		messContent += "<div class='checkId'>已选择调用栏目：<br/><select name='list2' id='sele2' multiple='multiple' ondblclick='moveOption(document.myform.list2, document.myform.list1)'></select></div></li>";

		messContent += "<li><div class='checkId'><span>调用条数：</span><br/><input class='setbipt' type='text' name='labelNum' value='" + val[1] + "'></div>";
		
		messContent += '<div class="checkId"><span>选择样式：</span><br/><select class="setselct" name="labelStyle" id="sele3" onchange="showStyleTxt(\'' + type + '\',this.value)"></select></div></li>';
		
		messContent += "<li><div class='checkId'>标题截取字数(填写0 则为不限制)：<br/><input class='setbipt' type='text' name='labelCountT' value='" + val[3] + "'></div>";
		messContent += "<div class='checkId'>描述截取字数(填写0 则为不限制)：<br/><input class='setbipt' type='text' name='labelCountD' value='" + val[4] + "'></div></li>";
				
		messContent += "<li><span>字串结尾是否加省略号：</span><br/><input type='radio' name='labelIsellipsis' value='true' " + ischecked(val[5], 'true') + ischecked(val[5], 0) + ">是 <input type='radio' name='labelIsellipsis' value='false' " + ischecked(val[5], 'false') + "> 否</li>";
				
		messContent += "<li><span>排序方式（正序）：</span><br/>按照 <select class='setselct' name='labelOrder' id='labelOrder'><option value='id' " + isselected(val[6], 'id') + ">id</option><option value='dtTime' " + isselected(val[6], 'dtTime') + ">添加日期</option><option value='pageId' " + isselected(val[6], 'pageId') + ">手动排序</option></select> 排序。从第 <input class='setbipt' type='text' name='labelFrom' id='labelFrom' value='" + val[7] + "'> 条数据开始调用</li>";
		
		messContent += "<li><span>样式内容：</span><br/><textarea class='editbq' name='styleContent' id='styleContent'></textarea></li>";
		messContent += "<li style='text-align:center;'><input class='savebt' type='submit' value='保存'></li>";
		messContent += "</form></ul></div>";
		break;
	case 'guestbook':
		style = val[2];		
		messContent = "<div class='setbox'><ul>";
		messContent += "<form action='lable_edit.php?m=" + type + "&num=" + num + "' method='post' name='myform'>";
		messContent += '<input type="hidden" name="labelId" id="showId"  value="' + val[0] + '">';
		messContent += "<li><div class='checkId'>可选择调用栏目（双击鼠标选择）：<br/><select name='list1' id='sele1' multiple='multiple' ondblclick='moveOption(document.myform.list1, document.myform.list2)'></select></div>";
		messContent += "<div class='checkId'>已选择调用栏目：<br/><select name='list2' id='sele2' multiple='multiple' ondblclick='moveOption(document.myform.list2, document.myform.list1)'></select></div></li>";

		messContent += "<li><div class='checkId'><span>调用条数：</span><br/><input class='setbipt' type='text' name='labelNum' value='" + val[1] + "'></div>";
		
		messContent += '<div class="checkId"><span>选择样式：</span><br/><select class="setselct" name="labelStyle" id="sele3" onchange="showStyleTxt(\'' + type + '\',this.value)"></select></div></li>';
		
		messContent += "<li><div class='checkId'>留言截取字数(填写0 则为不限制)：<br/><input class='setbipt' type='text' name='labelCountT' value='" + val[3] + "'></div>";
		messContent += "<div class='checkId'>回复截取字数(填写0 则为不限制)：<br/><input class='setbipt' type='text' name='labelCountD' value='" + val[4] + "'></div></li>";
				
		messContent += "<li><span>字串结尾是否加省略号：</span><br/><input type='radio' name='labelIsellipsis' value='true' " + ischecked(val[5], 'true') + ischecked(val[5], 0) + ">是 <input type='radio' name='labelIsellipsis' value='false' " + ischecked(val[5], 'false') + "> 否</li>";
				
		messContent += "<li><span>排序方式（正序）：</span><br/>按照 <select class='setselct' name='labelOrder' id='labelOrder'><option value='id' " + isselected(val[6], 'id') + ">id</option><option value='dtTime' " + isselected(val[6], 'dtTime') + ">添加日期</option><option value='pageId' " + isselected(val[6], 'pageId') + ">手动排序</option></select> 排序。从第 <input class='setbipt' type='text' name='labelFrom' id='labelFrom' value='" + val[7] + "'> 条数据开始调用</li>";
		
		messContent += "<li><span>样式内容：</span><br/><textarea class='editbq' name='styleContent' id='styleContent'></textarea></li>";
		messContent += "<li style='text-align:center;'><input class='savebt' type='submit' value='保存'></li>";
		messContent += "</form></ul></div>";
		break;
	case 'linkers':
		style = val[2];		
		messContent = "<div class='setbox'><ul>";
		messContent += "<form action='lable_edit.php?m=" + type + "&num=" + num + "' method='post' name='myform'>";
		messContent += '<input type="hidden" name="labelId" id="showId"  value="' + val[0] + '">';
		messContent += "<li><div class='checkId'>可选择调用栏目（双击鼠标选择）：<br/><select name='list1' id='sele1' multiple='multiple' ondblclick='moveOption(document.myform.list1, document.myform.list2)'></select></div>";
		messContent += "<div class='checkId'>已选择调用栏目：<br/><select name='list2' id='sele2' multiple='multiple' ondblclick='moveOption(document.myform.list2, document.myform.list1)'></select></div></li>";

		messContent += "<li><div class='checkId'><span>调用条数：</span><br/><input class='setbipt' type='text' name='labelNum' value='" + val[1] + "'></div>";
		
		messContent += '<div class="checkId"><span>选择样式：</span><br/><select class="setselct" name="labelStyle" id="sele3" onchange="showStyleTxt(\'' + type + '\',this.value)"></select></div></li>';
		
		messContent += "<li><div class='checkId'>链接名称截取字数(填写0 则为不限制)：<br/><input class='setbipt' type='text' name='labelCountT' value='" + val[3] + "'></div>";
		messContent += "<div class='checkId'>链接描述截取字数(填写0 则为不限制)：<br/><input class='setbipt' type='text' name='labelCountD' value='" + val[4] + "'></div></li>";
		messContent += "<li><div class='checkId'><span>调用方式：</span><br/><input type='radio' name='labelType' value='0' " + ischecked(val[5], '0') + ischecked(val[5], 0) + ">图片链接 <input type='radio' name='labelType' value='1' " + ischecked(val[5], '1') + "> 文字链接</div>";	
			
		messContent += "<div class='checkId'><span>字串结尾是否加省略号：</span><br/><input type='radio' name='labelIsellipsis' value='true' " + ischecked(val[6], 'true') + ischecked(val[6], 0) + ">是 <input type='radio' name='labelIsellipsis' value='false' " + ischecked(val[6], 'false') + "> 否</div></li>";
				
		messContent += "<li><span>排序方式（正序）：</span><br/>按照 <select class='setselct' name='labelOrder' id='labelOrder'><option value='id' " + isselected(val[7], 'id') + ">id</option><option value='dtTime' " + isselected(val[7], 'dtTime') + ">添加日期</option><option value='pageId' " + isselected(val[7], 'pageId') + ">手动排序</option></select> 排序。从第 <input class='setbipt' type='text' name='labelFrom' id='labelFrom' value='" + val[8] + "'> 条数据开始调用</li>";
		
		messContent += "<li><span>样式内容：</span><br/><textarea class='editbq' name='styleContent' id='styleContent'></textarea></li>";
		messContent += "<li style='text-align:center;'><input class='savebt' type='submit' value='保存'></li>";
		messContent += "</form></ul></div>";
		break;
	case 'mapshow':
		style = val[1];		
		messContent = "<div class='setbox'><ul>";
		messContent += "<form action='lable_edit.php?m=" + type + "&num=" + num + "' method='post' name='myform'>";
		messContent += '<input type="hidden" name="labelId" id="showId"  value="' + val[0] + '">';
		messContent += "<li><div class='checkId'>可选择调用栏目（双击鼠标选择）：<br/><select name='list1' id='sele1' multiple='multiple' ondblclick='moveOption(document.myform.list1, document.myform.list2)'></select></div>";
		messContent += "<div class='checkId'>已选择调用栏目：<br/><select name='list2' id='sele2' multiple='multiple' ondblclick='moveOption(document.myform.list2, document.myform.list1)'></select></div></li>";

		
		messContent += '<li><div class="checkId"><span>选择样式：</span><br/><select class="setselct" name="labelStyle" id="sele3" onchange="showStyleTxt(\'' + type + '\',this.value)"></select></div>';
		
		messContent += "<div class='checkId'>标题截取字数(填写0 则为不限制)：<br/><input class='setbipt' type='text' name='labelCountT' value='" + val[2] + "'></div></li>";
		messContent += "<li><span>内容截取字数：</span><br/><input style=' width:520px;' class='setbipt' type='text' name='labelCountC' value='" + val[3] + "'></li>";
		
			
		messContent += "<li><div class='checkId'><span>字串结尾是否加省略号：</span><br/><input type='radio' name='labelIsellipsis' value='true' " + ischecked(val[4], 'true') + ischecked(val[4], 0) + ">是 <input type='radio' name='labelIsellipsis' value='false' " + ischecked(val[4], 'false') + "> 否</div>";
		messContent += "<div class='checkId'><span>是否保留内容HTML代码：</span><br/><input type='radio' name='labelHastag' value='true' " + ischecked(val[5], 'true') + ">是 <input type='radio' name='labelHastag' value='false' " + ischecked(val[5], 'false') + ischecked(val[5], 0) + "> 否</div></li>";
				
		messContent += "<li><span>样式内容：</span><br/><textarea class='editbq' name='styleContent' id='styleContent'></textarea></li>";
		messContent += "<li style='text-align:center;'><input class='savebt' type='submit' value='保存'></li>";
		messContent += "</form></ul></div>";
		break;
	case 'nav_sub':
		style = val[1];		
		messContent = "<div class='setbox'><ul>";
		messContent += "<form action='lable_edit.php?m=" + type + "&num=" + num + "' method='post' name='myform'>";
		messContent += '<input type="hidden" name="labelId" id="showId"  value="' + val[0] + '">';
		messContent += "<li><div class='checkId'>可选择调用栏目（双击鼠标选择）：<br/><select name='list1' id='sele1' multiple='multiple' ondblclick='moveOption(document.myform.list1, document.myform.list2 ,true)'></select></div>";
		messContent += "<div class='checkId'>已选择调用栏目：<br/><select name='list2' id='sele2' multiple='multiple' ondblclick='moveOption(document.myform.list2, document.myform.list1)'></select></div></li>";
		messContent += '<li><div class="checkId"><span>选择样式：</span><br/><select class="setselct" name="labelStyle" id="sele3" onchange="showStyleTxt(\'' + type + '\',this.value)"></select></div>';
		messContent += "<div class='checkId'><span>菜单调用方式：</span><br/><select class='setselct' name='labelType'><option value='0' "+isselected(val[2],0)+">仅显示一级菜单<option value='1' "+isselected(val[2],1)+">展开的多级树状菜单<option value='2' "+isselected(val[2],2)+">隐藏的多级树状菜单</option></select></div></li>";
		messContent += "<li><div class='checkId'>调用条数(填写0 则为不限制)：<br/><input style=' width:520px;' class='setbipt' type='text' name='labelNum' value='" + val[3] + "'> </div></li>";		
		messContent += "<li><span>样式内容：</span><br/><textarea class='editbq' name='styleContent' id='styleContent'></textarea></li>";
		messContent += "<li style='text-align:center;'><input class='savebt' type='submit' value='保存'></li>";
		messContent += "</form></ul></div>";
		break;
	case 'poll':
		style = val[1];		
		messContent = "<div class='setbox'><ul>";
		messContent += "<form action='lable_edit.php?m=" + type + "&num=" + num + "' method='post' name='myform'>";
		messContent += '<input type="hidden" name="labelId" id="showId"  value="' + val[0] + '">';
		messContent += "<li><div class='checkId'>可选择调用栏目（双击鼠标选择）：<br/><select name='list1' id='sele1' multiple='multiple' ondblclick='moveOption(document.myform.list1, document.myform.list2 ,true)'></select></div>";
		messContent += "<div class='checkId'>已选择调用栏目：<br/><select name='list2' id='sele2' multiple='multiple' ondblclick='moveOption(document.myform.list2, document.myform.list1)'></select></div></li>";
		messContent += '<div class="checkId"><span>选择样式：</span><br/><select class="setselct" name="labelStyle" id="sele3" onchange="showStyleTxt(\'' + type + '\',this.value)"></select></div></li>';	
		messContent += "<li><span>样式内容：</span><br/><textarea class='editbq' name='styleContent' id='styleContent'></textarea></li>";
		messContent += "<li style='text-align:center;'><input class='savebt' type='submit' value='保存'></li>";
		messContent += "</form></ul></div>";
		break;
	default:
		style = val[2];		
		messContent = "<div class='setbox'><ul>";
		messContent += "<form action='lable_edit.php?m=" + type + "&num=" + num + "' method='post' name='myform'>";
		messContent += '<input type="hidden" name="labelId" id="showId"  value="' + val[0] + '">';
		messContent += "<li><div class='checkId'>可选择调用栏目（双击鼠标选择）：<br/><select name='list1' id='sele1' multiple='multiple' ondblclick='moveOption(document.myform.list1, document.myform.list2)'></select></div>";
		messContent += "<div class='checkId'>已选择调用栏目：<br/><select name='list2' id='sele2' multiple='multiple' ondblclick='moveOption(document.myform.list2, document.myform.list1)'></select></div></li>";
		messContent += "<li><div class='checkId'><span>调用条数：</span><br/><input class='setbipt' type='text' name='labelNum' value='" + val[1] + "'></div>";
		messContent += '<div class="checkId"><span>选择样式：</span><br/><select class="setselct" name="labelStyle" id="sele3" onchange="showStyleTxt(\'' + type + '\',this.value)"></select></div></li>';
		messContent += "<li><div class='checkId'>标题截取字数(填写0 则为不限制)：<br/><input class='setbipt' type='text' name='labelCountT' value='" + val[3] + "'></div>";
		messContent += "<div class='checkId'>描述截取字数(填写0 则为不限制)：<br/><input class='setbipt' type='text' name='labelCountD' value='" + val[4] + "'></div></li>";
		messContent += "<li><span>内容截取字数：</span><br/><input style=' width:520px;' class='setbipt' type='text' name='labelCountC' value='" + val[5] + "'></li>";		
		messContent += "<li><div class='checkId'><span>字串结尾是否加省略号：</span><br/><input type='radio' name='labelIsellipsis' value='true' " + ischecked(val[6], 'true') + ischecked(val[6], 0) + ">是 <input type='radio' name='labelIsellipsis' value='false' " + ischecked(val[6], 'false') + "> 否</div>";
		messContent += "<div class='checkId'><span>是否保留内容HTML代码：</span><br/><input type='radio' name='labelHastag' value='true' " + ischecked(val[7], 'true') + ">是 <input type='radio' name='labelHastag' value='false' " + ischecked(val[7], 'false') + ischecked(val[7], 0) + "> 否</div></li>";	
		messContent += "<li><span>排序方式（正序）：</span><br/>按照 <select class='setselct' name='labelOrder' id='labelOrder'><option value='id' " + isselected(val[7], 'id') + ">id</option><option value='dtTime' " + isselected(val[7], 'dtTime') + ">添加日期</option><option value='counts' " + isselected(val[8], 'counts') + ">点击次数</option><option value='ordering' " + isselected(val[8], 'ordering') + ">手动排序</option></select> 排序。从第 <input class='setbipt' type='text' name='labelFrom' id='labelFrom' value='" + val[9] + "'> 条数据开始调用</li>";
		messContent += "<li><span>样式内容：</span><br/><textarea class='editbq' name='styleContent' id='styleContent'></textarea></li>";
		messContent += "<li style='text-align:center;'><input class='savebt' type='submit' value='保存'></li>";
		messContent += "</form></ul></div>";
		break;
	}				
}
function showMenu(type, value) {
	var str = '';
	var str2 = '';
	gry.ajax({
			type: "POST",
			url: type=='poll'?"?a=showPollId":"?a=showId",
			data: "type=" + type + "&value=" + value,
			timeout: "100000",
			dataType: "json",
			success: function(html) {
				if(html!=null)
				{
					for (i = 1; i <= count(html['n']); i++) {
						str += '<option value="' + html['n'][i]['id'] + '">' + html['n'][i]['title'] + '</option>';
					}
					for (i = 1; i <= count(html['y']); i++) {
						str2 += '<option value="' + html['y'][i]['id'] + '">' + html['y'][i]['title'] + '</option>';
					}
					gry("#sele1").append(str);
					gry("#sele2").append(str2);
				}
			},
			error: function() {}
	});
}
function showStyle(type, style) {
	var str = '';
	gry.ajax({
		type: "POST",
		url: "?a=showStyle",
		data: "type=" + type,
		timeout: "100000",
		dataType: "json",
		success: function(html) {
			if(html!=null)
			{
				for (i = 0; i < html.length; i++) {
					if (style == html[i].split(":")[0]) str += '<option value="' + html[i].split(":")[0] + '" selected="selected">' + html[i].split(":")[1] + '</option>';
					else str += '<option value="' + html[i].split(":")[0] + '" >' + html[i].split(":")[1] + '</option>';
				}
				gry("#sele3").append(str);
			}
		},
		error: function() {}
	});
}
function showStyleTxt(type, style) {
	gry.ajax({
		type: "POST",
		url: "?a=showStyleTxt",
		data: "type=" + type + "&style=" + style,
		timeout: "100000",
		success: function(html) {
			gry("#styleContent").val(html);
		},
		error: function() {}
	});
}
function moveOption(e1, e2 ,tag) {
	try {
		for (var i = 0; i < e1.options.length; i++) {
			if (e1.options[i].selected) {
				var e = e1.options[i];
				e2.options.add(new Option(e.text, e.value));
				e1.remove(i);
				s=i;
				i = i - 1
			}
		}
		if(__type == 'nav_sub' || __type == 'poll' && tag){
			for (var i = 0; i < e2.options.length; i++) {
				if(i < e2.options.length-1){
					e = e2.options[i];
					e1.options.add(new Option(e.text, e.value));
					e2.remove(i);
					i = i - 1
				}
			}
			gry('#showId').val(e2.options[0].value);
		}else{
			gry('#showId').val(getvalue(document.myform.list2));
		}
	} catch(e) {}
}
function getvalue(geto) {
	var allvalue = "";
	for (var i = 0; i < geto.options.length; i++) {
		allvalue += geto.options[i].value + "|";
	}
	allvalue = allvalue.substring(0, allvalue.length - 1); return allvalue;
}
function ischecked(v, i) {
	if (v == i) return ' checked="checked"';
	else return '';
}
function isselected(v, i) {
	if (v == i) return ' selected="selected"';
	else return '';
}
function count(mixed_var, mode) {

	var key, cnt = 0;

	if (mixed_var === null || typeof mixed_var === 'undefined') {
		return 0;
	} else if (mixed_var.constructor !== Array && mixed_var.constructor !== Object) {
		return 1;
	}
	if (mode === 'COUNT_RECURSIVE') {
		mode = 1;
	}
	if (mode != 1) {
		mode = 0;
	}
	for (key in mixed_var) {
		if (mixed_var.hasOwnProperty(key)) {
			cnt++;
			if (mode == 1 && mixed_var[key] && (mixed_var[key].constructor === Array || mixed_var[key].constructor === Object)) {
				cnt += this.count(mixed_var[key], 1);
			}
		}
	}
	return cnt;
}
Array.prototype.in_array = function(e) { 
	for(i=0;i<this.length && this[i]!=e;i++); 
	return !(i==this.length); 
}