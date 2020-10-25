<%
/* controller of enroll
 * v2
 */
%><%@include file="./ctrlheader.inc.jsp"%><%

%><%@include file="../mod/User.class.jsp"%><%

if(act.equals("")){
	
	data.put("iname", "Wadelau"+(new java.util.Random()).nextInt());
	data.put("phonenum", ""+(new java.util.Random()).nextInt());

	mytpl = "enroll.html";

}
else if(act.equals("dosubmit")){

	outx.append("ctrl/enroll: dosubmit");

	User user = new User();

	user.set("realname", Wht.get(request, "iname"));

	HashMap hm = user.setBy("realname, inserttime, updatetime", null);

	
	outx.append("ctrl/enroll: dosubmit"+hm.toString()+", <a href='./?mod=enroll'>Continue</a>");


}
else{
	
	outx.append("ctrl/enroll: unknown act:["+act+"]");

}


%><%@include file="./ctrlfooter.inc.jsp"%><%

%>

