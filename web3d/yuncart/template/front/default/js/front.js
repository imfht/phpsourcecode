$.util = {
	isemail:function(email) {
		return /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(email);
	},
	objectLen:function(o) {
		var i = 0;
		for(var x in o) {
			i++;
		}
		return i;
	}
}
function chgimg() {
	$('#seccodeimg').attr('src','util.php?action=seccode&t='+new Date().getTime());
}
function jsservice(){
    var obj=$("#divMySer")
    if( obj.attr("class") == "service-open" )
        $("#divMySer").removeClass("service-open").addClass("service-close");
    else
        $("#divMySer").removeClass("service-close").addClass("service-open");
}