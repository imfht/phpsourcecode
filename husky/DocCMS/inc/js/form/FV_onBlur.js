var iname = [];
function initValid(frm) {
	var formElements = frm.elements;
	for (var i=0; i<formElements.length;i++) {
		var validType = formElements[i].getAttribute('valid');
		if (validType==null) continue;
		formElements[i].onblur = (function (a,b) {
			return function (){validInput(a,b)}
		})(formElements[i],frm);
	}
}
function validInput(ipt,frm,p) {
	if (p==null) p = 'errMsg_';
	var fv = new FormValid(frm);
	var formElements = frm.elements;
	var msgs = fvCheck(ipt,fv,formElements);
	if (msgs.length>0) {
		document.getElementById(p+ipt.name).innerHTML = msgs.join(',');
	} else {
		document.getElementById(p+ipt.name).innerHTML = '';
	}
}
