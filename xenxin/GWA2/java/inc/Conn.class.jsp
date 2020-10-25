<%
/* Connections configures, for all settings.
 * conn config, connecting, connection pool, long/short connections
 * v0.1
 * wadelau@ufqi.com
 * since Wed Jul 13 18:20:28 UTC 2011
 * Ported into Java by wadelau@ufqi.com, June 28, 2016
 */

%><%!
//- for db service
public class DbConn{
	
	//- connection poll?
	String myHost = "";
	int myPort = 0;
	String myUser = "";
	String myPwd = "";
	String myDb = "";
	String myDriver = "MYSQL";
	
	//- constructor
	public DbConn(String dbServer){
		
		if(dbServer == null || dbServer.equals("")){
			dbServer = "master";	
		}

		if(dbServer.equals("master")){
			//- master
			this.myHost = (String)Config.get("dbhost"); //- inc/Config.class
			this.myPort = (Integer)Config.get("dbport");
			this.myUser = (String)Config.get("dbuser");
			this.myPwd = (String)Config.get("dbpassword");
			this.myDb = (String)Config.get("dbname");
			this.myDriver = (String)Config.get("dbdriver");
		}
		else if(dbServer.equals("slave")){
			//- slave	

		}
		else if(dbServer.equals("userdb")){
			this.myHost = (String)Config.get("dbhost_userdb"); //- inc/Config.class
			this.myPort = (Integer)Config.get("dbport_userdb");
			this.myUser = (String)Config.get("dbuser_userdb");
			this.myPwd = (String)Config.get("dbpassword_userdb");
			this.myDb = (String)Config.get("dbname_userdb");
			this.myDriver = (String)Config.get("dbdriver_userdb");
		}
		else{
			System.out.println("Unknown dbServer:["+dbServer+"]. 1607021811.");	
		}
	}
}

//- for Cache service
public static class CacheConn{
	
    String myHost = "";
    int myPort = 0;
    String myDriver = "";
    int myExpire = 30*60;
    int myMaxConn = 5;
	//- @todo, socket pool, config, connection
	public CacheConn(String cacheServer){
        cacheServer = cacheServer==null ? "" : cacheServer;
        cacheServer = cacheServer.equals("") ? "Cache_Master" : cacheServer;
        if(cacheServer.equals("Cache_Master")){
            this.myHost = (String)Config.get("cachehost");
            this.myPort = (Integer)Config.get("cacheport");
            this.myDriver = (String)Config.get("cachedriver");
            this.myExpire = (Integer)Config.get("cacheexpire");
            this.myMaxConn = (Integer)Config.get("cachemaxconn");
        }
        else{
            debug("inc/Conn: unsupported cacheServer:["+cacheServer+"] 1808012241.");
        }
	}
	
}

//- session service
public static class SessionConn{
	
	//- @todo, socket pool
	public SessionConn(String sessionServer){
		//- @todo
	}
	
}

//- file service
public static class FileConn{
	
	//- @todo, file handler pool
	public FileConn(String FileServer){
		//- @todo
        //- linux, windows, mac
        //- local or remote
	}
	
}


%>
