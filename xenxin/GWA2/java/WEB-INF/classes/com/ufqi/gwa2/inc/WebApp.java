/* 
 * WebApp in useBean with .java
 * the Global and unique parent of all objects in this application
 * imprvs on time fields by Xenxin@ufqi, Tue, 13 Mar, 2018 19:24:12
 * first implemented in -PHP
 * ported into -Java
 * -GitHub-Wadelau
 */

package com.ufqi.gwa2.inc;

import java.util.HashMap;

import com.ufqi.gwa2.inc.WebAppInterface;

public class WebApp implements WebAppInterface{
	
	protected HashMap hm = new HashMap(); //- runtime container, local, regional
	public HashMap hmf = new HashMap(); //- persistent storage, global
	private String myId = "id";
	private String myIdName = "myId";
	private final String[] timeFieldArr = new String[]{"inserttime", "createtime", "savetime",
		"modifytime", "edittime", "updatetime"};
    private final static String Log_Tag = "inc/WebApp";

    /*
	Dba dba = null;
	Cachea cachea = null;
	Sessiona sessiona = null; //-  Fri Jun 29 12:08:49 UTC 2018
    Filea filea = null;
    */
	//- constructor
	public WebApp(HashMap hmcfg){
		//- cfg in HashMap, Thu Jun 21 10:31:25 UTC 2018
		//- db
		
	}
	
    //- destructor
    //- equivalent to __destruct , Tue Aug  7 09:37:17 UTC 2018
    //- @todo 
	
	//-
	public WebApp(){
		//- for constructor override 
		this(new HashMap());
		//debug("inc/Webapp: no args: dba:["+dba+"] sessiona:["+sessiona+"]");
	}
	
	//-
	public void set(String k, Object v){
		if(k == null || k.equals("")){
			//- @todo ?
		}
		else{
			this.hmf.put(k, v);	
		}
	}

	//-
	public String get(String k){
        return get(k, false);
	}

    //-
    public String get(String k, boolean noExtra){
        //- @todo if noExtra==false , try to retrieve info from db
		String tmp;
        Object obj = this.hmf.get(k);
        if(obj instanceof Integer){
            tmp = String.valueOf(obj);
        }
        else{
            tmp = (String)obj;
        }
		return tmp==null ? "" : tmp;
    }

    //-
    public HashMap getBy(String s1, String s2){
        //-@todo

        return (new HashMap());
    }

    //-
    public HashMap setBy(String s1, String s2){
        //-@todo

        return (new HashMap());
    }

    //-
    public void del(String k){
       this.hmf.remove(k); 
    }

    //-
    public HashMap rmBy(String k){
        //- @todo

        return (new HashMap());
    }

    //-
    public HashMap execBy(String fields, String conditions){
        //- @todo

        return (new HashMap());
    }

    //-
    public String getId(){
        //-

        return "";
    }

    //-
    public void setId(String myId){
        //- @todo
    }

    //- and more ...
	
}

