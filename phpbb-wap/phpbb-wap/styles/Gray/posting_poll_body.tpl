				<div class="title">投票</div>
				<div class="module bm-gray">
					<div id="poll-title">投票标题：</div>
					<div id="input-poll-title"><input type="text" name="poll_title" maxlength="255" value="{POLL_TITLE}" /></div>
				</div>
<!-- BEGIN poll_option_rows -->
				<div class="module">
					<div id="row-poll-option">投票选项：</div>
					<input type="text" name="poll_option_text[{poll_option_rows.S_POLL_OPTION_NUM}]" maxlength="255" value="{poll_option_rows.POLL_OPTION}" />
					<input type="submit" name="edit_poll_option" value="修改"/>
					<input class="subbutton" type="submit" name="del_poll_option[{poll_option_rows.S_POLL_OPTION_NUM}]" value="删除"/>
				</div>
<!-- END poll_option_rows -->
				<div class="module bm-gray">
					<div id="poll-option">添加选项：</div>
					<input type="text" name="add_poll_option_text" maxlength="255" value="{ADD_POLL_OPTION}"/>
					<input type="submit" name="add_poll_option" value="添加"/>
				</div>
				<div class="module bm-gray">
					有效期 <input type="text" name="poll_length" size="3" maxlength="3" value="{POLL_LENGTH}"/> 天（0为无限期）
				</div>
<!-- BEGIN switch_poll_delete_toggle -->
				<div class="module bm-gray">
					<input type="checkbox" name="poll_delete"/> 删除投票
				</div>
<!-- END switch_poll_delete_toggle -->