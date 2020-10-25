<%!
/*
 * interface to the rest of the world.
 */

public interface WebAppInterface{
	
	public void set(String key, Object value);
	public String get(String key);
	
	public HashMap setBy(String fields, String conditions);
	public HashMap getBy(String fields, String conditions);
	
	public void setTbl(String tbl);
	public String getTbl();
	
	public void setId(String iId);
	public String getId();

	public HashMap execBy(String fields, String conditions);
	public HashMap rmBy(String conditions);
	
	/*
	public String toString(Object obj);
	*/
	

}

%>