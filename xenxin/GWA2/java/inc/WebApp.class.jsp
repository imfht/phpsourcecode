<%
/* the Global and unique parent of all objects in this application
 * imprvs on time fields by Xenxin@ufqi, Tue, 13 Mar, 2018 19:24:12
 * first implemented in -PHP
 * ported into -Java
 * -GitHub-Wadelau
 */
%><%@include file="./WebApp.interface.jsp"%><%
%><%@include file="./Conn.class.jsp"%><%
%><%@include file="./Dba.class.jsp"%><%
%><%@include file="./Cachea.class.jsp"%><%
%><%@include file="./Sessiona.class.jsp"%><%
%><%@include file="./Zeea.class.jsp"%><%
%><%@include file="./Filea.class.jsp"%><%!

public class WebApp implements WebAppInterface{
	
	protected HashMap hm = new HashMap(); //- runtime container, local, regional
	public HashMap hmf = new HashMap(); //- persistent storage, global
	private String myId = "id";
	private final String myIdName = "myId";
	private final String[] timeFieldArr = new String[]{"inserttime", "createtime", "savetime",
		"modifytime", "edittime", "updatetime", "dinserttime", "dupdatetime"};
    private final static String logTag = "inc/WebApp";

	Dba dba = null;
	Cachea cachea = null;
	Sessiona sessiona = null; //-  Fri Jun 29 12:08:49 UTC 2018
    Filea filea = null;

	//- constructor
	public WebApp(HashMap hmcfg){
		//- cfg in HashMap, Thu Jun 21 10:31:25 UTC 2018
		//- db
		if(this.dba == null){
			String dbconf = "";
			if(hmcfg != null && hmcfg.containsKey("dbconf")){
				dbconf = (String)hmcfg.get("dbconf"); // @todo
				this.set("dbconf", dbconf);
			}
			this.dba = new Dba(dbconf);
		}
		//- cache
		if((boolean)Config.get("enable_cache")){
			if(this.cachea == null){
				String cacheconf = "";
				//@todo cfg.cacheconf
				this.set("cacheconf", cacheconf);
				this.cachea = new Cachea(cacheconf);
			}
		}
		//- session
		if((boolean)Config.get("enable_session")){
			if(this.sessiona == null){
				String sessionconf = "";
				this.set("sessionconf", sessionconf);
				this.sessiona = new Sessiona(sessionconf);
			}
		}
        //- filea
		if((boolean)Config.get("enable_file")){
			if(this.filea == null){
				String fileconf = "";
				this.set("fileconf", fileconf);
				this.filea = new Filea(fileconf);
			}
		}
	}
	
	//-
	public WebApp(){
		//- for constructor override 
		this(new HashMap());
		//debug("inc/Webapp: no args: dba:["+dba+"] sessiona:["+sessiona+"]");
	}
	
    //- destructor
    //- equivalent to __destruct , Tue Aug  7 09:37:17 UTC 2018
    public void finalize(){
        if(this.dba != null){
            this.dba.close();
        }
        //debug(logTag+" finalize is called to clean up....");
    }
	
