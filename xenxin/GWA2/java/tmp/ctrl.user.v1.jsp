<%
/* controller of user mod
 */
//- exec, 2/2
userCtrl();

%><%!
//- define, 1/2
public void userCtrl(){

	 //- something to do

	if(act.equals("signin")){
		//--
		outx.append("outx "+act+" in ctr/user");

	}
	else if(act.equals("dosignin")){
		 
		outx.append("outx "+act+" in ctr/user");

	}
	else{
		
		outx.append("outx unknown act:["+act+"] in ctr/user");

	}


	//- tpl

}

%>
