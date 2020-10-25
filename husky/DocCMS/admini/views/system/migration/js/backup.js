/*数据显示*/
$(function(){
	getBackupList(0);
});
function getBackupList(i) { 
	$(".delete"+i).remove();
	var url='./index.php?m=system&s=migration&a=getBackupList&i='+i;
	$.ajax({
			type:"GET",
			url:url,
			dataType:"json",
			cache:false,
			success:function(html)
			{
				for(var x in html)
				{
					lists(html[x],i);
				}
			}
		});
}
function checkall(form) {
	for(var i = 0;i < form.elements.length; i++) {
		var e = form.elements[i];
		if (e.name != 'chkall' && e.disabled != true) {
			e.checked = form.chkall.checked;
		}
	}
}
function lists(x,i){
	var list='';
	list+='<tr bgcolor="'+x.bgcolor+'"  align="center" class="delete'+i+'">';
	list+=' <td><input type="checkbox" name="filenames[]" value="'+x.filename+'"/></td>';
	list+=' <td>'+x.id+'</td>';
	list+=' <td class="px10" align="left">&nbsp;'+x.filename+'</td>';
	list+=' <td class="px10">'+x.filesize+' K</td>';
	list+=' <td class="px10">'+x.maketime+'</td>';
	list+=backupDataList(x);
	list+='</tr>';
	$("#appendFlag"+i).after(list);
}

function backupDataList(x)
{
	var list='';
	list=' <td>';
	list+='   <a href="./index.php?m=system&s=migration&a=importWebData&filename='+x.filename+'">导入</a> | ';
	list+='   <a href="./index.php?m=system&s=migration&a=deleteXMLWebData&filename='+x.filename+'" onclick="return confirm(\'您确认要删除此数据库备份?一旦删除，将不可恢复。\')">删除</a> | ';
	list+='   <a href="./index.php?m=system&s=migration&a=downloadXMLWebData&filename='+x.filename+'">下载</a>';
	list+=' </td>';
	return list;
}
/*选项卡*/
function setTab(m,n)
{
var tli=document.getElementById("menu"+m).getElementsByTagName("li");
var mli=document.getElementById("main"+m).getElementsByTagName("ul");
for(i=0;i<tli.length;i++)
	{
	   tli[i].className=i==n?"hover":"";
	   mli[i].style.display=i==n?"block":"none";
	}
}
/*打包*/
function _formSubmit(action,ind) { 
	var url='./index.php?m=system&s=migration&a='+action;
	$.ajax({
			type:"GET",
			url:url,
			cache:false,
			success:function(html)
			{
				$("#pakeageText").append($.trim(html));
				$("#pakeageText").attr("disabled","disabled");
				getBackupList(ind);
			}
		});
} 
function formSubmit(num) { 
	var url='./index.php?m=system&s=migration&a=export&num='+num;
	$.ajax({
			type:"GET",
			url:url,
			cache:false,
			success:function(html)
			{
				$("#pakeageText").attr("rows",num+1);
				$("#pakeageText").append($.trim(html)+"\n");
				if(num<22){
					formSubmit(++num);
				}else if(num==22)
				{
					_formSubmit('packageDatabase',0);				
				}else{
					return false;
				}
			}
		});
} 
function packageDatabase()
{
	$("#pakeageText").empty();
	$("#pakeageText").css({"display":"block"});
	$("#pakeageText").attr("disabled","");
		formSubmit(0);
}