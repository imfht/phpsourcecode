<?php
if(isset($widgets) && $widgets != null){
	foreach($widgets as $widget){
		echo "<div class=\"line line-widget\">";
		echo "<h4>{$widget['name']}</h4>
			<a href=\"{$widget['link']}\" target=\"_blank\">{$widget['link']}</a>
			<input type=\"hidden\" name=\"widget\" value=\"{$widget['id']}\" />";
		echo "</div>";
	}
}else{
	echo "<div class=\"line\">还没有内容哦</div>";
}
?>
