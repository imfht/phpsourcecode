/* User userBean with .java
 * User class for user center 
 * v0.1,
 * wadelau@ufqi.com,
 * Sat Jun 18 10:28:02 CST 2016
 */

package com.ufqi.gwa2.mod;

import java.util.HashMap;
import java.util.Map;
import java.util.Iterator;

import com.ufqi.gwa2.inc.WebApp;

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
		//this.setTbl("gwa2_info_usertbl");	
	}

	//-
	public String hiUser(){
	
		this.set("pagesize", 10);
		this.set("email", "%lzx%");
		this.set("email.2", "%163%");
		this.set("realname", "%");
		this.set("orderby", "id desc");
		this.set("iname", "iname from WEB-INF/class:"+(new java.util.Date()));
		
		return (String)this.get("iname") + ", Welcome! ";

	}

}

