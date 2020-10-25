function do_register_check(id) {
	check = ('username' == id) ? 1 : 2;

	$('div#'+id).removeClass('has-success has-warning');
	$('div#'+id).find('span').removeClass('glyphicon-ok glyphicon-warning-sign');

	$.get('?c=user&a=do_register_check&check='+check+'&'+id+'='+$('input#'+id).val(), function(rs){
			if ('s0' != rs) { 
			$('div#'+id).addClass('has-warning');
			$('div#'+id).find('span').addClass('glyphicon-warning-sign');
			$('div#'+id).find('p').text(rs);
			} else {
			$('div#'+id).addClass('has-success');
			$('div#'+id).find('span').addClass('glyphicon-ok');
			$('div#'+id).find('p').text('');
			}
			can_register();
			});
}
function check_password() {
	$('div#password').removeClass('has-success has-warning');
	$('div#password').find('span').removeClass('glyphicon-ok glyphicon-warning-sign');
	if ('' == $('input#password').val()) { 
		$('div#password').addClass('has-warning');
		$('div#password').find('span').addClass('glyphicon-warning-sign');
	} else {
		$('div#password').addClass('has-success');
		$('div#password').find('span').addClass('glyphicon-ok');
		if ('' != $('input#r_password').val()) check_r_password();
	}
	can_register();
}
function check_r_password() {
	$('div#r_password').removeClass('has-success has-warning');
	$('div#r_password').find('span').removeClass('glyphicon-ok glyphicon-warning-sign');
	if ('' == $('input#r_password').val() || $('input#password').val() != $('input#r_password').val()) { 
		$('div#r_password').addClass('has-warning');
		$('div#r_password').find('span').addClass('glyphicon-warning-sign');
		$('div#r_password').find('p').text("两次密码不一致！^_^");
	} else {
		$('div#r_password').addClass('has-success');
		$('div#r_password').find('span').addClass('glyphicon-ok');
		$('div#r_password').find('p').text('');
	}
	can_register();
}
function can_register() {
	if ($('div#username').hasClass('has-success') && $('div#email').hasClass('has-success') && $('div#password').hasClass('has-success') && $('div#r_password').hasClass('has-success')) {
		$('button#do_register').removeClass('disabled');
	} else {
		$('button#do_register').addClass('disabled');
	}
}
