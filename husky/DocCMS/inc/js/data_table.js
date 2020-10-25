// JavaScript Document
function f(name){
	var cbx=document.getElementsByTagName("input");
	for(var i=0;i<cbx.length;i++)
	{
		if(cbx[i].type =="checkbox" && cbx[i].name==name)
		{
			if (cbx[i].checked)
			{
				var t=cbx[i].parentElement;
				while(t.tagName!='TR'){ t=t.parentNode;}
				t.style.backgroundColor="#ffb";
			}
			else
			{
				var t=cbx[i].parentElement;
				while(t.tagName!='TR'){ t=t.parentNode;}
				t.style.backgroundColor="";
			}
		}
	}
}

function btn_check_all(name,flag){//flag false=off true=on
	var cbx=document.getElementsByTagName("input");
	for(var i=0;i<cbx.length;i++)
	{
		if(cbx[i].type =="checkbox" && cbx[i].name==name)
		{
			if(flag)
			cbx[i].checked="checked";
			else
			cbx[i].checked="";
		}
	}
}
function deleteAll(action_to)
{	
	if(confirm("真的要删除吗?")) {
		var a = new Array(); 
		var cbx=document.getElementsByTagName("input");
		for (var i=0;i<cbx.length;i++) {
			if (cbx[i].checked==true && cbx[i].type =="checkbox") {
				a.push(cbx[i].value);
			}
		}
		if (a.length==0) {
			alert('没有选择！');
			return false;
		}
		else
		{
		/*alert(action_to+"&ids="+a.join(','));*/
		window.location.href=action_to+"&ids="+a.join(','); 
		}
	}
}
function move_to(to,action_to)
{
	if(to)
	{
		if(confirm("真的要移动吗?")) {
			var a = new Array(); 
			var cbx=document.getElementsByTagName("input");
			for (var i=0;i<cbx.length;i++) {
				if (cbx[i].checked==true && cbx[i].type =="checkbox") {
					a.push(cbx[i].value);
				}
			}
			if (a.length==0) {
				alert('没有选择！');
				return false;
			}
			else
			{
			window.location.href=action_to+"&move_to="+to+"&ids="+a.join(','); 
			}
		}
	}
}
function cw(sender)
{
	if(sender.style.backgroundColor!="#ffb")
	sender.style.backgroundColor='';
}
function cy(sender)
{
	if(sender.style.backgroundColor!="#ffb")
	sender.style.backgroundColor='#ffc';
}

function showHide(el)
{
	if (document.getElementById(el).style.display == 'block')
	{
		document.getElementById(el).style.display = 'none';
	}
	else
	{
		document.getElementById(el).style.display = 'block';
	};
}

function SetCookie(name,value)//两个参数，一个是cookie的名子，一个是值
{
    var Days = 30; //此 cookie 将被保存 30 天
    var exp = new Date();    //new Date("December 31, 9998");
    exp.setTime(exp.getTime() + Days*24*60*60*1000);
    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
	
	if(value==0)
	$('#admini_help').html(help1);
	else
	$('#admini_help').html(help2);
}

function getCookie(name)//取cookies函数        
{
     var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
     if(arr != null) return unescape(arr[2]); return null;
}
