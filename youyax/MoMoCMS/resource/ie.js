function ltie8(){
	if(navigator.userAgent.indexOf("MSIE")>0)  
	{
		if(navigator.userAgent.indexOf("MSIE 6.0")>0){
		 	return true;
		}
		if(navigator.userAgent.indexOf("MSIE 7.0")>0){
			return true;
		}
		if(navigator.userAgent.indexOf("MSIE 8.0")>0){
			return true;
		}
		return false;
	}else{
		return false;	
	}
}