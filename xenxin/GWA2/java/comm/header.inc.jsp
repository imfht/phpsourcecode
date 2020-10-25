<%@page 
language="java" 
pageEncoding="UTF-8"%><%
//- app header
%><%@include file="./preheader.inc.jsp"%><%
rtvdir = request.getServletPath();
appdir = application.getRealPath(rtvdir);

if(true){
	int ipos = rtvdir.lastIndexOf('/');
	if(ipos > -1){
		rtvdir = rtvdir.substring(0, ipos); 
	}
	ipos = appdir.lastIndexOf('/');
	appdir = appdir.substring(0, (ipos==-1 ? 0 : ipos));
	if(appdir.equals("")){ //- in case of Windows
		appdir = (getServletContext().getRealPath("/"));
		appdir = appdir.replaceAll("\\\\", "/");
		appdir = appdir.substring(0, appdir.length()-1);
		appdir += "" + rtvdir;
	}
}
//outx.append("\n\tcomm/header: requestURL:["+request.getRequestURL()+"] servletPath:["+request.getServletPath()+"] rtvdir:["+rtvdir+"] appdir:["+appdir+"] 1607100803.\n"); //- appdir:["+appdir+"] 

rtvdir = rtvdir.equals("") ? "." : rtvdir; data.put("rtvdir", rtvdir);

sid = Wht.get(request, "sid");
if(sid.equals("")){ sid = "" + ((new java.util.Random()).nextInt((999999-100000)+1) + 100000); }
url = rtvdir + "/?sid=" + sid; 

//- user
user = new User();
if(true){
	userid = user.getUserIdByCookie(request);
	if(!tmpUserId.equals("")){ user.setId(userid); }
}
//- lang
if(true){
	String ilang = "zh"; String icountry = "CN"; HashMap langconf = new HashMap();
	String reqtLang = Wht.get(request, "lang"); // read from para or cookie?
	if(reqtLang.equals("")){
		String langs = user.getLangByCookie(request);
		if(langs.equals("")){
			langs = Wht.getHeader(request, "Accept-Language");
			int sepPos = langs.indexOf(",");
			if(sepPos > -1){
				langs = langs.substring(0, sepPos);
			}
		}
        if(langs.indexOf("-") > -1){
		    String[] langArr = langs.split("-");
		    ilang = langArr[0]; icountry = langArr[1];
        }
        else{
            debug("comm/header: langs:["+langs+"]");
            ilang = langs;
        }
	}
	else{
		ilang = reqtLang;
	}
	if(ilang.equals("en")){ icountry = "US"; }
	if(ilang.equals("zh")){ icountry = "CN"; }
	langconf.put("language", ilang); langconf.put("country", icountry);
	lang = new Language(langconf);
	debug("ilang:"+ilang+" icountry:"+icountry+" welcome:"+lang.get("welcome"));
	//- @todo
	data.put("welcome", lang.get("welcome"));
	data.put("language", ilang); data.put("languagecountry", icountry);
	//- set to cookie? @todo
	if(!reqtLang.equals("")){
		lang.addLang2Cookie(true);
	}
}

fmt = Wht.get(request, "fmt");
//- set header according to fmt
response.setCharacterEncoding("utf-8");
if(fmt.equals("")){
	response.setContentType("text/html;charset=utf-8");
}
else{
	if(fmt.equals("xml")){
		response.setContentType("text/xml;charset=utf-8");
	}
	else if(fmt.equals("json")){
		response.setContentType("application/json;charset=utf-8");
	}
	else{
		debug("comm/header: unsupported fmt:["+fmt+"]");
	}
}

mytpl = ""; data.put("viewdir", "view/default"); data.put("tpldir", appdir + "/view/default");
%>