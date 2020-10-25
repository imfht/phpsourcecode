<%
/* controller of user mod
 * v2
 */
%><%@include file="./ctrlheader.inc.jsp"%><%


//- list request headers
if(true){
	java.util.Enumeration eNames = request.getHeaderNames();
	while (eNames.hasMoreElements()) {
		String name = (String) eNames.nextElement();
		String value = (String)(request.getHeader(name));

		outx.append("\nheader: k:["+name+"] v:["+value+"]\n");

	}
}


//- print system env
if(true){
	java.util.Properties p = new java.util.Properties();
	p = System.getProperties();
	HashMap m = new HashMap(p);
	//outx.append("\n\n" + m.keySet() +"=" +m.values()+"\n\n");
	//- jdk 8
	//m.keySet().forEach(k, v) -> out.println("system: k:["+k+"] v:["+v+"]");
	
	Iterator entries = m.entrySet().iterator();
	while (entries.hasNext()) {
		Map.Entry entry = (Map.Entry) entries.next();
		String key = (String)entry.getKey();
		Object value = entry.getValue();
		outx.append("\nkey:["+key+"] value:["+value+"]\n");
	}
	
}


//-- list response header 
if(true){
	java.util.Collection eNames = response.getHeaderNames();
	java.util.Iterator ei = eNames.iterator();
	while (ei.hasNext()) {
		String name = (String) ei.next();
		String value = (String)(response.getHeader(name));

		outx.append("\nresponse: k:["+name+"] v:["+value+"]\n");

	}
}

//- list class loader
if(true){
	ClassLoader loaderx = Wht.class.getClassLoader(); 
	while (loaderx != null) { 
		outx.append("\nclassloader: "+loaderx.toString()+"\n"); 
		loaderx = loaderx.getParent(); 
	}

}


%><%@include file="./ctrlfooter.inc.jsp"%><%

%>
