<?php


function Ok3w_Article_List($ClassID,$Rows,$Cels,$LeftN,$IsCommend,$DisClass,$DisTime,$TimeFormat,$DisHits,$OrderType)
{

    $link=MySQL_connect('localhost','root','mmeizhen');
    mysql_select_db('okdb');
    mysql_query('set names utf8');

    $sql="select Id, ClassID,Title,TitleColor,TitleURL,AddTime,Hits";
    $sql.=" from Ok3w_Article where ChannelID=1 and IsPass=1 and IsDelete=0";

    if($ClassID!="") $sql.= " and SortPath like '%," & $ClassID & ",%'";
    switch($OrderType)
    {
        case "hot":
            $sql .= " order by Hits desc,AddTime desc,ID desc";
            break;
        case "rnd":
            $sql .= " order by newid(),ID desc";
            break;
        case "new":
            $sql .= " order by AddTime desc,ID desc";
            break;
        default:
            $sql .= " order by IsTop desc, IsCommend desc,AddTime desc,ID desc";
    }

    $sql.=" limit 0,". $Rows * $Cels;


    echo  "<table width='100%' border='0' cellspacing='0' cellpadding='0'>";


    $result =mysql_query($sql, $link)  ;



    for ($II = 0; $II < $Cels; $II++)
    {
        echo $II. "<br />";
        if (!$result)
        {
            echo "<td>&nbsp;</td>";
            if ($DisHits Or $DisTime) echo "<td>&nbsp;</td>";
        } else
        {
            while ($info = mysql_fetch_array($result))
            {
                $ChannelID=1;
                $HTML_Article_Path="/";
                echo "<td class='list_title'>";
                if ($DisClass)
                   echo "[<a href='" . Page_URL($ChannelID, $info["ClassID"], "") . "' >" . GetClassName($info["ClassID"]) . "</a>]";
                   //echo "[<a href= " . Page_URL($ChannelID, $info["ClassID"], "") . " >" & GetClassName($info["ClassID"]) . "</a>]";


                echo Format_TitleURL($HTML_Article_Path, $info["Id"], $info["Title"], $info["TitleColor"], $info["TitleURL"], $LeftN);
                echo "</td>";
                if ($DisHits || $DisTime) echo "<td class='list_title_r'>" ;
                if ($DisHits) echo "查看:" . Rs("Hits");
                if ($DisTime) echo date("Y-m-d H:i:s" ,strtotime( $info["AddTime"] ));
                if ($DisHits || $DisTime) echo "</td>";
            }
        }
        if ($Cels != 1 && $II <> $Cels) echo "<td>&nbsp;</td>";
    }


    echo "</tr>";

    echo "</table>";
    return;
}
    /*


    For II=1 To $Cels ;
    ifRs.Eof Then;
        echo  "<td>&nbsp;</td>";
        if$DisHits Or $DisTime Then echo  "<td>&nbsp;</td>";
    Else;
        echo  "<td class=""list_title"">";
        if$DisClass Then;
            echo  "[<a href=""" & Page_URL(ChannelID,Rs("$ClassID"),"") & """>" & GetClassName(Rs("$ClassID")) & "</a>]";
        End If;
        echo  Format_TitleURL(HTML_Article_Path,Rs("ID"),Rs("Title"),Rs("TitleColor"),Rs("TitleURL"),$LeftN);
        echo  "</td>";
        if$DisHits Or $DisTime Then echo ("<td class=""list_title_r"">");
        if$DisHits Then;
            echo  "查看:" & Rs("Hits");
        End If;
        if$DisTime Then;
            echo  Format_Time(Rs("AddTime"),$TimeFormat);
        End If;
        if$DisHits Or $DisTime Then echo  "</td>";
        Rs.MoveNext;
    End If;
    if$Cels<>1 And II<>$Cels Then;
        echo  "<td>&nbsp;</td>";
    End If;
    Next;
    echo  "</tr>"  ;
    Loop;
    Rs.Close ;*/
