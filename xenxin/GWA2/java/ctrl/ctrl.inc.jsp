<%
//- something included in all modes across the app
//- exec, 2/2
includeCtrl();

%><%!
//- define, 1/2
public void includeCtrl(){
	
	if(true){ //-  something shared across the app

		//-
		//- page header and footer

		//sid = "" + (new java.util.Random()).nextInt();

		//outx.append("\t/ctrl/include: sid:["+sid+"]\n");

		data.put("sid", sid);

		data.put("time", (new Date()));

	}

}

//- embeded in /index
public void setCrsPageResponse(HashMap crsPage, javax.servlet.http.HttpServletResponse response){
	
	//- response headers from child/crs page
	try{

	Iterator entries = crsPage.entrySet().iterator();
	while (entries.hasNext()) {
		Map.Entry entry = (Map.Entry) entries.next();
		String key = (String)entry.getKey();
		if(key.indexOf("response") >= 0){
			Object value = entry.getValue();
			outx.append("Key = " + key + ", Value = " + value);
			String[] setArr = key.split("::"); 
			if(setArr[1].equals("setHeader")){ // crsPage.put("response::setHeader::Location", "/?mod=user&act=signin");
				response.setHeader(setArr[2], (String)value);
				if(setArr[2].indexOf("Location") > -1){
					response.setStatus(302);	
				}
			}
			else if(setArr[1].equals("addCookie")){ // crsPage.put("response::addCookie::", "COOKIE_BODY");
				response.addCookie((javax.servlet.http.Cookie)value);
			}
			else if(setArr[1].equals("sendError")){ // crsPage.put("response::sendError::", "HTTP_Error_CODE");
				response.sendError((int)value);
			}
			else if(setArr[1].equals("setStatus")){ // crsPage.put("response::setStatus::", "HTTP_Error_CODE");
				response.sendError((int)value);
			}
			else{
				debug("Unsupported setResponse:["+setArr[1]+"]. 1607290748.");
			}
		}
		else{
			//out.println("not resp Key = " + key );
		}
	}

	}
	catch(Exception ex){
		ex.printStackTrace();
		/*
		 * in case that not system console and collect output to specified log file
		 * 08:48 Thursday, September 19, 2019
		java.io.StringWriter sw = new java.io.StringWriter();
		ex.printStackTrace(new java.io.PrintWriter(sw));
		String exceptionStr = sw.toString();
		*/
	}

}

%>