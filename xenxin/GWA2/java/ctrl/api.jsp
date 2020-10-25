<%
/* controller of enroll
 * v2
 */
%><%@include file="./ctrlheader.inc.jsp"%><%


if(act.equals("get")){


if(fmt.equals("json")){
	
	data.put("jsonbody", new HashMap());



}

}
else{
	
	outx.append("ctrl/enroll: unknown act:["+act+"]");

}



%><%@include file="./ctrlfooter.inc.jsp"%><%

%>


