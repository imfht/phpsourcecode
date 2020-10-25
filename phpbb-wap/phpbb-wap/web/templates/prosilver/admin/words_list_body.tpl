				<h1>{L_WORDS_TITLE}</h1>
				<p>{L_WORDS_TEXT}</p>
				<form method="post" action="{S_WORDS_ACTION}">
				<fieldset class="tabulated">
				<p class="quick">
					{S_HIDDEN_FIELDS}
					<input class="button2" name="add" type="submit" value="{L_ADD_WORD}" />
				</p>
				<table cellspacing="1">
				<thead>
				<tr>
					<th>{L_WORD}</th>
					<th>{L_REPLACEMENT}</th>
					<th>{L_ACTION}</th>
				</tr>
				</thead>
				<tbody>
				<!-- BEGIN words -->
				<tr class="{words.ROW_CLASS}">
					<td style="text-align:center">{words.WORD}</td>
					<td style="text-align:center">{words.REPLACEMENT}</td>
					<td style="text-align:center"><a href="{words.U_WORD_EDIT}" title="{L_EDIT}"><img src="../templates/prosilver/admin/images/icon_edit.gif" alt="" /></a> <a href="{words.U_WORD_DELETE}" title="{L_DELETE}"><img src="../templates/prosilver/admin/images/icon_delete.gif" alt="" /></a></td>
				</tr>
				<!-- END words -->
				</tbody>
				</table>
				</fieldset>
				</form>