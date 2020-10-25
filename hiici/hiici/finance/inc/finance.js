function get_i_is(item_id, page) {
	$.get('?c=finance&a=get_item_my_item_i_is&item_id='+item_id+'&page='+page, function(rs){
		if ('e0' == rs) { 
			alert(rs);
			return;
		} 
		$('div#i_is').html(rs);
	});
}
function pre_invest_search() {
	location = '?c=finance&a=pre_invest_manage&&search='+$('input#pre_invest_search').val();
}
