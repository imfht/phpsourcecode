<%@page import="java.text.SimpleDateFormat,
java.text.DateFormat,
java.util.Date,
java.util.Calendar"%><%
/*
 * @abstract: Web and/or HTTP Tools, some utils in common in the sys
 * @author: wadelau@hotmail.com
 * @since: 2009-01-09 09:21
 * @update: Fri Jun 10 10:57:21 CST 2016, revised for -GWA2
 * @ver: 0.2
 */
%><%!

public final static class Wht{

	/*
	 * get,stead of request.getParameter 
	 * @param HTTPRequest, request
	 * @param String, fieldkey
	 * @return String, formated fieldvalue 	
	 */
	public static String get(HttpServletRequest request, String field){
		String tmps = request.getParameter(field);
        if(tmps == null || tmps.equals("")){
			//- float?
			Object obj = request.getAttribute(field);
			if(obj != null 
				&& (obj instanceof Integer || obj instanceof Double 
					|| obj instanceof Float || obj instanceof Long)){
				tmps = String.valueOf(obj);
			}
			else{
				tmps = (String)obj;
			}
        }
        return Wht._enSafe(tmps);
	}

	/*
	 * getHeader,stead of request.getHeader 
	 * @param HTTPRequest, request
	 * @param String, fieldkey
	 * @return String, formated fieldvalue 	
	 */
	public static String getHeader(HttpServletRequest request, String field){
		return Wht._enSafe(request.getHeader(field));
	}

	/*
	 * getURI,stead of request.getRequestURI 
	 * @param HTTPRequest, request
	 * @return String, formated uri	
	 */
	public static String getURI(HttpServletRequest request){
		return request.getRequestURI()==null?"":request.getRequestURI();
	}

	/*
	 * getQuery,stead of request.getQueryString 
	 * @param HTTPRequest, request
	 * @return String, formated querystring	
	 */
	public static String getQuery(HttpServletRequest request){
		String params = Wht._enSafe(request.getQueryString());
		if(params.contentEquals("")) {}
		else { params = "?" + params; }
		return params;
	}

	/*
	 * ensafe user input
	 */
	private static String _enSafe(String s){
		s = s==null ? "" : s;
		s = s.replaceAll("<", "&lt;");
		s = s.replaceAll("\"", "&quot;");
		return s;
	}

	/*
	 * parseInt, instead of Integer.parseInt
	 * @param String, numberInString
	 * @return int, int
	 * @failed with a return value -1 
	 */
	public static int parseInt(String numstr){
		numstr = numstr== null ? "" : numstr;
		numstr = numstr.equals("null") ? "" : numstr;
		if(numstr.equals("")){
			return -1;
		}
		try	{
			return Integer.parseInt(numstr);
		}
		catch(Exception ex){
			ex.printStackTrace();
		}
		return -1;
	}
	
	//-
    public static int parseInt(Object obj){
        return Wht.parseInt(String.valueOf(obj));
    }

	/*
	 * inString, find where the string in another string, which separated by ","
	 * @param String haystack, the string list
	 * @reurn boolean true|false, if yes true, then false
	 */
	public static boolean inString(String needle,String haystack){
		boolean matched=false;
		if( haystack==null || haystack.equals("") || needle==null || needle.equals("")){
			return matched; 
		}
		else{
			int ipos = haystack.indexOf(needle);
			if(ipos > -1){
				matched = true;
			}
			//System.out.println("Wht.inString: needle:["+needle+"] hays:["+haystack+"] matched:["+matched+"]");
		}
		return matched;
	}

	/*
	 * inList, find where the needle in haystack, which separated by separator
	 * @param String haystack, the string list
	 * @reurn boolean true|false, if yes true, then false
	 */
	public static boolean inList(String needle,String haystack, String sep){
		boolean matched=false;
		if( haystack==null || haystack.equals("") || needle==null || needle.equals("")){
			return matched; 
		}
		else{
			if(sep == null){
				sep = ""; //- String.indexOf	
			}	
			if(!haystack.startsWith(sep)){
				haystack = sep + haystack;
			}
			if(!haystack.endsWith(sep)){
				haystack += sep;
			}
			if(haystack.indexOf(sep + needle + sep)>-1){
				matched=true;
			}
		}
		return matched;
	}
	//-
	public static boolean inList(String needle, String haystack){
		return Wht.inList(needle, haystack, ",");
	}

	/*
	 * startsWith, batchly java.lang.String.startsWith, with haystack split by "|"
	 * @param String needle, which will be used to search with
	 * @param String haystack, which will be used in
	 * @return boolean true|false
	 */
	public static boolean startsWith(String needle,String haystack){
		boolean matched=false;
		if(needle==null || haystack==null || haystack.equals("")){
			return matched;
		}	
		else{
			java.util.regex.Pattern regp=java.util.regex.Pattern.compile("^("+haystack+").*");
			java.util.regex.Matcher regm=regp.matcher(needle);
			if(regm.matches()){
				matched=true;
			}
			regp=null;
			regm=null;
		}
		return matched;

	}

