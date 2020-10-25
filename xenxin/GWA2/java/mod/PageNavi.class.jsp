<%
/* PageNavi class in -Java 
 * v0.1,
 * wadelau@ufqi.com,
 * Fri Jul  6 15:38:12 UTC 2018
 */
%><%!

public class PageNavi extends WebApp{
	
	// constants
	private static final String logTag = "mod/PageNavi ";
	
	//- variables
	private HashMap para = null;
	private HashMap pdef = null;

	private String file = "";
	private String query = "";
	private String url = ""; 

	HttpServletRequest request = null;
	HashMap parahm = new HashMap();

	//- constructor ?
	public PageNavi(){
		//- @todo
		this(null); // request is null
	}
	
	//-
	public PageNavi(HttpServletRequest request){
				
		if(request == null){
			debug("mod/PageNavi: failed for request null.");
		}
        else{

		this.request = request;
		this.para = 	new HashMap();
		this.pdef = new HashMap();
		this.pdef.put("pnpn", 1); //- page navigation page num
		this.pdef.put("pnps", 100); //- page size
		this.pdef.put("pntc", 0); //- total count
		/*
		 * pnsm, page navi search mode
		 * pnob, page navi order by
		 * pnsk, page navi search keyword
		 * oppnsk, operator of page navi search keyword
		 * see GWA2 manual book
		 */
		this.pdef.put("barlength", 8); //- page list length

		this.file = request.getRequestURL() + "";
		this.query = request.getQueryString()==null ? "" : request.getQueryString();
		this.query = this.query.replaceAll("&pnpn=([0-9]*)", "");	
		this.url = file + "?" + this.query;	
		this.hmf.put("url", this.url);	

		//- init parameters
		java.util.Enumeration paraNames = request.getParameterNames();
		String xname = null; String xvalue = null;
		while (paraNames.hasMoreElements()) {
			xname = (String)paraNames.nextElement();
			xvalue = request.getParameter(xname);
			xvalue = xvalue==null ? "" : xvalue;
			if(xvalue.equals("") && this.pdef.containsKey(xname)){
				this.para.put(xname, this.pdef.get(xname));
			}
			else{
				this.para.put(xname, xvalue);
			}
			this.hmf.put(xname, this.para.get(xname));
			this.parahm.put(xname, xvalue);
		}
		 //- init attributes
        paraNames = request.getAttributeNames();
        //String xname = null; String xvalue = null;
        while (paraNames.hasMoreElements()) {
            xname = (String)paraNames.nextElement();
            xvalue = String.valueOf(request.getAttribute(xname));
            xvalue = xvalue==null ? "" : xvalue;
            if(xvalue.equals("") && this.pdef.containsKey(xname)){
                this.para.put(xname, this.pdef.get(xname));
            }
            else{
                this.para.put(xname, xvalue);
            }
			//debug("trans attributes: xname:"+xname+" xval:"+xvalue);
            this.hmf.put(xname, this.para.get(xname));
            this.parahm.put(xname, xvalue);
        }

        }

		//- init predefined values
		this.pdef.forEach((xname2, xvalue2)->{
			int xvalue3 = Wht.parseInt(this.para.get(xname2)); 
			//xvalue2 = (String)this.pdef.get(xname2);
			if(xvalue3 <= 0){ this.para.put(xname2, xvalue2); }
			this.hmf.put(xname2, this.para.get(xname2));
			});
		    
        //- append params if post
        this.url = (String)this.hmf.get("url");
        this.url = this._appendPostParams(request, this.url, this.parahm);
        this.hmf.put("url", this.url);
			
		//- call parent's constuctor
		//- do it implicitly	
	}