/*

    Private Sub Ok3w_Article_PreNext(ClassID,ID)
        $Sql="select ID,Title,TitleColor,TitleURL from Ok3w_Article where SortPath like '%," & ClassID & ",%' and IsPass=1 and IsDelete=0 and ID>" & ID & " order by ID asc"
        Set oRs = Conn.Execute($Sql)
        ifoRs.Eof And oRs.bof Then
            echo ("上一篇：没有了")
            Else
                echo (Format_TitleURL(HTML_Article_Path,oRs("ID"),"上一篇：" & oRs("Title"),oRs("TitleColor"),oRs("TitleURL"),50))
        End If
        echo ("<br />")
        $Sql="select ID,Title,TitleColor,TitleURL from Ok3w_Article where SortPath like '%," & ClassID & ",%' and IsPass=1 and IsDelete=0 and ID<" & ID & " order by ID desc"
        Set oRs = Conn.Execute($Sql)
        ifoRs.Eof And oRs.bof Then
            echo ("下一篇：没有了")
            Else
                echo (Format_TitleURL(HTML_Article_Path,oRs("ID"),"下一篇：" & oRs("Title"),oRs("TitleColor"),oRs("TitleURL"),50))
        End If
    End Sub

*/
 function Ok3w_Article_Gundong($ClassID,$TopN,$Width,$Height,$Speed)
{

    $sql = "select  Id,Title,TitleColor,TitleURL,picfile from Ok3w_Article";
    $sql .= " where ChannelID=1 and IsPass=1 and IsDelete=0 and IsMove=1";
    if($ClassID != "") $sql .= " and SortPath like '%," . $ClassID . ",%'";
    $sql .= " order by IsTop desc,IsCommend desc,Id desc";
    $mysqli=GetConn();
    $result = $mysqli->query($sql);

    $pics="";
    $links="";
    $texts="";
    while ($row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $pics.=$row["picfile"]."|";
        $links.= Format_TitleURL("/",$row["Id"], $row["Title"], $row["TitleColor"], $row["TitleURL"] ,50)."|";
        $texts.=$row["Title"]."|";
    }

    $pics=substr($pics,0,-1);
    $links=substr($links,0,-1);
    $texts=substr($texts,0,-1);

    $str= <<<EOT
<script language="javascript">
function __Ok3w_Article_Gundong__003620(){
	var RndID = 'pro3620';
	var StrGD = '·<a href="./show.asp?id=25" target="_blank">滚动新闻</a>&nbsp;&nbsp;
	·<a href="./show.asp?id=22" target="_blank">滚动新闻</a>
	&nbsp;&nbsp;·<a href="./show.asp?id=21" target="_blank">滚动新闻</a>&nbsp;&nbsp;
	·<a href="./show.asp?id=20" target="_blank">滚动新闻</a>&nbsp;&nbsp;
	·<a href="./show.asp?id=19" target="_blank">滚动新闻</a>&nbsp;&nbsp;
	·<a href="./show.asp?id=26" target="_blank">滚动新闻</a>&nbsp;&nbsp;
	·<a href="./show.asp?id=24" target="_blank">滚动新闻</a>&nbsp;&nbsp;
	·<a href="./show.asp?id=23" target="_blank">滚动新闻</a>&nbsp;&nbsp;';
Ok3w_Marquee(RndID,StrGD,468,13,120);}__Ok3w_Article_Gundong__003620();
</script>
EOT;

    return $str;
}
/*
    Randomize
    RndID = Int(Rnd()*10000)
    RndFun = "__Ok3w_Article_Gundong__00" & RndID
    $Sql="select top " & TopN & " Id,Title,TitleColor,TitleURL from Ok3w_Article where ChannelID=1 and IsPass=1 and IsDelete=0 and IsMove=1"
    ifClassID<>"" Then $Sql=$Sql & " and SortPath like '%," & ClassID & ",%'"
    $Sql=$Sql & " order by IsTop desc,IsCommend desc,Id desc"
    Rs.Open $Sql,Conn,0,1
    StrTmp = ""
    Do While Not Rs.Eof
        StrTmp = StrTmp & "·" & Format_TitleURL(HTML_Article_Path,Rs("ID"),Rs("Title"),Rs("TitleColor"),Rs("TitleURL"),50) & "&nbsp;&nbsp;"
        Rs.MoveNext
    Loop
    Rs.Close
    StrTmp = Replace(StrTmp,"'","\'")

    echo ("<script language=""javascript"">")
    echo ("function " & RndFun & "()")
    echo ("{")
    echo ("	var RndID = 'pro" & RndID & "';")
    echo ("	var StrGD = '" & StrTmp & "';")
    echo ("	Ok3w_Marquee(RndID,StrGD," & Width & "," & Height & "," & Speed & ");")
    echo ("}")
    echo (RndFun & "();")
    echo ("</script>")
    End Sub

*/
function Ok3w_Article_ImgGD($ClassID,$Rows,$Cels,$Width,$Height,$ImgW,$ImgH,$CharX,$IsCommend,$OrderType,$Speed)
{
    $Sql="select Id,Title,TitleColor,TitleURL,PicFile from Ok3w_Article ";
    $Sql.="where ChannelID=1 and IsPass=1 and IsDelete=0 and IsPic=1 " ;
    if($ClassID!="")
        $Sql.=" and SortPath like '%," . $ClassID . ",%'";
    if($IsCommend)
        $Sql.= " and IsCommend=1";
    switch ($OrderType)
    {
        case "hot":
            $Sql .= " order by Hits desc,AddTime desc,ID desc";
            break;
        case "rnd":
            $Sql .= " order by newid(),ID desc";
            break;
        case "new":
            $Sql .= " order by AddTime desc,ID desc";
            break;
        default:
            $Sql .= " order by IsTop desc,IsCommend desc,AddTime desc,ID desc";
            break;
    }

    $Sql.=" limit 0,".$Rows * $Cels;


    $mysqli=GetConn();
    $result = $mysqli->query($Sql);

    $pics="";
    $links="";
    $texts="";
    while ($row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $pics.=$row["PicFile"]."|";
        $links.= Format_TitleURL("/",$row["Id"], $row["Title"], $row["TitleColor"], $row["TitleURL"] ,50)."|";
        $texts.=$row["Title"]."|";
    }

    $str= <<<EOT
<script language="javascript">
function __Ok3w_Article_ImgFlash__008514()
{
    var pics="$pics";
    var links="$links";
    var texts="$texts";
    var focus_width=278;
    var focus_height=197;
    var text_height=18;
    var swf_height = focus_height + text_height;
    Ok3w_insertFlash("./", focus_width, focus_height, swf_height, text_height, pics, links, texts);
}
__Ok3w_Article_ImgFlash__008514();
</script>
EOT;

    return $str;
}
  /*  Randomize
    RndID = Int(Rnd()*10000)
    RndFun = "__Ok3w_Article_ImgGD__00" & RndID
    $Sql="select top " & Rows * Cels & " Id,Title,TitleColor,TitleURL,PicFile from Ok3w_Article where ChannelID=1 and IsPass=1 and IsDelete=0 and IsPic=1"
    ifClassID<>"" Then $Sql=$Sql & " and SortPath like '%," & ClassID & ",%'"
    ifIsCommend Then $Sql=$Sql & " and IsCommend=1"
    Select Case OrderType
        Case "hot"
            $Sql = $Sql & " order by Hits desc,AddTime desc,ID desc"
        Case "rnd"
            ifDb_Type = "$Sql" Then
                $Sql = $Sql & " order by newid(),ID desc"
            Else
                Randomize
                $Sql = $Sql & " order by Rnd(-(ID+"&Rnd()&")),ID desc"
            End If
        Case "new"
            $Sql = $Sql & " order by AddTime desc,ID desc"
        Case Else
            $Sql = $Sql & " order by IsTop desc,IsCommend desc,AddTime desc,ID desc"
    End Select
    StrTmp = ""
    Rs.Open $Sql,Conn,0,1
    StrTmp = "<table border=""0"" cellspacing=""5"" cellpadding=""0"">"
    Do While Not Rs.Eof
    StrTmp = StrTmp & "<tr>"
    For img=1 To Cels
    StrTmp = StrTmp & "<td align=""center"">"
    ifRs.Eof Then
        StrTmp = StrTmp & "&nbsp;"
    Else
        StrTmp = StrTmp & "<div style=""width:" & ImgW & "px; height:" & ImgH & "px; border:1px solid #CCCCCC; margin:0px 0px 5px 0px; padding:2px;"">"
        ifRs("TitleURL")="" Then
            uTmp = Page_URL(ChannelID,"",Rs("ID"))
        Else
            uTmp = Rs("TitleURL")
        End If
        StrTmp = StrTmp & "<a href=""" & uTmp & """ target=""_blank"">"
        StrTmp = StrTmp & "<img src=""" & ReplaceUpFilePath(Rs("PicFile")) & """ width=""" & ImgW & """ height=""" & ImgH & """ alt=""" & Rs("Title") & """ border=""0"" />"
        StrTmp = StrTmp & "</a>"
        StrTmp = StrTmp & "</div>"
        StrTmp = StrTmp & Format_TitleURL(HTML_Article_Path,Rs("ID"),Rs("Title"),Rs("TitleColor"),Rs("TitleURL"),CharX)
        Rs.MoveNext
    End If
    StrTmp = StrTmp & "</td>"
    Next
    StrTmp = StrTmp & "</tr>"
    Loop
    Rs.Close
    StrTmp = StrTmp & "</table>"

    StrTmp = Replace(StrTmp,"'","\'")

    echo ("<script language=""javascript"">")
    echo ("function " & RndFun & "()")
    echo ("{")
    echo  "var RndID = 'pro" & RndID & "';"
    echo  "var StrGD = '" & StrTmp & "';"
    echo  "Ok3w_Marquee(RndID,StrGD," & Width & "," & Height & "," & Speed & ");"
    echo  "}"
    echo  RndFun & "();"
    echo  "</script>"
    End Sub
    */

  function Ok3w_Article_ImgFlash($ClassID,$Width,$Height)
{

     echo <<<EOT


<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="278" height="215"><param name="allowScriptAccess" value="sameDomain"><param name="movie" value="./images/focus.swf"><param name="quality" value="high"><param name="bgcolor" value="#FFFFFF"><param name="menu" value="false"><param name="wmode" value="opaque"><param name="FlashVars" value="pics=upfiles/200911/20091112154545760.jpg|upfiles/200911/2009111215466814.jpg&amp;links=./show.asp?id=7|./show.asp?id=6&amp;texts=轮播新闻|轮播新闻&amp;borderwidth=278&amp;borderheight=197&amp;textheight=18"></object>

EOT;

}

