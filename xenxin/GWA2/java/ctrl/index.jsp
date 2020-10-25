<%@page 
language="java" 
pageEncoding="UTF-8"%><%
/* By this way, a request will trigger the second instance,
 * which may be an overheat on performace, but can be extended to a large scale
 * noted by wadelau @ Fri Jun 17 06:46:42 CST 2016
  * need pageEncoding for non-ascill characters in the source code, e.g. Chinese, Japanese, Russia... 17:29 2020-07-29
 */

%><%@include file="./ctrlheader.inc.jsp"%><%

//- main busi logic

//- outx and data should added up and should not out print in the child pages
outx.append("\n\tbgn: output in /ctrl/index. @"+(new java.util.Date())+"\n");

act = act.equals("") ? "index" : act;
if(act.equals("index")){
	outx.append("\t/ctrl/index: succ. get act:["+act+"] with mod:["+mod+"]\n");
	data.put("time-"+mod+"-"+act, "we are now at "+(new Date()));
}
else{
	outx.append("\n\t/ctrl/index: fail. reach Unknown act:["+act+"] with mod:["+mod+"]\n");
}

outx.append("\n\tend: appending to outx in /ctrl/index."+(new java.util.Date()) + "\n");

/*
 * Transfer http headers to parent page
 * response.setHeader("Location", "/?mod=user&act=signin");
 * this will not work in 'include' mode, alternative way as below:
 */
//crsPage.put("response::setHeader::Location", url+"&mod=user&act=signin"); 
/* format: 
 *	reseponse::Method::Key, Value
 * e.g.
 *	"response::addCookie::", "COOKIE_BODY"
 *	"response::sendError::", "HTTP_Error_CODE"
 */

//- output
if(fmt.equals("") && mytpl.equals("")){
	mytpl = "homepage.html";
}

%><%
%><%@include file="./ctrlfooter.inc.jsp"%><%
%>