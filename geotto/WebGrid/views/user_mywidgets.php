<?php
$pop_img = INCLUDES."/img/widget_pop.png";
$close_img = INCLUDES."/img/widget_close.png";

if(isset($widgets) && $widgets != null){
	foreach($widgets as $widget){
		$height = ($widget['height'] == null)?120:$widget['height'];
		
		echo "<div class=\"widget\">";
		echo "<div class=\"widget-header\">
			<div class=\"widget-header-title\">{$widget['name']}</div>
			<div class=\"widget-header-close\">
				<img src=\"$close_img\" />
			</div>
			<div class=\"widget-header-pop\">
				<img src=\"$pop_img\" />
			</div>
		</div>
		<iframe class=\"widget-content\" src=\"{$widget['link']}\" style=\"height:{$height}px\"></iframe>
		<input type=\"hidden\" name=\"widget\" value=\"{$widget['id']}\" />
		<input type=\"hidden\" name=\"widget_link\" value=\"{$widget['link']}\" />";
		echo "</div>";
	}
}else{
	echo "<div style=\"color: white\">快点击下面的添加按钮添加喜欢的控件吧</div>";
}
?>
