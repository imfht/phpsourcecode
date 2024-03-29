<%
Class Ok3w_Class
    Public ID
    Public ChannelID
    Public SortName
    Public ParentID
    Public SortPath
    Public OrderID
	Public IsPic
	Public PageSize
	Public IsNav
	Public gotoURL
	
	Public Function IsHaveNextClass(ClassId)
		Sql = "select count(ID) from Ok3w_Class where ParentID=" & ClassId
		If Conn.Execute(Sql)(0)=0 Then 
			IsHaveNextClass = False
			Else
				IsHaveNextClass = True
		End If
	End Function
	
    '添加记录
    Public Sub Add()
        Call GetFormData()
'        Sql = "select count(*) from Ok3w_Class where SortName='" & SortName & "' and ParentID=" & ParentID
'        If Conn.Execute(Sql)(0)<>0 Then
'			Session("ErrMsg") = "相同的分类已经存在，请不要重复添加。"
'			Call ActionOk("Class_Manage.asp")
'        End If
        Set oRs = Server.CreateObject("Adodb.RecordSet")
        Sql = "select * from Ok3w_Class where 1=2"
        oRs.Open Sql,Conn,1,3
        oRs.AddNew()
        Call UpdateRs(oRs)
        oRs.Update()
        oRs.Close()
        Set oRs = Nothing
		
		ID = Conn.Execute("select max(ID) from Ok3w_Class")(0)
		Sql = "update Ok3w_Class set SortPath=SortPath+'" & ID & ",' where ID=" & ID
		Conn.Execute Sql
    End Sub
	
    '修改记录
    Public Sub Edit()
        Call GetFormData()
