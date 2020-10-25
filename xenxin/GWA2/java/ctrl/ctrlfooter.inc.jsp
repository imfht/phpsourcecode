<%
//- something in end of a ctrl
//- wadelau@ufqi.com,  Sat Jun 18 11:05:03 CST 2016

// strings need to be put back explictly
crsPage.put("data", data);
crsPage.put("outx", outx);
crsPage.put("mod", mod);
crsPage.put("act", act);
crsPage.put("mytpl", mytpl);
crsPage.put("fmt", fmt);
crsPage.put("url", url);

crsPage.put("user", user.toHash());
outx.append("\n\t output in ctrl/ctrlfooter.inc.jsp, mytpl:["+mytpl+"]\n\n");
request.setAttribute("crsPage", crsPage);

//- end an inner modfs
}
catch(Exception exInnerMod){
	exInnerMod.printStackTrace();
}
finally{
	//- clear
	%><%@include file="../comm/aftfooter.inc.jsp"%><%
}
%>