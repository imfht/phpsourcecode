<%@page language="java" pageEncoding="UTF-8" autoFlush="true" session="false"%><%
/* -GWA2 is ported into Java
 * Wadelau@ufqi.com
 * v0.1, Thu Jun  9 14:02:51 CST 2016
 * v0.2, Thu Jul 28 04:19:21 CST 2016
 * v0.3, Tue Jul  2 12:02:28 HKT 2019, + global thread-safe
 */

// the application entry...

synchronized(this){ //- global thread-safe, bgn

//-  entry header
%><%@include file="./comm/header.inc.jsp"%><%

//- main logic
mod = Wht.get(request, "mod");
act = Wht.get(request, "act");
if(mod == null){ mod = ""; }else{ mod = mod.trim(); }
if(act == null){ act = ""; }else{ act = act.trim(); }
if(mod.equals("")){
  mod = "index";    
}
data.put("mod", mod);
data.put("act", act);

/*
 * Due to 
 * 1) issues of performance and security of java.lang.reflection,
 * 		We do not use it as routing or dynamic module invoking at present.
 * 2) issues of performance and seperated runtime environment of dispatcher forward,
 * 		we do not use it as page embedded for routing at present.
 * Though dynamic dispatcher include, similiar to the 2nd one,
 *  it will trigger two instances of a single request, it still is better than
 *		wrap all entries in a single jsp servlet.
 */

//- some logic loading 
StringBuffer modf = new StringBuffer(rtvdir).append("/ctrl/").append(mod).append(".jsp");
String modfs = modf.toString();
String realModfs = application.getRealPath(modfs);

if((new File(realModfs)).exists()){

	//- collect runtime data into request for cross page
	HashMap crsPage = new HashMap();
	crsPage.put("data", data);
	crsPage.put("outx", outx);
	crsPage.put("mod", mod);
	crsPage.put("act", act);
	crsPage.put("mytpl", mytpl);
	crsPage.put("fmt", fmt);
	crsPage.put("url", url);
	crsPage.put("sid", sid);
	crsPage.put("user", user.toHash()); //- save an object properties to a hashmap, then restore the instance in another page

	//- append to request to cross page
	request.setAttribute("crsPage", crsPage);

	//- process of another instance	
	request.getRequestDispatcher(modfs).include(request, response);

	//- restore runtime data into response
	crsPage = (HashMap)request.getAttribute("crsPage");
	
	//- response headers from child/crs page, defined in ctrl/ctrl
	setCrsPageResponse(crsPage, response);

	// variables needs to be retrieved explictly 
    //
    data = (HashMap)crsPage.get("data");
    outx = (StringBuffer)crsPage.get("outx");
	mod = (String)crsPage.get("mod");
	act = (String)crsPage.get("act");
	mytpl = (String)crsPage.get("mytpl");
	fmt = (String)crsPage.get("fmt");
	url = (String)crsPage.get("url");
	act = (String)crsPage.get("act");

	user = new User((HashMap)crsPage.get("user")); //- wadelau@ufqi.com on Wed Jul 27 00:02:08 CST 2016
	//outx.append("/index: time-in-index-restore: ["+user.get("time-in-index")+"] userid:["+user.getId()+"]");
	
}
else{
	//- no exist//- continue this way
	outx.append("\n/index: Unknown mod:["+mod+"] with act:["+act+"] modfs:["+modfs+"]. 201107080706.\n");	
	//- #todo: log
}

//- something shared across the app, out of comm/header
if(true){
	%><%@include file="./ctrl/ctrl.inc.jsp"%><%
}

//- footer
%><%@include file="./comm/footer.inc.jsp"%><%

} //- global thread-safe, end

%>
