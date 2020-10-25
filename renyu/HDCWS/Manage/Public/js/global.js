function refreshimage(){
	var cap =document.getElementById("captcha");
	cap.src=cap.src+'?t=' + Math.random();
}
function selectcheckbox(form){
	for(var i = 0;i < form.elements.length; i++){
		var e = form.elements[i];
		if(e.name != 'chkall' && e.disabled != true) e.checked = form.chkall.checked;
	}
}
function change(id, choose){document.getElementById(id).value = choose.options[choose.selectedIndex].title;}
$(function(){$('.M').hover(function(){$(this).addClass('active');},function(){$(this).removeClass('active');});});
