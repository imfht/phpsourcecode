$(document).ready(function(){
	$("input#bd_search").keyup(function(e){  
		if (38 == e.keyCode || 40 == e.keyCode) {
			if (38 == e.keyCode) f_l = 'last'; 
			else if (40 == e.keyCode) f_l = 'first'; 

			if (!$("ul.bd_wd_s li.hover").html()) {
				$("ul.bd_wd_s li:"+f_l).addClass('hover');
			} else {
				if (38 == e.keyCode) $("ul.bd_wd_s li.hover").prev().addClass('hover');
				else if (40 == e.keyCode) $("ul.bd_wd_s li.hover").next().addClass('hover');

				$("ul.bd_wd_s li.hover:"+f_l).removeClass('hover');
			}
			$('input#bd_search').val($("ul.bd_wd_s li.hover").html());
		} else {
			$.get('?c=bd_search&a=get_bd_wd&wd='+$('input#bd_search').val(), function(rs){
				rs = eval(rs);
				if (!rs || !rs.s[0]) { $("ul.bd_wd_s:visible").hide(); return; }

				$("ul.bd_wd_s:hidden").show();
				var wd_s = ''; 
				for (r in rs.s) wd_s += '<li>'+rs.s[r]+'</li>'; 
				$("ul.bd_wd_s").html(wd_s);
				$("ul.bd_wd_s li").click(function(){ location = '?c=bd_search&a=bd_search&wd='+$(this).html(); });
			});
		}
	});  
});
