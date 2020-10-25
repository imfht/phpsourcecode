function do_forum_add() {
	$.post('?c=forum&a=do_forum_add', $('form#do_forum_add').serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$('form#do_forum_add').find('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		alert('创建成功！^_^');
		location = '?c=forum&a=forum_list&m_falter=1';
	});
}
function do_forum_edit(forum_id) {
	$.post('?c=forum&a=do_forum_edit', $('form#do_forum_edit').serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$('form#do_forum_edit').find('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		alert('保存成功！^_^');
		location = '?c=forum&a=topic_list&forum_id='+forum_id;
	});
}
function pay_forum_add() {
	$.post('?c=forum&a=pay_forum_add', $('form#do_forum_add').serialize(), function(rs){
		document.write(rs);
	});
}
function do_pay_forum_add() {
	if (!confirm ("O_O 本次创建板块花费： "+$('form#do_forum_add').find('input[name=pay]').val()+"（元），确定创建吗！")) return;
	encrypt_password();
	$.post('?c=forum&a=do_pay_forum_add', $('form#do_forum_add').serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$('form#do_forum_add').find('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		alert('创建成功！^_^');
		location = '?c=forum&a=forum_list&m_falter=1';
	});
}
function do_forum_del(forum_id) {
	if (!confirm (" O_O 这项操作是完全不可逆的！确定要删除吗！")) return;
	if (!confirm (" O_O 请再确认一次，确定要删除吗！")) return;
	location = '?c=forum&a=do_forum_del&forum_id='+forum_id;
}
function do_topic_add(forum_id) {
	$.post('?c=forum&a=do_topic_add', $('form#do_topic_add').serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$('form#do_topic_add').find('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		alert('创建成功！^_^');
		location = '?c=forum&a=topic_list&forum_id='+forum_id;
	});
}
function do_topic_edit(topic_id) {
	$.post('?c=forum&a=do_topic_edit', $('form#do_topic_edit').serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$('form#do_topic_edit').find('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		alert('保存成功！^_^');
		location = '?c=forum&a=topic_show&topic_id='+topic_id;
	});
}
function do_topic_r_edit(reply_id) {
	$.post('?c=forum&a=do_topic_r_edit', $('form#do_topic_r_edit').serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$('form#do_topic_r_edit').find('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		alert('保存成功！^_^');
		location = '?c=forum&a=to_topic_show_reply&reply_id='+reply_id;
	});
}
function pay_topic_add() {
	$.post('?c=forum&a=pay_topic_add', $('form#do_topic_add').serialize(), function(rs){
		document.write(rs);
	});
}
function do_pay_topic_add(forum_id) {
	if (!confirm ("O_O 本次发帖花费： "+$('form#do_topic_add').find('input[name=pay]').val()+"（元），确定发帖吗！")) return;
	encrypt_password();
	$.post('?c=forum&a=do_pay_topic_add', $('form#do_topic_add').serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$('form#do_topic_add').find('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		alert('创建成功！^_^');
		location = '?c=forum&a=topic_list&forum_id='+forum_id;
	});
}
function do_pay_topic_top(forum_id) {
	if (!confirm ("O_O 本次置顶花费： "+$('form#do_pay_topic_top').find('input[name=pay]').val()+"（元），确定置顶吗！")) return;
	encrypt_password();
	$.post('?c=forum&a=do_pay_topic_top', $('form#do_pay_topic_top').serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$('form#do_pay_topic_top').find('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		alert('置顶成功！^_^');
		location = '?c=forum&a=topic_list&forum_id='+forum_id;
	});
}
function do_topic_digest(topic_id) {
	$.get('?c=forum&a=do_topic_digest&topic_id='+topic_id, function(rs){
		if ('s0' != rs) { alert(rs); return; } 
		location = '?c=forum&a=topic_show&topic_id='+topic_id;
	});
}
function do_topic_order(topic_id, t) {
	$.get('?c=forum&a=do_topic_order&topic_id='+topic_id, function(rs){
		if ('s0' == rs) { 
			$("a#do_topic_"+topic_id+"_order").html('取消订单');
			$('span#order_c_s').text(parseInt($('span#order_c_s').text()) + 1);
			if (2 == t) auto_run_stop();
		} else if ('s1' == rs) { 
			$("a#do_topic_"+topic_id+"_order").html('<b class="glyphicon glyphicon-th-list"></b> 下订单');
			$('span#order_c_s').text(parseInt($('span#order_c_s').text()) - 1);
			if (2 == t) auto_run('to_start_t(2)', 100);
			if (1 == t) { auto_run('to_start_t(1)', 100); $("a#do_topic_"+topic_id+"_order").removeAttr('href'); }
		} else if ('s2' == rs) { 
			location = out_s_u;
		} else if ('s3' == rs) { 
			location = '?c=forum&a=topic_order&topic_id='+topic_id;
		} else if ('e1' == rs) { 
			alert('收货信息不完整！^_^');
			location = '?c=account&a=account_addr_set';
		} else { alert(rs); }
	});
}
function do_topic_order_t(topic_id) {
	$.post('?c=forum&a=do_topic_order', $('form#do_topic_order_t').serialize(), function(rs){
		if ('s0' != rs) { 
			alert(rs);
			return;
		}
		alert('成功下单！^_^');
		location = '?c=forum&a=topic_show&topic_id='+topic_id;
	});
}
function do_t_o_complete(topic_id, user_id) {
	$.get('?c=forum&a=do_order_complete&topic_id='+topic_id+'&user_id='+user_id, function(rs){
		if ('s0' == rs) { 
			$("a#do_t_"+topic_id+"_o_complete").remove();
		} else { alert(rs); }
	});
}
function do_topic_del(topic_id, forum_id) {
	if (!confirm (" O_O 确定要删除吗！")) return;
	$.get('?c=forum&a=do_topic_del&topic_id='+topic_id, function(rs){
		if ('s0' == rs) { 
			$('span#topic_del_c').text(parseInt($('span#topic_del_c').text()) + 1);
		} else if ('s1' == rs) {
			alert('成功删除！^_^');
			location = '?c=forum&a=topic_list&forum_id='+forum_id;
		} else {
			alert(rs);
			return;
		}
	});
}
function do_topic_r_del(reply_id) {
	if (!confirm (" O_O 确定要删除吗！")) return;
	$.get('?c=forum&a=do_topic_r_del&reply_id='+reply_id, function(rs){
		if ('s0' == rs) { 
			$('div#r_'+reply_id).find('span#topic_r_del_c').text(parseInt($('div#r_'+reply_id).find('span#topic_r_del_c').text()) + 1);
		} else if ('s1' == rs) {
			alert('成功删除！^_^');
			$('div#r_'+reply_id).remove();
		} else {
			alert(rs);
			return;
		}
	});
}
function do_order_complete(order_id) {
	$.get('?c=forum&a=do_order_complete&order_id='+order_id, function(rs){
		if ('s0' == rs) { 
			$("a#do_order_"+order_id+"_complete").remove();
		} else { alert(rs); }
	});
}
function do_order_complete_all(topic_id, page) {
	$.get('?c=forum&a=do_order_complete_all&topic_id='+topic_id+'&page='+page, function(rs){
		if ('s0' == rs) { 
			$("a.order-complete").remove();
		} else { alert(rs); }
	});
}
function do_order_complete_user_all(user_id) {
	$.get('?c=forum&a=do_order_complete_all&user_id='+user_id, function(rs){
		if ('s0' == rs) { 
			$("a.order-complete").remove();
		} else { alert(rs); }
	});
}
function order_search(topic_id) {
	location = '?c=forum&a=topic_my_order&topic_id='+topic_id+'&search='+$('input#order_search').val();
}
function forum_list_search(m_falter) {
	location = '?c=forum&a=forum_list&m_falter='+m_falter+'&search='+$('input#forum_list_search').val();
}
function topic_my_list_search(digest_falter, m_falter) {
	setCookie('forum_topic_digest_filter', digest_falter, 365, '/');
	location = '?c=forum&a=topic_my_list&m_falter='+m_falter+'&search='+$('input#topic_my_list_search').val();
}
function topic_list_search(forum_id, digest_falter) {
	setCookie('forum_topic_digest_filter', digest_falter, 365, '/');
	location = '?c=forum&a=topic_list&forum_id='+forum_id+'&search='+$('input#topic_list_search').val();
}
function get_forum_kind_nav() {
	$.get('?c=forum&a=get_forum_kind_nav', function(rs){
		if ('e0' == rs) { 
			alert(rs);
			return;
		}
		$('a#drop_forum_kind').removeAttr("onclick");
		$('ul#menu_forum_kind').html(rs);
	});
}
function do_forum_city_edit() {
	$.post('?c=forum&a=do_forum_city_edit', $('form#forum_city_edit').serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$('form#forum_city_edit').find('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		alert('设置成功！^_^');
		location = '?c=forum&a=forum_city_edit';
	});
}
function do_forum_kind_add() {
	$.post('?c=forum&a=do_forum_kind_add', $('form#forum_kind_add').serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$('form#forum_kind_add').find('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		alert('创建成功！^_^');
		location = '?c=forum&a=forum_kind_manage';
	});
}
function do_forum_kind_edit() {
	$.post('?c=forum&a=do_forum_kind_edit', $('form#forum_kind_edit').serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$('form#forum_kind_edit').find('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		alert('设置成功！^_^');
		location = '?c=forum&a=forum_kind_manage';
	});
}
function do_forum_carousel_add() {
	$.post('?c=forum&a=do_forum_carousel_add', $('form#forum_carousel_add').serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$('form#forum_carousel_add').find('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		alert('创建成功！^_^');
		location = '?c=forum&a=forum_carousel_manage';
	});
}
function do_forum_carousel_edit() {
	$.post('?c=forum&a=do_forum_carousel_edit', $('form#forum_carousel_edit').serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$('form#forum_carousel_edit').find('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		alert('设置成功！^_^');
		location = '?c=forum&a=forum_carousel_manage';
	});
}
function check_auto_bg_url() {
	$('input#forum_bg_url').toggle();
}
function check_auto_intro() {
	$('textarea#forum_intro').toggle();
}
function check_topic_orders() {
	$('div#topic_orders_s').toggle();
}
function check_start_t(value) {
	if (0 != value) $('input#start_t_s').show(); else $('input#start_t_s').hide();
}
function check_order_l(value) {
	if (1 == value) $('input#order_l_n').show(); else $('input#order_l_n').hide();
}
function check_out_s(value) {
	if (1 == value) $('input#out_s_u').show(); else $('input#out_s_u').hide();
}
function user_has_order() {
	if(window.Notification && 940 > $('.container').width()) {
		$('body').append('<audio src="forum/inc/notify.mp3" autoplay style="display:none"></audio>');
		var n = new Notification('HIICI', { body : '新的订单!' });
		n.onclick = function() {
			location = '?c=forum&a=topic_my_list';
		};
		setTimeout(function(){ n.close(); }, 10000);
	}
}
if (null != location.href.match(/topic_add|topic_edit/)) get_location();
var ls_m = 0;
function get_location(ls) {
	if (1 == ls) ls_m = 1;
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(show_ps);
	}
}
function show_ps(ps) {
	if (1 == ls_m) {
		$('body').append('<script src="js/vendor/geohash.js"></script>');
		setCookie('forum_topic_geolocation', geohash.encode(ps.coords.latitude, ps.coords.longitude).substring(0,6), 365, '/');
		reload_topic_list();
	} else {
		$('input[name="geo"]').val(ps.coords.latitude+', '+ps.coords.longitude);
	}
}

