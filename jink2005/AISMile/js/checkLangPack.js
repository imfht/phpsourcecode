/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function checkLangPack(token){
	if ($('#iso_code').val().length == 2)
	{
		$('#lang_pack_loading').show();
		$('#lang_pack_msg').hide();
		doAdminAjax(
			{
				controller:'AdminLanguages',
				action:'checkLangPack',
				token:token,
				ajax:1,
				iso_lang:$('#iso_code').val(), 
				ps_version:$('#ps_version').val()
			},
			function(ret)
			{
				$('#lang_pack_loading').hide();
				ret = $.parseJSON(ret);
				if( ret.status == 'ok')
				{
					content = $.parseJSON(ret.content);
					message = langPackOk + ' <b>'+content['name'] + '</b>) :'
						+'<br />' + langPackVersion + ' ' + content['version']
						+ ' <a href="http://www.milebiz.com/download/lang_packs/gzip/' + content['version'] + '/'
						+ $('#iso_code').val()+'.gzip" target="_blank" class="link">'+download+'</a><br />' + langPackInfo;
					$('#lang_pack_msg').html(message);
					$('#lang_pack_msg').show();
				}
				else
					showErrorMessage(ret.error);
			}
		 );
	 }
}

