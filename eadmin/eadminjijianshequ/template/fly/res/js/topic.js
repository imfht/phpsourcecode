$(function()
{
	if ($('.tabbable').length)
	{
		AWS.load_list_view_tem(G_BASE_URL + 'index.php?c=Ajaxinfo&a=gettopic&order=new&id=' + CONTENTS_TOPIC_TITLE, $('#c_all_more'), $('#c_all_list'), 1, function(data){
			
			var html = '';
			
			
			for(var i=0;i<data.data.data.length;i++){
				
			
					html = html+Hogan.compile(AW_TEMPLATE.httopiclist).render(
							{
								id:data.data.data[i].id,
								uid:data.data.data[i].uid,
								url:data.data.data[i].url,
								rzicon:data.data.data[i].rzicon,
								userurl:data.data.data[i].userurl,
								userhead:data.data.data[i].userhead,
								nickname:data.data.data[i].nickname,
								title:data.data.data[i].title,
								reply:data.data.data[i].reply,
								
								replystr:data.data.data[i].replystr,
								praise:data.data.data[i].praise,
								view:data.data.data[i].view,
							});
					
			
             
				
			
			}
			
			return html;
			
		});
		
		AWS.load_list_view_tem(G_BASE_URL + 'index.php?c=Ajaxinfo&a=gettopic&order=tj&id=' + CONTENTS_TOPIC_TITLE, $('#c_recommend_more'), $('#c_recommend_list'), 1, function(data){
			
	var html = '';
			
			
			for(var i=0;i<data.data.data.length;i++){
				
			
					html = html+Hogan.compile(AW_TEMPLATE.httopiclist).render(
							{
								id:data.data.data[i].id,
								uid:data.data.data[i].uid,
								url:data.data.data[i].url,
								rzicon:data.data.data[i].rzicon,
								userurl:data.data.data[i].userurl,
								userhead:data.data.data[i].userhead,
								nickname:data.data.data[i].nickname,
								title:data.data.data[i].title,
								reply:data.data.data[i].reply,
								
								replystr:data.data.data[i].replystr,
								praise:data.data.data[i].praise,
								view:data.data.data[i].view,
							});
					
			
             
				
			
			}
			
			return html;
		});

		AWS.load_list_view_tem(G_BASE_URL + 'index.php?c=Ajaxinfo&a=gettopic&order=mysc&id=' + CONTENTS_TOPIC_TITLE, $('#bp_favorite_more'), $('#c_favorite_list'), 1, function (data) { 
			
	var html = '';
			
			
			for(var i=0;i<data.data.data.length;i++){
				
			
					html = html+Hogan.compile(AW_TEMPLATE.httopiclist).render(
							{
								id:data.data.data[i].id,
								uid:data.data.data[i].uid,
								url:data.data.data[i].url,
								rzicon:data.data.data[i].rzicon,
								userurl:data.data.data[i].userurl,
								userhead:data.data.data[i].userhead,
								nickname:data.data.data[i].nickname,
								title:data.data.data[i].title,
								reply:data.data.data[i].reply,
								
								replystr:data.data.data[i].replystr,
								praise:data.data.data[i].praise,
								view:data.data.data[i].view,
							});
					
			
             
				
			
			}
			
			
			
			if ($('#c_favorite_list a').attr('id')) { $('#i_favorite').show() }
			
			return html;
		});
	}


});