function Ok3w_Article_Class_PageTitle($SortPath)
{
    $cTmp = substr($SortPath,0,-1);
    $Sql="select ID,SortName from Ok3w_Class where ID in(". $cTmp. ") order by ID desc";

    $mysqli=GetConn();
    $result = $mysqli->query($Sql);

    while ($row = $result->fetch_array(MYSQLI_ASSOC))
    {

        echo $row["SortName"]." - " ;

    }
}


function Ok3w_Article_Class_Nav($SortPath)
{
    $cTmp = substr($SortPath, 0, -1);
    $Sql = "select ID,SortName from Ok3w_Class where ID in(" . $cTmp . ") order by ID";

    $mysqli = GetConn();
    $result = $mysqli->query($Sql);
    while ($row = $result->fetch_array(MYSQLI_ASSOC))
    {
        echo "<a href='" . Page_URL(1, $row["ID"], "") . "'>" . $row["SortName"] . "</a> &gt;&gt; ";
    }
}


function Ok3w_Article_aList($ClassID, $ImgCels, $ImgW, $ImgH, $CharX)
{
    $Sql = "select * from Ok3w_Class where ParentID=" . $ClassID . " and gotoURL='' order by OrderID";
    $mysqli = GetConn();
    $result = $mysqli->query($Sql);
    $row1 = $result->fetch_array(MYSQLI_ASSOC);

    if ($row1)
    {
        $Sql = "select * from Ok3w_Class where ID=" . $ClassID;
        $res = $mysqli->query($Sql,MYSQLI_ASSOC);
        $row=mysqli_fetch_array($res);

        $IsPic = $row["IsPic"];
        $PageSize = $row["PageSize"];
        if ($IsPic == 1)
            Ok3w_Article_pList($ClassID, $PageSize, $ImgCels, $ImgW, $ImgH, $CharX);
        else
            Ok3w_Article_bList($ClassID, $PageSize);

    } else
        Ok3w_Article_sList($row1,8, $ImgCels, $ImgW, $ImgH, $CharX);
}

function Ok3w_Article_bList($ClassID, $TopN)
{
    echo "<table border='0' cellspacing='0' cellpadding='0' class='dragTable' width='100%'>   <tr> ";
    echo "  <td class='head'><h3 class='L'></h3>";
    echo "<span class='TAG'><a href='" . Page_URL(1, $ClassID, "") . ">" . GetClassName($ClassID) . "</a></span>";
    echo "<span class='more'><a href='" . Page_URL(1, $ClassID, "") . ">更多...</a></span></td>";
    echo "  </tr>   <tr>  <td class='middle'>";
    echo "<table width='100%' border='0' cellspacing='0' cellpadding='0' style='margin:5px 0px 5px 0px;'>  <tr><td>";
    echo "		</td>     </tr>    </table></td>  </tr> </table>";
}

function Ok3w_Article_sList($oRs, $TopN,$ImgCels,$ImgW,$ImgH,$CharX)
{
    $ChannelID=1;
    echo "<table border='0' cellspacing='0' cellpadding='0' class='dragTable' width='100%'>";
    echo "  <tr>";
    echo "    <td class='head'><h3 class='L'></h3>";
    echo "        <span class='TAG'><a href='" . Page_URL($ChannelID, $oRs["ID"], $ID) . ">";
    echo GetClassName($oRs["ID"]) . "</a></span><span class='more'><a href='";
    echo Page_URL($ChannelID, $oRs["ID"], $ID) . ">更多...</a></span></td>";
    echo "  </tr>";
    echo "  <tr>";
    echo "    <td class='middle'><table width='100%' border='0' cellspacing='0' cellpadding='0' style='margin:5px 0px 5px 0px;'>";
    echo "      <tr>";
    echo "        <td>";

    echo "	</td>";
    echo "      </tr>";
    echo "    </table></td>";
    echo "  </tr>";
    echo "</table>";
}

