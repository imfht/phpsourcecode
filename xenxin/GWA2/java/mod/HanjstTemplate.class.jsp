<%
/* Template class for Hanjst 
 * v0.1,
 * wadelau@ufqi.com, Xenxin@ufqi.com
 * Fri Jan  4 01:59:34 UTC 2019
 */

%><%@page import="com.google.gson.Gson,
com.google.gson.GsonBuilder"
%><%!

public class HanjstTemplate extends WebApp{
	
	private static final String Log_Tag = "mod/Template ";
    private String[] repPathTags = new String[]{"images", "css", "js", "pics"};
	
	//- constructor ?
	public HanjstTemplate(){
		//- for constructor override
		//this(new HashMap());
        //- @todo
	}

    //- read tpl contents
	public String readTemplate(String mytpl, String tpldir, String viewdir){
		String tplcont = "";
		HashMap hmtmp = this.getBy("file:", null, (new HashMap(){{put("file", tpldir+"/"+mytpl);}}));
		if((boolean)hmtmp.get(0)){
			tplcont = (String)hmtmp.get(1);
			tplcont = this.replacePath(tplcont, viewdir);
		}
		return tplcont;
	}
	
	//- HashMap 2 JSON
    public String map2Json(HashMap hmdata){
        String rtnStr = "";
        GsonBuilder gsonMapBuilder = new GsonBuilder();
        Gson gsonObject = gsonMapBuilder.create();
        rtnStr = gsonObject.toJson(hmdata);
        return rtnStr;
    }

    //- replace resrc path
    public String replacePath(String tplcont, String viewdir){
		tplcont = tplcont==null ? "" : tplcont;
        String[] repTags = this.repPathTags; 
        for(int ti=0; ti<repTags.length; ti++){
			if(repTags[ti]!=null && !repTags[ti].equals("")){
				if(tplcont.indexOf("'"+repTags[ti]+"/") > -1){
					tplcont = tplcont.replaceAll("'"+repTags[ti]+"/", "'"+viewdir+"/"+repTags[ti]+"/");
				}
				if(tplcont.indexOf("\""+repTags[ti]+"/") > -1){
					tplcont = tplcont.replaceAll("\""+repTags[ti]+"/",  "\""+viewdir +"/"+repTags[ti]+"/"); 
				}
			}
        }
        return tplcont;
    }
	
	//- replace elements
	public String replaceElement(String tplcont, HashMap replaceList){
		tplcont = tplcont==null ? "" : tplcont;
		if(replaceList == null){
			return tplcont;
		}
		else{
			//debug(tplcont); debug(replaceList);
			String tmpks = null;
			for(Object tmpk : replaceList.keySet()){
				tmpks = (String)tmpk;
				if(tplcont.indexOf(tmpks) > -1){
					tplcont = tplcont.replaceAll(tmpks, String.valueOf(replaceList.get(tmpk)));
				}
			}
		}
		return tplcont;
	}

}
%>