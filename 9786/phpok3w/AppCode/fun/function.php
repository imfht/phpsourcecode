<?php


function Application($param)
{
	return "";
}
function Format_TitleURL($HTML_Path,$ID,$Title,$TitleColor,$TitleURL,$LeftN)
{
    $oTitle =  $Title ;
    If ($TitleColor<>"") $oTitle = "<font color=" . $TitleColor . ">" . $oTitle . "</font>";
	If ($TitleURL=="")
    {
        $Target = " "  ;
        $TitleURL = Page_URL(1,"",$ID);
    }
    Else
        $Target = " target='_blank'";


	return  "<a href=" . $TitleURL . " " . $Target . ">" . $oTitle . "</a>";

}
function LeftX($Str,$N)
{

    $j = 0;
    $StrTmp = "";
    for ($i = 1; $i < strlen($Str); $i++)
    {
        $ch = substr($Str, $i, 1);
        $StrTmp = $StrTmp . $ch;
        If (ord($ch) < 0)
            $j += 2;
        Else
            $j++;

        If ($j >= $N) break;
    }
    return  $StrTmp;
}


function ExecSqlReturnOneValue($Sql)
{
    $links=GetConn();
    $result = mysqli_query($links, $Sql);
    $info=mysqli_fetch_array($result, MYSQLI_NUM);
   return $info[0];
}

function CmdSafeLikeSqlStr($str)
{
    $str = str_replace("'", "''", $str);
    $str = str_replace("[","[[]", $str);
    $str = str_replace("%","[%]", $str);
    $str = str_replace("_","[_]", $str);
    return $str;
}

function MessageBox($Msg,$gotoUrl)
{
    echo "<script language='javascript'>";
	echo "alert('$Msg'');";
	if ($gotoUrl=="")
        echo "history.back();";
    else
        echo "document.URL='$gotoUrl';";

	echo "</script>";
	exit();
}
function OutStr($Str)
{
    $strer = $Str;
    $strer = str_replace("<", "&lt;", $strer);
    $strer = str_replace(">", "&gt;", $strer);
    $strer = str_replace(CHR(13) & Chr(10), "<br>", $strer); //'换行
    $strer = str_replace(CHR(32), "&nbsp;", $strer); //    '空格
    $strer = str_replace(CHR(9), "&nbsp;", $strer); //    'table
    $strer = str_replace(CHR(39), "&#39;", $strer); //   '单引号
    $strer = str_replace(CHR(34), "&quot;", $strer); //   '双引号
    return $strer;
}


