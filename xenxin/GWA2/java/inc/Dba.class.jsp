<%
/* DB Administration, handling all db transations across the site.
 * v0.1
 * wadelau@gmail.com
 * since Wed Jul 13 18:22:06 UTC 2011
 * Thu Sep 11 16:34:20 CST 2014
 * Ported into Java by wadelau@ufqi.com, June 28, 2016s
 */

%><%@page import="java.sql.Connection,
java.sql.SQLException,
java.sql.Statement,
java.sql.ResultSetMetaData,
java.util.HashSet"%><%
%><%@include file="./MySql.class.jsp"%><%
%><%!

public final class Dba { //- db administrator

	protected DbConn dbConf;
	protected DbDriver dbDrv;
	private final HashSet<String> Sql_Operator_List = new HashSet<String>(
			java.util.Arrays.asList(new String[]{
				" ","^","~",":","!","/",
				"*","&","%","+","=","|",
				">","<","-","(",")",","
				}
			));
	private final String[] Sql_Sep_List_Right = new String[]{" ", ")", ","}; //- 22:45 Wednesday, April 29, 2020
    private final static String Log_Tag = "inc/Dba ";

	//- constructor	
	public Dba(String xconf){
		xconf = xconf==null ? "master" : xconf;
		//System.out.println(" dbname in inc/Dba.. ["+xconf+"]");
		this.dbConf = new DbConn(xconf); 
        String dbDriver = "";
        if(xconf.equals("")){
		    dbDriver = (String)Config.get("dbdriver");
        }
        else{
		    dbDriver = (String)Config.get("dbdriver_"+xconf);
        }
		if(dbDriver.equals("MYSQL")){
			this.dbDrv = new MySql(this.dbConf);	
		}
		else{
			debug("Dba.class: Unknown dbDriver:["+dbDriver+"]. 1607021745.\n");	
		}
		//System.out.println("Dba.class: dbDriver:["+dbDriver+"] dbc:["+this.dbDrv+"]. 1607021957.\n");	

	}

    //- destructor
    public void finalize(){
        //- @todo
    }

	//- select / retrieve
	public HashMap select(String sql, HashMap args){
	
		HashMap hm = new HashMap();
		
		Object[] idxArr = sortObject(sql, args);
		
		HashMap tmpHm = new HashMap();
		boolean hasLimitOne = false;
		int pageSize = 0;
		if(args.containsKey("pagesize")){
			String tmpps = String.valueOf(args.get("pagesize"));
			if(tmpps.equals("")){}else{ pageSize = Integer.parseInt(tmpps); }
		}
		if(sql.indexOf("limit 1 ") > -1 || pageSize == 1){
			hasLimitOne = true;
			tmpHm = this.dbDrv.readSingle(sql, args, idxArr);
		}
		else{
			tmpHm = this.dbDrv.readBatch(sql, args, idxArr);
		}
		//debug("inc/Dba: select:["+tmpHm+"] hasLimitOne:"+hasLimitOne);	
		if((boolean)tmpHm.get(0)){ //- what's for?
			hm.put(0, true);
			try{
				if(hasLimitOne){
					hm.put(1, tmpHm.get(1));	
				}
				else{
					hm.put(1, tmpHm.get(1));
				}
			}
			catch(Exception ex){
				ex.printStackTrace();
			}
		}
		else{
			hm.put(0, false);
			hm.put(1, tmpHm.get(1));
		}
		tmpHm = null; idxArr = null;
		//hm.put("read-in-Dba", (new Date()));	

		return hm;
	
	}
	
	
	//- update / write
	public HashMap update(String sql, HashMap args){
	
		HashMap hm = new HashMap();

		Object[] idxArr = sortObject(sql, args);
		
		HashMap tmpHm = this.dbDrv.query(sql, args, idxArr);
		
		if((boolean)tmpHm.get(0)){
			hm.put(0, true);
			HashMap inHm = new HashMap();
			inHm.put("insertedid", tmpHm.get(1));
			inHm.put("affectedrows", tmpHm.get(1));
			hm.put(1, inHm);
		}
		else{
			hm.put(0, false);
			hm.put(1, tmpHm.get(1));
		}

		//hm.put("write-in-Dba", (new Date())+" args:["+args+"] idxArr:["+idxArr+"]");	
		tmpHm = null; idxArr = null;
		
		return hm;
	
	}
	
