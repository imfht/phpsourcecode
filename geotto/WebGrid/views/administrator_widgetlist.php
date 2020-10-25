<?php
if(isset($widgets) && $widgets != null){
	foreach($widgets as $widget){
		echo "<div class=\"line line-widget\" style=\"cursor: pointer;\">";
		echo "<div  class=\"block\">{$widget['name']}</div>
			<div class=\"block\">{$widget['link']}</div>
			<div class=\"block\">{$widget['height']}</div>
			<input type=\"hidden\" name=\"widget\" value=\"{$widget['id']}\" />";
		echo "</div>";
	}
}
?>