/*
Function IsSelfRefer()
	server_v1=Lcase(Request.ServerVariables("HTTP_REFERER"))
	server_v2=Lcase(Request.ServerVariables("SERVER_NAME"))
	If InStr(server_v1,server_v2)<1 Then
		IsSelfRefer = False'不是
		Else 
			IsSelfRefer = True'是
	End If
End Function

Private Sub Page_Err(Msg)
	Response.Write(Msg)
	Response.End()
End Sub

Function myCdbl(str)
	If str = "" Or Not IsNumeric(str) Then
		Call Page_Err("参数错误：要求为数字型。")
		Else
			myCdbl = Cdbl(str)
	End If
End Function

Private Sub MessageBox(Msg,gotoUrl)
	Response.Write("<script language=""javascript"">")
	Response.Write("alert(""" & Msg & """);")
	If gotoUrl="" Then
		Response.Write("history.back();")
		Else
			Response.Write("document.URL='" & gotoUrl & "';")
	End If
	Response.Write("</script>")
	Response.End()
End Sub


Function OutStr(Str)
  strer=Str
  if strer="" or isnull(strer) then
    OutStr="":exit function
  end if
  strer=replace(strer,"<","&lt;")
  strer=replace(strer,">","&gt;")
  strer=replace(strer,CHR(13) & Chr(10),"<br>")    '换行
  strer=replace(strer,CHR(32),"&nbsp;")    '空格
  strer=replace(strer,CHR(9),"&nbsp;")    'table
  strer=replace(strer,CHR(39),"&#39;")    '单引号
  strer=replace(strer,CHR(34),"&quot;")    '双引号
  OutStr = strer
End Function

Function UBBCode(strer)
	If strer="" Then Exit Function
	dim re
	set re=new RegExp
	re.IgnoreCase =true
	re.Global=true
	  
	re.Pattern="(javascript)"
	strer=re.Replace(strer,"&#106avascript")
	re.Pattern="(jscript:)"
	strer=re.Replace(strer,"&#106script:")
	re.Pattern="(js:)"
	strer=re.Replace(strer,"&#106s:")
	re.Pattern="(value)"
	strer=re.Replace(strer,"&#118alue")
	re.Pattern="(about:)"
	strer=re.Replace(strer,"about&#58")
	re.Pattern="(file:)"
	strer=re.Replace(strer,"file&#58")
	re.Pattern="(document.cookie)"
	strer=re.Replace(strer,"documents&#46cookie")
	re.Pattern="(vbscript:)"
	strer=re.Replace(strer,"&#118bscript:")
	re.Pattern="(vbs:)"
	strer=re.Replace(strer,"&#118bs:")
	re.Pattern="(on(mouse|exit|error|click|key))"
	strer=re.Replace(strer,"&#111n$2")
	  
	're.Pattern="\[pic\](http|https|ftp):\/\/(.[^\[]*)\[\/pic\]"
	'strer=re.Replace(strer,"<IMG SRC='http://$2' border=0 onload=""javascript:if(this.width>screen.width-430)this.width=screen.width-430"">")
	re.Pattern="\[IMG\](http|https|ftp):\/\/(.[^\[]*)\[\/IMG\]"
	strer=re.Replace(strer,"<a href='http://$2' target=_blank><IMG SRC='http://$2' border=0 alt='按此在新窗口浏览图片' onload=""javascript:if(this.width>350)this.width=350""></a>")
	're.Pattern="(\[FLASH=*([0-9]*),*([0-9]*)\])(.[^\[]*)(\[\/FLASH\])"
	'strer= re.Replace(strer,"<a href='$4' TARGET=_blank>[全屏欣赏]</a><br><OBJECT codeBase=http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=4,0,2,0 classid=clsid:D27CDB6E-AE6D-11cf-96B8-444553540000 width=$2 height=$3><PARAM NAME=movie VALUE='$4'><PARAM NAME=quality VALUE=high><embed src='$4' quality=high pluginspage='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash' type='application/x-shockwave-flash' width=$2 height=$3>$4</embed></OBJECT>")
	're.Pattern="(\[FLASH\])(http://.[^\[]*(.swf))(\[\/FLASH\])"
	'strer= re.Replace(strer,"<a href=""$2"" TARGET=_blank>[全屏欣赏]</a><br><OBJECT codeBase=http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=4,0,2,0 classid=clsid:D27CDB6E-AE6D-11cf-96B8-444553540000 width=500 height=400><PARAM NAME=movie VALUE=""$2""><PARAM NAME=quality VALUE=high><embed src=""$2"" quality=high pluginspage='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash' type='application/x-shockwave-flash' width=500 height=400>$2</embed></OBJECT>")
	re.Pattern="(\[URL\])(.[^\[]*)(\[\/URL\])"
	strer= re.Replace(strer,"<A HREF='$2' class=blue TARGET=_blank>$2</A>")
	re.Pattern="(\[URL=(.[^\[]*)\])(.[^\[]*)(\[\/URL\])"
	strer= re.Replace(strer,"<A HREF='$2' class=blue TARGET=_blank>$3</A>")
	re.Pattern="(\[EMAIL\])(\S+\@.[^\[]*)(\[\/EMAIL\])"
	strer= re.Replace(strer,"<A HREF=""mailto:$2"">$2</A>")
	re.Pattern="(\[EMAIL=(\S+\@.[^\[]*)\])(.[^\[]*)(\[\/EMAIL\])"
	strer= re.Replace(strer,"<A HREF=""mailto:$2"" TARGET=_blank>$3</A>")
	re.Pattern = "^(http://[A-Za-z0-9\./=\?%\-&_~`@':+!]+)"
	strer = re.Replace(strer,"<a target=_blank href=$1>$1</a>")
	re.Pattern = "(http://[A-Za-z0-9\./=\?%\-&_~`@':+!]+)$"
	strer = re.Replace(strer,"<a target=_blank href=$1>$1</a>")
	re.Pattern = "([^>='])(http://[A-Za-z0-9\./=\?%\-&_~`@':+!]+)"
	strer = re.Replace(strer,"$1<a target=_blank href=$2>$2</a>")
	re.Pattern = "^(ftp://[A-Za-z0-9\./=\?%\-&_~`@':+!]+)"
	strer = re.Replace(strer,"<a target=_blank href=$1>$1</a>")
	re.Pattern = "(ftp://[A-Za-z0-9\./=\?%\-&_~`@':+!]+)$"
	strer = re.Replace(strer,"<a target=_blank href=$1>$1</a>")
	re.Pattern = "[^>='](ftp://[A-Za-z0-9\.\/=\?%\-&_~`@':+!]+)"
	re.Pattern="\[color=(.[^\[]*)\](.[^\[]*)\[\/color\]"
	strer=re.Replace(strer,"<font color=$1>$2</font>")
	re.Pattern="\[face=(.[^\[]*)\](.[^\[]*)\[\/face\]"
	strer=re.Replace(strer,"<font face=$1>$2</font>")
	re.Pattern="\[align=(.[^\[]*)\](.[^\[]*)\[\/align\]"
	strer=re.Replace(strer,"<div align=$1>$2</div>")
	re.Pattern="\[align=(.[^\[]*)\](.*)\[\/align\]"
	strer=re.Replace(strer,"<div align=$1>$2</div>")
	re.Pattern="\[center\](.[^\[]*)\[\/center\]"
	strer=re.Replace(strer,"<div align=center>$1</div>")
	re.Pattern="\[i\](.[^\[]*)\[\/i\]"
	strer=re.Replace(strer,"<i>$1</i>")
	re.Pattern="\[u\](.[^\[]*)(\[\/u\])"
	strer=re.Replace(strer,"<u>$1</u>")
	re.Pattern="\[b\](.[^\[]*)(\[\/b\])"
	strer=re.Replace(strer,"<b>$1</b>")
	re.Pattern="\[size=([1-6])\](.[^\[]*)\[\/size\]"
	strer=re.Replace(strer,"<font size=$1>$2</font>")
	  
	set re=Nothing
	UBBCode = strer
End Function

Function filterhtml(fstring)
    if isnull(fstring) or trim(fstring)="" then
        filterhtml=""
        exit function
    end if
    set  re = new  regexp
    re.ignorecase=true
    re.global=true
    re.pattern="<(.+?)>"
    fstring = re.replace(fstring, "")
    set   re=nothing
	filterhtml = fstring
End Function

Function ExecSqlReturnOneValue(Sql)
	Set opRs = Server.CreateObject("Adodb.RecordSet")
	opRs.Open Sql,Conn,0,1
	If  opRs.Eof And opRs.Bof Then 
		ExecSqlReturnOneValue = ""
		Else
			ExecSqlReturnOneValue = opRs(0)
	End If
	opRs.Close
	Set opRs = Nothing
End Function

Function Format_Time(s_Time,n_Flag)
	Dim y, m, d, h, mi, s
	Format_Time = ""
	If IsDate(s_Time) = False Then Exit Function
	y = cstr(year(s_Time))
	m = cstr(month(s_Time))
	If len(m) = 1 Then m = "0" & m
	d = cstr(day(s_Time))
	If len(d) = 1 Then d = "0" & d
	h = cstr(hour(s_Time))
	If len(h) = 1 Then h = "0" & h
	mi = cstr(minute(s_Time))
	If len(mi) = 1 Then mi = "0" & mi
	s = cstr(second(s_Time))
	If len(s) = 1 Then s = "0" & s
	Select Case n_Flag
	Case 0
		Format_Time = m & "-" & d
	Case 1
		' yyyy-mm-dd hh:mm:ss
		Format_Time = y & "-" & m & "-" & d & " " & h & ":" & mi & ":" & s
	Case 2
		' yyyy-mm-dd
		Format_Time = y & "-" & m & "-" & d
	Case 3
		' hh:mm:ss
		Format_Time = h & ":" & mi & ":" & s
	Case 4
		' yyyy年mm月dd日
		Format_Time = y & "年" & m & "月" & d & "日"
	Case 5
		' yyyymmdd
		Format_Time = y & m & d
	End Select
End Function

Function CmdSafeLikeSqlStr(Str)
	Str = Replace(Str,"'","''")
	Str = Replace(Str,"[","[[]")
	Str = Replace(Str,"%","[%]")
	Str = Replace(Str,"_","[_]")
	CmdSafeLikeSqlStr = Str
End Function

Function ReplaceTest(str,patrn, replStr)
  Dim regEx, str1 
  str1 = str
  Set regEx = New RegExp
  regEx.Pattern = patrn
  regEx.IgnoreCase = True
  regEx.global=true
  ReplaceTest = regEx.Replace(str1, replStr)
End Function


Function GetPageUrlPath()
	SERVER_NAME = Request.ServerVariables("SERVER_NAME")
	SERVER_PORT = Request.ServerVariables("SERVER_PORT")
	PATH_INFO = Request.ServerVariables("PATH_INFO")
	PATH_TMP = Split(PATH_INFO,"/")
	PATH_INFO = Replace(PATH_INFO,PATH_TMP(Ubound(PATH_TMP)),"")
	URL = "http://" & SERVER_NAME
	If SERVER_PORT<>80 Then URL = URL & ":" & SERVER_PORT
	URL = URL & PATH_INFO
	GetPageUrlPath = URL
End Function
*/
Function GetAdSense($SN)
{
    echo "<script language='javascript' src='/js/ok3w_" . $SN . ".js'></script>";
}
function GetClassName($ClassId)
{
    if($ClassId=="0")
        return "--------";
    $sql="select SortName from Ok3w_Class where ID=".$ClassId;
    return ExecSqlReturnOneValue($sql);
}

