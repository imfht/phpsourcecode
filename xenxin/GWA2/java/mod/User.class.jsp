<%
/* User class for user center 
 * v0.1,
 * wadelau@ufqi.com,
 * Sat Jun 18 10:28:02 CST 2016
 */

%><% // @include file="../inc/WebApp.class.jsp" //- relocated into comm/preheader.inc
%><%
%><%!

public class User extends WebApp{
	
	private static final String User_Db_Alias = "myuserdb";
	
	//- constructor ?
	public User(){
		//- for constructor override
		this(new HashMap());
	}

	//-
	//- restore an object from hashmap, refer to WebApp.toHash
	//- wadelau@ufqi.com,  Thu Jul 28 03:46:17 CST 2016
	public User(HashMap fromHm){
		//- call super Constructor with args if applicable, e.g. different DbAlias
		//- i.e. WebApp->Contructor(HashMap hmcfg);
		//super((HashMap)(new HashMap<String, String>(){{put("dbconf", User_Db_Alias);}}));
		//- @todo
		fromHm = fromHm==null ? (new HashMap()) : fromHm;
		Iterator entries = fromHm.entrySet().iterator();
		while (entries.hasNext()) {
			Map.Entry entry = (Map.Entry) entries.next();
			String key = (String)entry.getKey();
			Object value = entry.getValue();
			//System.out.println("User/Constructor: restore: key:["+key+"] value:["+value+"]\n");
			this.set(key, value);
		}
		entries = null;
		
		String dbConfig = "userdb";
		this.set("dbconfig", dbConfig);
		this.setTbl("gwa2_info_usertbl");	
	}

	//- get unique secureid
	public String generateSecureId(HttpServletRequest request){
		String sid = "";
		sid = this.sessiona.generateSid(this, request);
		return sid;
	}

	//- get cookie sid
	public String getCookieSid(){
		String sid = "";
		sid = ""; //- @todo
		return sid;
	}
	
	//- get user id from cookie
	public String getUserIdByCookie(HttpServletRequest request){
		String sid = "";
		if(this.sessiona != null){
			//- @todo
		}
		sid = ""; //- @todo
		return sid;
	}
	
	//- get lang id from cookie
	public String getLangByCookie(HttpServletRequest request){
		String sid = "";
		if(this.sessiona != null){
			//- @todo
		}
		sid = ""; //- @todo
		return sid;
	}

	//-
	public String hiUser(){
	
		this.set("pagesize", 10);
		this.set("email", "%lzx%");
		this.set("email.2", "%163%");
		this.set("realname", "%");
		this.set("orderby", "id desc");
		
		HashMap userInfo = this.getBy("id, email, realname, updatetime", 
				"(email like ?  or email like ?) and realname like ?");

		userInfo.put("read-in-User-timestamp", (new Date()) + "userinfo:["+userInfo.toString()+"]");	

		HashMap userInfo2 = this.execBy("desc "+this.getTbl(), null);
		userInfo.put("read-in-User-by-execBy", userInfo2);

		this.setId("136");
		userInfo2 = this.rmBy("id=?");
		userInfo.put("delete-in-User-by-rmBy", userInfo2);

		return (String)this.get("iname") + ", Welcome! "+ userInfo.toString();
	}

}

%>