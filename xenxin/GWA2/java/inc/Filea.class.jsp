<%
/* Files Administration, handling all file transations across the site.
 * v0.1
 * wadelau@gmail.com, wadelau@ufqi.com
 * since Thu Dec 27 08:10:35 UTC 2018
 * Ported into Java by wadelau@ufqi.com, Dec 27, 2018
 */
 
%><%@page import="java.io.*,
java.util.regex.*"%><%
%><%@include file="./FileSystem.class.jsp"%><%
%><%!

public final class Filea { //- files administrator

	protected FileConn fileConf;
	
	protected FileDriver fileDrv;
	
    private final static String Log_Tag = "inc/Filea ";

	//- constructor	
	public Filea(String xconf){
		xconf = xconf==null ? "master" : xconf;
		this.fileConf = new FileConn(xconf); 
        String fileDriver = "";
        if(xconf.equals("")){
		    fileDriver = (String)Config.get("filedriver");
        }
        else{
		    fileDriver = (String)Config.get("dbdriver_"+xconf);
        }
		if(fileDriver.equals("LINUX")){
			this.fileDrv = new FileSystem(this.fileConf);	
		}
		else{
			debug(Log_Tag+"Unknown fileDriver:["+fileDriver+"]. 1607021745.\n");	
		}
		//debug(Log_Tag+"dbDriver:["+dbDriver+"] dbc:["+this.dbDrv+"]. 1607021957.\n");
	}

    //- destructor
    public void finalize(){
        //- @todo
    }

	//- 
	public HashMap read(String myfile, HashMap args){
		HashMap hm = new HashMap();
		HashMap tmpHm = this.fileDrv.read(myfile, args);
		if((boolean)tmpHm.get(0)){ //- what's for?
			hm.put(0, true);
			try{
				hm.put(1, tmpHm.get(1));	
			}
            catch(Exception ex){
				ex.printStackTrace();
			}
		}
		else{
			hm.put(0, false);
			hm.put(1, tmpHm.get(1));
		}
		tmpHm = null; 
		return hm;
	}
	
	//- 
	public HashMap write(String myfile, String contents, HashMap args){
		HashMap hm = new HashMap();
		HashMap tmpHm = this.fileDrv.write(myfile, contents, args);
		if((boolean)tmpHm.get(0)){
			hm.put(0, true);
			HashMap inHm = new HashMap();
			inHm.put(0, tmpHm.get(1));
			hm.put(1, inHm);
		}
		else{
			hm.put(0, false);
			hm.put(1, tmpHm.get(1));
		}
		//debug(Log_Tag+""+ (new Date())+" args:["+args+"] idxArr:["+idxArr+"]");	
		tmpHm = null; 
		return hm;
	}
	
	//-
	public HashMap rm(String myfile){
		HashMap hmResult = null;
		hmResult = this.fileDrv.rm(myfile);
		return hmResult;
	}
	
    //-
    public void close(){
        if(this.fileDrv != null){
            this.fileDrv.close();
        }
    }

}


//- define for all drivers
public interface FileDriver{
	
	//- @todo in Impls

	//private void _init();

	public HashMap read(String myfile, HashMap args);

	public HashMap write(String myfile, String contents, HashMap args);
	
    public HashMap rm(String myfile);

    public void close();
	

}

%>
