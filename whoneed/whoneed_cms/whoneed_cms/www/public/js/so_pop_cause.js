/**
 * 搜索框特效引发及监听
 * @author huanghm
 */
 /*
document.addEventListener('DOMContentLoaded', function(){
	
	
	document.body.focus();
	var oSoPopText = ai.i("js-so-pop-text");
	var oSoPopPromote = ai.i("js-so-pop-promote");
	var oSoPop= ai.i("js-so-pop");
	var iLoadSoPopJs = 1;
	oSoPop.style.display = 'block';
	oSoPopPromote.style.display = 'none';
	
	oSoPopText.addEventListener("focus",function(){
		
		if(oSoPop.className != "so-pop"){
			oSoPopPromote.style.display = 'block';
		}
		
		oSoPop.className = "so-pop";
		
		
		if(iLoadSoPopJs){
			iLoadSoPopJs = 0;
			ai.scriptLoad(jsurl+"so_pop.js",function(){});
		}
		if(this.value=="搜索"){
			this.value = "";
		}
		
		//ai.hideUrl();
		
	});


}, false);
*/