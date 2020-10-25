<%
//- controller of homepage


//- something to do

act = act.equals("") ? "index" : act;

//- actions
if(mod.equals("index")){ //- something displayed in homepage only

	if(act.equals("index")){
		
		outx.append("outxi act:["+act+"] in ctrl/index.");

	}
	else{

		outx.append("outx in ctrl/index, unknown act:["+act+"].");

	}

}

if(true){ //-  something shared across the app
	//-
	//- page header and footer
}

//- tpl



%>