/*
'频道名称
Function GetChannelName(ChannelId)
	If ChannelId="" Then
		GetChannelName = "--------"
		Exit Function
	End If
	Sql = "select ChannelName from Ok3w_Channel where ChannelId=" & ChannelId
	GetChannelName = ExecSqlReturnOneValue(Sql)
End Function

'栏目名称
Function GetClassName(ClassId)
	If ClassId="0" Then
		GetClassName = "--------"
		Exit Function
	End If
	Sql = "select SortName from Ok3w_Class where ID=" & ClassId
	GetClassName = ExecSqlReturnOneValue(Sql)
End Function

Function GetSortPath(ClassId)
	If ClassId = "" Then
		GetSortPath = ""
		ClassId = -1
	Else
		ClassId = Cdbl(ClassId)
		GetSortPath = Conn.Execute("select SortPath from Ok3w_Class where ID=" & Cdbl(ClassID))(0)
	End If
End Function 

'父栏目名称
Function GetParentClassName(ClassId)
	Sql="select ParentID,SortName from Ok3w_Class where ID=" & ClassId
	Set oRs = Conn.Execute(Sql)
	a_ParentID = oRs(0)
	a_SortName = oRs(1)
	oRs.Close
	If a_ParentID <> 0 Then
		Sql="select ID,SortName from Ok3w_Class where ID=" & a_ParentID
		Set oRs = Conn.Execute(Sql)
		a_ParentID = oRs(0)
		a_SortName = oRs(1)
		oRs.Close
	End If
	Set oRs = Nothing
	GetParentClassName = a_SortName
End Function

Function IsHaveNextClass(ClassID)
	Sql="select count(ID) from Ok3w_Class where ParentID=" & ClassID & " and IsNav=1"
	If Conn.Execute(Sql)(0) = 0 Then
		IsHaveNextClass = False
	Else
		IsHaveNextClass = True
	End If
End Function

Function GetCommentsCount(TypeID,TableID)
	Sql="select count(ID) from Ok3w_Guest where TypeID=" & TypeID & " and TableID=" & TableID
	If Application(SiteID & "_Ok3w_SiteIsGuest")="1" Then Sql = Sql & " and IsPass=1"
	GetCommentsCount = ExecSqlReturnOneValue(Sql)
End Function

'分类ID下拉列表选择菜单
Private Sub InitClassSelectOption(ChannelId,ParentID,ChkID)
	Dim opRs,cTmp,cLen,cCount
	Set opRs = Server.CreateObject("Adodb.RecordSet")
	Sql = "select ID,SortName,SortPath from Ok3w_Class where ChannelId=" & ChannelId & " and ParentID=" & ParentID & " and gotoURL='' order by OrderID"
	opRs.Open Sql,Conn,0,1
	Do While Not opRs.Eof
		Response.Write("<option value=""" & opRs("ID") & """")
		If ChkID = opRs("ID") Then Response.Write(" selected=""selected""")
		Response.Write(">")
		cTmp = Split(opRs("SortPath"),",")
		cLen = Ubound(cTmp) - 2
		For cCount=1 To cLen
			Response.Write("│&nbsp;")
		Next
		Response.Write("├" & opRs("SortName") & "</option>")
		
		Call InitClassSelectOption(ChannelId,opRs("ID"),ChkID)
		opRs.MoveNext
	Loop
	opRs.Close
	Set opRs = Nothing
End Sub

Function ReplaceUpFilePath(uPath)
	ReplaceUpFilePath = uPath
End Function

Private Sub OutThisPageContent(aID,Content,PageType)
	thisPage = Request.QueryString("thisPage")
	If thisPage<>"" Then thisPage = myCdbl(thisPage)
	If thisPage="" Then thisPage=1
	thisPage = Cint(thisPage)
	
	If InStr(Content,"[Ok3w_NextPage]")>0 Then
		Content_Tmp = Split(Content,"[Ok3w_NextPage]")
		Page_Count = Ubound(Content_Tmp)+1
		If thisPage> Page_Count Then thisPage = Page_Count
		OutContent = Content_Tmp(thisPage-1)
	Else
		OutContent = Content
'		cLen = Len(Content)
'		If cLen Mod 2000 = 0 Then
'			Page_Count = cLen \ 2000
'		Else
'			Page_Count = cLen \ 2000 + 1
'		End If
'		If thisPage> Page_Count Then thisPage = Page_Count
'		OutContent = Mid(Content,(thisPage-1)*2000+1,2000)
	End If
	
	OutContent = ReplaceTest(OutContent,"<img ","<img style=""cursor:pointer;"" onclick=""ImageOpen(this)"" onload=""ImageZoom(this,560,700)"" ")
	OutContent = ReplaceTest(OutContent,"""upfiles/","""" & Htmldns & "upfiles/")
	OutContent = ReplaceTest(OutContent,"""editor/","""" & Htmldns & "editor/")
	
	If Trim(Application(SiteID & "_Ok3w_SitePublicKeyWords"))<>"" And Application(SiteID & "_Ok3w_SitePublicKeyWords")<>"0" Then
		kTmp = Split(Application(SiteID & "_Ok3w_SitePublicKeyWords"),vbCrLf)
		For kk=0 To Ubound(kTmp)
			uTmp = Split(kTmp(kk),"|")
			If Ubound(uTmp)=1 Then
				OutContent = ReplaceTest(OutContent,uTmp(0),"<span class=keyword><a href=" & uTmp(1) & " target=_blank>" & uTmp(0) & "<a></span>")
			End If
		Next
	End If
	
	Response.Write(OutContent)
	
	If Page_Count>1 Then
		Response.Write("<span></span><div class=""thisPageNav"">")
		For iPage=1 To Page_Count
			If iPage = 1 Then
				If PageType="html" Then
					URL = aID & ".html"
				Else
					URL = "?id=" & aID
				End If
			Else
				If PageType="html" Then
					URL = aID & "_" & iPage & ".html"
				Else
					URL = "?id=" & aID & "&thisPage=" & iPage
				End If
			End If
			If iPage = thisPage Then
				Response.Write("<strong style=""color:#FF0000;"">第" & iPage & "页</strong> ")
				Else
					Response.Write("<a href=""" & URL & """>第" & iPage & "页</a> ")
			End If
		Next
		Response.Write("</div>")
	End If
End Sub

Function GetUserDengjiValue(id,dj)
	SiteUserDengji = Application(SiteID & "_Ok3w_SiteUserDengji")
	If SiteUserDengji<>"" Then
		DJ1 = Split(SiteUserDengji,"|")
		DJ2 = Split(DJ1(id),",")
		GetUserDengjiValue = DJ2(dj-1)
	End If
End Function

Function GetUserDengjiID(Jifen)
	SiteUserDengji = Application(SiteID & "_Ok3w_SiteUserDengji")
	DJ1 = Split(SiteUserDengji,"|")
	DJ2 = Split(DJ1(1),",")
	Max_ADJ = Ubound(DJ2)
	For dj_item = Max_ADJ To 0 Step -1
		If DJ2(dj_item)<>"" Then
			If Jifen>=Cdbl(DJ2(dj_item)) Then
				GetUserDengjiID = dj_item + 1
				Exit Function
			End If
		End If
	Next
End Function

Private Sub InitUserDengjiSelectOption(dj)
	SiteUserDengji = Application(SiteID & "_Ok3w_SiteUserDengji")
	DJ_Tmp = Split(SiteUserDengji,"|")
	dj_Name = Split(DJ_Tmp(0),",")
	For dj_item = 0 To Ubound(dj_Name)
		If dj_Name(dj_item)="" Then Exit Sub
		Response.Write("<option value=""" & dj_item + 1 & """")
		If Cint(dj) = dj_item + 1 Then Response.Write(" selected=""selected""")
		Response.Write(">" & dj_Name(dj_item) & "</option>")
	Next
End Sub

Private Sub InitSoftBaseChkItem(ChkStr,objName,objType)
	ItemSTr = Application(SiteID & "_Ok3w_Site" & objName)
	ItmpTmp = Split(ItemSTr,"|")
	Select Case objType
		Case "checkbox"
			For II=0 To Ubound(ItmpTmp)
				Response.Write("<input name=""" & objName & """ type=""" & objType & """ value=""" & ItmpTmp(II) & """")
				If InStr("," & ChkStr & "," , "," & Trim(ItmpTmp(II)) & ",")>0 Then	Response.Write(" checked")
				Response.Write(">" & ItmpTmp(II) & " ")
			Next
		Case "select"
			For II=0 To Ubound(ItmpTmp)
				Response.Write("<option value=""" & ItmpTmp(II) & """")
				If Trim(ItmpTmp(II)) = ChkStr Then	Response.Write(" selected")
				Response.Write(">" & ItmpTmp(II) & "</option>")
			Next
	End Select
End Sub

Private Sub SoftstarImg(star)
	For II=1 To star
		'Response.Write("<img src=""" & Htmldns & "images/star.gif"" />")
		Response.Write("★")
	Next
End Sub

Private Sub Delete_ArticleHTML(IDS)
	Sql="select ID,Content from Ok3w_Article where ID in(" & IDS & ")"
	Set oRs = Conn.Execute(Sql)
	Set fso = CreateObject("Scripting.FileSystemObject")
	Do While Not oRs.Eof
		h_ID = oRs(0)
		Content = oRs(1)
		If InStr(Content,"[Ok3w_NextPage]")>0 Then
			Content_Tmp = Split(Content,"[Ok3w_NextPage]")
			Page_Count = Ubound(Content_Tmp)+1
		Else
			'cLen = Len(Content)
			'If cLen Mod 2000 = 0 Then
			'	Page_Count = cLen \ 2000
			'Else
			'	Page_Count = cLen \ 2000 + 1
			'End If
			Page_Count = 1
		End If
		For iPage=1 To Page_Count
			If iPage = 1 Then
				FilePath = dbdns & HTML_Article_Path & h_ID & ".html"
			Else
				FilePath = dbdns & HTML_Article_Path & h_ID & "_" & iPage & ".html"
			End If
						
			If fso.FileExists(Server.MapPath(FilePath)) Then
				Set f = fso.GetFile(Server.MapPath(FilePath))
				f.delete()
				Set f = Nothing
			End If
		Next
		oRs.MoveNext
	Loop
	Set fso = Nothing			
	oRs.Close
	Set oRs = Nothing
End Sub

Private Sub Delete_SoftHTML(IDS)
	Sql="select ID,Softintro from Ok3w_Soft where ID in(" & IDS & ")"
	Set oRs = Conn.Execute(Sql)
	Set fso = CreateObject("Scripting.FileSystemObject")
	Do While Not oRs.Eof
		h_ID = oRs(0)
		Content = oRs(1)
		If InStr(Content,"[Ok3w_NextPage]")>0 Then
			Content_Tmp = Split(Content,"[Ok3w_NextPage]")
			Page_Count = Ubound(Content_Tmp)+1
		Else
			'cLen = Len(Content)
			'If cLen Mod 2000 = 0 Then
			'	Page_Count = cLen \ 2000
			'Else
			'	Page_Count = cLen \ 2000 + 1
			'End If
			Page_Count = 1
		End If
		For iPage=1 To Page_Count
			If iPage = 1 Then
				FilePath = dbdns & HTML_Soft_Path & h_ID & ".html"
			Else
				FilePath = dbdns & HTML_Soft_Path & h_ID & "_" & iPage & ".html"
			End If
					
			If fso.FileExists(Server.MapPath(FilePath)) Then
				Set f = fso.GetFile(Server.MapPath(FilePath))
				f.delete()
				Set f = Nothing
			End If
		Next
		oRs.MoveNext
	Loop
	Set fso = Nothing			
	oRs.Close
	Set oRs = Nothing
End Sub

Function Page_URL(ChannelID,ClassID,ID)
	Select Case ChannelID
		Case 1
			If ClassID<>"" Then
				Page_URL = "list.asp?id=" & ClassID
			Else
				Page_URL = "show.asp?id=" & ID
			End If
		Case 2
			Page_URL = "about.asp?id=" & ID
		Case 3
			If ClassID<>"" Then
				Page_URL = "soft_list.asp?id=" & ClassID
			Else
				Page_URL = "soft_show.asp?id=" & ID
			End If
	End Select
	Page_URL = Htmldns & Page_URL
End Function

*/