	//-
	public void set(String k, Object v){
		if(k == null || k.equals("")){
			//- batchly set
			if(v instanceof HashMap){
				HashMap tmpMap = (HashMap)v;
				for(Object tmpk : tmpMap.keySet()){
					this.set((String)tmpk, tmpMap.get(tmpk));
				}
			}
			else{
				debug(logTag+" unknown object:["+v+"]");
			}
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
    public void del(String k){
       this.hmf.remove(k); 
    }
	
	/* mandatory return $hm = (0 => true|false, 1 => string|array); in GWA2 PHP
	 * Thu Jul 21 11:31:47 UTC 2011, wadelau@gmail.com
	 * update by extending to readObject by wadelau, Sat May  7 11:06:37 CST 2016
     * with built-in cache support in -Java by Xenxin, Fri Aug  3 11:10:05 UTC 2018
	 */
	public HashMap getBy(String fields, String args, HashMap hmCache){
		HashMap hm = new HashMap();
        //- from cache
		int colonPos = fields.indexOf(":");
		boolean isGetCache = fields.indexOf("cache:") > -1 ? true : false;
        if(hmCache != null && hmCache.size() > 0 
			&& (colonPos < 0 || isGetCache)){
            hm = this.readObject("cache:", hmCache);
            if((boolean)hm.get(0)){
                debug(logTag + ": read cache succ. args:"+hmCache);
            }
            else if(!isGetCache){
                debug(logTag + ": read cache fail. try db with args:"+hmCache);
                this.set("cache:" + fields, hmCache.get("key"));
                hm = this.getBy(fields, args);
            }
			else{
				//- get cache but failed.
			}
        }
        else if( colonPos > -1){ //- from objects
            hm = this.readObject(fields, hmCache);
        }
        else{
        //- from db
		StringBuffer sqls = new StringBuffer();
		boolean hasLimitOne = false;
		int pageNum = 1; //- default pagenum set to "1", unless pre set in hmvar, 20080903
		int pageSize = 0; //- default pagesize set to "0", unless pre set in hmvar, "0" means all, no limit, 20080903
		if(this.hmf.containsKey("pagenum")){
			String tmppn = (String)(this.hmf.get("pagenum"));
			pageNum = Integer.parseInt(tmppn);
		}
		if(this.hmf.containsKey("pagesize")){
			String tmpps = String.valueOf(this.hmf.get("pagesize"));
			pageSize = Integer.parseInt(tmpps);
		}
		sqls.append("select ").append(fields).append(" from ").append(this.getTbl()).append(" where ");
		if(args == null || args.equals("")){
			if(this.getId().contentEquals("")){
				sqls.append(" 1=1 ");
			}
			else{
				sqls.append(this.myId).append("=?");
				hasLimitOne = true;
			}
		}
		else{
			sqls.append(args);
		}
		if(this.hmf.containsKey("groupby")){
			sqls.append(" group by ").append(this.hmf.get("groupby"));
		}
		if(this.hmf.containsKey("orderby")){
			sqls.append(" order by ").append(this.hmf.get("orderby"));
		}
		if(hasLimitOne){
			sqls.append(" limit 1");
		}
		else{
			if(pageSize == 0){
				pageSize = 99999;
			}
			sqls.append(" limit ").append((pageNum-1)*pageSize).append(", ").append(pageSize);
		}
		hm = this.dba.select(sqls.toString(), this.hmf);	
        this._setCache(hm, fields);
		sqls = null; fields = null; args = null;

	    }
		return hm;
	}

	//- @override
    //- simple db query
	public HashMap getBy(String fields, String args){
		HashMap hmCache = new HashMap();
		return this.getBy(fields, args, hmCache);
	}

	//- @override
    //- direct to read objects
	public HashMap getBy(String fields, HashMap args){
		return this.getBy(fields, null, args);
	}

	//-
	/* mandatory return $hm = (0 => true|false, 1 => string|array); in GWA2 PHP
	 * Thu Jul 21 11:31:47 UTC 2011, wadelau@gmail.com
	 * update by extending to writeObject by wadelau, Sat May  7 11:06:37 CST 2016
	 */
	public HashMap setBy(String fields, String args, HashMap xargs){
		HashMap hm = new HashMap();
	    //- to objects
        if(fields.indexOf(":") > -1){
            hm = this.writeObject(fields, xargs);
        }	
        else{
        //- to db
		StringBuffer sqls = new StringBuffer();
		boolean isUpdate = false; String tmpId = this.getId();
		if((tmpId.equals("") ||tmpId.equals("0")) 
				&& (args == null || args.equals(""))){
			sqls.append("insert into ").append(this.getTbl()).append(" set ");
		}
		else{
			sqls.append("update ").append(this.getTbl()).append(" set ");
			isUpdate = true;
		}
		String[] fieldArr = fields.split(",");
		String timeFields = java.util.Arrays.toString(this.timeFieldArr);
		for(String f: fieldArr){
			f = f==null ? "" : f.trim();
			if(Wht.inString(f, timeFields) && this.get(f).equals("")){
				sqls.append(f).append("=NOW(), "); // assume MySQL?
				this.hmf.remove(f);
			}
			else{
				sqls.append(f).append("=?, ");
			}
		}
		int sqlsLen = sqls.length();
		sqls.delete(sqlsLen-2, sqlsLen); //- drop ", "
		boolean isSqlReady = true;
		if(args == null || args.equals("")){
			if(tmpId.equals("") || tmpId.equals("0")){
				if(isUpdate){
					debug(logTag + " unconditonal update is forbidden. 1607072133.");
					hm.put("0", false);
					hm.put("1", (new HashMap()).put("errordesc", "unconditonal update is forbidden. 1607072133."));
					isSqlReady = false;
				}
			}
			else{
				sqls.append(" where ").append(this.myId).append("=?");
			}
		}
		else{
			sqls.append(" where ").append(args);
		}
		if(isSqlReady){
			if(!this.getId().equals("")){
				this.hmf.put("pagesize", 1);
			}
			hm = this.dba.update(sqls.toString(), this.hmf);
			hm.put("isupdate", isUpdate);
			//-rm old cache when updt succ, 2020-08-20
			if(xargs != null && (boolean)hm.get(0)){
				this.rmBy("cache:"+xargs.get("key"));
			}
		}
		sqls = null; args = null; fields = null;

        }
		return hm;
	}

    //- @override
    //- simple db save
    public HashMap setBy(String fields, String args){
        return setBy(fields, args, null);
    }

    //- @override
    //- direct write objects
    public HashMap setBy(String fields, HashMap xargs){
        return setBy(fields, null, xargs);
    }
    
	//- initial added on Mon Jan 23 12:20:24 GMT 2012 by wadelau@ufqi.com
	//- ported from GWA2PHP by wadelau, Sun Jul 17 22:13:39 CST 2016
	public HashMap execBy(String sql, String args, HashMap hmCache){
		HashMap hm = new HashMap();
		args = args==null ? "" : args.trim();
        String origSql = sql; String sqlx = null;
		int pos = -1;
		if(sql == null || sql.equals("")){
			hm.put("0", false);
			hm.put("1", (new HashMap()).put("errordesc", "sql:["+sql+"] is null. 1607172158.")); 
		}
		else{
			sqlx = sql.trim().toUpperCase();
			pos = sqlx.indexOf("SELECT ");
			if(pos == 0){
				//- normal	
			}
			else{
				pos = sqlx.indexOf("DESC ");
				if(pos == 0){
					//-normal
				}
				else{
					pos = sqlx.indexOf("SHOW ");	
				}
			}
		}
        //- via cache
        if(pos == 0 && hmCache != null && hmCache.size() > 0){
            hm = this.readObject("cache:", hmCache);
            if((boolean)hm.get(0)){
                //- debug(logTag + " execBy read cache succ..."+hmCache);
            }
            else{
                debug(logTag + " execBy read cache failed and try db...");
                this.set("cache:" + origSql, hmCache.get("key"));
                hm = this.execBy(sql, args);
            }
        }
        else{
        //- via db
		// remedy for time fields, Mar 13, 2018
		String nowStr = (new SimpleDateFormat("yyyy-MM-dd HH:mm:ss")).format(new Date());
		for(String timef : this.timeFieldArr){
			if(sql.indexOf(timef) > -1 && this.get(timef).equals("")){
				this.set(timef, (nowStr));	
			}
		}	
		if(!args.equals("")){
			if(sqlx.indexOf(" WHERE") > -1){
				sql += args;	
			}
			else{
				sql += " where " + args;	
			}
		}
		if(pos == 0){
			//- read mode
			hm = this.dba.select(sql, this.hmf);
			//- set cache
			this._setCache(hm, origSql); 
		}
		else{
			//- write mode
			hm = this.dba.update(sql, this.hmf);
			//-rm old cache when updt succ, 09:57 2020-08-20
			if(hmCache != null && (boolean)hm.get(0)){
				this.rmBy("cache:"+hmCache.get("key"));
			}
		}
		sql = null; sqlx = null; args = null;

        }
		return hm;
	} 

	//- @override
	//- execBy with no cache
	public HashMap execBy(String sql, String args){
		HashMap hmCache = new HashMap();
		return this.execBy(sql, args, hmCache);
	}

	/*
	 * mandatory return $hm = (0 => true|false, 1 => string|array);
	 * Thu Jul 21 11:31:47 UTC 2011, wadelau@gmail.com
	 * reported by wadelau@ufqi.com, Sun Jul 17 22:15:17 CST 2016
	 */
	public HashMap rmBy(String args){
		HashMap hm = new HashMap();
		args = args==null ? "" : args;
		boolean isSqlReady = false;
		StringBuffer sqlb = new StringBuffer("delete from ");
		sqlb.append(this.getTbl()).append(" where ");
		if(args.equals("")){
			if(this.getId().equals("")){
				hm.put(0, false);
				hm.put(1, (new HashMap()).put("errordesc", 
                    "unconditional deletion is strictly forbidden. stop it. sql:["
                    + sqlb.toString()+"] conditions:["+ args + "]"));
			}
			else{
				sqlb.append(this.myId).append("=?");
				isSqlReady = true;
				
			}
		}
		else{
			boolean isRmCache = false;boolean isFile = false;
			if(args.indexOf("cache:")==0){ isRmCache = true; }
			else if(args.indexOf("file:")==0){ isFile = true; }
			if(isRmCache){
				//- rm cache when updt, xenxin@ufqi.com, 12:05 2020-08-20
				//- args=cache:keyString
				hm = this.writeObject("cache:", Wht.initHashMap("key", args.substring(6))); // rm cache without value as key.
			}
			else if(isFile){
				hm = this.filea.rm(args.substring(5));
			}
			else{
				sqlb.append(args);
				isSqlReady = true;
			}
		}
		if(isSqlReady){
			System.out.println(logTag + " rmBy: sql:["+sqlb.toString()+"]");
			hm = this.dba.update(sqlb.toString(), this.hmf);	
			if(!this.getId().equals("")){
				this.setId("");		
			}
		}
		sqlb = null; args = null;
		return hm;
	}

	//-
	public String getTbl(){
		return this.get("tbl");
	}
	
	//-
	public void setTbl(String tbl){
		this.set("tbl", tbl);
		if(this.dba == null){
			this.dba = new Dba("");
		}
	}
	
	//-
	public String getId(){
		String xId = this.get(this.myId);
		if(!xId.equals("")){
			return xId;
		}
		else{
			String xIdName = this.get(this.myIdName);
			if(!xIdName.equals("")){
				xId = this.get(xIdName);
				this.setMyId(xIdName);
				return xId;
			}
			else{
				return "";
			}
		}
	}
	
	//-
	public void setId(String id){		
		this.set(this.myId, id);
	}
	
	//-
	public void setMyId(String myId){	
		this.myId = myId;
		this.set(this.myIdName, myId);
	}

	//-
	//- export properties to hashmap
	//- for cross-page object, refer to mod/User fromHm
	//- wadelau@ufqi.com, Tue Jul 26 22:56:54 CST 2016
	public HashMap toHash(){	
		return this.hmf;
	}
 
	//- get list by ids
	//- Xenxin@ufqi, 11:25 Monday, May 6, 2019
	public HashMap getListByIds(String ids){
		HashMap hmrtn = null; //new HashMap();
		final String tmpIds = ids; final String tmpTbl = this.getTbl();
		hmrtn = this.execBy("select * from "+tmpTbl+" where id in ("+ids+")", "", 
			(new HashMap(){{ put("key", tmpTbl+"-list-"+tmpIds);}}));
		if((boolean)hmrtn.get(0)){
			hmrtn = (HashMap)hmrtn.get(1);
			HashMap hmtmp = null; HashMap hmNew = new HashMap();
            String tmpId = null;
            for(Object key : hmrtn.keySet()){
                hmtmp = (HashMap)hmrtn.get(key);
                tmpId = Wht.getString(hmtmp, "id");
                hmNew.put(tmpId, hmtmp);
            }
            hmrtn = hmNew;
		}
		else{
			hmrtn = new HashMap();
		}
		return hmrtn;
	}
	
	//- get ids from a hashmap
	public String getIds(HashMap hm, String idName){
        String myIds = ""; 
        if(hm != null && hm.size()>0){
            HashMap hmtmp = new HashMap();
            for(Object key : hm.keySet()){
                hmtmp = (HashMap)hm.get(key);
                myIds += Wht.getString(hmtmp, idName)+",";
            }
            if(Wht.endsWith(myIds, ",")){
                myIds = myIds.substring(0, myIds.length()-1);
            }
        }
        return myIds;
    }
 
    //- private methods
    //- read an object 
    protected HashMap readObject(String type, HashMap args){
        HashMap rtnobj = new HashMap();
        type = type==null ? "" : type;
        HashMap hmtmp = new HashMap();
        if(type.equals("cache:")){
            //- cache service
            rtnobj.put(0, true);
            if(this.cachea != null){
                rtnobj = (HashMap)this.cachea.get((String)args.get("key"));
            }
			else{
				rtnobj.put(0, false);
			}
            if((boolean)rtnobj.get(0)){
                rtnobj.put(0, true);
                rtnobj.put(1, rtnobj.get(1));
            }
            else{
                hmtmp.put("errcode", 1606140931);
                hmtmp.put("errordesc", rtnobj.get(1));
                final HashMap hmtmp2 = hmtmp;
                rtnobj = new HashMap(){{
                        put(0, false);
                        put(1, hmtmp2);
                    }}; 
            }
        }
        else if(type.equals("file:")){
            rtnobj.put(0, true);
            if(this.filea != null){
                rtnobj = (HashMap)this.filea.read((String)args.get("file"), hmtmp);
            }
			else{
				rtnobj.put(0, false);
			}
            if((boolean)rtnobj.get(0)){
                rtnobj.put(0, true);
                rtnobj.put(1, rtnobj.get(1));
            }
            else{
                hmtmp.put("errcode", 1901021637);
                hmtmp.put("errordesc", rtnobj.get(1));
                final HashMap hmtmp2 = hmtmp;
                rtnobj = new HashMap(){{
                        put(0, false);
                        put(1, hmtmp2);
                    }};
            }
        }
        else if(type.equals("url:")){
            //- @todo
        }
        else{
           //- @todo 
        }
        return rtnobj;
    }

    //- write an object to somewhere
    protected HashMap writeObject(String type, HashMap args){
        HashMap rtnobj = new HashMap();
        type = type==null ? "" : type;
        HashMap hmtmp = new HashMap();
        if(type.equals("cache:")){
            //- cache service
            rtnobj.put(0, true);
            if(this.cachea != null){
                if(!args.containsKey("value")){
                    rtnobj = this.cachea.rm((String)args.get("key")); //- see this.rmBy
                }
                else{
                    if(args.containsKey("expire")){
                        rtnobj = this.cachea.set((String)args.get("key"),
                                    args.get("value"), Integer.valueOf((String)args.get("expire")));
                    }
                    else{
                        rtnobj = this.cachea.set((String)args.get("key"),
                                    args.get("value"));
                    }
                }
            }
            else{
				rtnobj.put(0, false);
			}
            if((boolean)rtnobj.get(0)){
                rtnobj.put(0, true);
                rtnobj.put(1, rtnobj.get(1));
            }
            else{
                hmtmp.put("errcode", 1606140930);
                hmtmp.put("errordesc", rtnobj.get(1));
                final HashMap hmtmp2 = hmtmp;
                rtnobj = new HashMap(){{
                        put(0, false);
                        put(1, hmtmp2);
                    }}; 
            }
        }
        else if(type.equals("file:")){
            //- @todo
        }
        else if(type.equals("url:")){
            //- @todo
        }
        else{
           //- @todo 
        }
        return rtnobj;
    }

    //-
	//- set back cache when successful retrieve
    private boolean _setCache(HashMap hm, String fields){
        //- built-in cache successful resultset
        boolean issucc = false;
        if((boolean)hm.get(0)){
            boolean noExtra = true;
            String key = (String)this.get("cache:" + fields, noExtra);
            if(key != null && !key.equals("")){
            	final HashMap hmtmp2 = hm;
                this.setBy("cache:", (new HashMap(){{
                    put("key", key);
                    put("value", hmtmp2.get(1));
                    }}));
                this.set("cache:" + fields, "");
                issucc = true;
            }
        }
        else{
            //- @todo
        }
        return issucc;
    }
	
}

%>