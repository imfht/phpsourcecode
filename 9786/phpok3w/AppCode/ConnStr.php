<%
Const CMSDataBase = 0

Dim sql_databasename,sql_password,sql_username,sql_localname
Dim SysSiteDbPath

SysSiteDbPath = "Db/Ok3w.Net###ASP$$v4.9.ASP"		'修改数据库名称后，需要相应的修改这里
ConnStr = "Provider=Microsoft.Jet.OLEDB.4.0;Data Source=" & Server.MapPath(dbdns & SysSiteDbPath)
%>