<%
/* In this way, all ctrls are wrapped in a single class, __jsp_index 
 * which might be too many methods and classes loaded for every request, but cannot be extended into a quite large scale
 * noted by xenxin @Sat Jun 18 09:07:58 CST 2016
 */
%><%@page import="
java.io.File
"%><%
//- controller of homepage

//- exec, 2/2
indexCtrl();

%><%!
//- define, 1/2
public void indexCtrl(){


	//- something to do
	act = act.equals("") ? "index" : act;

	//- actions
	if(mod.equals("index")){ //- something displayed in homepage only

		if(act.equals("index")){
			
			outx.append("outx act:["+act+"] in ctrl/index.\n");

		}
		else{

			outx.append("outx in ctrl/index, unknown act:["+act+"].");

		}

	}

	//- shared funcs relocated into ctrl/include.jsp

	//- tpl
	if(fmt.equals("")){
		mytpl = "homepage.html";
	}

}

%>