'        Sql = "select count(*) from Ok3w_Class where SortName='" & SortName & "'  and ParentID=" & ParentID & " and ID<>" & ID
'        If Conn.Execute(Sql)(0)<>0 Then
'           	Session("ErrMsg") = "相同的分类已经存在，请不要重复添加。"
'			Call ActionOk("Class_Manage.asp")
'        End If
        Set oRs = Server.CreateObject("Adodb.RecordSet")
        Sql = "select * from Ok3w_Class where ID= " & ID
        oRs.Open Sql,Conn,1,3
        Call UpdateRs(oRs)
        oRs.Update
        oRs.Close
        Set oRs = Nothing
    End Sub
	
    '删除记录
    Public Sub Del()
		Call GetFormData()
		
		Select Case ChannelID
			Case 1,2
				Sql = "select ID from Ok3w_Article where SortPath like '%," & ID & ",%'"
				Set oRs = Conn.Execute(Sql)
				IDS = ""
				Do While Not oRs.Eof					
					IDS = IDS & oRs("ID")
					oRs.MoveNext
					If Not oRs.Eof Then IDS = IDS & ","
				Loop
				oRs.Close
				Set oRs = Nothing
				
				If IDS<>"" Then
					Call Delete_ArticleHTML(IDS)
					
					Sql = "delete from Ok3w_Guest where TypeID=2 and pID in(" & IDS & ")"
					Conn.execute Sql
				End If
				
				Sql = "delete from Ok3w_Article where  SortPath like '%," & ID & ",%'"
				Conn.Execute Sql
			
			Case 3
				Sql = "select ID from Ok3w_Soft where SortPath like '%," & ID & ",%'"
				Set oRs = Conn.Execute(Sql)
				IDS = ""
				Do While Not oRs.Eof
					IDS = IDS & oRs("ID")
					oRs.MoveNext
					If Not oRs.Eof Then IDS = IDS & ","
				Loop
				oRs.Close
				Set oRs = Nothing
				
				If IDS<>"" Then
					Call Delete_SoftHTML(IDS)
					
					Sql = "delete from Ok3w_Guest where TypeID=3 and pID in(" & IDS & ")"
					Conn.execute Sql
				End If
				
				Sql = "delete from Ok3w_Soft where SortPath like '%," & ID & ",%'"
				Conn.Execute Sql
		End Select

		Sql = "delete from Ok3w_Class where  SortPath like '%," & ID & ",%'"
		Conn.Execute Sql
		
		Sql = "delete from Ok3w_Class where  ID=" & ID
		Conn.Execute Sql
    End Sub
	
    '接收表单
    Private Sub GetFormData()
		ID = Request.Form("ID")
		If ID="" Then ID = GetMaxClassID() + 1
        ChannelID = Request.Form("ChannelID")
		OrderID = Request.Form("OrderID")
		ParentID = Request.Form("ParentID")
        SortName = Request.Form("SortName")
        SortPath = Request.Form("SortPath")
		IsPic = Request.Form("IsPic")
		If IsPic = "" Then IsPic = 0
		PageSize = Request.Form("PageSize")
		If PageSize = "" Then PageSize = 20
		IsNav = Request.Form("IsNav")
		If IsNav = "" Then IsNav = 0
		gotoURL = Request.Form("gotoURL")
    End Sub
	
    '更新记录集
    Private Sub UpdateRs(ByRef Rs)
		Rs("ID") = ID
        Rs("ChannelID") = ChannelID
        Rs("SortName") = SortName
        Rs("ParentID") = ParentID
        Rs("SortPath") = SortPath
        Rs("OrderID") = OrderID
		Rs("IsPic") = IsPic
		Rs("PageSize") = PageSize
		Rs("IsNav") = IsNav
		Rs("gotoURL") = gotoURL
    End Sub
	
    '从记录集中读数据
    Private Sub GetRs(ByRef Rs)
        ID = Rs("ID")
        ChannelID = Rs("ChannelID")
        SortName = Rs("SortName")
        ParentID = Rs("ParentID")
        SortPath = Rs("SortPath")
        OrderID = Rs("OrderID")
		IsPic = Rs("IsPic")
		PageSize = Rs("PageSize")
		IsNav = Rs("IsNav")
		gotoURL = Rs("gotoURL")
    End Sub
	
	Public Function GetMaxClassID()
        Set clsRs = Server.CreateObject("Adodb.RecordSet")
        Sql = "select max(ID) from Ok3w_Class"
        clsRs.Open Sql,Conn,0,1
        If IsNull(clsRs(0)) Then
        GetMaxClassID = 0
            Else
                GetMaxClassID = clsRs(0)
        End If
        clsRs.Close
        Set clsRs = Nothing
    End Function
	
	Public Function GetMaxClassOrder(ParentID)
        Set clsRs = Server.CreateObject("Adodb.RecordSet")
        Sql = "select max(OrderID) from Ok3w_Class where ParentID=" & ParentID
        clsRs.Open Sql,Conn,0,1
        If IsNull(clsRs(0)) Then
        GetMaxClassOrder = 0
            Else
                GetMaxClassOrder = clsRs(0)
        End If
        clsRs.Close
        Set clsRs = Nothing
    End Function
	
	Private Sub ClassToJs(ParentID,ChkID)
		Dim oRs,cTmp,cLen,cCount,Tmp
		Set oRs = Server.CreateObject("Adodb.RecordSet")
		Sql = "select ID,ChannelId,SortName,SortPath from Ok3w_Class where ParentID=" & ParentID & " and gotoURL='' order by ChannelId,ParentID,OrderID"
		oRs.Open Sql,Conn,0,1
		Do While Not oRs.Eof
			ChkID = ChkID + 1
			Tmp = ""
			Tmp = "Ok3w_ClassArr[" & ChkID & "] = new Array (" & oRs("ChannelId") & "," & oRs("ID") & ","""
			cTmp = Split(oRs("SortPath"),",")
			cLen = Ubound(cTmp) - 2
			For cCount=1 To cLen
				Tmp = Tmp & "│ "
			Next
			Tmp = Tmp & "├" & oRs("SortName") & """);" & vbCrLf
			Ok3w_Public_StrTmp = Ok3w_Public_StrTmp & Tmp
			Call ClassToJs(oRs("ID"),ChkID)
			oRs.MoveNext
		Loop
		oRs.Close
		Set oRs = Nothing
	End Sub
	
	Public Sub Html_ClassJs()
		Ok3w_Public_StrTmp = "var Ok3w_ClassArr = new Array();" & vbCrLf
		Call ClassToJs(0,-1)
		Call WriteFile(Server.MapPath(dbdns & "js/class.js"),Ok3w_Public_StrTmp)
		Ok3w_Public_StrTmp = ""
	End Sub
End Class
%>	