function Ok3w_Search_List($ClassID,$sType,$Keyword,$TopN)
{
    echo  "<br /><br /><h1>Sorry!没有找到任何相关内容。</h1><br /><br />";
}

    /*
Randomize
RndID = Int(Rnd()*10000)
RndFun = "__Ok3w_Article_ImgFlash__00" & RndID
echo  "<script language=""javascript"">"
echo  "function " & RndFun & "()"
echo  "{"
	$Sql="select top 5 Id,Title,PicFile,TitleColor,TitleURL from Ok3w_Article where ChannelID=1 and IsPass=1 and IsPlay=1 and IsDelete=0"
	ifClassID<>"" Then $Sql=$Sql & " and SortPath like '%," & ClassID & ",%'"
	$Sql=$Sql & " order by IsTop desc,IsCommend desc,Id desc"
	Rs.Open $Sql,Conn,0,1
	pics = ""
	links = ""
	texts = ""
	Do While Not Rs.Eof
		ID = Rs("ID")
		Title = Rs("Title")
		PicFile = ReplaceUpFilePath(Rs("PicFile"))
		TitleURL = Rs("TitleURL")
		ifTitleURL = "" Then TitleURL = Page_URL(ChannelID,"",ID)
		
		pics = pics & PicFile & "|"
		links = links & TitleURL & "|"
		texts = texts & Title & "|"
		Rs.MoveNext
	Loop
	Rs.Close
	iftexts<>"" Then
		pics = Left(pics,Len(pics)-1)
		links = Left(links,Len(links)-1)
		texts = Left(texts,Len(texts)-1)
	End If
	
echo  "var pics=""" & pics & """;"
echo  "var links=""" & links & """;"
echo  "var texts=""" & texts & """;"
	
echo  "var focus_width=" & Width & ";"
echo  "var focus_height=" & Height - 18 & ";"
echo  "var text_height=18;"
echo  "var swf_height = focus_height + text_height;"
	 
echo  "Ok3w_insertFlash(""" & Htmldns & """, focus_width, focus_height, swf_height, text_height, pics, links, texts);"
echo  "}"
echo  RndFun & "();"
echo  "</script>"
End Sub


Private Sub Ok3w_Article_aList(ClassID,ImgCels,ImgW,ImgH,CharX)
	$Sql="select * from Ok3w_Class where ParentID=" & ClassID & " and gotoURL='' order by OrderID"
	Set oRs = Conn.Execute($Sql)
	ifoRs.Eof And oRs.Bof Then
		oRs.Close
		Set oRs = Nothing
		
		$Sql="select * from Ok3w_Class where ID=" & ClassID
		Set oRs=Conn.Execute($Sql)
		IsPic = oRs("IsPic")
		PageSize = oRs("PageSize")
		ifIsPic=1 Then
			Call Ok3w_Article_pList(ClassID,PageSize,ImgCels,ImgW,ImgH,CharX)
			Else
				Call Ok3w_Article_bList(ClassID,PageSize)
		End If
	Else
		Call Ok3w_Article_sList(oRs,8,ImgCels,ImgW,ImgH,CharX)
		oRs.Close
		Set oRs = Nothing
	End If
End Sub


Private Sub Ok3w_Article_sList(ByRef oRs,TopN,ImgCels,ImgW,ImgH,CharX)
Do While Not oRs.Eof
echo ("<table border=""0"" cellspacing=""0"" cellpadding=""0"" class=""dragTable"" width=""100%"">")
echo ("  <tr>")
echo ("    <td class=""head""><h3 class=""L""></h3>")
echo ("        <span class=""TAG""><a href=""" & Page_URL(ChannelID,oRs("ID"),"") & """>" & GetClassName(oRs("ID")) & "</a></span><span class=""more""><a href=""" & Page_URL(ChannelID,oRs("ID"),"") & """>更多...</a></span></td>")
echo ("  </tr>")
echo ("  <tr>")
echo ("    <td class=""middle""><table width=""100%"" border=""0"" cellspacing=""0"" cellpadding=""0"" style=""margin:5px 0px 5px 0px;"">")
echo ("      <tr>")
echo ("        <td>")

ifoRs("IsPic") = 1 Then
echo ("<table width=""100%"" border=""0"" align=""center"" cellpadding=""0"" cellspacing=""0"">")
$Sql="select top " & TopN & " Id,Title,TitleColor,TitleURL,PicFile from Ok3w_Article where IsPass=1 and IsDelete=0 and SortPath like '%," & oRs("ID") & ",%' order by AddTime desc,ID desc"
Rs.Open $Sql,Conn,0,1
Do While Not Rs.Eof
echo ("<tr>")
For iCount = 1 To ImgCels  
echo ("<td width=""" & 100\ImgCels & "%"" align=""center"">")
ifRs.Eof Then
	echo ("&nbsp;")
Else
	ifRs("TitleURL")="" Then
		uTmp = Page_URL(ChannelID,"", Rs("ID"))
	Else
		uTmp = Rs("TitleURL")
	End If
    echo ("<a href=""" & uTmp & """ target=""_blank"">")
	echo ("<img src=""" & ReplaceUpFilePath(Rs("PicFile")) & """ alt=""" & Rs("Title") & """ width=""" & ImgW & """ height=""" & ImgH & """ border=""0"" class=""listimg"" /><br />")
    echo (LeftX(Rs("Title"),CharX*2) & "</a>")
	Rs.MoveNext
End If
echo ("</td>")
Next
echo ("</tr>")
Loop
Rs.Close
echo ("</table>")
'==========================================================
Else
Call Ok3w_Article_List(oRs("ID"),8,1,50,False,False,True,1,False,"new")
End If

echo ("		</td>")
echo ("      </tr>")
echo ("    </table></td>")
echo ("  </tr>")
echo ("</table>")

oRs.MoveNext
Loop
End Sub



Private Sub Ok3w_Article_bList(ClassID,TopN)
echo ("<table border=""0"" cellspacing=""0"" cellpadding=""0"" class=""dragTable"" width=""100%"">")
echo ("  <tr>")
echo ("    <td class=""head""><h3 class=""L""></h3>")
echo ("        <span class=""TAG""><a href=""" & Page_URL(ChannelID,ClassID,"") & """>" & GetClassName(ClassID) & "</a></span><span class=""more""><a href=""" & Page_URL(ChannelID,ClassID,"") & """>更多...</a></span></td>")
echo ("  </tr>")
echo ("  <tr>")
echo ("    <td class=""middle""><table width=""100%"" border=""0"" cellspacing=""0"" cellpadding=""0"" style=""margin:5px 0px 5px 0px;"">")
echo ("      <tr>")
echo ("        <td>")

$Sql="select Id,Title,Content,TitleColor,TitleURL,AddTime from Ok3w_Article where IsPass=1 and IsDelete=0 and SortPath like '%," & ClassID & ",%' order by AddTime desc,ID desc"
Call Page.GetRs(Conn,Rs,$Sql,TopN)
Do While Not Rs.Eof And Not Page.Eof
echo ("<div class=""s_tit"">" & Format_TitleURL(HTML_Article_Path,Rs("ID"),Rs("Title"),Rs("TitleColor"),Rs("TitleURL"),50) & "<span class=""list_title_r""> [" & Format_Time(Rs("AddTime"),2) & "]</span></div>")
echo ("<div class=""s_des"">" & Trim(Left(filterhtml(Rs("Content")),100)) & "...</div>")
Rs.MoveNext
Page.MoveNext
Loop
Rs.Close

Call Page.GetPageList()

echo ("		</td>")
echo ("      </tr>")
echo ("    </table></td>")
echo ("  </tr>")
echo ("</table>")
End Sub


Private Sub Ok3w_Article_pList(ClassID,TopN,Cels,ImgW,ImgH,CharX)
echo ("<table border=""0"" cellspacing=""0"" cellpadding=""0"" class=""dragTable"" width=""100%"">")
echo ("  <tr>")
echo ("    <td class=""head""><h3 class=""L""></h3>")
echo ("        <span class=""TAG""><a href=""" & Page_URL(ChannelID,ClassID,"") & """>" & GetClassName(ClassID) & "</a></span><span class=""more""><a href=""" & Page_URL(ChannelID,ClassID,"") & """>更多...</a></span></td>")
echo ("  </tr>")
echo ("  <tr>")
echo ("    <td class=""middle""><table width=""100%"" border=""0"" cellspacing=""0"" cellpadding=""0"" style=""margin:5px 0px 5px 0px;"">")
echo ("      <tr>")
echo ("        <td>")

echo ("<table width=""100%"" border=""0"" align=""center"" cellpadding=""0"" cellspacing=""0"">")
$Sql="select Id,Title,TitleColor,TitleURL,PicFile from Ok3w_Article where IsPass=1 and IsDelete=0 and SortPath like '%," & ClassID & ",%' order by AddTime desc,ID desc"
Call Page.GetRs(Conn,Rs,$Sql,TopN)
Do While Not Rs.Eof And Not Page.Eof
echo ("  <tr>")
For iCount = 1 To Cels  
echo ("    <td width=""" & 100\Cels & "%"" align=""center"">")
ifRs.Eof Or Page.Eof Then
	echo ("&nbsp;")
Else
	ifRs("TitleURL")="" Then
		uTmp = Page_URL(ChannelID,"",Rs("ID"))
	Else
		uTmp = Rs("TitleURL")
	End If
    echo ("<a href=""" & uTmp & """ target=""_blank"">")
	echo ("<img src=""" & ReplaceUpFilePath(Rs("PicFile")) & """ alt=""" & Rs("Title") & """ width=""" & ImgW & """ height=""" & ImgH & """ border=""0"" class=""listimg"" /><br />")
    echo (LeftX(Rs("Title"),CharX * 2) & "</a>")
	Rs.MoveNext
	Page.MoveNext
End If
echo ("</td>")
Next
echo ("  </tr>")
Loop
Rs.Close
echo ("</table>")
echo ("<div style=""border-bottom:1px dotted #CCCCCC; margin:8px 0px;""></div>")

Call Page.GetPageList()

echo ("		</td>")
echo ("      </tr>")
echo ("    </table></td>")
echo ("  </tr>")
echo ("</table>")
End Sub


Private Sub Ok3w_Search_List(ClassID,sType,Keyword,TopN)
$Sql="select Id,Title,TitleColor,TitleURL,Content,AddTime from Ok3w_Article where IsPass=1 and IsDelete=0 and ChannelID=1"
Select Case sType
	Case "2"
		$Sql = $Sql & " and Content like '%" & Keyword & "%'"
	Case Else
		$Sql = $Sql & " and Title like '%" & Keyword & "%'"
End Select
$Sql = $Sql & " order by AddTime desc,ID desc"
Call Page.GetRs(Conn,Rs,$Sql,TopN)
ifNot(Rs.Eof And Rs.Bof) Then
Do While Not Rs.Eof And Not Page.Eof

echo ("<div class=""s_tit"">" & Format_TitleURL(HTML_Article_Path,Rs("ID"),Rs("Title"),Rs("TitleColor"),Rs("TitleURL"),50) & "<span class=""list_title_r""> [" & Format_Time(Rs("AddTime"),2) & "]</span></div>")
echo ("<div class=""s_des"">" & Trim(Left(filterhtml(Rs("Content")),100)) & "...</div>")

Rs.MoveNext
Page.MoveNext
Loop
Call Page.GetPageList()
Else
echo ("<br /><br /><h1>Sorry!没有找到任何相关内容。</h1><br /><br />")
End If
Rs.Close
End Sub


Private Sub Ok3w_Article_SmallClass(ClassID)	
	$Sql="select ID,SortName,gotoURL from Ok3w_Class where ParentID=" & ClassID & " and IsNav=1 order by OrderID"
	Rs.Open $Sql,Conn,0,1
	ifRs.Eof And Rs.Bof Then
		$Sql = "select ParentID from Ok3w_Class where ID=" & ClassId
		a_ParentID = Exec$SqlReturnOneValue($Sql)
		ifa_ParentID<>"" Then
		ifa_ParentID<>0 Then
			Rs.Close
			$Sql="select ID,SortName,gotoURL from Ok3w_Class where ParentID=" & a_ParentID & " and IsNav=1 order by OrderID"
			Rs.Open $Sql,Conn,0,1
		End If
		End If
	End If
	ifNot Rs.Eof Then
	echo ("<strong style=""font-size:12px;"">" & GetParentClassName(ClassID) & "<span>&gt;&gt;</span></strong>")
	Do While Not Rs.Eof
		ifRs("gotoURL")="" Then
			echo ("<a href=""" & Page_URL(ChannelID,Rs("ID"),"") & """>" & Rs("SortName") & "</a>")
		Else
			echo ("<a href=""" & Rs("gotoURL") & """ target=""_blank"">" & Rs("SortName") & "</a>")
		End If
		Rs.MoveNext
		ifNot Rs.Eof Then echo ("<span>|</span>")
	Loop
	End If
	Rs.Close
End Sub


Private Sub Ok3w_Article_Class_Nav(SortPath)
cTmp = Left(SortPath,Len(SortPath)-1)
$Sql="select ID,SortName from Ok3w_Class where ID in(" & cTmp & ") order by ID"
Set oRs = Conn.Execute($Sql)
Do While Not oRs.Eof
	echo ("<a href=""" & Page_URL(ChannelID,oRs("ID"),"") & """>" & oRs("SortName") & "</a>")
	oRs.MoveNext
	ifNot oRs.Eof Then echo (" &gt;&gt; ")
Loop
oRs.Close
Set oRs = Nothing
End Sub


Private Sub Ok3w_Article_Class_PageTitle(SortPath)
cTmp = Left(SortPath,Len(SortPath)-1)
$Sql="select ID,SortName from Ok3w_Class where ID in(" & cTmp & ") order by ID desc"
Set oRs= Conn.Execute($Sql)
Do While Not oRs.Eof
	echo (oRs("SortName"))
	oRs.MoveNext
	ifNot oRs.Eof Then echo (" - ")
Loop
oRs.Close
Set oRs = Nothing
End Sub

Private Sub Ok3w_Article_IndexClassImg(ClassID,ww,hh,TopN,LeftN)
	$Sql="select top " & TopN & " Id,Title,TitleColor,TitleURL,Description,PicFile from Ok3w_Article where ChannelID=1 and IsPass=1 and IsDelete=0 and IsIndexImg=1"
	ifClassID<>"" Then $Sql=$Sql & " and SortPath like '%," & ClassID & ",%'"
	$Sql=$Sql & " order by IsTop desc,IsCommend desc,AddTime desc,Id desc"
	Rs.Open $Sql,Conn,0,1
	ifRs.Eof And Rs.Bof Then
	Else
		Do While Not Rs.Eof
			echo ("<div class=""indeclassimg"" style=""height:" & hh & "px;"">")
			ifRs("TitleURL")="" Then
				TitleURL = Page_URL(ChannelID,"",Rs("ID"))
			Else
				TitleURL = Rs("TitleURL")
			End If
			echo ("<a href=""" & TitleURL & """ title=""" & Rs("Title") & """ target=""_blank"">")
			echo ("<img src=""" & Rs("PicFile") & """ width=""" & ww & """ height=""" & hh & """ border=""0"" />")
			echo ("<strong>" & LeftX(Rs("Title"),LeftN*2) & "</strong><br />" & OutStr(Rs("Description")))
			echo ("</a>")
			echo ("</div>")
			Rs.MoveNext
		Loop
	End If
	Rs.Close
End Sub

Private Sub Ok3w_Article_Hits(ID)
echo ("<span id=""Article_Hits""></span><iframe style=""display:none;"" src=""" & Htmldns & "c/hits.php?type=news&id=" & ID & """></iframe>")
End Sub



Private Sub Ok3w_Soft_List(ClassID,Rows,Cels,LeftN,IsCommend,DisClass,DisTime,TimeFormat,DisHits,OrderType)
$Sql="select top " & Rows * Cels & " ID,SoftName,TitleColor,TitleURL,ClassID,Updatetime,Hits from Ok3w_Soft where ChannelID=3 and IsPass=1 and IsDelete=0"
ifClassID<>"" Then $Sql=$Sql & " and SortPath like '%," & ClassID & ",%'"
ifIsCommend Then $Sql=$Sql & " and IsCommend=1"
Select Case OrderType
	Case "hot"
		$Sql = $Sql & " order by Hits desc,Updatetime desc,ID desc"
	Case "rnd"
		ifDb_Type = "$Sql" Then
			$Sql = $Sql & " order by newid(),ID desc"
		Else
			Randomize
			$Sql = $Sql & " order by Rnd(-(ID+"&Rnd()&")),ID desc"
		End If
	Case "new"
		$Sql = $Sql & " order by Updatetime desc,ID desc"
	Case Else
		$Sql = $Sql & " order by IsTop desc,IsCommend desc,Updatetime desc,ID desc"
End Select
Rs.Open $Sql,Conn,0,1
echo ("<table width=""100%"" border=""0"" cellspacing=""0"" cellpadding=""0"">")
Do While Not Rs.Eof
echo ("<tr>")
For II=1 To  Cels
ifRs.Eof Then
echo ("<td>&nbsp;</td>")
ifDisHits Or DisTime Then echo ("<td>&nbsp;</td>")
Else
echo ("<td class=""list_title"">")
ifDisClass Then
	echo ("[<a href=""" & Page_URL(ChannelID, Rs("ClassID"),"") & """>" & GetClassName(Rs("ClassID")) & "</a>]")
End If
echo (Format_TitleURL(HTML_Soft_Path,Rs("ID"),Rs("SoftName"),Rs("TitleColor"),Rs("TitleURL"),LeftN))
echo ("</td>")
ifDisHits Or DisTime Then echo ("<td class=""list_title_r"">")
ifDisHits Then
	echo ("下载:" & Rs("Hits") & " ")
End If
ifDisTime Then
	echo (Format_Time(Rs("Updatetime"),TimeFormat))
End If
ifDisHits Or DisTime Then echo ("</td>")
Rs.MoveNext
End If
ifCels<>1 And II<>Cels Then
	echo ("<td>&nbsp;</td>")
End If
Next
echo ("</tr>")
Loop
Rs.Close 
echo ("</table>")
End Sub


Private Sub Ok3w_Soft_ImgFlash(ClassID,Width,Height)
Randomize
RndFun = "__Ok3w_Soft_ImgFlash__00" & Int(Rnd()*10000)
echo ("<script language=""javascript"">")
echo ("function " & RndFun & "()")
echo ("{")
	$Sql="select top 5 ID,SoftName,Softimageurl,TitleColor,TitleURL from Ok3w_Soft where ChannelID=3 and Softimageurl<>'' and IsPlay=1 and IsDelete=0"
	ifClassID<>"" Then $Sql=$Sql & " and SortPath like '%," & ClassID & ",%'"
	$Sql=$Sql & " order by IsTop desc,IsCommend desc,Id desc"
	Rs.Open $Sql,Conn,0,1
	pics = ""
	links = ""
	texts = ""
	Do While Not Rs.Eof
		ID = Rs("ID")
		Title = Rs("SoftName")
		PicFile = Htmldns & Rs("Softimageurl")
		TitleURL = Rs("TitleURL")
		ifTitleURL = "" Then TitleURL = Page_URL(ChannelID,"",ID)
		
		pics = pics & PicFile & "|"
		links = links & TitleURL & "|"
		texts = texts & Title & "|"
		Rs.MoveNext
	Loop
	Rs.Close
	iftexts<>"" Then
		pics = Left(pics,Len(pics)-1)
		links = Left(links,Len(links)-1)
		texts = Left(texts,Len(texts)-1)
	End If
	
echo  "var pics=""" & pics & """;"
echo  "var links=""" & links & """;"
echo  "var texts=""" & texts & """;"
	
echo  "var focus_width=" & Width & ";"
echo  "var focus_height=" & Height - 18 & ";"
echo  "var text_height=18;"
echo  "var swf_height = focus_height + text_height;"
	 
echo  "Ok3w_insertFlash(""" & Htmldns & """, focus_width, focus_height, swf_height, text_height, pics, links, texts);"
echo  "}"
echo  RndFun & "();"
echo  "</script>" 
End Sub
%>

<%
Private Sub Ok3w_Soft_aList(ClassID,ImgCels,ImgW,ImgH,CharX)
	$Sql="select * from Ok3w_Class where ID=" & ClassID
	Set oRs=Conn.Execute($Sql)
	IsPic = oRs("IsPic")
	PageSize = oRs("PageSize")
	ifIsPic=1 Then
		Call Ok3w_Soft_pList(ClassID,PageSize,ImgCels,ImgW,ImgH,CharX)
	Else
		Call Ok3w_Soft_sList(ClassID,PageSize)
	End If
End Sub

Private Sub Ok3w_Soft_sList(ClassID,TopN)
$Sql="select * from Ok3w_Soft where IsPass=1 and IsDelete=0 and SortPath like '%," & ClassID & ",%' order by Updatetime desc,ID desc"
Call Page.GetRs(Conn,Rs,$Sql,TopN)

echo ("<table border=""0"" cellspacing=""0"" cellpadding=""0"" class=""dragTable"" width=""100%"" style=""margin-top:0px;"">")
echo ("  <tr>")
echo ("    <td class=""head""><h3 class=""L""></h3>")
echo ("        <span class=""TAG""><a href=""" & Page_URL(ChannelID,ClassID,"") & """>" & GetClassName(ClassID) & "</a></span><span class=""more""><a href=""" & Page_URL(ChannelID,ClassID,"") & """>更多...</a></span></td>")
echo ("  </tr>")
echo ("  <tr>")
echo ("    <td class=""middle""><table width=""100%"" border=""0"" cellspacing=""0"" cellpadding=""0"" style=""margin:5px 0px 5px 0px;"">")
echo ("      <tr>")
echo ("        <td>")

Do While Not Rs.Eof And Not Page.Eof

echo ("<div class=""s_tit"">" & Format_TitleURL(HTML_Soft_Path,Rs("ID"),Rs("SoftName"),Rs("TitleColor"),Rs("TitleURL"),50) & "</div>")
echo ("<div class=""s_kk"">日期：" & Rs("Updatetime") & " 分类：" & GetClassName(Rs("ClassID")) & " 大小：" & Rs("Softsize") & Rs("Softsizeunit") & " 语言：" & Rs("Softlanguage") & " 授权：" & Rs("Softlicence") & "</div>")
echo ("<div class=""s_des"">" & Left(Trim(Replace(filterhtml(Rs("Softintro"))," ","")),100) & "...</div>")
	
	Rs.MoveNext
	Page.MoveNext
Loop
Rs.Close

Call Page.GetPageList()

echo ("		</td>")
echo ("      </tr>")
echo ("    </table></td>")
echo ("  </tr>")
echo ("</table>")
End Sub


Private Sub Ok3w_Soft_pList(ClassID,TopN,Cels,ImgW,ImgH,CharX)
echo ("<table border=""0"" cellspacing=""0"" cellpadding=""0"" class=""dragTable"" width=""100%"" style=""margin-top:0px;"">")
echo ("  <tr>")
echo ("    <td class=""head""><h3 class=""L""></h3>")
echo ("        <span class=""TAG""><a href=""" & Page_URL(ChannelID,ClassID,"") & """>" & GetClassName(ClassID) & "</a></span><span class=""more""><a href=""" & Page_URL(ChannelID,ClassID,"") & """>更多...</a></span></td>")
echo ("  </tr>")
echo ("  <tr>")
echo ("    <td class=""middle""><table width=""100%"" border=""0"" cellspacing=""0"" cellpadding=""0"" style=""margin:5px 0px 5px 0px;"">")
echo ("      <tr>")
echo ("        <td>")

echo ("<table width=""100%"" border=""0"" align=""center"" cellpadding=""0"" cellspacing=""0"">")
$Sql="select ID,SoftName,Softimageurl,TitleColor,TitleURL from Ok3w_Soft where IsPass=1 and IsDelete=0 and SortPath like '%," & ClassID & ",%' order by Updatetime desc,ID desc"
Call Page.GetRs(Conn,Rs,$Sql,TopN)
Do While Not Rs.Eof And Not Page.Eof
echo ("  <tr>")
For iCount = 1 To Cels  
echo ("    <td width=""" & 100\Cels & "%"" align=""center"">")
ifRs.Eof Or Page.Eof Then
	echo ("&nbsp;")
Else
	ifRs("TitleURL")="" Then
		aTmp = Page_URL(ChannelID,"",Rs("ID"))
	Else
		aTmp = Rs("TitleURL")
	End If
	echo ("<a href=""" & aTmp & """ target=""_blank""><img src=""" & ReplaceUpFilePath(Rs("Softimageurl")) & """ alt=""" & Rs("SoftName") & """ width=""" & ImgW & """ height=""" & ImgH & """ border=""0"" class=""listimg"" /><br />")
    echo (LeftX(Rs("SoftName"),CharX*2) & "</a>")
	Rs.MoveNext
	Page.MoveNext
End If
echo ("</td>")
Next	
echo ("  </tr>")
Loop
Rs.Close
echo ("</table>")

echo ("<div style=""border-bottom:1px dotted #CCCCCC; margin:8px 0px;""></div>")

Call Page.GetPageList()

echo ("		</td>")
echo ("      </tr>")
echo ("    </table></td>")
echo ("  </tr>")
echo ("</table>")
End Sub


Private Sub Ok3w_Soft_Search(ClassID,sType,Keyword,TopN)
$Sql="select * from Ok3w_Soft where IsPass=1 and IsDelete=0 and ChannelID=3"
Select Case sType
	Case "2"
		$Sql = $Sql & " and Softintro like '%" & Keyword & "%'"
	Case Else
		$Sql = $Sql & " and SoftName like '%" & Keyword & "%'"
End Select
$Sql = $Sql & " order by Updatetime desc,ID desc"
Call Page.GetRs(Conn,Rs,$Sql,TopN)
ifNot(Rs.Eof And Rs.Bof) Then
Do While Not Rs.Eof And Not Page.Eof

echo ("<div class=""s_tit"">" & Format_TitleURL(HTML_Soft_Path,Rs("ID"),Rs("SoftName"),Rs("TitleColor"),Rs("TitleURL"),50) & "</div>")
echo ("<div class=""s_kk"">日期：" & Rs("Updatetime") & " 分类：" & GetClassName(Rs("ClassID")) & " 大小：" & Rs("Softsize") & Rs("Softsizeunit") & " 语言：" & Rs("Softlanguage") & " 授权：" & Rs("Softlicence") & "</div>")
echo ("<div class=""s_des"">" & Left(Trim(Replace(filterhtml(Rs("Softintro"))," ","")),100) & "...</div>")

	Rs.MoveNext
	Page.MoveNext
Loop
Call Page.GetPageList()
Else
echo ("<br /><br /><h1>Sorry!没有找到任何相关内容。</h1><br /><br />")
End If
Rs.Close
End Sub


Private Sub Ok3w_Soft_Class_Nav(SortPath)
cTmp = Left(SortPath,Len(SortPath)-1)
$Sql="select ID,SortName from Ok3w_Class where ID in(" & cTmp & ") order by ID"
Set oRs = Conn.Execute($Sql)
Do While Not oRs.Eof
	echo ("<a href=""" & Page_URL(ChannelID,oRs("ID"),"") & """>" & oRs("SortName") & "</a>")
	oRs.MoveNext
	ifNot oRs.Eof Then echo (" &gt;&gt; ")
Loop
oRs.Close
Set oRs = Nothing
End Sub


Private Sub Ok3w_Soft_Class_PageTitle(SortPath)
cTmp = Left(SortPath,Len(SortPath)-1)
$Sql="select ID,SortName from Ok3w_Class where ID in(" & cTmp & ") order by ID desc"
Set oRs= Conn.Execute($Sql)
Do While Not oRs.Eof
	echo (oRs("SortName"))
	oRs.MoveNext
	ifNot oRs.Eof Then echo (" - ")
Loop
oRs.Close
Set oRs = Nothing
End Sub

Private Sub Ok3w_DisNextClass(ChannelID,ClassID)
echo ("<table width=""100%"" border=""0"" cellspacing=""0"" cellpadding=""0"">")
$Sql="select ID,SortName,gotoURL from Ok3w_Class where ChannelID=" & ChannelID & "and  ParentID=" & ClassID & " and IsNav=1 order by OrderID"
Rs.Open $Sql,Conn,0,1
ifRs.Eof And Rs.Bof Then
	$Sql = "select ParentID from Ok3w_Class where ChannelID=" & ChannelID & " and ID=" & ClassId
	a_ParentID = Exec$SqlReturnOneValue($Sql)
	Rs.Close
	$Sql="select ID,SortName,gotoURL from Ok3w_Class where ChannelID=" & ChannelID & " and ParentID=" & a_ParentID & " and IsNav=1 order by OrderID"
	Rs.Open $Sql,Conn,0,1
End If
Do While Not Rs.Eof	  
echo ("<tr>")
For i=1 To 2		
echo ("<td><div class=""a_class"">")
ifRs.Eof Then
	echo ("&nbsp;")
Else
ifRs("gotoURL")="" Then
	echo ("<a href=""" & Page_URL(ChannelID,Rs("ID"),"") & """>" & Rs("SortName") & "</a>")
Else
	echo ("<a href=""" & Rs("gotoURL") & """ target=""_blank"">" & Rs("SortName") & "</a>")
End If
Rs.MoveNext
End If
echo ("</div></td>")
ifi<>2 Then echo ("<td width=""8""></td>")
Next
echo ("</tr>")
ifNot Rs.Eof Then echo ("<tr height=""8""><td colspan=""3""></td></tr>")
Loop
Rs.Close
echo ("</table>")
End Sub
*/

