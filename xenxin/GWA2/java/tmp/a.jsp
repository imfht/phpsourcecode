<%@page 
import="java.util.Date,
java.util.HashMap,
java.util.Map,
java.util.Iterator,
java.io.File,
org.lilystudio.smarty4j.Context,
org.lilystudio.smarty4j.Engine,
org.lilystudio.smarty4j.Template" 
language="java" 
pageEncoding="UTF-8"%><%

%><%@include file="../comm/header.inc.jsp"%><%

//outx.append("time in tmp/a: ["+(new java.util.Date())+"]");
out.println("resp in tmp/a:[] 中文 ! sayHi:[" +sayHi()+ "]");


%><%!

private String sayHi(){
	
	return "Hi 中国！";

}

%>