	//- public methods
	//-
	public String getNavi(){
		StringBuffer naviStr = new StringBuffer();	
		this.para = this.hmf;
		String tmpurl = "";
		if(this.get("totalcount").equals("") && !this.get("pntc").equals("")){
			this.set("totalcount", this.get("pntc"));
			this.para.put("totalcount", this.get("pntc"));
			this.para.put("pntc", this.get("pntc"));
		}
		if(Wht.parseInt(this.get("totalcount")) > 0){
			this.para.put("pntc", this.hmf.get("totalcount"));
			tmpurl = (String)this.hmf.get("url");
			tmpurl = tmpurl.replaceAll("&pntc=[0-9]*", "");
			if(tmpurl.indexOf("?") == -1){
				tmpurl += "?_pndf=1";
			}
			tmpurl += "&pntc=" + this.para.get("pntc");
			this.hmf.put("url", tmpurl);
			this.para.put("url", tmpurl);
		}
		//- @todo
		//- parameters via HTTP POST

		int pntc = Wht.parseInt(this.para.get("pntc"));
		int pnpn = Wht.parseInt(this.para.get("pnpn"));
		int pnps = Wht.parseInt(this.para.get("pnps"));
		int navilen = Wht.parseInt(this.para.get("barlength"));
		int totalpage = (pntc % pnps) == 0 ? (pntc / pnps) : (Wht.parseInt(pntc / pnps) + 1);
		tmpurl = (String)this.para.get("url");
		int endpage = pnpn + navilen;
		 
		naviStr.append("&nbsp;&nbsp;<b>页号: &nbsp;<a href=\"javascript:pnAction('")
			.append(tmpurl).append("&pnpn=1');\" title=\"第一页\">|&laquo;</a></b>&nbsp; ");
		for(int i=(pnpn-navilen); i<endpage && i<=totalpage; i++){
			if(i > 0){
				if(i == pnpn){
					naviStr.append(" <span id=\"currentpage\" style=\"color:green;font-weight:bold;font-size:18px\">")
						.append(i).append("</span> ");
				}
				else{
					naviStr.append(" <a href=\"javascript:pnAction('").append(tmpurl).append("&pnpn=")
						.append(i).append("');\" style=\"font-size:14px\">").append(i).append("</a> ");
				}
			}	
		}
		naviStr.append(" &nbsp;<b><a href=\"javascript:pnAction('").append(tmpurl).append("&pnpn=").append(totalpage)
			.append("');\" title=\"最后一页\">&raquo;|</a> </b> &nbsp; &nbsp; ");
		naviStr.append("<a href=\"javascript:void(0);\" title=\"改变显示条数\" onclick=\"javascript:")
			.append("var pnps=window.prompt('请输入新的每页显示条数:','").append(pnps).append("');")
			.append(" if(pnps>0){ myurl='").append(tmpurl).append("'; myurl=myurl.replace('/pnps/','/opnps/'); ")
			.append("doAction(myurl+'&pnps='+pnps);};\"><b>").append(pnps).append("</b>条/页</a> &nbsp; ")
			.append("共 <b>").append(pntc).append("</b>条 / <b>").append(totalpage).append("</b>页 &nbsp;");

		return naviStr.toString();
	}

	//-
	public HashMap getNaviNum(){
		HashMap navihm = new HashMap();
		this.para = this.hmf;
		String tmpurl = "";
		if(this.get("totalcount").equals("") && !this.get("pntc").equals("")){
			this.set("totalcount", this.get("pntc"));
			this.para.put("totalcount", this.get("pntc"));
			this.para.put("pntc", this.get("pntc"));
		}
		if(Wht.parseInt(this.get("totalcount")) > 0){
			this.para.put("pntc", this.hmf.get("totalcount"));
			tmpurl = (String)this.hmf.get("url");
			tmpurl = tmpurl.replaceAll("&pntc=[0-9]*", "");
			if(tmpurl.indexOf("?") == -1){
				tmpurl += "?_pndf=1";
			}
			tmpurl += "&pntc=" + this.para.get("pntc");
			this.hmf.put("url", tmpurl);
			this.para.put("url", tmpurl);
		}
		//- @todo
		//- parameters via HTTP POST

		int pntc = Wht.parseInt(this.para.get("pntc"));
		int pnpn = Wht.parseInt(this.para.get("pnpn"));
		int pnps = Wht.parseInt(this.para.get("pnps"));
		int navilen = Wht.parseInt(this.para.get("barlength"));
		int totalpage = (pntc % pnps) == 0 ? (pntc / pnps) : (Wht.parseInt(pntc / pnps) + 1);
		tmpurl = (String)this.para.get("url");
		int endpage = pnpn + navilen;
		int[] pageArr = new int[navilen*2];
		
		navihm.put("totalpage", totalpage); navihm.put("totalrecord", pntc);
		navihm.put("url", tmpurl); navihm.put("pnps", pnps);
		int pj = 0;
		for(int i=pnpn-navilen; i<pnpn+navilen && i<=totalpage; i++){
			if(i > 0){
				if(i == pnpn){
					navihm.put("pnpn", i);
				}
				pageArr[pj] = i; pj++;
				//debug(logTag+"page i:"+i+" pj:"+pj);
			}
		}
		if(!navihm.containsKey("pnpn")){
			navihm.put("pnpn", 1);
		}
        navihm.put("pages", pageArr);

		return navihm;
	}

