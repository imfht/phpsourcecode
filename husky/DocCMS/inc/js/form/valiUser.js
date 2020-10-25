function viliUser(username){  
	xmlhttp=null; 
	if(window.XMLHttpRequest)
	{
        xmlhttp=new XMLHttpRequest();
    }
    else if(window.ActiveXObject)
	{
		try
		{
           xmlhttp=new ActiveXObject("Msxml2.XMLHttp");
        }
		catch(e)
		{
			try
			{
				xmlhttp=new ActiveXobject("Microsoft.XMLHttp");
			}
			catch(e)
			{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
    xmlhttp.onreadystatechange = function (){
			if(xmlhttp.readyState == 4){
            	if(xmlhttp.status == 200){
                document.getElementById("authentication").innerHTML = xmlhttp.responseText ;
            }   
        }
	}
	var url="/?m=user&a=checkusername&username="+escape(username);
    xmlhttp.open("GET",url,true) ;
    xmlhttp.send(null) ;
}