<%
/* 
 * Session Driver, general session
 * v0.1
 * Xenxin@ufqi.com
 * since Fri Jun 29 07:12:38 UTC 2018
 */
%><%@page import="java.util.*"%><%
%><%!

public final class SessionX implements SessionDriver {

	//- variables
	private static final String Data_Sep = "";
	private String Session_Private_Key = "";
	private static final int Sign_Length = 24;
	private static final String Special_Tag_For_B62x = "";
	private static final String Sep_Tag_For_Cookie = ".";

	//- constructor
	public SessionX(SessionConn myConn){
		//- @todo
		this.Session_Private_Key = (String)Config.get("sign_key");

	}	
	
	//- methods, public
		//- generate an uniqe secure id
	public String generateSid(User user, HttpServletRequest request){
		String sid = "";
		String sep = this.Data_Sep;
		String params = user.getId(); // userid, +lang?
		String signData = this._getSignData(params, request);
		sid = this._md5Remedy(Zeea.sha1(signData));
		sid = sid + this.Sep_Tag_For_Cookie + params;
		return sid;
	}
	
	//- validate a foreign id
	public HashMap checkSid(User user, HttpServletRequest request, String sid){
		HashMap hmrtn = new HashMap();
		boolean isValid = false;
		String[] sidArr = sid.split("\\"+this.Sep_Tag_For_Cookie);
		String recvSid = sidArr[0];
		String params = "";
		if(sidArr.length > 1){
			params = sidArr[1]; //- more segmentations?
        }
		String signData = this._getSignData(params, request);
		String genSid = this._md5Remedy(Zeea.sha1(signData));
		if(genSid.equals(recvSid)){
			isValid = true;
		}
		if(isValid){
			hmrtn.put(0, true);
			hmrtn.put(1, params); // userid?
		}
		else{
			hmrtn.put(0, false);
			hmrtn.put(1, "");
		}
		return hmrtn;
	}

	//-
	public Object get(String key){

		//- @todo
		return (new Object());

	}

	//-
	public boolean set(String key, Object val){
		boolean isSucc = true;

		return isSucc;
	}
	
	//-
	public boolean rm(String key){
		boolean isSucc = true;

		return isSucc;
	}
	
    //-
    public String getCookieSidSep(){
        return this.Sep_Tag_For_Cookie;
    }

    //-
    public void close(){
        //- @todo
    }

	//- methods, private
	private String _getSignData(String params, HttpServletRequest request){
		StringBuffer databf = new StringBuffer();
		String sep = this.Data_Sep;
		return databf.toString();
	}
	
	//-
	private String _md5Remedy(String md5Str){
		return md5Str.substring(2, this.Sign_Length+2); //- fromIndex, endIndex
	}
	
}

%>
