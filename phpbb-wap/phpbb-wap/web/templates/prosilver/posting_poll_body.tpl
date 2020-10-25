		<div class="panel bg3" id="poll-panel">
			<div class="inner"><span class="corners-top"><span></span></span>
			<p>{L_ADD_POLL_EXPLAIN}</p>
			<fieldset class="fields2">
				<!-- BEGIN switch_poll_delete_toggle -->
				<dl>
					<dt><label>{L_POLL_DELETE}:</label></dt>
					<dd><label><input type="checkbox" name="poll_delete" /></label></dd>
					</dl>
				<!-- END switch_poll_delete_toggle -->
				<dl>
					<dt><label>{L_POLL_QUESTION}:</label></dt>
					<dd><input type="text" name="poll_title" id="poll_title" size ="60" maxlength="255" value="{POLL_TITLE}" class="inputbox autowidth" /></dd>
				</dl>
				<!-- BEGIN poll_option_rows -->
				<dl>
					<dt><label>{L_POLL_OPTION}:</label></dt>
					<dd><input type="text" name="poll_option_text[{poll_option_rows.S_POLL_OPTION_NUM}]" size="35" class="inputbox autowidth" maxlength="60" value="{poll_option_rows.POLL_OPTION}" />&nbsp; <input type="submit" name="edit_poll_option" value="{L_UPDATE_OPTION}" class="button2" /> <input type="submit" name="del_poll_option[{poll_option_rows.S_POLL_OPTION_NUM}]" value="{L_DELETE_OPTION}" class="button2" /></dd>
				</dl>
				<!-- END poll_option_rows -->
				<dl>
					<dt><label>{L_POLL_OPTION}:</label></dt>
					<dd><input type="text" name="add_poll_option_text" size="35" maxlength="255" class="inputbox autowidth" value="{ADD_POLL_OPTION}" />&nbsp; <input type="submit" name="add_poll_option" value="{L_ADD_OPTION}" class="button2" /></dd>
				</dl>
				<hr class="dashed" />
				<dl>
					<dt><label>{L_POLL_LENGTH}:</label></dt>
					<dd><label><input type="text" name="poll_length" size="3" maxlength="3" value="{POLL_LENGTH}" class="inputbox autowidth" /> {L_DAYS}</label></dd>
					<dd>{L_POLL_LENGTH_EXPLAIN}</dd>
				</dl>
			</fieldset>
			<span class="corners-bottom"><span></span></span></div>
		</div>