function Ok3w_Site_Link($TopN,$LeftN,$oT,$cT)
{

    $StrTmp="";
    $Sql = "select  * from Ok3w_Link where Ltype=1 and Ctype=". $cT . " order by Lorder,Lid limit 0,".$TopN;
    $mysqli=GetConn();
    $result = $mysqli->query($Sql);
    while ($row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $StrTmp .="<a href='" . $row["Lurl"] . "' target='_blank'><img src='";
        $StrTmp .=$row["Lpic"] . "' alt='" . $row["Lname"] . "' width='88' height='31' border='0' /></a> ";
    }
    return $StrTmp;
}
    /*
StrTmp = ""
ifoT = 1 Then
$Sql = "select top " & TopN & " * from Ok3w_Link where Ltype=1 and Ctype=" & cT & " order by Lorder,Lid"
Rs.Open $Sql,Conn,0,1
Do While Not Rs.Eof
	StrTmp = StrTmp & "<a href=""" & Rs("Lurl") & """ target=""_blank""><img src=""" & Rs("Lpic") & """ alt=""" & Rs("Lname") & """ width=""88"" height=""31"" border=""0"" /></a> "
	Rs.MoveNext
Loop
Rs.Close
End If
ifoT=0 Then
$Sql = "select top " & TopN & " * from Ok3w_Link where Ltype=0 and Ctype=" & cT & "  order by Lorder,Lid"
Rs.Open $Sql,Conn,0,1
Do While Not Rs.Eof
	StrTmp = StrTmp & "<a href=""" & Rs("Lurl") & """ target=""_blank"">" & Rs("Lname") & "</a>"
	Rs.MoveNext
	ifNot Rs.Eof Then StrTmp = StrTmp & "<span>|</span>"
Loop
Rs.Close
End If
echo (StrTmp)
End Sub


Function Ok3w_Site_Tongji()
	echo (Application(SiteID & "_Ok3w_SiteTongji"))
End Function

*/
?>