	<script type="text/javascript">
	// <![CDATA[
		var form_name = 'postform';
		var text_name = 'message';
	// ]]>
	</script>
	<script type="text/javascript" src="templates/prosilver/editor.js"></script>
	<h2>{L_EMOTICONS}</h2>
	<div class="panel">
		<div class="inner"><span class="corners-top"><span></span></span>
			<!-- BEGIN smilies_row -->
			<!-- BEGIN smilies_col -->
				<a href="#" onclick="insert_text('{smilies_row.smilies_col.SMILEY_CODE}', true, true); return false;"><img src="{smilies_row.smilies_col.SMILEY_IMG}" alt="{smilies_row.smilies_col.SMILEY_DESC}" title="{smilies_row.smilies_col.SMILEY_DESC}" /></a>
			<!-- END smilies_col -->
			<!-- END smilies_row -->
		<span class="corners-bottom"><span></span></span></div>
	</div>
	<a  href="#" onclick="window.close(); return false;">{L_CLOSE_WINDOW}</a>