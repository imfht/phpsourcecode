<%
/* DB Driver, MySql, implements DbDriver
 * v0.1
 * wadelau@gmail.com
 * since Wed Jul 13 18:22:06 UTC 2011
 * Thu Sep 11 16:34:20 CST 2014
 * Ported into Java by wadelau@ufqi.com, June 28, 2016s
 * With socket pool, Xenxin@Ufqi,  Fri Jul 20 12:30:29 UTC 2018
 *    need socket pool support from Tomcat, Resin, JBoss, or inc/SocketPool
 */

%><%@page import="javax.sql.DataSource,
java.sql.DriverManager,
java.sql.ResultSet,
java.sql.PreparedStatement,
javax.naming.InitialContext,
javax.naming.Context
"%><%

%><%!

//- mysql driver
public final class MySql implements DbDriver {

	private String myHost = "";
	private int myPort = 3306;
	private String myUser = "";
	private String myPwd = "";
	private String myDb = "";
    private boolean hasSocketPool = false;
    
    protected Context ctx = null; 
	protected Connection dbConn = null;
    //protected DataSource ds = null;	
    protected DataSource ds = null;	
    private final static String Log_Tag = "inc/Mysql ";

    //- constructor
	public MySql(DbConn dbConf){
		
		this.myHost = dbConf.myHost;	
		this.myPort = dbConf.myPort; 
		this.myUser = dbConf.myUser; 
		this.myPwd = dbConf.myPwd; 
		this.myDb = dbConf.myDb; 

        this.hasSocketPool = (boolean)Config.get("db_enable_socket_pool");
        //debug("inc/MySql: db:"+this.myDb+" haspool:"+hasSocketPool);
        if(this.hasSocketPool && !this.myDb.equals("")){
            try{
				this.ctx = new InitialContext();
				this.ds = (DataSource)ctx.lookup("java:comp/env/jdbc/"+this.myDb);
            }
            catch(Exception ex){
                ex.printStackTrace();
            }
        }
		else{
			//- no connection pool or emty db , ref: -R/z2SU 
		}
	}

    //- destructor
    public void finalize(){
        //- @todo
    }

	//- init connection 
	private void _init(){	
		try{
			if(this.dbConn == null){
                if(this.hasSocketPool){
                    //- with socket pool
                    this.dbConn = this.ds.getConnection();
                    //debug(Log_Tag+"get a new conn from pool:"+this.dbConn);
                }
                else{
                     /*
                      * standalone without socket pool
                      */
                    Class.forName("com.mysql.jdbc.Driver"); //- need prior to JDBC 4.0
                    this.dbConn = DriverManager.getConnection("jdbc:mysql://" + this.myHost + ":" 
                        + this.myPort + "/" + this.myDb + "?" + "user=" + this.myUser 
                        + "&password=" + this.myPwd + "&useSSL=false&characterEncoding=utf8");
                }
			}
		}
		catch(Exception ex){
			ex.printStackTrace();	
		}
		finally{
			//- release? long connection?  
		}
	}
	
