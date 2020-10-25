/*
 *
 * GWA2 in useBean with .java
 * import from GWA2 in .jsp
 * wadelau@{ufqi, hotmail, gmail}.com
 * 
 * since Tue Apr  9 03:28:28 UTC 2019
 */

package com.ufqi.gwa2.inc;

import java.util.HashMap;

public interface WebAppInterface{

    public void set(String key, Object value);
    public String get(String key);

    public HashMap setBy(String fields, String conditions);
    public HashMap getBy(String fields, String conditions);

    //public void setTbl(String tbl);
    //public String getTbl();

    public void setId(String iId);
    public String getId();

    public HashMap execBy(String fields, String conditions);
    public HashMap rmBy(String conditions);

    /*
       public String toString(Object obj);
     */

}
