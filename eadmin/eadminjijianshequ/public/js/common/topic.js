$(function()
{
	if ($('.tabbable').length)
	{
		AWS.load_list_view(G_BASE_URL + 'index.php?c=Ajaxinfo&a=gettopic&order=new&id=' + CONTENTS_TOPIC_TITLE, $('#c_all_more'), $('#c_all_list'), 1, function(){});
		
		AWS.load_list_view(G_BASE_URL + 'index.php?c=Ajaxinfo&a=gettopic&order=tj&id=' + CONTENTS_TOPIC_TITLE, $('#c_recommend_more'), $('#c_recommend_list'), 1, function(){});

		AWS.load_list_view(G_BASE_URL + 'index.php?c=Ajaxinfo&a=gettopic&order=mysc&id=' + CONTENTS_TOPIC_TITLE, $('#bp_favorite_more'), $('#c_favorite_list'), 1, function () { if ($('#c_favorite_list a').attr('id')) { $('#i_favorite').show() } });
	}


});