	//-
	public String getInitUrl(){
		String str = "";
		String[] fieldArr = new String[]{"tbl", "tit", "db"};

		this.file = request.getRequestURL() + "";
		StringBuffer tmpquery = new StringBuffer();
		this.parahm.forEach((xname, xvalue)->{	
			if(Arrays.asList(fieldArr).contains(xname)){
				tmpquery.append("&").append(xname).append("=").append(xvalue);
			}	
			});
		String query = tmpquery.toString();
		if(query.equals("")){
			str = file;
		}
		else{
			query = query.substring(1);
			str = file + "?" + query;
		}

		return str;
	}

	//- 
	public String getOrder(){
		String str = "";
		StringBuffer strb = new StringBuffer();
		this.parahm.forEach((k, v)->{
			String xname = (String)k; String xvalue = (String)v;
			if(xname.indexOf("pnob") > -1){
				strb.append(xname.substring(4));
				if(xvalue.equals("1")){
					strb.append(" desc"); //- 0 for asc, 1 for desc
				}
				strb.append(",");
			}
			});
		str = strb.toString();
		if(str.equals("")){}
		else{
			str += "1 ";
		}
		return str;
	}

	//-
	public int getAsc(String field){
		int iasc = 0; //- 0 for asc, 1 for desc
		if(this.hmf.containsKey("isasc")){
			if(field == null || field.equals("") 
				|| (!field.equals("") && this.getOrder().equals(field))){
				iasc = Wht.parseInt(this.hmf.get("isasc"));
			}
		}
		else{
			this.parahm.forEach((k, v)->{
				String xname = (String)k; String xvalue = (String)v;
				if(field == null || field.equals("") || field.equals(xname.substring(4)) 
					&& xname.indexOf("pnob") == 0){
					if(xvalue.equals("1")){
						//iasc = 1;
                        this.hmf.put("isasc", 1);
						//break;
						return;
					}
				}
				});	
			iasc = Wht.parseInt(this.hmf.get("isasc"));
		}
		return iasc;
	}

