function do_agreement() {
	if ($('input#password').val()) encrypt_password();
	$.post('?c=websocket&a=do_agreement', $('form#do_agreement').serialize(), function (rs){ 
		if (rs != 's0') {
			rs = $.parseJSON(rs);
			$('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		location = '?c=websocket&a=hiici_ui';
	});
}
function log(msg) {  
	q_audio.play();
	msg = msg.replace(/<(\/)?./gi, '<$1a');
	msg = msg.replace(/o+n+/gi, '');
	$('#log').append("<br>"+msg);  
	$('.c_cont').scrollTop(Number.MAX_VALUE);
}  
function check_p_m() {
	$('input#password').toggle();
}
