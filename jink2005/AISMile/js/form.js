/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function ajaxStates (id_state_selected)
{
	$.ajax({
		url: "ajax.php",
		cache: false,
		data: "ajaxStates=1&id_country="+$('#id_country').val() + "&id_state=" + $('#id_state').val(),
		success: function(html)
		{
			if (html == 'false')
			{
				$("#contains_states").fadeOut();
				$('#id_state option[value=0]').attr("selected", "selected");
			}
			else
			{
				$("#id_state").html(html);
				$("#contains_states").fadeIn();
				$('#id_state option[value=' + id_state_selected + ']').attr("selected", "selected");
			}
		}
	});
	$("#contains_cities").fadeOut();
	$('#id_city option[value=0]').attr("selected", "selected");

	if (module_dir && vat_number)
	{
		$.ajax({
			type: "GET",
			url: module_dir + "vatnumber/ajax.php?id_country=" + $('#id_country').val(),
			success: function(isApplicable)
			{
				if(isApplicable == 1)
					$('#vat_area').show();
				else
					$('#vat_area').hide();
			}
		});
	}
}

function ajaxCity (id_city_selected,id_state_selected)
{
    if(id_state_selected == 0)
        id_state_selected = $('#id_state').val();
	$.ajax({
		url: "ajax.php",
		cache: false,
		data: "ajaxCities=1&id_state="+id_state_selected + "&id_city=" + $('#id_city').val(),
		success: function(html)
		{
			if (html == 'false')
			{
				$("#contains_cities").fadeOut();
				$('#id_city option[value=0]').attr("selected", "selected");
			}
			else
			{
				$("#id_city").html(html);
				$("#contains_cities").fadeIn();
				$('#id_city option[value=' + id_city_selected + ']').attr("selected", "selected");
			}
		}
	});
}