/*****************************/
/*	手机商务通邀请框		*/
/*****************************/
var LiveAutoInvite0='您好，来自%IP%的朋友';
var LiveAutoInvite1=window.location.href;
var LiveAutoInvite2='';
var LrinviteTimeout={lrinvitetimeout};	//首次弹出邀请框秒数
var LR_next_invite_seconds={lr_next_invite_seconds};	//再次弹出间隔秒数
var lr_xCenter=240/2;		//邀请框水平位置
var lr_yCenter=170/2;		//邀请框垂直位置
var newswthtml='<div id="sjnewswt"><span class="sl_swt_tit">专家在线咨询</span><a class="sl_swt_x" target="_self" href="javascript:;" onclick="LR_HideInvite();LR_RefuseChat();return false;"></a><a class="sl_swt_pic" target="_blank" href="{swtdir}" onclick="gotoswt(event,this,\'sj_swt_body\');"><img src="{swtdir}/{swtskins}/img/zj_{zjpy}.gif" /></a><span class="sl_swt_ts"><a href="{swtdir}" target="_blank" onclick="gotoswt(event,this,\'sj_swt_body2\');">快速找答案的方法，问在线专家！<span style="color:#FF0004">会更轻松、更具针对性!</span></a></span><a id="sjswtzxzx" style="font-size:16px;" class="sl_swt_zxzx" target="_blank" href="{swtdir}" onclick="gotoswt(event,this,\'sj_swt_body3\');">在线咨询</a><a class="sl_swt_mfdh" target="_blank" href="{dhurl}">免费电话</a></div>'
var i = 0;
//文字变色
$$$(function() {
	textcolorful();
})
function textgetColor(){
	i++;
	switch(i){ 
		case 1:return "#ff0000";
		case 2:return "#ff6600";
		case 3:return "#3366cc";
		default:return "#6A5102";
	}
}
function textcolorful(){
	var o=document.getElementById("sjswtzxzx");
	if(o){
		o.style.color=textgetColor();
	}
	if(i>3)i=0;
	setTimeout('textcolorful()',700);
}
del_swt_left();		//隐藏商务通侧边栏
reLR_showInviteDiv(); 	//强制重定义邀请框函数
//调用商务通JS（模版最后才加载商务通JS）
document.writeln('<script language="javascript" src="http://{swturl}/JS/LsJS.aspx?siteid={swtid}&float=1"></script>');