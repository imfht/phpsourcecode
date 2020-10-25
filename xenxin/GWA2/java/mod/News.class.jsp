<%
/* User class for user center 
 * v0.1,
 * wadelau@ufqi.com,
 * Sat Jun 18 10:28:02 CST 2016
 */

%><% // @include file="../inc/WebApp.class.jsp" //- relocated to comm/preheader.inc

%><%!

public class News extends WebApp{
	
	//- constructor ?
	public News(){
		//-
		this.setTbl("gwa2_info_usertbl");

	}

	//-
	public String hiUser(){
	
		this.set("pagesize", 10);
		this.set("email", "%lzx%");
		this.set("email.2", "%163%");
		this.set("realname", "%");
		this.set("orderby", "id desc");
		String baseCK = "mod-news";
		
		HashMap userInfo = this.getBy("id, email, realname, updatetime", 
				"(email like ?  or email like ?) and realname like ?",
				(new HashMap(){{ put("key", baseCK+"email-163"); }}));

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