function Page_URL($ChannelID,$ClassID,$ID)
{
    $url="";
    switch($ChannelID)
    {
        case 1:
            if($ClassID!="")
                $url="list.php?id=".$ClassID;
            else
                $url="show.php?id=".$ID;
            break;
        case 2:
            $url="about.php?id=".$ID;
            break;
        case 3:
            if($ClassID!="")
                $url="soft_list.php?id=".$ClassID;
            else
                $url="soft_show.php?id=".$ID;
            break;
    }

    return $url;
}



function Display_HearNav($ChannelID)
{
    $Sql = "select ID,SortName,gotoURL from Ok3w_Class where ChannelID=" . $ChannelID . " and ParentID=0 and IsNav=1 order by OrderID";
    $mysqli = GetConn();
    $result = $mysqli->query($Sql); //判断结果集中行的数目是否大于0
    while ($row1 = $result->fetch_array(MYSQLI_ASSOC))
    {
        //循环输出结果集中的记录
        if ($row1["gotoURL"] == "")
            $gotoURL = Page_URL($ChannelID, $row1["ID"], "");
        else
            $gotoURL = $row1["gotoURL"];

        echo "<span><a href='" . $gotoURL . "'>" . $row1["SortName"] . "</a></span>";
    }


    /*
                {
        Is["gotoURL"] = )
            gotoURL = Page_URL(ChannelID,$Rs["ID"],];
        Else
            gotoURL = $Rs["gotoURL"];

        echo "<span><a href=" & gotoURL & ">" & $Rs["SortName"] & "</a></span>";
        Rs.MoveNext;
    }
    */

}
/*
	Sql="select ID,SortName,gotoURL from Ok3w_Class where ChannelID=" & ChannelID & " and ParentID=0 and IsNav=1 order by OrderID"
	Rs.Open Sql,Conn,0,1
	Do While Not Rs.Eof
		If Rs("gotoURL") = "" Then
			gotoURL = Page_URL(ChannelID,Rs("ID"),"")
		Else
			gotoURL = Rs("gotoURL")
		End If
		Response.Write("<span><a href=""" & gotoURL & """>" & Rs("SortName") & "</a></span>")
		Rs.MoveNext
	Loop
	Rs.Close
End Sub

Private Sub Display_Footer()
	Sql="select ID,SortName,gotoURL from Ok3w_Class where ChannelID=2 and IsNav=1 order by OrderID"
	Rs.Open Sql,Conn,0,1
	Do While Not Rs.Eof
	If Rs("gotoURL") = "" Then
		MaxID = Conn.Execute("select max(ID) from Ok3w_Article where ClassID=" & Rs("ID"))(0)
		pageURL = Page_URL(2,"",MaxID)
	End If
	If Rs("gotoURL") = "" Then
		Response.Write("<a href=""" & pageURL & """>" & Rs("SortName") & "</a>")
	Else
		Response.Write("<a href=""" & Rs("gotoURL") & """ target=""_blank"">" & Rs("SortName") & "</a>")
	End If
	Rs.MoveNext
	If Not Rs.Eof Then Response.Write(" - ")
	Loop
	Rs.Close
End Sub

Private Sub Display_BookUserHead(imgW,imgH,xCount,cCount)
	tmp = 0
	For i=1 To xCount
		If tmp = cCount Then
			Response.Write("<br />")
			tmp = 1
		Else
			tmp = tmp + 1
		End If
		Response.Write("<input name=""pID"" type=""radio"" value=""" & i & """")
		If i=1 Then
			Response.Write(" checked=""checked""")
		End If
		Response.Write(" />")
        Response.Write("<a href=""javascript:;"" onclick=""frmBook.pID[" & i-1 & "].checked=true;""><img alt=""Ok3w.Net -- 点击选择"" src=""images/book/" & i & ".jpg"" width=""" & imgW & """ height=""" & imgH & """  border=""0"" /></a> ")
	Next
End Sub
*/
?>