	//-- sort param ? and its variable name
	//- bugfix on 23:03 Wednesday, April 29, 2020 
	public Object[] sortObject(String sqlstr, HashMap hmvar){
		Object[] obj = new Object[1];
		if(hmvar != null){
			int ki=0; String k=null;
			int hmsize=hmvar.size();
			obj=new Object[hmsize+1];
			int sqlLen=sqlstr.length();
			Object[] tmpobj=new Object[sqlLen];
			
			int tmpidx=0;
			int selectPos = sqlstr.indexOf("select ");
			int wherePos = sqlstr.indexOf(" where ");
			HashSet sqloplist = this.Sql_Operator_List;
			int keyLen = 0; int keyPos = -1;
			String preK = null; String aftK = null;
			//- compute position of each field
			int[] keyPosArr = new int[sqlLen]; int keyPosI = 0;
			Iterator itr=hmvar.keySet().iterator();
			int lastKeyPos = -1;
			while(itr.hasNext()){
				k=(String)itr.next();
				k = k==null ? "" : k;
				if(k.equals("")){
					continue; 
				}
				keyLen = k.length();
				keyPos = sqlstr.indexOf(k);
				if(keyPos > -1){
					while(keyPos != -1){
						preK = sqlstr.substring(keyPos-1, keyPos);
						tmpidx = keyPos + keyLen; tmpidx = tmpidx>=sqlLen ? sqlLen-1 : tmpidx;
						aftK = sqlstr.substring(tmpidx, tmpidx+1);
						if(sqloplist.contains(preK) && sqloplist.contains(aftK)){
							if(selectPos > -1){
								if(keyPos > wherePos){
									tmpobj[keyPos] = k;
								}
								else{
									//- select fields
								}
							}
							else{
								tmpobj[keyPos] = k;
							}
							//System.out.println("inc/Dba: sortObject: k:["+k+"] pos:["+keyPos+"] lastKeyPos:["+lastKeyPos+"] preK:["+preK+"] aftK:["+aftK+"] sql:["+sqlstr+"] ");
						}
						else{
							//System.out.println("Dba.sortObject: found illegal key preset. k:["+k+"] pos:["
							//		+keyPos+"] preK:["+preK+"] aftK:["+aftK+"] sql:["+sqlstr+"]");
						}
						lastKeyPos = keyPos;
						keyPos = sqlstr.indexOf(k,  tmpidx);
					}
				}
				else{
					//System.out.println("Dba.sortObject: no such key:["+k+"] in sql:["+sqlstr+"]");
				}
			}
			//- figure out each param mark, i.e., "?"
			int tmpi = 0;  int paramPos = 0; int minParamPos = sqlLen - 1;
			int[] paramPosArr = new int[sqlLen]; tmpi = 0;
			for(String SqlSepR : this.Sql_Sep_List_Right){
				tmpidx = sqlstr.indexOf("?"+SqlSepR);
				while(tmpidx > -1){
					paramPosArr[tmpi] = tmpidx;
					//System.out.println("inc/Dba: tmpi:"+tmpi+" paramPos:"+tmpidx);
					if(tmpidx < minParamPos){ minParamPos = tmpidx; }
					tmpidx = sqlstr.indexOf("?"+SqlSepR, tmpidx+1); 
					tmpi++;
				}
			}
			if(sqlstr.substring(sqlLen-1, sqlLen).equals("?")){
				paramPosArr[tmpi] = sqlLen - 1;
			}
			//System.out.println("inc/Dba: 22-tmpi:"+tmpi+" paramPos:"+tmpidx+" lastChar:"+sqlstr.substring(sqlLen-1, sqlLen));
			
			//- sort each field by its pos
			tmpi = 0; Object kiso = null; String kis = null; int maxKeyPos = 0;
			HashMap<String, Integer> kSerial = new HashMap<String, Integer>();
			int tmpj = 0; lastKeyPos = -1;
			tmpi = tmpj = 0;
			for(int i=0; i<sqlLen; i++){
				k = (String)tmpobj[i];
				if(k != null){
					kiso = kSerial.get(k);
					kis = kiso==null ? "" : kiso.toString();
					if(kis.equals("")){
						obj[tmpi] = k;
						kSerial.put(k, 1);
					}
					else{
						ki = Integer.valueOf(kis);
						obj[tmpi] = k + "." + (ki+1);
						kSerial.put(k, ki+1);
					}
					//System.out.println("inc/Dba: i:["+i+"] tmpi:["+tmpi+"] k:["+k+"] obj-k:"+obj[tmpi]);
					keyPosArr[keyPosI++] = i;
					tmpi++;
					lastKeyPos = i;
					if(i > maxKeyPos){ maxKeyPos = i; }
				}
			}
			//- match each param mark with its field
			boolean hasMark = false; 
			tmpi = tmpj = 0;  lastKeyPos = -1; int nextKeyPos = -1;
			
			if(minParamPos < maxKeyPos){
				Object[] obj2 = new Object[hmsize+1]; int obj2i = 0;
				int keyPosArrLen = sqlLen - 1; int paramPosArrLen = sqlLen-1;
				while(tmpj < keyPosArrLen){
					keyPos = keyPosArr[tmpj];
					if(keyPos > 0){
						//System.out.println("inc/Dba: keyPosArr: key-i:["+tmpj+"] keyPos:["+keyPos+"] lastKeyPos:"+lastKeyPos+" k:"+obj[tmpj]);
						nextKeyPos = keyPosArr[tmpj+1];
						hasMark = false;  tmpi = 0;
						while(tmpi < paramPosArrLen){
							paramPos = paramPosArr[tmpi];
							if(paramPos > 0){
								if(paramPos > keyPos){
									if(nextKeyPos > 0){
										if(paramPos < nextKeyPos){
											hasMark = true;
										}
									}
									else{
										hasMark = true;
									}
								}
								//System.out.println("\tinc/Dba: paramPosArr: param-i:["+tmpi+"] paramPos:["+paramPos+"] lastKeyPos:"+lastKeyPos+" nextKeyPos:"+nextKeyPos+" hasMark:"+hasMark);
								if(hasMark){ obj2[obj2i++] = obj[tmpj]; break; }
							}
							tmpi++;
						}
						lastKeyPos = keyPos;
					}
					tmpj++;
				}
				//- override
				obj = obj2; obj2 = null;
			}
			else{
				//- bypass: (a, b, c) ...(?, ?, ?)
				//- bypass: single param: a=?
			}
			
			itr=null; tmpobj=null; 
			
		}
		//System.out.println("inc/Dba: obj:["+obj+"] vars:["+hmvar+"]");
		return obj;
	
	}

    //-
    public void close(){
        if(this.dbDrv != null){
            this.dbDrv.close();
        }
    }
		
}


//- define for all drivers
//- diff with GWA2 in PHP
public interface DbDriver{
	
	//- @todo in Impls

	//private void _init();

	public HashMap query(String sql, HashMap args, Object[] idxArr);

	public HashMap readSingle(String sql, HashMap args, Object[] idxArr);
	
	public HashMap readBatch(String sql, HashMap args, Object[] idxArr);
	
	public void selectDb(String myDb);
	
	public int getLastInsertedId();
	
	public int getAffectedRows();

    public void close();
	

}

%>
