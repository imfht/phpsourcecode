$(document).ready(function()
{	
	bindStateInputAndUpdate();
});

function bindStateInputAndUpdate()
{
	$('select#id_country').change(function(){
		updateState();
	    updateCity();
		updateNeedIDNumber();
		updateZipCode();
	});
	$('select#id_state').change(function(){
    	updateCity();
		updateNeedIDNumber();
		updateZipCode();
	});
	updateState();
	updateCity();
	updateNeedIDNumber();
	updateZipCode();
	
	if ($('select#id_country_invoice').length != 0)
	{
		$('select#id_country_invoice').change(function(){
			updateState('invoice');
        	updateCity('invoice');
			updateNeedIDNumber('invoice');
			updateZipCode();
		});
		if ($('select#id_country_invoice:visible').length != 0)
		{
			updateState('invoice');
        	updateCity('invoice');
			updateNeedIDNumber('invoice');
			updateZipCode('invoice');
		}
	}
	if ($('select#id_state_invoice').length != 0)
	{
		$('select#id_state_invoice').change(function(){
        	updateCity('invoice');
			updateNeedIDNumber('invoice');
			updateZipCode();
		});
		if ($('select#id_state_invoice:visible').length != 0)
		{
        	updateCity('invoice');
			updateNeedIDNumber('invoice');
			updateZipCode('invoice');
		}
	}
};

function updateState(suffix)
{
	$('select#id_state'+(suffix !== undefined ? '_'+suffix : '')+' option:not(:first-child)').remove();
	var states = countries[$('select#id_country'+(suffix !== undefined ? '_'+suffix : '')).val()];
	if(typeof(states) != 'undefined')
	{
		$(states).each(function (key, item){
			$('select#id_state'+(suffix !== undefined ? '_'+suffix : '')).append('<option value="'+item.id+'"'+ (idSelectedCountry == item.id ? ' selected="selected"' : '') + '>'+item.name+'</option>');
		});
		
		$('p.id_state'+(suffix !== undefined ? '_'+suffix : '')+':hidden').slideDown('slow');
	}
	else
		$('p.id_state'+(suffix !== undefined ? '_'+suffix : '')).hide();
}

function updateCity(suffix)
{
	$('select#id_city'+(suffix !== undefined ? '_'+suffix : '')+' option:not(:first-child)').remove();
	$.ajax({
	  url: "ajax.php",
	  cache: false,
	  data: "ajaxCities=1&id_state="+$('select#id_state'+(suffix !== undefined ? '_'+suffix : '')).val()+"&id_city=0",
	  success: function(html)
	  {
	  	if (html == 'false')
		{
          $('p.id_city'+(suffix !== undefined ? '_'+suffix : '')).hide();
		}
	  	else
	  	{
          $('select#id_city'+(suffix !== undefined ? '_'+suffix : '')).html(html);		
		  $('p.id_city'+(suffix !== undefined ? '_'+suffix : '')+':hidden').slideDown('slow');
	  	}
	  }
	});
}

function updateNeedIDNumber(suffix)
{
	var idCountry = parseInt($('select#id_country'+(suffix !== undefined ? '_'+suffix : '')).val());

	if ($.inArray(idCountry, countriesNeedIDNumber) >= 0)
		$('.dni'+(suffix !== undefined ? '_'+suffix : '')).slideDown('slow');
	else
		$('.dni'+(suffix !== undefined ? '_'+suffix : '')).slideUp('fast');
}

function updateZipCode(suffix)
{
	var idCountry = parseInt($('select#id_country'+(suffix !== undefined ? '_'+suffix : '')).val());
	
	if (countriesNeedZipCode[idCountry] != 0)
		$('.postcode'+(suffix !== undefined ? '_'+suffix : '')).slideDown('slow');
	else
		$('.postcode'+(suffix !== undefined ? '_'+suffix : '')).slideUp('fast');
}
