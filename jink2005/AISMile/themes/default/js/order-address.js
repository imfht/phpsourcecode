/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

$(document).ready(function()
{
	if (typeof(formatedAddressFieldsValuesList) != 'undefined')
		updateAddressesDisplay(true);
	resizeAddressesBox();
});

//update the display of the addresses
function updateAddressesDisplay(first_view)
{
	// update content of delivery address
	updateAddressDisplay('delivery');

	var txtInvoiceTitle = "";

	try{
		var adrs_titles = getAddressesTitles();
		txtInvoiceTitle = adrs_titles.invoice;
	}
	catch (e)
	{

	}

	if(!first_view)
	{
		if (orderProcess == 'order')
			updateAddresses();
	}
	return true;
}

function updateAddressDisplay(addressType)
{
	if (formatedAddressFieldsValuesList.length <= 0)
		return false;

	var idAddress = $('#id_address_' + addressType + '').val();
	buildAddressBlock(idAddress, addressType, $('#address_'+ addressType));

	// change update link
	var link = $('ul#address_' + addressType + ' li.address_update a').attr('href');
	var expression = /id_address=\d+/;
	if (link)
	{
		link = link.replace(expression, 'id_address='+idAddress);
		$('ul#address_' + addressType + ' li.address_update a').attr('href', link);
	}
	resizeAddressesBox();
}

function updateAddresses()
{
	var idAddress_delivery = $('#id_address_delivery').val();
	var idAddress_invoice = $('input[type=checkbox]#addressesAreEquals:checked').length == 1 ? idAddress_delivery : $('#id_address_invoice').val();
	$.ajax({
		type: 'POST',
		url: baseUri,
		async: true,
		cache: false,
		dataType : "json",
		data: {
			processAddress: true,
			step: 2,
			ajax: 'true',
			controller: 'order',
			'multi-shipping': $('#id_address_delivery:hidden').length,
			id_address_delivery: idAddress_delivery,
			id_address_invoice: idAddress_invoice,
			token: static_token
		},
		success: function(jsonData)
		{
			if (jsonData.hasError)
			{
				var errors = '';
				for(error in jsonData.errors)
					//IE6 bug fix
					if(error != 'indexOf')
						errors += jsonData.errors[error] + "\n";
				alert(errors);
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			if (textStatus != 'abort')
				alert("TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
		}
	});
	resizeAddressesBox();
}
