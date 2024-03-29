<%
Class Ok3w_Article
    Public Id
    Public ChannelID
    Public ClassID
	Public SortPath
    Public Title
	Public TitleColor
	Public TitleURL
	Public Keywords
	Public Description
    Public Content
    Public Author
    Public ComeFrom
    Public AddTime
    Public Inputer
    Public IsPass
    Public IsPic
    Public PicFile
    Public IsTop
    Public IsCommend
	Public IsDelete
	Public IsMove
	Public IsPlay
	Public IsIndexImg
	Public IsUserAdd
	Public GiveJifen
	Public vUserGroupID
	Public vUserMore
	Public vUserJifen
	Public pMoodStr
    Public Hits
	
    '类初始化
    Private Sub Class_Initialize()
		vUserMore = 1
		vUserJifen = 0
		IsPass = 1
		pMoodStr = "0,0,0,0,0,0,0,0"
		Hits = 0
		AddTime = Now()
		If Session(SiteID & "_Ok3w_Tmp_ComeFrom") = "" Then
			Session(SiteID & "_Ok3w_Tmp_ComeFrom") = Application(SiteID & "_Ok3w_SiteName")
		End If
		Inputer = Session(SiteID & "_Ok3w.Net_AdminName")
		ComeFrom = Session(SiteID & "_Ok3w_Tmp_ComeFrom")
		Author = Application(SiteID & "_Ok3w_SiteName")
    End Sub
    '类终止
    Private Sub Class_Terminate()
    End Sub
	
	'读记录
	Public Sub Load(Id)
		Set clsRs = Server.CreateObject("Adodb.RecordSet")
		Sql = "select * from Ok3w_Article where Id=" & Id
        clsRs.Open Sql,Conn,1,1
		If clsRs.Eof And clsRs.Bof Then
			Response.Write("资源不存在")
			Response.End()
		End If
        Call GetRs(clsRs)
        clsRs.Close()
        Set clsRs = Nothing
	End Sub
	
	Public Sub HitsAdd(Id)
		Sql = "update Ok3w_Article set Hits = Hits + 1 where Id=" & Id
		Conn.Execute Sql
	End Sub
	
    Public Sub Add()
        Call GetFormData()
        Set clsRs = Server.CreateObject("Adodb.RecordSet")
        Sql = "select * from Ok3w_Article where 1=2"
        clsRs.Open Sql,Conn,1,3
        clsRs.AddNew()
        Call UpdateRs(clsRs)
        clsRs.Update()
        clsRs.Close()
        Set clsRs = Nothing
    End Sub

    Public Sub Edit()
        Call GetFormData()
        Set clsRs = Server.CreateObject("Adodb.RecordSet")
        Sql = "select * from Ok3w_Article where Id= " & Id
        clsRs.Open Sql,Conn,1,3
        Call UpdateRs(clsRs)
        clsRs.Update
        clsRs.Close
        Set Rs = Nothing
		
		If IsPass=1 And IsUserAdd=1 And GiveJifen=0 Then Call User_Give_Jifen(Id)
    End Sub
    
    Private Sub GetFormData()
        Id = Request.QueryString("Id")
		If Id = "" Then Id=GetMaxArticleID()+1
        ChannelID = Request.QueryString("ChannelID")
        ClassID = Request.Form("ClassID")
		SortPath = GetSortPath(ClassId)
        Title = Request.Form("Title")
		TitleColor = Request.Form("TitleColor")
		TitleURL = Request.Form("TitleURL")
		Keywords = Request.Form("Keywords")
		Description = Request.Form("Description")
		
		For i = 1 To Request.Form("Content").Count 
			Content = Content & Request.Form("Content")(i) 
		Next
		If Request.Form("eWebEditorUpFile") = "1" Then
			ePATH_INFO = Request.ServerVariables("PATH_INFO")
			eTmp = Split(ePATH_INFO,"/")
			ePATH_INFO = ""
			For ee=0 To Ubound(eTmp)-2
				ePATH_INFO = ePATH_INFO + eTmp(ee) + "/"
			Next
			Content = Replace(Content,"../upfiles/","upfiles/")
			Content = Replace(Content,"../editor/","editor/")
			Content = Replace(Content,ePATH_INFO & "upfiles/","upfiles/")
			Content = Replace(Content,ePATH_INFO & "editor/","editor/")
		End If
		
        Author = Request.Form("Author")
        ComeFrom = Request.Form("ComeFrom")
		If ComeFrom<>Session(SiteID & "_Ok3w_Tmp_ComeFrom") Then
			Session(SiteID & "_Ok3w_Tmp_ComeFrom") = ComeFrom
		End If
        AddTime = Request.Form("AddTime")
		Inputer = Request.Form("Inputer")
		If Inputer="" Then Inputer = Admin.AdminName
        IsPass = Request.Form("IsPass")
		If IsPass = "" Then IsPass = 0
        IsPic = Request.Form("IsPic")
		If IsPic = "" Then IsPic = 0
        PicFile = Request.Form("PicFile")
        IsTop = Request.Form("IsTop")
		If IsTop = "" Then IsTop = 0
        IsCommend = Request.Form("IsCommend")
		If IsCommend = "" Then IsCommend = 0
		IsDelete = Request.Form("IsDelete")
		If IsDelete = "" Then IsDelete = 0
		IsMove = Request.Form("IsMove")
		If IsMove = "" Then IsMove = 0
		IsPlay = Request.Form("IsPlay")
		If IsPlay = "" Then IsPlay = 0
		IsIndexImg = Request.Form("IsIndexImg")
		If IsIndexImg = "" Then IsIndexImg = 0
		IsUserAdd = Request.Form("IsUserAdd")
		If IsUserAdd = "" Then IsUserAdd = 0
		GiveJifen = Request.Form("GiveJifen")
		If GiveJifen = "" Then GiveJifen = 0
		vUserGroupID = Request.Form("vUserGroupID")
		If vUserGroupID = "" Then vUserGroupID = 0
		vUserMore = Request.Form("vUserMore")
		If vUserMore = "" Then vUserMore = 0
		vUserJifen = Request.Form("vUserJifen")
		If vUserJifen = "" Then vUserJifen = 0
		pMoodStr = Request.Form("pMoodStr")
		If pMoodStr = "" Then pMoodStr = "0,0,0,0,0,0,0,0"
        Hits = Request.Form("Hits")
    End Sub
	
    '更新记录集
    Private Sub UpdateRs(ByRef Rs)
		Rs("ID") = Id
        Rs("ChannelID") = ChannelID
        Rs("ClassID") = ClassID
		Rs("SortPath") = SortPath
        Rs("Title") = Title
		Rs("TitleColor") = TitleColor
        Rs("TitleURL") = TitleURL
		Rs("Keywords") = Keywords
        Rs("Description") = Description
        Rs("Content") = Content
        Rs("Author") = Author
        Rs("ComeFrom") = ComeFrom
        Rs("AddTime") = AddTime
        Rs("Inputer") = Inputer
        Rs("IsPass") = IsPass
        Rs("IsPic") = IsPic
        Rs("PicFile") = PicFile
        Rs("IsTop") = IsTop
        Rs("IsCommend") = IsCommend
		Rs("IsDelete") = IsDelete
		Rs("IsMove") = IsMove
		Rs("IsPlay") = IsPlay
		Rs("IsIndexImg") = IsIndexImg
		Rs("IsUserAdd") = IsUserAdd
		Rs("GiveJifen") = GiveJifen
		Rs("vUserGroupID") = vUserGroupID
		Rs("vUserMore") = vUserMore
		Rs("vUserJifen") = vUserJifen
		Rs("pMoodStr") = pMoodStr
        Rs("Hits") = Hits
    End Sub
    '从记录集中读数据
    Private Sub GetRs(ByRef Rs)
        Id = Rs("Id")
        ChannelID = Rs("ChannelID")
        ClassID = Rs("ClassID")
		SortPath = Rs("SortPath")
        Title = Rs("Title")
		TitleColor = Rs("TitleColor")
        TitleURL = Rs("TitleURL")
		Keywords = Rs("Keywords")
        Description = Rs("Description")
        Content = Rs("Content")
        Author = Rs("Author")
        ComeFrom = Rs("ComeFrom")
        AddTime = Rs("AddTime")
        Inputer = Rs("Inputer")
        IsPass = Rs("IsPass")
        IsPic = Rs("IsPic")
		PicFile = Rs("PicFile")
        IsTop = Rs("IsTop")
        IsCommend = Rs("IsCommend")
		IsDelete = Rs("IsDelete")
		IsMove = Rs("IsMove")
		IsPlay = Rs("IsPlay")
		IsIndexImg = Rs("IsIndexImg")
		IsUserAdd = Rs("IsUserAdd")
		GiveJifen = Rs("GiveJifen")
		vUserGroupID = Rs("vUserGroupID")
		vUserMore = Rs("vUserMore")
		vUserJifen = Rs("vUserJifen")
		pMoodStr = Rs("pMoodStr")
        Hits = Rs("Hits")
    End Sub
	
	'开通/关闭新闻
	Public Sub Pass(action,Id)
		If Id <> "" Then
			Sql = "update Ok3w_Article set IsPass=" & action & " where Id in(" & Id & ")"
			Conn.Execute Sql
			
			If action = 1 Then Call User_Give_Jifen(Id)
		End If
	End Sub
	
	'置顶/取消置顶
	Public Sub Top(action,Id)
		If Id <> "" Then
			sql = "update Ok3w_Article set IsTop=" & action & " where Id in(" & Id & ")"
			Conn.Execute sql
		End If
	End Sub

	'推荐/取消推荐
	Public Sub Commend(action,Id)
		If Id <> "" Then
			sql = "update Ok3w_Article set IsCommend=" & action & " where Id in(" & Id & ")"
			Conn.Execute sql
		End If
	End Sub
	
	'图片/取消图片
	Public Sub Pic(action,Id)
		If Id <> "" Then
			sql = "update Ok3w_Article set IsPic=" & action & " where Id in(" & Id & ")"
			Conn.Execute sql
		End If
	End Sub
	
	'恢复
	Public Sub Resumption(action,Id)
		If Id <> "" Then
			sql = "update Ok3w_Article set  IsDelete=0 where Id in(" & Id & ")"
			Conn.Execute sql
		End If
	End Sub

	'删除
	Public Sub Del(Id)
		If Id <> "" Then
			If IsHtml Then Call Delete_ArticleHTML(ID)
			
			Sql = "update Ok3w_Article set IsDelete=1 where Id in(" & Id & ")"
			Conn.Execute Sql
		
		End If
	End Sub
	
	'彻底删除
	Public Sub OkDel(Id)
		If Id <> "" Then
			sql = "delete from Ok3w_Article where IsDelete=1 and Id in(" & Id & ")"
			Conn.Execute sql
			
			Sql = "delete from Ok3w_Guest where TypeID=2 and pID in(" & Id & ")"
			Conn.execute Sql
		End If
	End Sub
	
	'取最大ArticleID
    Public Function GetMaxArticleID()
        Set clsRs = Server.CreateObject("Adodb.RecordSet")
        Sql = "select max(ID) from Ok3w_Article"
        clsRs.Open Sql,Conn,0,1
        If IsNull(clsRs(0)) Then
        GetMaxArticleID = 0
            Else
                GetMaxArticleID = clsRs(0)
        End If
        clsRs.Close
        Set clsRs = Nothing
    End Function
	
	Public Sub User_Give_Jifen(ID)
		Sql = "update Ok3w_User set Jifen=Jifen+" & Application(SiteID & "_Ok3w_SiteUserTGJifen") & " where User_Name in(select Inputer from Ok3w_Article where ID in(" & ID & ") and IsUserAdd=1 and GiveJifen=0)"
		Conn.Execute Sql
		
		Sql = "update Ok3w_Article set GiveJifen=" & Application(SiteID & "_Ok3w_SiteUserTGJifen") & " where ID in(" & ID & ") and IsUserAdd=1 and GiveJifen=0"
		Conn.Execute Sql	
	End Sub
	
	Public Sub User_Article_Save(a_ID)
		ID = a_ID
		If ID = "" Then ID=GetMaxArticleID()+1
        ChannelID = 1
        ClassID = Request.Form("ClassID")
		SortPath = GetSortPath(ClassId)
        Title = Request.Form("Title")
		TitleColor = ""
		TitleURL = ""
		Keywords = ""
		Description = ""
		For i = 1 To Request.Form("Content").Count 
			Content = Content & Request.Form("Content")(i) 
		Next
        Author = Request.Form("Author")
        ComeFrom = Request.Form("ComeFrom")
        AddTime = Now()
		Inputer = Replace(Session(SiteID & "_Ok3w.Net_User_Name"),"'","")
		IsPass = 0
		IsPic = 0
        PicFile = ""
        IsTop = 0
        IsCommend = 0
		IsDelete = 0
		IsMove = 0
		IsPlay = 0
		IsIndexImg = 0
		IsUserAdd = 1
		GiveJifen = 0
		vUserGroupID = 0
		vUserMore = 1
		vUserJifen = 0
        Hits = 0
		
		Set clsRs = Server.CreateObject("Adodb.RecordSet")
		If a_ID="" Then
			Sql = "select * from Ok3w_Article where 1=2"
			clsRs.Open Sql,Conn,1,3
			clsRs.AddNew()
			Else
				Sql = "select * from Ok3w_Article where ID=" & Cdbl(a_ID)
				clsRs.Open Sql,Conn,1,3
		End If
        Call UpdateRs(clsRs)
        clsRs.Update()
        clsRs.Close()
	End Sub
	
	Public Sub User_Article_Del(a_ID,User_Name)
		Sql = "delete from Ok3w_Article where ID=" & Cdbl(a_ID) & " and IsPass=0 and Inputer='" & User_Name & "'"
		Conn.Execute Sql
	End Sub
End Class
%>

