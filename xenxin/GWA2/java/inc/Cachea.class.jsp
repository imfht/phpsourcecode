<%
/* Cache Administration, handling all cache transations across the site.
 * v0.1
 * wadelau@gmail.com
 * since Wed Jul 13 18:22:06 UTC 2011
 * Thu Sep 11 16:34:20 CST 2014
 * Ported into Java by wadelau@ufqi.com, June 28, 2016s
 * with various connection and/or different drivers support
 */

%><%@include file="./Memcached.class.jsp"%><%
%><%!

public final class Cachea { //- cache administrator

    //- variables
    private final static int MAX_CONNECTION = 5;
	private CacheConn myConn;	
	private CacheDriver myDriver;
    private int myExpire = 30 * 60;
    private final static String Log_Tag = "inc/Cachea";
	
	//- constructor
	public Cachea(String hmconf){
		hmconf = hmconf==null ? "" : hmconf;
        hmconf = hmconf.equals("") ? "Cache_Master" : hmconf;
        this.myConn = new CacheConn(hmconf); 
        this.myExpire = this.myConn.myExpire;
        String strDriver = (String)Config.get("cachedriver");
        if(strDriver.equals("MEMCACHED")){
            this.myDriver = new Memcached(this.myConn);
            //debug("inc/Cachea: drv:"+myDriver+" is initiated by conn:"+myConn);
        }
        else{
			System.out.println(Log_Tag + " Unsupported cacheDriver:["
				+strDriver+"]. 1808012202.");
        }	
	}

    //- methods
    public HashMap get(String key){
       Object myobj = this.myDriver.get(key); 
       final Object tmpObj = myobj;
       if(myobj != null){
            return (new HashMap(){{
                put(0, true);
                put(1, tmpObj);
                }});
        }
        else{
            return (new HashMap(){{
                put(0, false);
                put(1, "error:"+tmpObj); 
                }});
        }
    }

    //-
    public HashMap set(String key, Object value){
        return this.set(key, value, this.myExpire); 
    }

    //-
    public HashMap set(String key, Object value, int seconds){
        boolean issucc = this.myDriver.set(key, value, seconds); 
        final boolean tmpSucc = issucc;
        if(issucc){
            return (new HashMap(){{
                put(0, true);
                put(1, tmpSucc);
                }});
        }
        else{
            return (new HashMap(){{
                put(0, false);
                put(1, "error:"+tmpSucc); 
                }});
        }
    }

    //-
    public HashMap rm(String key){
        boolean issucc = this.myDriver.rm(key);
        final boolean tmpSucc = issucc;
        if(issucc){
            return (new HashMap(){{
                put(0, true);
                put(1, tmpSucc);
                }});
        }
        else{
            return (new HashMap(){{
                put(0, false);
                put(1, "error:"+tmpSucc); 
                }});
        }
    }

    //-
    public void close(){
        this.myDriver.close();
    }
	
}

//----
//- interface of CacheDriver
public interface CacheDriver{
    
    //- @todo in Impls classes

    //private void _init();

    public Object get(String key);
    
    public boolean set(String key, Object value);
    
    public boolean set(String key, Object value, int seconds);

    public boolean rm(String key);

    public void close();

}


%>