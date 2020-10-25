/*****************************/
/*	PC商务通邀请框		*/
/*****************************/
var LiveAutoInvite0='您好，来自%IP%的朋友';
var LiveAutoInvite1=window.location.href;
var LiveAutoInvite2='';
var LrinviteTimeout={lrinvitetimeout};	//首次弹出邀请框秒数
var LR_next_invite_seconds={lr_next_invite_seconds};	//再次弹出间隔秒数
var lr_xCenter=446/2;		//邀请框水平位置
var lr_yCenter=327/2;		//邀请框垂直位置
var newswthtml='<div id="pcnewswt"><a href="{swtdir}" target="_blank" class="sl_swt_body" onclick="gotoswt(event,this,\'pc_swt_body\');"></a><a href="javascript:;" onclick="LR_HideInvite();LR_RefuseChat();return false;" class="sl_swt_close"></a></div>';
del_swt_left();		//隐藏商务通侧边栏
reLR_showInviteDiv(); 	//强制重定义邀请框函数
//调用商务通JS（模版最后才加载商务通JS）
document.writeln('<script language="javascript" src="http://{swturl}/JS/LsJS.aspx?siteid={swtid}&float=1"></script>');