function to_start_t(t) {
	start_t_x -= 0.1;
	if (0 >= start_t_x) {
		if (1 == t) $("a#do_topic_"+t_id+"_order").html('<b class="glyphicon glyphicon-th-list"></b> 下订单').attr('href', 'javascript:do_topic_order('+t_id+')');
		if (2 == t) $("a#do_topic_"+t_id+"_order").html('已结束').removeAttr('href');
		return auto_run_stop();
	}
	if (1 == t) $("a#do_topic_"+t_id+"_order").html(parseInt(start_t_x/3600)+'小时'+parseInt((start_t_x%3600)/60)+'分'+parseInt(start_t_x%60)+'秒'+parseInt((start_t_x*10)%10)+'后开始...');
	if (2 == t) $("a#do_topic_"+t_id+"_order").html('<b class="glyphicon glyphicon-th-list"></b> 下订单，'+parseInt(start_t_x/3600)+'小时'+parseInt((start_t_x%3600)/60)+'分'+parseInt(start_t_x%60)+'秒'+parseInt((start_t_x*10)%10)+'后结束');
}
function do_topic_r_add() {
	if ('' == $('#topic_r_content').val()) { $('#topic_r_content').parent().parent().removeClass('hidden-xs'); $('.topic-r-r.hidden-xs').removeClass('hidden-xs'); return; }

	$.post('?c=forum&a=do_topic_r_add', $("form#topic_reply").serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		} 
		alert('发布成功！^_^');
		location = '?c=forum&a=to_topic_show_end&topic_id='+t_id;
	});
}
function do_topic_up() {
	$.get('?c=forum&a=do_topic_up&topic_id='+t_id, function(rs){
		if ('s0' != rs) { 
			alert(rs);
			return;
		} 
		$('span#topic_up_c').text(parseInt($('span#topic_up_c').text()) + 1);
		$('div.main-up-user-face').find('h5').after('<a href="?c=center&user_id='+auth_id+'"><img src="img/center/user_face/'+auth_id+'_min.jpg"/></a>');
	});
}
function get_topic_r_r_form(reply_id) {
	$('div#r_'+reply_id).find('a#get_topic_r_r_form').attr('href', 'javascript:null');
	$.get('?c=forum&a=get_topic_r_r_form&reply_id='+reply_id, function(rs){
		if ('e0' == rs) { 
			alert(rs);
			return;
		}
		$('div#r_'+reply_id).find('div.topic-r-r').after(rs);
	});
}
function do_topic_r_r_add(reply_id) {
	$.post('?c=forum&a=do_topic_r_add', $('div#r_'+reply_id).find("form#r_r_form").serialize(), function(rs){
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		} 
		alert('发布成功！^_^');
		location = '?c=forum&a=to_topic_show_end&topic_id='+t_id;
	});
}
function do_topic_r_up(reply_id) {
	$.get('?c=forum&a=do_topic_r_up&reply_id='+reply_id, function(rs){
		if ('s0' != rs) { 
			alert(rs);
			return;
		} 
		$('div#r_'+reply_id).find('span#topic_r_up_c').text(parseInt($('div#r_'+reply_id).find('span#topic_r_up_c').text()) + 1);
	});
}
