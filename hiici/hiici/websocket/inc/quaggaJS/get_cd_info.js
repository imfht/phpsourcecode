function get_cd_info(cd) {
	$.ajax({
		type : 'get',
		url : 'http://s.taobao.com/search?ajax=true&sort=sale-desc&q='+cd,
		dataType : 'jsonp',
		success : function(rs){
			if (rs.mods.itemlist && rs.mods.itemlist.data) {
				var rb = rs.mods.itemlist.data.auctions;
				$('div.qr-d').append('<p><a href="?c=forum&a=index_tejia&wd='+cd+'">'+rb[0].raw_title+'：¥'+rb[0].view_price+'</a></p>'); 
			} else {
				$.get('?c=bd_search&a=get_bd_search&wd='+cd, function(rs){
					rs = rs.replace(/<\/?em>/g, '');
					var tls = rs.match(/<a[^>]*>([^<]*)<\/a>/g);
					if (tls) {
						for (i in tls) {
							if (i >= 3) break;
							var tl = tls[i].match(/<a[^>]*>([^<]*)<\/a>/);
							$('div.qr-d').append('<p><a href="?c=forum&a=index_tejia&wd='+tl[1]+'">'+tl[1]+'</a></p>'); 
						}
					} else {
						$.get('?c=index&a=get_file&f_url=http://www.liantu.com/tiaoma/query.php?ean='+cd, function(rs){
							rs = $.parseJSON(rs);
							$('div.qr-d').append('<p><a href="?c=forum&a=index_tejia&wd='+rs.supplier+'">'+rs.supplier+'：¥'+rs.price+'</a></p>'); 
							});
					}
				});
			}
		}
	});
}