	//-
	public HashMap query(String sqlstr, HashMap args, Object[] idxArr){
	
		HashMap hm = new HashMap();
		hm.put("query-in-MySql", (new Date()));	

		if(this.dbConn == null){
			this._init();
		}
		
		PreparedStatement pstmt =  null ;
		try{
			
			sqlstr = sqlstr.trim();
			pstmt = this.dbConn.prepareStatement(sqlstr,Statement.RETURN_GENERATED_KEYS);
			int paraCount = (pstmt.getParameterMetaData()).getParameterCount();
			ResultSet rs = null ;
			//System.out.println("sqlstr:["+sqlstr+"] pstmt:["+pstmt+"]");
			
			int myj = 1 ;
			for(int myi=0;myi<idxArr.length && myi<paraCount;myi++){
				//System.out.println("inc/Mysql: myi:["+myi+"] val:["+String.valueOf(idxArr[myi])+"]");
				//pstmt.setString(myi,String.valueOf(idxArr[myi-1]));
				//pstmt.setObject(myi,idxArr[myi-1]);
				if( idxArr[myi] != null ){
					//pstmt.setObject(myi+1,idxArr[myi]);
					pstmt.setObject(myj, args.get(idxArr[myi]));
					myj++;
				}
			}
							
			int affectrows = pstmt.executeUpdate();
			if(affectrows > 0){
				rs = pstmt.getGeneratedKeys();
				int genId = 0;
				if(rs!=null && rs.next()){
					genId = rs.getInt(1);
					//System.out.println("rs-1:["+rs.getString(1)+"]");	
					rs.close();
					rs = null ;		
				}
				if(genId > 0){
					affectrows = genId;	
				}
			}
			
			hm.put(0, true);
			hm.put(1, affectrows);
		
		}
		catch (Exception ex){
			hm.put(0, false);
			hm.put(1, 0);
			ex.printStackTrace();
			//System.out.println("err@DBACT.execSQLSafe():"+e);
		}
		finally{
			free( pstmt ) ; //@todo
		}
		
		return hm;

	}

	//-
	public HashMap readSingle(String sqlstr, HashMap args, Object[] idxArr){
	
		HashMap hm = new HashMap();
		hm.put("readSingle-in-MySql", (new Date()));	

		if(this.dbConn == null){
			this._init();
		}
		
		PreparedStatement pstmt = null ;
		HashMap hmtmp = new HashMap();		
		try{
			
			sqlstr = sqlstr.trim();
			if( sqlstr.indexOf(" limit") < 0 ){
				sqlstr += " limit 1 ";
			}

			pstmt = this.dbConn.prepareStatement(sqlstr);
			int paraCount = (pstmt.getParameterMetaData()).getParameterCount();
			if( idxArr!=null ){
				int myj = 1 ;
				for( int myi=0;myi<idxArr.length && myi<paraCount;myi++ ){
					//System.out.println("MySql.readSingle: myj:["+myj+"] myi:["+myi+"] idxArr-i:["+idxArr[myi]+"]");
					if( idxArr[myi] != null ){
						//pstmt.setObject(myi+1,idxArr[myi]);
						pstmt.setObject(myj, args.get(idxArr[myi]));
						myj++;
					}
					else{
						System.out.println("MySql.readSingle: ???");
					}
				}
			}

			//hm.put("1", pstmt.executeQuery() );
			ResultSet rs = pstmt.executeQuery();
			ResultSetMetaData rsmd = rs.getMetaData();
			if( rs.next() ){
				hmtmp = new HashMap();
				int cci = rsmd.getColumnCount() ;
				String fieldname = null  ;
				String fieldvalue = null  ;
				for(int i=1; i<=cci;i++){
					fieldname = rsmd.getColumnName(i) ;
					fieldvalue = rs.getString(i) ; //- fieldname, remedy by wadelau, 13:01 18 July 2016
					fieldname = fieldname.toLowerCase() ;
					hmtmp.put(fieldname, fieldvalue) ;
				}
				hm.put(0, true);
				HashMap hmtmp2 = new HashMap();
				hmtmp2.put(0, hmtmp);
				hm.put(1, hmtmp2); //- hm[1][0]
			}
			else{
				hm.put(0, false);
				hmtmp.put(0, "No Record. 1906241109.");
				hm.put(1, hmtmp); //- hm[1][0]
			}
			hmtmp = null; rsmd = null;
			rs.close(); rs = null;
						
		}
		catch (Exception ex){
			hm.put(0, false);
			hmtmp.put(0, "No Record. 1906241109.");
			hm.put(1, hmtmp); //- hm[1][0]
			ex.printStackTrace();
			//System.out.println("DBACT.getExistSafe():"+e+" sql:["+sqlstr+"]");
		}
		finally{
			free(pstmt); // @todo
		}
		
		return hm;
	}
	
