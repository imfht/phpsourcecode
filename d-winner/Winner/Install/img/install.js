// JavaScript Document
function toInstall(val,act){
	var url = 'http://'+window.location.host;
	$.getJSON("http://server.piocms.com/dwuss/index.php/Admin/project/install?callback=?",{mode: 'Winner',domain: url,mail: val ,key:'e1a111321d2cc0c2ba779e7ccd43994d', version:'3.0.4'}, function(data){
		$.post('inc/putdata.act.php',{act:'put',serial:data.serial});
	});
}