	//-
	public String getCondition(WebApp obj, User user){
		String str = "";
		String pnsmx = this.get("pnsm");
        if(pnsmx.equals("")){
            pnsmx = Wht.get(this.request, "pnsm");
        }
		pnsmx = pnsmx.equals("") ? "or" : "and";
        final String pnsm = pnsmx; //- used in enclosing hm.forEach
		
		int objpnps = Wht.parseInt(obj.get("pagesize"));
		if(objpnps > 0){
			this.hmf.put("pnps", objpnps);
		}

        StringBuffer strb = new StringBuffer();
        this.parahm.forEach((k, v)->{
            String xname = (String)k; String xvalue = (String)v;
			//debug("getCondition xname:"+xname+" xval:"+xvalue);
            if(!xname.equals("pnsk") && xname.indexOf("pnsk") == 0){
                String field = xname.substring(4);
                String linkField = field;
                if(xname.indexOf("=") > -1){
                    String[] sArr = field.split("=");
                    field = sArr[0];
                    linkField = sArr[1];
                }
                //- for select
                if(true){
                    String tmpv = Wht.get(this.request, field);
                    if(!tmpv.equals("") && !tmpv.equals(xvalue)){
                        xvalue = tmpv;
                    }
                }
                if(xvalue.length() > 3 && Wht.startsWith(xvalue, "%")){
                    //- urldecode, @todo
                }
                if(xvalue.indexOf("tbl:") == 0){
                    //- sub query in sql, @todo
                }
                else if(xvalue.indexOf("in::") == 0){
                    //- sub query in sql, @todo
                }
                else{
                    String fieldopv = Wht.get(this.request, "oppnsk"+field);
                    if(fieldopv.equals("")){ fieldopv = "="; }
                    else{
                        if(fieldopv.indexOf("%") == 0){
                        //- urldecode, @todo
                        }
                        fieldopv = fieldopv.replaceAll("&lt;", "<");
                        fieldopv = fieldopv.replaceAll("&gt;", ">");
                    }
                    //- list operators
                    strb.append(" ").append(pnsm).append(" ");
                    if(fieldopv.equals("inlist")){
                        xvalue = this.addQuote(xvalue);
                        strb.append(field)
                            .append(" in (").append(xvalue).append(")");
                        obj.del(field);
                    }
                    else if(fieldopv.equals("inrange")){
                        String[] sArr = xvalue.split(",");
                        strb.append("(").append(field).append(" >= ")
                            .append(this.addQuote(sArr[0])).append(" and ").append(field)
                            .append(" <= ").append(this.addQuote(sArr[1])).append(")");
                        obj.del(field);
                    }
                    else if(fieldopv.equals("contains")){
                        strb.append(field).append(" like ?");
                        obj.set(field, "%"+xvalue+"%");
                    }
                    else if(fieldopv.equals("notcontains")){
                        strb.append(field).append(" not like ?");
                        obj.set(field, "%"+xvalue+"%");
                    }
                    else if(fieldopv.equals("startswith")){
                        strb.append(field).append(" like ?");
                        obj.set(field, xvalue+"%");
                    }
                    else if(fieldopv.equals("endswith")){
                        strb.append(field).append(" like ?");
                        obj.set(field, "%"+xvalue);
                    }
                    else if(fieldopv.equals("!=")){
                        strb.append(field).append(" <> ?");
                        obj.set(field, xvalue);
                    }
                    else if(fieldopv.equals("regexp")){
                        strb.append(field).append(" regexp ?");
                        obj.set(field, xvalue);
                    }
                    else if(fieldopv.equals("notregexp")){
                        strb.append(field).append(" not regexp ?");
                        obj.set(field, xvalue);
                    }
                    else{
                        strb.append(field).append(" ")
                            .append(fieldopv).append(" ").append("?");
						obj.set(field, xvalue);
                    }
                }
            }
            });
        str = strb.toString();
		if(str.equals("")){
		}
		else{
			str = str.substring(4); // rm first seg of pnsm
		}
		return str;
	}

	//- private methods
	//-
	private String getSignPara(){
		String str = "";
		//- @todo
		return str;
	}

	//-
	private String addQuote(String s){
		String str = "";
		if(s.indexOf("'") > -1){
			s = s.replaceAll("'", "\\'"); 
		}
		str = "'"+s+"'";
		return str;
	}

	//-
	private String embedSql(String field, String val){
		String str = "";
		//- @todo
		return str;
	}

	//-
	private boolean isNumeric(String type){
		boolean isnum = false;

		return isnum;	
	}
	
	//- 
    private String _appendPostParams(HttpServletRequest request, String url, HashMap parahm){
        String rtnurl = url;
        String v = null; 
        for(Object obj : parahm.keySet()){
            v = (String)parahm.get(obj);
            String k = (String)obj;
            //debug("k:"+k+" v:"+v);
            if((k.startsWith("pnsk") || k.startsWith("oppnsk")) 
                && url.indexOf("&"+k+"=") == -1){
                rtnurl += "&"+k+"="+v;
            }       
        }       
        return rtnurl;
    }

}

%>
