/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

$(document).ready(function(){

	$('#saveImportMatchs').unbind('click').click(function(){ 
	
	var newImportMatchs = $('#newImportMatchs').attr('value');
	if (newImportMatchs == '')
		jAlert(errorEmpty);
	else
	{
		var matchFields = '';
		$('.type_value').each( function () {
			matchFields += '&'+$(this).attr('id')+'='+$(this).attr('value');
		});
		$.ajax({
	       type: 'GET',
	       url: 'index.php',
	       async: false,
	       cache: false,
	       dataType : "json",
	       data: 'ajax=1&action=saveImportMatchs&skip='+$('input[name=skip]').attr('value')+'&newImportMatchs='+newImportMatchs+matchFields+'&tab=AdminImport&token='+token,
	       success: function(jsonData)
	       {
				$('#valueImportMatchs').append('<option id="'+jsonData.id+'" value="'+matchFields+'" selected="selected">'+newImportMatchs+'</option>');
				$('#selectDivImportMatchs').fadeIn('slow');
	       },
	      error: function(XMLHttpRequest, textStatus, errorThrown) 
	       {
	       		jAlert('TECHNICAL ERROR Details: '+XMLHttpRequest.responseText);
	       		
	       }
	   });

	}
	});
	
	$('#loadImportMatchs').unbind('click').click(function(){ 
	
		var idToLoad = $('select#valueImportMatchs option:selected').attr('id');
		$.ajax({
		       type: 'GET',
		       url: 'index.php',
		       async: false,
		       cache: false,
		       dataType : "json",
		       data: 'ajax=1&action=loadImportMatchs&idImportMatchs='+idToLoad+'&tab=AdminImport&token='+token,
		       success: function(jsonData)
		       {
					var matchs = jsonData.matchs.split('|')
					$('input[name=skip]').val(jsonData.skip);
					for (i=0;i<matchs.length;i++)
						$('#type_value\\['+i+'\\]').val(matchs[i]).attr('selected',true);
		       },
		      error: function(XMLHttpRequest, textStatus, errorThrown) 
		       {
		       		jAlert('TECHNICAL ERROR Details: '+XMLHttpRequest.responseText);
		       		
		       }
		   });
	});
	
	$('#deleteImportMatchs').unbind('click').click(function(){ 
	
		var idToDelete = $('select#valueImportMatchs option:selected').attr('id');
		$.ajax({
		       type: 'GET',
		       url: 'index.php',
		       async: false,
		       cache: false,
		       dataType : "json",
		       data: 'ajax=1&action=deleteImportMatchs&idImportMatchs='+idToDelete+'&tab=AdminImport&token='+token,
		       success: function(jsonData)
		       {
					$('select#valueImportMatchs option[id=\''+idToDelete+'\']').remove();
					if ($('select#valueImportMatchs option').length == 0)
						$('#selectDivImportMatchs').fadeOut();
		       },
		      error: function(XMLHttpRequest, textStatus, errorThrown) 
		       {
		       		jAlert('TECHNICAL ERROR Details: '+XMLHttpRequest.responseText);
		       }
		   });
	
	});
});