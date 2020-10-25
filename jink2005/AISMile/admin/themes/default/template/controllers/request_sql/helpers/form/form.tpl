{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{extends file="helpers/form/form.tpl"}

{block name="after"}
	<br />
	<fieldset>
		<legend>{l s='List of MySQL Tables:'}</legend>
		<div id="selectTables" style="float:left;width:200px">
			<select id="table" size="10">
				{foreach $tables as $table}
					<option value="{$table}">{$table}</option>
				{/foreach}
			</select><br />
			<input type="button" id="add_table" value="{l s='Add table'}" />
		</div>

		<div id="listAttributes" style="width:300px;margin-left:250px"></div>

	</fieldset>
{/block}

{block name="script"}
	$(document).ready(function() {
		$('#selectTables select option').click(function(){
			var table = $(this).val();
			//list attributes:
			$.ajax({
				url: 'index.php',
				data: {
					table: table,
					controller: 'adminrequestsql',
					token: '{$token}',
					action: 'addrequest_sql',
					ajax: true
				},
				context: document.body,
				dataType: 'json',
				context: this,
				async: false,
				success: function(data){
					var html = "<table class='table'>";
						html += "<thead>";
							html += "<tr>";
								html += "<th>{l s='Attribute'}</th>";
								html += "<th>{l s='Type'}</th>";
								html += "<th>{l s='Action'}</th>";
							html += "</tr>";
						html += "</thead>";
						html += "<tbody>";
						for (var i=0; i < data.length; i++)
						{
							html += "<tr>";
								html += "<td>"+data[i].Field+"</td>";
								html += "<td>"+data[i].Type+"</td>";
								html += "<td><input type=\"button\" class=\"add_attribute\" value=\"{l s='add attribute'}\" onclick=\"javascript:AddRequestSql('"+data[i].Field+"');\"/></td>";
							html += "</tr>";
						}
						html += "</tbody>";
					html += "</table>";
					$('#listAttributes').html(html);
				}
			});
		});

		$('#add_table').click(function(){
			var table = $('#selectTables select').val();

			if (!table)
				jAlert("{l s='Please choose table.'}");
			else
				AddRequestSql(table);
		});
	});

	function AddRequestSql(string)
	{
		var sql = $('#sql').val();
		$('#sql').val(sql+' '+string);
		return false;
	}
{/block}