<%
/* Session Administration, handling all cache transations across the site.
 * v0.1
 * Xenxin@ufqi.com
 * since Fri Jun 29 07:08:55 UTC 2018
 */

%><%@include file="./SessionX.class.jsp"%><%
%><%!

public final class Sessiona { //- Session administrator

	//- variables

	protected SessionConn myConn;
	
	protected SessionDriver myDriver;
	
	//-
	//- @todo
	public Sessiona(String hmconf){
		hmconf = hmconf==null ? "master" : hmconf;
		this.myConn = new SessionConn(hmconf);
		String strDriver = (String)Config.get("sessiondriver");
		if(strDriver.equals("SESSIONX")){
			this.myDriver = new SessionX(this.myConn);
		}
		else{
			System.out.println("inc/Sessiona: Unsupported sessionDriver:["
				+strDriver+"]. 1806291532.");
		}
	}

	//- methods, public
	public String generateSid(User user, HttpServletRequest request){
		String sid = "";
		sid = this.myDriver.generateSid(user, request);
		return sid;

	}

    //-
    public String getCookieSidSep(){
        return this.myDriver.getCookieSidSep();
    }

	//-
	public HashMap checkSid(User user, HttpServletRequest request, String sid){
		return this.myDriver.checkSid(user, request, sid);
	}

    //-
    public void close(){
        //-
        this.myDriver.close();
    }
	
	//-
	private String _md5k(String key){
		//- @todo
		return "";
	}

}

//- define for all drivers
public interface SessionDriver{
	
	//- @todo in Impls

	//private void _init();

	public String generateSid(User user, HttpServletRequest request);
	
	public HashMap checkSid(User user, HttpServletRequest request, String sid);

	public Object get(String key);

	public boolean set(String key, Object val);
	
	public boolean rm(String key);
	
    public String getCookieSidSep();

    public void close();

}


%>
