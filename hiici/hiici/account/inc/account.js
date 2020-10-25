function do_pay_password_set() {
	$.post('?c=account&a=do_pay_password_set', $('form#do_pay_password_set').serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$('form#do_pay_password_set').find('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		alert('重置成功！^_^');
		location = '?c=account&a=pay_password_set';
	});
}
function do_account_addr_set() {
	$.post('?c=account&a=do_account_addr_set', $('form#do_account_addr_set').serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$('form#do_account_addr_set').find('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		alert('保存成功！^_^');
		location = '?c=account&a=account_addr_set';
	});
}