	/*
	 * endsWith, batchly java.lang.String.endsWith, with haystack split by "|"
	 * @param String needle, which will be used to search with
	 * @param String haystack, which will be used in
	 * @return boolean true|false
	 */
	public static boolean endsWith(String needle,String haystack){
		boolean matched=false;
		if(needle==null || haystack==null || haystack.equals("")){
			return matched;
		}	
		else{
			java.util.regex.Pattern regp=java.util.regex.Pattern.compile(".*("+haystack+")$");
			java.util.regex.Matcher regm=regp.matcher(needle);
			if(regm.matches()){
				matched=true;
			}
			regp=null;
			regm=null;
		}
		return matched;
	}

	//
	//-- some special functional methods
	//

	/*
	 * collectUA
	 * @param HttpServletRequest request
	 * @return String mobtype	
	 */
	public static String collectUA(HttpServletRequest request) {
		//-- collecting user-agenct in very beginning, added on 20090108
		String mobtype = getHeader(request,"User-Agent");
		if (mobtype.equals("") || ( mobtype.startsWith("Java") )){
			mobtype=get(request,"myua");
		}
		String hdua = getHeader(request,"ua");
		if(!hdua.equals("") ){
			mobtype = hdua;
		}
		return mobtype;
	}
	
	/*
	 * collectIP
	 * @param HttpServletRequest request
	 * @return String ip
	 */
	public static String collectIP(HttpServletRequest request){
		String remoteip=request.getRemoteAddr();
		if (remoteip.startsWith("127.0")){
			remoteip=get(request,"myip");
		}
		if(remoteip.equals("")){
			if(request.getHeader("X-Forwarded-For")!=null){
				remoteip=request.getHeader("X-Forwarded-For");
			}
			else{
				remoteip = "0.0.0.0";
			}
		}
		return remoteip;
	}

	/*
	 * checkClientUA
	 * @param String myua, my current user-agent
	 * @param String targetua, expecting user-agent
	 * @param HttpServletRequest request
	 */
	public static boolean checkClientUA(String myua, String targetua, HttpServletRequest request) {
		//--check whether a special ua is some kind of client in very beginning, added on 20090108
		boolean matched=false;
		if(myua==null || myua.equals("")){
			myua = getHeader(request,"User-Agent");
		}
		if(!myua.equals("")){
			targetua = targetua.toUpperCase();
			if(myua.toUpperCase().indexOf(targetua) > -1){
				matched=true;
			}
		}
		return matched;
	}
	
	//-
	//- check whether a class has been loaded
	public boolean isClass(String className){
		try{
			Class.forName(className);
			return true;
		}
		catch (final ClassNotFoundException e){
			return false;
		}
	}
	
	//- get date in diff days
	public static Date getDay(int adjustDay){
		return getDateDiff(adjustDay*86400);
	}
	
	//- get date by diff in second
	//- Xenxin, 09:11 Saturday, April 18, 2020
	public static Date getDateDiff(int mySecond){
		Calendar ca = Calendar.getInstance();
		Date now = new Date();
		ca.setTime(now);
		ca.add(Calendar.SECOND, mySecond);
		Date target = ca.getTime();
		now = null; ca = null;
		return target;
	}
	
	//- get a string from an object, e.g. HashMap
	public static String getString(Object obj, String myk){
		String s = "";
		if(obj instanceof HashMap){
			HashMap hmobj = (HashMap)obj;
			s = String.valueOf(hmobj.get(myk));
            s = s==null ? "" : s;
            s = s.equals("null") ? "" : s;
		}
		return s;
	}
	//- get a booean from an object, e.g. HashMap
	//- becareful! getBoolean(hm, "0") != getBoolean(hm, 0)
	public static boolean getBoolean(Object obj, String myk){
		boolean tf = false;
		if(obj instanceof HashMap){
			HashMap hmobj = (HashMap)obj;
			Object tmpobj = (Object)hmobj.get(myk);
            if(tmpobj != null){
                tf = (boolean)tmpobj;
            }
		}
		return tf;
	}
	//- get a booean from an object, e.g. HashMap
	//- becareful! getBoolean(hm, "0") != getBoolean(hm, 0)
	public static boolean getBoolean(Object obj, int myk){
		boolean tf = false;
		tf = Wht.getBoolean(obj, ""+myk);
		return tf;
	}
    //- init a hashMap with pairs
    public static HashMap initHashMap(String keys, String values){
        HashMap hmRtn = new HashMap();
        if(keys.indexOf(",") > -1){
            String[] keyArr = keys.split(",");
            String[] valueArr = values.split(","); int ki = 0;
            for(String k : keyArr){
                hmRtn.put(k, valueArr[ki]); ki++;
            }
        }
        else{
            hmRtn.put(keys, values);
        }
        return hmRtn;
    }

    //- rm x chars from end 
    public static String rmEnd(String s, int ilen){
        return s = s.substring(0, s.length()-ilen);
    }
	
	//- set an element into a 2-d map
	public static HashMap set2DMap(HashMap firstMap, String secMapKey, 
			String key, Object val){
		boolean hasDone = false;
		if(firstMap!=null){
			HashMap secMap = (HashMap)firstMap.get(secMapKey);
			if(secMap==null){ secMap = new HashMap(); }
			secMap.put(key, val);
			firstMap.put(secMapKey, secMap);
			hasDone = true;
		}
		return firstMap;
	}
	
	//- rdmInt
    public static int rdmInt(){
        return Wht.rdmInt(10000);
    }
	//- rdmInt with seed
    public static int rdmInt(int mySeed){
        return ((new java.util.Random()).nextInt((mySeed)));
    }
}
%>