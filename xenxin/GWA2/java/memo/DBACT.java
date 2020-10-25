
/*
 * Jsp DB transactions interface
 * wadelau@hotmail.com
 * lastupdated 2006.01.14 06:18
 * reported into GWA2, in memory of those hard-working days.....
 * moved in Sun Jul  3 12:16:01 CST 2016
 */

package com.ufqi.base;

import com.ufqi.base.DBConn;
import com.ufqi.base.WebBean;
import java.sql.*;
import java.util.Collection;
import java.util.HashMap;
import java.util.Set;
import java.util.Iterator;
import java.util.regex.*;

public final class DBACT extends WebBean
{
	
	protected HashMap hm = null ;
	protected String sql = "" ;

	public DBACT()
	{
		//--- constructor
	}
	
	//--- update on 20071129 by wadelau
	public HashMap readDataSafe(String sqlstr,Object[] varsarr)
	{
		HashMap hm = new HashMap();
		PreparedStatement pstmt =  null ; 
		try
		{
			if( !getConnection() )
			{
				return null ;
			}
			else
			{
				//sqlstr = sqlstr.trim();
				pstmt = conn.prepareStatement(sqlstr);
				//ResultSet rs = null ;
				if( varsarr!=null )
				{
					int myj = 1 ;
					for(int myi=0;myi<varsarr.length;myi++)
					{
						//pstmt.setString(myi,String.valueOf(varsarr[myi-1]));
						//pstmt.setObject(myi,(Object)varsarr[myi-1]);
						if( varsarr[myi] != null )
						{
							//pstmt.setObject(myi+1,varsarr[myi]);
							pstmt.setObject(myj,varsarr[myi]);
							myj++ ;
						}
					}
				}
				hm = getRs( pstmt.executeQuery() ) ;	
				//free(pstmt);
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
			System.out.println(e);
		}
		finally
		{
			free(pstmt);
			//freeConn(); //--- has been moved into the upper method, 20071220
		}
		return hm;
	}

	public HashMap getExistSafe(String sqlstr,Object[] varsarr)
	{
		hm = null ;
		if( sqlstr==null || sqlstr.equals("") )
		{
			return hm ;	
		}
		PreparedStatement pstmt = null ;
		try
		{
			if( sqlstr.indexOf(" limit") < 0 )
			{
				sqlstr += " limit 1 ";
			}
			if( !getConnection() )
			{
				return hm ;
			}
			else
			{
				sqlstr = sqlstr.trim();
				pstmt = conn.prepareStatement(sqlstr);
				//ResultSet rs = null ;
				if( varsarr!=null )
				{
					int myj = 1 ;
					for( int myi=0;myi<varsarr.length;myi++ )
					{
						//System.out.println("DBACT::getExistSafe::varsarr "+myi+":"+varsarr[myi]+" myj:"+myj);
						//pstmt.setString(myi,String.valueOf(varsarr[myi-1]));
						if( varsarr[myi] != null )
						{
							//pstmt.setObject(myi+1,varsarr[myi]);
							pstmt.setObject(myj,varsarr[myi]);
							myj++;
						}
						//pstmt.setObject(myi,varsarr[myi-1]);
					}
				}
				//rs = pstmt.executeQuery();	
				hm = getInfo( pstmt.executeQuery() ) ;
					
				//free(pstmt);
			}
		}
		catch (Exception ex)
		{
			ex.printStackTrace();
			//System.out.println("DBACT.getExistSafe():"+e+" sql:["+sqlstr+"]");
		}
		finally
		{
			free(pstmt);
			//freeConn(); //--- has been moved into the upper method, 20071220
		}
		//System.out.println("dbact:hm:["+hm+"]");
		return hm ;
	}
	
	/**
	* execSQL(), execute an update action to db, usally, "insert, delete, update"
	* para sqlstr, an sql which will be executed
	* para needNewId, when sql is "insert" like, name it to 1, then will be rtnval=newid
	* para rtnval, empty when fail to exec, else affected rows or new id
	*/

	//--- added on 20071125 by wadelau
	public String execSQLSafe(String sqlstr,Object[] varsarr, int needNewId)
	{
		String rtnval = "" ;
		PreparedStatement pstmt =  null ;
		try
		{
			if( !getConnection() )
			{
				return rtnval;
			}
			else
			{
				if(!(sqlstr.indexOf("insert")>-1))
				{
					needNewId = 0 ;
				}
				sqlstr = sqlstr.trim();
				if( needNewId == 1 )
				{
					pstmt = conn.prepareStatement(sqlstr,Statement.RETURN_GENERATED_KEYS);
				}
				else
				{
					pstmt = conn.prepareStatement(sqlstr);
				}
				ResultSet rs = null ;
				//System.out.println("sqlstr:["+sqlstr+"] pstmt:["+pstmt+"] neednewid:["+needNewId+"]");
				
				int myj = 1 ;
				for(int myi=0;myi<varsarr.length;myi++)
				{
					//System.out.println("myi:["+myi+"] val:["+String.valueOf(varsarr[myi])+"]");
					//pstmt.setString(myi,String.valueOf(varsarr[myi-1]));
					//pstmt.setObject(myi,varsarr[myi-1]);
					if( varsarr[myi] != null )
					{
						//pstmt.setObject(myi+1,varsarr[myi]);
						pstmt.setObject(myj,varsarr[myi]);
						myj++;
					}
				}
								
				//affectrows = pstmt.executeUpdate(sqlstr,Statement.RETURN_GENERATED_KEYS);
				int affectrows = pstmt.executeUpdate();
				//System.out.println("sqlstr:["+sqlstr+"] pstmt:["+pstmt+"] affectrows:["+affectrows+"]");
				if(affectrows>0)
				{
					rs = pstmt.getGeneratedKeys();
					if(rs!=null && rs.next())
					{
						//rtnval = rs.getString(1);
						rtnval = rs.getString(1);
						//System.out.println("rs-1:["+rs.getString(1)+"]");	
						rs.close();
						rs = null ;		
					}
				}
				//free( pstmt );
			}
		}
		catch (Exception ex)
		{
			ex.printStackTrace();
			//System.out.println("err@DBACT.execSQLSafe():"+e);
		}
		finally
		{
			free( pstmt ) ;
			//freeConn(); //--- has been moved into the upper method, 20071220
		}
		return rtnval ;
	}

	//--- added on 20071126 by wadelau, try to update a record, when not exist, create a new one
	//--- only one-table support
	//--- todo: update two table ?
	public String updatetbl( String tblname, String id, String fields, Object[] obj )
	{
		String rtnstr = "" ;
		if( tblname==null || tblname.equals("") )
		{
			System.out.println("dbact::updatetbl::tblname invalid. tblname:["+tblname+"]");
			return rtnstr ;
		}
		boolean needcreate = false ;
		if( id==null || id.equals("") )
		{
			//---create an new account
			sql = "insert into "+tblname+" set ";
			needcreate = true ;
		}
		else
		{
			if( fields==null || fields.equals("") )
			{
				sql = "delete from "+tblname+" " ;	
			}
			else
			{
				//sql = "update user_ufqi set sid=?,pwd=?,location=?,jobtype=?,feat=?,referid=? where id=? limit 1 ";
				sql = "update "+tblname+" set " ;	
			}
		}
		if( fields != null && !fields.equals("") )
		{
			String[] fieldarr = fields.split(",") ;
			int ilen = fieldarr.length ;
			for( int i=0; i<ilen; i++ )
			{
				sql += fieldarr[i]+"=?";
				if( i < ilen - 1 )
				{
					sql += ", ";
				}
			}
		}
		if( !needcreate )
		{
			sql += " where id=?  limit 1 " ;
			execSQLSafe(sql,obj,0) ;
			rtnstr = "1" ;
		}
		else
		{
			rtnstr = execSQLSafe(sql, obj, 1 ) ;
		}
		System.out.println("dbact::updatetbl sql:["+sql+"] rtnstr:["+rtnstr+"]");
		return rtnstr ;	
	}

	//--- added on 20080903 by wadelau, try to update a record, when not exist, create a new one, with hashmap
	/* e.g.
	 * HashMap hmvar=new HashMap();
	 * hmvar.put("pagesize","1"); //--- max record to be updated by this query
	 * hmvar.put("name","wadelau");
	 * hmvar.put("age","23");
	 * hmvar.put("name.2","wade%"); //--- value to be assigned to the second  "name" 
	 * hmvar.put("age.2","20");
	 * String result=dbact.update("update TBLNAME set age=?, name=? where age>=? and name like ?",hmvar);
	 *
	 */
	public String update( String sqlstr, HashMap hmvar )
	{
		String rtnstr = "" ;
		if( sqlstr==null || sqlstr.equals("") )
		{
			System.out.println("dbact::update: invalid sqlstr:["+sqlstr+"]");
			return rtnstr ;
		}
		if(hmvar==null || hmvar.size()==0)
		{
			System.out.println("dbact::update: invalid hmvar:["+hmvar+"]");
			return rtnstr;
		}
		boolean needcreate = false ;
		sqlstr=sqlstr.trim();
		if(sqlstr.startsWith("insert"))
		{
			needcreate=true;
		}
		
		try
		{
			Object[] obj=sortObject(hmvar,sqlstr);	
			if( !needcreate )
			{
				int limitsize=1;
				if(hmvar.containsKey("pagesize"))
				{
					String tmpsize=(String)hmvar.get("pagesize");
					if(tmpsize.equals("all"))
					{
						tmpsize="999999";
					}
					limitsize=Integer.parseInt(tmpsize);
				}
				sqlstr+=" limit "+limitsize;
				execSQLSafe(sqlstr,obj,0) ;
				rtnstr = "1" ;
			}
			else
			{
				rtnstr = execSQLSafe(sqlstr, obj, 1 ) ;
			}
			System.out.println("dbact::update sql:["+sqlstr+"] rtnstr:["+rtnstr+"] hmvar:["+hmvar+"]");
			obj=null;
			hmvar=null;
			sqlstr=null;
		}
		catch(Exception ex)
		{
			ex.printStackTrace();
		}
		return rtnstr ;	
	}

		
	public HashMap selecttbl( String fields, String tblname, String wherestr, 
					Object[] objarr, int pagesize, int pagenum, String orderby )
	{
		hm = null ;
		boolean isparavalid = true ;
		String errmsg = "dbact::select:";
		if( tblname==null || tblname.equals("") )
		{
			isparavalid = false ;
			errmsg = "tblname:["+tblname+"]";
		}
		if( fields==null || fields.equals("") )
		{
			isparavalid = false ;
			errmsg = "fields:["+fields+"]";
		}
		if( !isparavalid )
		{
			System.out.println("err@"+errmsg+" is invalid.");
			return hm ;
		}
		else
		{
			sql = "select "+fields+" from "+tblname+" " ;
			if( wherestr!=null && !wherestr.equals("") )
			{
				sql += " where "+ wherestr ;
			}
			if( orderby!=null && !orderby.equals("") )
			{
				sql += " order by "+orderby+" " ;
			}
			if( pagenum==0 )
			{
				pagenum = 1 ;
			}
			if( pagesize==0 )
			{
				pagesize = 200 ; //--- allow default max records return per query.
			}
			sql += " limit "+((pagenum-1)*pagesize)+","+pagesize;
			//System.out.println("DBACT.java: currsql:["+sql+"]");
			if( pagesize==1 )
			{
				hm = getExistSafe( sql, objarr ) ;
			}
			else
			{
				hm = readDataSafe( sql, objarr ) ;
			}
			
		}
		return hm ;
	}

	//--- added on 20080903 by wadelau, select data from db one or batch, with hashmap
	/* e.g.
	 * 
	 * HashMap hmvar=new HashMap();
	 * hmavr.put("age",""+23);
	 * hmvar.put("name","wanglin");
	 * hmvar.put("pagenum","1");  //--- current page no
	 * hmvar.put("pagesize","1"); //--- records per page
	 * hmvar.put("orderby","id desc");  //--- sort result
	 * hmvar.put("groupby","age");  //--- group result
	 * HashMap hmdata=dbact.select("select * from TBLNAME where age>=? and name like ?",hmvar);
	 *
	 */
	public HashMap select(String sqlstr, HashMap hmvar)
	{
		hm = null ;
		if( sqlstr==null || sqlstr.equals("") )
		{
			System.out.println("err@DBACT.java:select sql:["+sqlstr+"] invalid.");
			return hm ;
		}
		else
		{
			try
			{
				String wherestr="";
				int wherepos=sqlstr.indexOf(" where");
				if(wherepos>-1)
				{
					wherestr=sqlstr.substring(wherepos);
				}
				
				Object[] obj=sortObject(hmvar,wherestr);

				if(hmvar.containsKey("orderby"))
				{
					sqlstr+=" order by "+(String)hmvar.get("orderby");
				}
				if(hmvar.containsKey("groupby"))
				{
					sqlstr+=" group by "+(String)hmvar.get("groupby");
				}
				int pagenum=1;
				int pagesize=200;
				if(hmvar.containsKey("pagenum"))
				{
					String tmpnum=(String)hmvar.get("pagenum");
					pagenum=Integer.parseInt(tmpnum);
				}
				if(hmvar.containsKey("pagesize"))
				{
					String tmpsize=(String)hmvar.get("pagesize");
					pagesize=Integer.parseInt(tmpsize);
				}
				sqlstr+=" limit "+((pagenum-1)*pagesize)+","+pagesize;
				if(pagesize==1)
				{
					hm = getExistSafe( sqlstr, obj ) ;
				}
				else
				{
					hm = readDataSafe( sqlstr, obj ) ;
				}
				//System.out.println("com.ufqi.base.DBACT.java: select, sql:["+sqlstr+"] hmvar:["+hmvar+"] res:["+hm+"]");
				wherestr=null;
				obj=null;
				hmvar=null;
				sqlstr=null;
			}
			catch(Exception ex)
			{
				ex.printStackTrace();
			}
		}
		return hm ;
	}
	
	//---
	private Object[] sortObject(HashMap hmvar, String sqlstr)
	{
		int hmsize=1;
		Object[] obj=new Object[hmsize];
		if(hmvar!=null)
		{
			int ki=0;
			String k=null;
			hmsize=hmvar.size();
			obj=new Object[hmsize];
			//int whlen=newwh.length();
			sqlstr=" "+sqlstr;
			if(sqlstr.indexOf("insert")>-1 || sqlstr.indexOf("update")>-1)
			{
				sqlstr=sqlstr.replaceAll(",",", ");
			}
			int whlen=sqlstr.length();
			Object[] tmpobj=new Object[whlen];
			int tmpindex=-1;
			int tmpindex2=-1;
			int tmpindex1=-1;
			int tmpki=1;
			int tmpidx=0;
			Set set=hmvar.keySet();
			Iterator itr=set.iterator();
			while(itr.hasNext())
			{
				k=(String)itr.next();
				k=k==null?"":k;
				if(k.equals("") || k.equals("orderby") || k.equals("pagesize") 
					|| k.equals("pagenum") || k.equals("groupby")
				)
				{
					continue;
				}
				else
				{
					tmpki=1;
					tmpidx=0;
					/* instance: com.ufqi.exp.EXP.java: getRelatedSubject
					 *	Attention: 
					 *		one field matches more than two values, 
					 *		name it as "field.2","field.3", "field.N", etc, as hash key
					 */
					tmpindex1=sqlstr.indexOf("("+k);	
					tmpindex2=sqlstr.indexOf(" "+k);
					while((tmpindex1!=-1 || tmpindex2!=-1))
					{
						if(tmpindex1==-1)
						{
							tmpindex=tmpindex2;
						}
						else if(tmpindex2==-1)
						{
							tmpindex=tmpindex1;
						}
						else if(tmpindex1!=-1 && tmpindex2!=-1)
						{
							tmpindex=tmpindex1>tmpindex2?tmpindex2:tmpindex1;
						}
						if(tmpindex!=-1)
						{
							if(hmvar.containsKey(k+"."+tmpki))
							{
								tmpobj[tmpindex]=hmvar.get(k+"."+tmpki);
							}
							else
							{
								tmpobj[tmpindex]=hmvar.get(k);
							}
							//System.out.println("com.ufqi.base.DBACT.java: sortObject-1, sqlstr:["+sqlstr+"] k:["+k+"] tmpindex:["+tmpindex+"] obj:["+tmpobj[tmpindex]+"] tmpki:["+tmpki+"]");	
							tmpki++;
							tmpidx=tmpindex;
							//tmpindex=-1;
							if(tmpindex1>tmpindex)
							{
								tmpindex=tmpindex1;
							}
							else if(tmpindex2>tmpindex)
							{
								tmpindex=tmpindex2;
							}
							if(tmpindex!=tmpidx)
							{
								if(hmvar.containsKey(k+"."+tmpki))
								{
									tmpobj[tmpindex]=hmvar.get(k+"."+tmpki);
								}
								else
								{
									tmpobj[tmpindex]=hmvar.get(k);
								}
								//System.out.println("com.ufqi.base.DBACT.java: sortObject, sqlstr:["+sqlstr+"] k:["+k+"] tmpindex:["+tmpindex+"] obj:["+tmpobj[tmpindex]+"] tmpki:["+tmpki+"]  222");	
								tmpki++;
							}
						}
						tmpindex1=sqlstr.indexOf("("+k,tmpindex1+1);
						tmpindex2=sqlstr.indexOf(" "+k,tmpindex2+1);
					}
				}
			}
			tmpindex=0;
			for(ki=0;ki<tmpobj.length;ki++)
			{
				if(tmpobj[ki]!=null)
				{
					obj[tmpindex]=tmpobj[ki];
					tmpindex++;
				}
			}
			set=null;
			itr=null;
			tmpobj=null;
		}
		return obj;
	}
	
}
