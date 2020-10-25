
package com.ufqi.base;

/**
 * @since 2005-12-30 23:27
 * @author wadelau
 */
import java.sql.*;
import com.ufqi.base.DBConn;
import java.util.HashMap;

public class WebBean 
{
	
	protected Connection conn = null;
	protected Statement stmt = null;
	
	//public String sql = null ;
	//DBACT dbact = new DBACT() ;
	//public HashMap hm = null ;
	
	protected void free(Statement stmt)
	{
		try
		{
			if (stmt != null)
			{
				stmt.close();
			}
		} 
		catch (SQLException e)
		{
			println(e);
		}
		freeConn();
	}

	protected void freeConn()
	{
		try
		{
			if (conn != null)
			{
				conn.close();
			}
		}
		catch (SQLException e)
		{
			println(e);
		}
	}

	protected boolean getConnection() throws Exception
	{
		return getConnection("");
	}

	protected void println(Exception ex)
	{
		ex.printStackTrace(System.out);
		System.out.println(ex);
	}

	protected boolean getConnection(String dbname) throws Exception
	{
		if (conn == null || conn.isClosed())
		{
            if(dbname.equals("")){
			    conn = DBConn.getConnection();
            }
            else{
			    conn = DBConn.getConnection(dbname);
            }
		}
		if (conn == null || conn.isClosed())
		{
			System.out.println("error: no avaible conn to use");
			return false;
		}
		return true;
	}
	
	//--- added on 20071124 by wadelau, read single record and save in an hashmap
	protected HashMap getInfo( ResultSet rs ) throws SQLException
	{
		HashMap hm = null ;
		ResultSetMetaData rsmd = rs.getMetaData();
		if( rs.next() )
		{
			hm = new HashMap();
			int cci = rsmd.getColumnCount() ;
			String fieldname = null  ;
			String fieldvalue = null  ;
			for(int i=1; i<=cci;i++)
			{
				fieldname = rsmd.getColumnName(i) ;
				fieldvalue = rs.getString(fieldname) ;
				fieldname = fieldname.toLowerCase() ;
				hm.put( fieldname,fieldvalue ) ;
			}
		}
		rs.close();
		//System.out.println("webbean:hm["+hm+"]");
		return hm ;
	}

	//--- added on 20071124 by wadelau, read records and save in an hashmap
	protected HashMap getRs( ResultSet rs ) throws SQLException
	{
		HashMap hm = new HashMap();
		int count = 0 ;
		ResultSetMetaData rsmd = rs.getMetaData() ;
		int icc = rsmd.getColumnCount() ;
		String fieldname = null ;
		String fieldvalue = null ;
		while ( rs.next() )
		{
			HashMap hmtmp = new HashMap() ;
			for(int i=1; i<=icc; i++ )
			{
				fieldname = rsmd.getColumnName(i) ;
				fieldvalue = rs.getString(fieldname);
				fieldname = fieldname.toLowerCase() ;
				hmtmp.put(fieldname, fieldvalue);
			}
			hm.put(""+count,hmtmp);
			count++;
		}
		hm.put("count",""+count);
		rs.close();
		return hm ;
	}
	
}