	//-
	public HashMap readBatch(String sqlstr, HashMap args, Object[] idxArr){
		HashMap hm = new HashMap();
		if(this.dbConn == null){
			this._init();
		}
	    //debug("readBatch-in-MySql:"+(new Date())+" sql:["+sqlstr+"] dbConn:"+this.dbConn);	
		PreparedStatement pstmt =  null ; 
		HashMap hmtmp = new HashMap();
		try{
			pstmt = this.dbConn.prepareStatement(sqlstr);
			int paraCount = (pstmt.getParameterMetaData()).getParameterCount();
			if( idxArr!=null ){
				int myj = 1 ;
				for(int myi=0;myi<idxArr.length && myi<paraCount;myi++){
					if( idxArr[myi] != null ){
						//System.out.println("MySql.readBatch: myj:["+myj+"] myi:["+myi+"] idxArr-i:["+idxArr[myi]+"]");
						//pstmt.setObject(myi+1,idxArr[myi]);
						pstmt.setObject(myj, args.get(idxArr[myi]));
						myj++ ;
					}
				}
			}
			//hm.put("1", pstmt.executeQuery() );
			ResultSet rs = pstmt.executeQuery();
			HashMap hmtmp2 = null;
			int count = 0 ;
			ResultSetMetaData rsmd = rs.getMetaData() ;
			int icc = rsmd.getColumnCount() ;
			String fieldname = null ;
			String fieldvalue = null ;
			while ( rs.next() ){
				hmtmp2 = new HashMap() ;
				for(int i=1; i<=icc; i++ ){
					fieldname = rsmd.getColumnName(i) ;
					fieldvalue = rs.getString(i); // rs.getString(fieldname); remedy by wadelau, Sun Jul 17 22:51:13 CST 2016
					fieldname = fieldname.toLowerCase() ;
					hmtmp2.put(fieldname, fieldvalue);
				}
				//hmtmp.put("yyy"+count, hmtmp2);
				hmtmp.put(count, hmtmp2);
				count++;
			}
			//hmtmp.put("count",""+count);
			if(count > 0){
				hm.put(0, true);
				hm.put(1, hmtmp);
			}
			else{
				hm.put(0, false);
				hmtmp.put(0, "No Record. 1806241110.");
				hm.put(1, hmtmp);
			}	
			hmtmp = null; hmtmp2 = null; rsmd = null;
			rs.close(); rs = null;
			
		}
		catch (Exception e){
			hm.put(0, false);
			hmtmp.put(0, "No Record. 1806241112.");
			hm.put(1, hmtmp);
			e.printStackTrace();
			//System.out.println(e);
            debug("read failed? sql:["+sqlstr+"]");
		}
		finally{
			free(pstmt); // @todo
		}
		
		return hm;

	}
	
	//-
	public void selectDb(String myDb){
		
		this.myDb = myDb;
		this.query("use " + this.myDb, (new HashMap()), (new Object[]{}));
		
	}
	
	//- @todo
	public int getLastInsertedId(){
		return 0;
	}
	
	//- 
	public int getAffectedRows(){
		return 0;
	}
	
	//- @todo
	protected void free(Statement stmt){
		try{
			if(stmt != null){
				stmt.close();
                stmt = null;
			}
		} 
		catch (SQLException ex){
			ex.printStackTrace();
		}
		freeConn(); //- @todo, return to pool
		
	}

	//- @todo
	protected void freeConn(){	
		try{
			if (dbConn != null){
				dbConn.close(); //- back to connection pool for re use?
                dbConn = null;
			}
		}
		catch (SQLException ex){
			ex.printStackTrace();
		}
		
	}

    //-
    public void close(){
        //- when to make hard close or soft close to pool?
        this.freeConn();
    }
	
}

%>
